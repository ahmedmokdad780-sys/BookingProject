<?php

namespace App\Http\Controllers;

use App\Models\Apartment;
use App\Models\Booking;
use App\Services\NotificationService;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class BookingController extends Controller
{
    public function index(Request $request)
    {
        $status = $request->query('status');

        $query = Booking::where('user_id', Auth::id())
            ->with(['apartment.images']);

        if ($status && $status !== 'all') {
            $query->where('status', $status);
        }

        return response()->json([
            'success' => true,
            'data' => $query->latest()->get()
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'apartment_id' => 'required|exists:apartments,id',
            'start_date'   => 'required|date|after_or_equal:today',
            'end_date'     => 'required|date|after:start_date',
        ]);

        $apartment = Apartment::findOrFail($request->apartment_id);

        // 1. منع حجز الشقة الخاصة
        if ($apartment->user_id === Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكنك حجز شقتك الخاصة'
            ], 403);
        }

        // حساب عدد الأيام والسعر الإجمالي
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);
        $days = $start->diffInDays($end) + 1;
        $totalPrice = $days * $apartment->price;

        // 2. التحقق من رصيد المستأجر قبل إنشاء الحجز
        $user = Auth::user();
        if ($user->balance < $totalPrice) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، رصيدك الحالي (' . $user->balance . ') غير كافٍ لإتمام هذا الحجز. التكلفة الإجمالية: ' . $totalPrice
            ], 400);
        }

        // 3. التحقق من تضارب المواعيد
        $hasConflict = Booking::where('apartment_id', $request->apartment_id)
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'success' => false,
                'message' => 'يوجد تضارب مع حجز آخر في هذه الفترة'
            ], 409);
        }

        // 4. إنشاء الحجز
        $booking = Booking::create([
            'user_id'      => Auth::id(),
            'apartment_id' => $apartment->id,
            'start_date'   => $request->start_date,
            'end_date'     => $request->end_date,
            'total_price'  => $totalPrice,
            'status'       => 'pending',
        ]);

        NotificationService::bookingPending($booking);
        NotificationService::notifyOwnerNewBooking($booking);

        return response()->json([
            'success' => true,
            'message' => 'تم طلب الحجز بنجاح، رصيدك كافٍ وبانتظار موافقة المالك.',
            'data'    => $booking->load('apartment')
        ], 201);
    }


    public function update(Request $request, $id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($id);


        if (in_array($booking->status, ['cancelled', 'rejected'])) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل هذا الحجز'
            ], 403);
        }
        $start = Carbon::parse($request->start_date);
        $end   = Carbon::parse($request->end_date);

        $hasConflict = Booking::where('apartment_id', $booking->apartment_id)
            ->where('id', '!=', $booking->id) // ❗ استثناء الحجز الحالي
            ->whereIn('status', ['approved', 'pending'])
            ->where(function ($q) use ($start, $end) {
                $q->whereBetween('start_date', [$start, $end])
                    ->orWhereBetween('end_date', [$start, $end])
                    ->orWhere(function ($q) use ($start, $end) {
                        $q->where('start_date', '<=', $start)
                            ->where('end_date', '>=', $end);
                    });
            })
            ->exists();

        if ($hasConflict) {
            return response()->json([
                'success' => false,
                'message' => 'لا يمكن تعديل الحجز بسبب تضارب في التواريخ'
            ], 409);
        }

        $booking->update([
            'start_date' => $request->start_date,
            'end_date'   => $request->end_date,
            'status'     => 'pending',
        ]);

        $booking->update($request->only('start_date', 'end_date'));

        NotificationService::bookingUpdated($booking);
        NotificationService::notifyOwnerBookingUpdated($booking);

        return response()->json([
            'success' => true,
            'message' => 'تم تعديل الحجز وبانتظار موافقة صاحب الشقة'
        ]);
    }

    public function cancel($id)
    {
        $booking = Booking::with(['apartment.user', 'user'])
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        if ($booking->status === 'approved') {
            $tenant = $booking->user;
            $owner  = $booking->apartment->user;
            $amount = $booking->total_price;
            $tenant->balance += $amount;
            $tenant->save();
            $owner->balance -= $amount;
            $owner->save();
        }

        $booking->update(['status' => 'cancelled']);

        NotificationService::bookingCancelled($booking);
        NotificationService::notifyOwnerBookingCancelled($booking);

        return response()->json([
            'success' => true,
            'message' => 'تم إلغاء الحجز وإرجاع الرصيد'
        ]);
    }

    /****************************** */
    public function rate(Request $request, $booking_id)
    {
        $booking = Booking::where('user_id', Auth::id())->findOrFail($booking_id);
        if (now()->format('Y-m-d') < $booking->end_date) {
            return response()->json([
                'success' => false,
                'message' => 'عذراً، لا يمكنك التقييم إلا بعد انتهاء فترة الإقامة.'
            ], 403);
        }

        $alreadyReviewed = Review::where('booking_id', $booking_id)->exists();
        if ($alreadyReviewed) {
            return response()->json([
                'success' => false,
                'message' => 'لقد قمت بتقييم هذا الحجز بالفعل.'
            ], 400);
        }

        $request->validate([
            'rating' => 'required|integer|min:1|max:5',
            'comment' => 'nullable|string|max:1000'
        ]);

        $review = Review::create([
            'user_id' => Auth::id(),
            'apartment_id' => $booking->apartment_id,
            'booking_id' => $booking->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'شكراً لتقييمك!',
            'data' => [
                'id' => $review->id,
                'user_name' => Auth::user()->name . ' ' . Auth::user()->last_name,
                'rating' => $review->rating,
                'apartment_id' => $review->apartment_id
            ]
        ]);
    }

    public function getApartmentStats($apartment_id)
    {
        $apartment = Apartment::findOrFail($apartment_id);

        $stats = Review::where('apartment_id', $apartment_id)
            ->selectRaw('AVG(rating) as average, COUNT(*) as total')
            ->first();

        return response()->json([
            'success' => true,
            'data' => [
                'apartment_id' => $apartment->id,
                'apartment_name' => $apartment->name,
                'average_rating' => round($stats->average, 1) ?: 0,
                'total_reviews' => $stats->total
            ]
        ]);
    }

    /******** Show date*/
    public function ShowbookedDates($apartmentId)
    {
        $apartment = Apartment::with(['bookings' => function ($q) {
            $q->whereIn('status', ['approved', 'pending'])
                ->orderBy('start_date', 'asc');
        }])->findOrFail($apartmentId);

        $formattedBookings = [];

        foreach ($apartment->bookings as $booking) {
            $formattedBookings[] = [
                'from' => \Carbon\Carbon::parse($booking->start_date)->format('Y-m-d'),
                'to'   => \Carbon\Carbon::parse($booking->end_date)->format('Y-m-d'),
            ];
        }

        return response()->json([
            'success' => true,
            'apartment_id' => $apartmentId,
            'booked_periods' => $formattedBookings
        ]);
    }
}
