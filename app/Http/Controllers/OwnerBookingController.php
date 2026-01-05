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

    $tenant = $booking->user;
    $owner  = $booking->apartment->user;
    $amount = $booking->total_price;

    // التحقق من رصيد المستأجر
    if ($tenant->balance < $amount) {
        return response()->json([
            'success' => false,
            'message' => 'رصيد المستأجر غير كافٍ لإتمام الحجز.'
        ], 400);
    }

    // خصم من المستأجر
    $tenant->balance -= $amount;
    $tenant->save();

    // إضافة للمؤجر
    $owner->balance += $amount;
    $owner->save();

    // تغيير حالة الحجز
    $booking->status = 'approved';
    $booking->save();

    // إرسال الإشعار
    NotificationService::bookingApproved($booking);

    return response()->json([
        'success' => true,
        'message' => 'تمت الموافقة على الحجز وتم خصم الرصيد من المستأجر وإضافته للمؤجر.',
        'booking' => $booking
    ]);
}


    public function reject($id)
    {
        $booking = Booking::with('apartment')
            ->whereHas('apartment', fn($q) => $q->where('user_id', Auth::id()))
            ->findOrFail($id);

        $booking->update(['status' => 'rejected']);
        NotificationService::bookingRejected($booking);

        return response()->json([
            'success' => true,
            'message' => 'تم رفض الحجز'
        ]);
    }
}
