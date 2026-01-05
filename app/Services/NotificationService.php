<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notifications;

class NotificationService
{
    private static function send($userId, $title, $body)
    {
       Notifications::create([
            'user_id' => $userId,
            'title'   => $title,
            'body'    => $body,
            'type'    => 'booking'
        ]);
    }

    // ===== المستأجر =====
    public static function bookingPending(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'الحجز قيد الانتظار',
            'طلب الحجز قيد المراجعة من صاحب الشقة'
        );
    }

    public static function bookingApproved(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم الحجز بنجاح',
            'تمت الموافقة على طلب الحجز'
        );
    }

    public static function bookingRejected(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم رفض الحجز',
            'نأسف، تم رفض طلب الحجز'
        );
    }

    public static function bookingCancelled(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم إلغاء الحجز',
            'تم إلغاء الحجز بنجاح'
        );
    }

    public static function bookingUpdated(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم تعديل الحجز',
            'تم تعديل تفاصيل الحجز'
        );
    }

    // ===== صاحب الشقة =====
    public static function notifyOwnerNewBooking(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'طلب حجز جديد',
            'تم طلب حجز على شقتك'
        );
    }

    public static function notifyOwnerBookingUpdated(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'تعديل حجز',
            'قام المستأجر بتعديل طلب الحجز'
        );
    }

    public static function notifyOwnerBookingCancelled(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'إلغاء حجز',
            'قام المستأجر بإلغاء الحجز'
        );
    }
}
