<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Services\NotificationService;
use Illuminate\Support\Facades\Auth;

class OwnerBookingController extends Controller
{
    public function approve($id)
    {
        $booking = Booking::with(['apartment', 'user'])
            ->whereHas('apartment', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        if ($booking->status === 'approved') {
            return response()->json(['message' => 'هذا الحجز تمت الموافقة عليه مسبقاً.'], 400);
        }

        $tenant = $booking->user;
        $owner  = $booking->apartment->user;
        $amount = $booking->total_price;

        if ($tenant->balance < $amount) {
            return response()->json([
                'success' => false,
                'message' => 'لم يعد رصيد المستأجر كافياً لإتمام العملية حالياً.'
            ], 400);
        }


        $tenant->balance -= $amount;
        $tenant->save();
        $owner->balance += $amount;
        $owner->save();
        $booking->status = 'approved';
        $booking->save();
        NotificationService::bookingApproved($booking);

        return response()->json([
            'success' => true,
            'message' => 'تمت الموافقة بنجاح وتم تحويل المبلغ للمالك.',
            'booking' => $booking
        ]);
    }


    public function reject($id)
    {
        $booking = Booking::with('apartment')
            ->whereHas('apartment', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);
             if ($booking->status === 'rejected') {
            return response()->json(['message' => 'هذا الحجز تم رفضه مسبقاً.'], 400);
        }

        $booking->update(['status' => 'rejected']);
        NotificationService::bookingRejected($booking);

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الحجز'
        ]);
    }

    public function index()
    {
        $bookings = Booking::with([
            'user:id,name',
            'apartment:id,name,price'
        ])
            ->whereHas('apartment', function ($q) {
                $q->where('user_id', Auth::id());
            })
            ->whereIn('status', ['pending', 'شكراً لتقييمك!'])->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => $bookings
        ]);
    }
}
