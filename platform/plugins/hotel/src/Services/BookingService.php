<?php

namespace Botble\Hotel\Services;

use Botble\Base\Facades\EmailHandler;
use Botble\Hotel\Models\Customer;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Payment\Repositories\Interfaces\PaymentInterface;

class BookingService
{
    public function processBooking(int $bookingId, string|null $chargeId = null)
    {
        $booking = app(BookingInterface::class)->findById($bookingId);

        if (! $booking) {
            return false;
        }

        if ($chargeId) {
            $payment = app(PaymentInterface::class)->getFirstBy(['charge_id' => $chargeId]);

            if ($payment) {
                $booking->payment_id = $payment->id;
                $booking->save();
            }
        }

        if (! Customer::query()->where('email', $booking->address->email)->exists()) {
            Customer::query()->create([
                'first_name' => $booking->address->first_name,
                'last_name' => $booking->address->last_name,
                'email' => $booking->address->email,
                'phone' => $booking->address->phone,
            ]);
        }

        EmailHandler::setModule(HOTEL_MODULE_SCREEN_NAME)
            ->setVariableValues([
                'booking_name' => $booking->address->first_name ? $booking->address->first_name . ' ' . $booking->address->last_name : 'N/A',
                'booking_email' => $booking->address->email ?? 'N/A',
                'booking_phone' => $booking->address->phone ?? 'N/A',
                'booking_address' => $booking->address->id ? $booking->address->address . ', ' . $booking->address->city . ', ' . $booking->address->state . ', ' . $booking->address->country . ', ' . $booking->address->zip : 'N/A',
                'booking_link' => route('public.booking.information', $booking->transaction_id),
            ]);

        EmailHandler::sendUsingTemplate('booking-confirmation', $booking->address->email);
        EmailHandler::sendUsingTemplate('booking-notice-to-admin');

        return $booking;
    }
}
