<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Notifications;

class NotificationService
{
    private static function send($userId, $title, $body, $type, $bookingId = null)
    {
        Notifications::create([
            'user_id'    => $userId,
            'booking_id' => $bookingId,
            'title'      => $title,
            'body'       => $body,
            'type'       => $type,
        ]);
    }

    // ===== المستأجر =====
    public static function bookingPending(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'الحجز قيد الانتظار',
            'تم إرسال طلب الحجز وبانتظار موافقة صاحب الشقة',
            'booking_pending',
            $booking->id
        );
    }

    public static function bookingApproved(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تمت الموافقة على الحجز',
            'وافق صاحب الشقة على طلب الحجز',
            'booking_approved',
            $booking->id
        );
    }

    public static function bookingRejected(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم رفض الحجز',
            'قام صاحب الشقة برفض طلب الحجز',
            'booking_rejected',
            $booking->id
        );
    }

    public static function bookingCancelled(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم إلغاء الحجز',
            'تم إلغاء الحجز بنجاح',
            'booking_cancelled',
            $booking->id
        );
    }

    public static function bookingUpdated(Booking $booking)
    {
        self::send(
            $booking->user_id,
            'تم تعديل الحجز',
            'تم تعديل الحجز وبانتظار موافقة صاحب الشقة',
            'booking_updated',
            $booking->id
        );
    }

    // ===== المؤجر =====
    public static function notifyOwnerNewBooking(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'طلب حجز جديد',
            'لديك طلب حجز جديد على شقتك',
            'owner_new_booking',
            $booking->id
        );
    }

    public static function notifyOwnerBookingUpdated(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'تعديل حجز',
            'قام المستأجر بتعديل الحجز',
            'owner_booking_updated',
            $booking->id
        );
    }

    public static function notifyOwnerBookingApproved(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'تمت الموافقة على الحجز',
            'قمت بالموافقة على الحجز',
            'owner_booking_approved',
            $booking->id
        );
    }

    public static function notifyOwnerBookingRejected(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            'تم رفض الحجز',
            'قمت برفض الحجز',
            'owner_booking_rejected',
            $booking->id
        );
    }

    public static function notifyOwnerBookingCancelled(Booking $booking)
    {
        self::send(
            $booking->apartment->user_id,
            ' تم الغاء الحجز على شقتك',
            'owner_booking_cancelled',
            $booking->id
        );
    }

}
