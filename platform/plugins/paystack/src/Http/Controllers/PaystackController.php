<?php

namespace Botble\Paystack\Http\Controllers;

use Botble\Base\Http\Controllers\BaseController;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Hotel\Services\BookingService;
use Botble\Payment\Enums\PaymentStatusEnum;
use Illuminate\Http\Request;
use Paystack;

class PaystackController extends BaseController
{
    public function getPaymentStatus(Request $request, BaseHttpResponse $response, BookingService $bookingService)
    {
        $result = Paystack::getPaymentData();

        $booking = app(BookingInterface::class)->findById($result['data']['metadata']['order_id']);

        if (! $result['status']) {
            return $response
                ->setError()
                ->setNextUrl(url($result['data']['metadata']['return_url']))
                ->setMessage($result['message']);
        }

        do_action(PAYMENT_ACTION_PAYMENT_PROCESSED, [
            'amount' => $result['data']['amount'] / 100,
            'currency' => $result['data']['currency'],
            'charge_id' => $result['data']['reference'],
            'payment_channel' => PAYSTACK_PAYMENT_METHOD_NAME,
            'status' => PaymentStatusEnum::COMPLETED,
            'customer_id' => null,
            'payment_type' => 'direct',
            'order_id' => $result['data']['metadata']['order_id'],
        ], $request);

        $bookingService->processBooking($result['data']['metadata']['order_id'], $result['data']['reference']);

        return $response
            ->setNextUrl(route('public.booking.information', $booking->transaction_id))
            ->setMessage(__('Booking successfully!'));
    }
}
