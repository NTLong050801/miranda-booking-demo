<?php

namespace Botble\Hotel\Providers;

use Botble\Hotel\Models\Customer;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Hotel\Services\BookingService;
use Botble\Payment\Models\Payment;
use Botble\Payment\Supports\PaymentHelper;
use Collective\Html\HtmlFacade as Html;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Js;
use Illuminate\Support\ServiceProvider;

class HookServiceProvider extends ServiceProvider
{
    public function boot(): void
    {
        add_filter(BASE_FILTER_TOP_HEADER_LAYOUT, [$this, 'registerTopHeaderNotification'], 140);
        add_filter(BASE_FILTER_APPEND_MENU_NAME, [$this, 'countPendingBookings'], 140, 2);
        add_filter(BASE_FILTER_MENU_ITEMS_COUNT, [$this, 'getMenuItemCount'], 140);

        if (defined('PAYMENT_FILTER_REDIRECT_URL')) {
            add_filter(PAYMENT_FILTER_REDIRECT_URL, function ($checkoutToken) {
                return route('public.booking.information', $checkoutToken ?: session('booking_transaction_id'));
            }, 123);
        }

        if (defined('PAYMENT_FILTER_CANCEL_URL')) {
            add_filter(PAYMENT_FILTER_CANCEL_URL, function () {
                return route('public.booking.form', ['token' => session('checkout_token')] + ['error' => true, 'error_type' => 'payment']);
            }, 123);
        }

        if (defined('PAYMENT_ACTION_PAYMENT_PROCESSED')) {
            add_action(PAYMENT_ACTION_PAYMENT_PROCESSED, function ($data) {
                $orderIds = $data['order_id'];
                $orderId = Arr::first($orderIds);

                PaymentHelper::storeLocalPayment($data);

                return $this->app->make(BookingService::class)->processBooking($orderId, $data['charge_id']);
            });
        }

        if (defined('PAYMENT_FILTER_PAYMENT_DATA')) {
            add_filter(PAYMENT_FILTER_PAYMENT_DATA, function (array $data, Request $request) {
                $orderIds = (array)$request->input('order_id', []);

                $booking = $this->app->make(BookingInterface::class)
                    ->findById(Arr::first($orderIds));

                $products = [
                    [
                        'id' => $booking->id,
                        'name' => $booking->room->room->name,
                        'price' => $booking->amount,
                        'price_per_order' => $booking->amount,
                        'qty' => 1,
                    ],
                ];

                $address = [
                    'name' => $booking->address->first_name . ' ' . $booking->address->last_name,
                    'email' => $booking->address->email,
                    'phone' => $booking->address->phone,
                    'country' => $booking->address->country,
                    'state' => $booking->address->state,
                    'city' => $booking->address->city,
                    'address' => $booking->address->address,
                    'zip' => $booking->address->zip,
                ];

                return [
                    'amount' => (float)$booking->amount,
                    'shipping_amount' => 0,
                    'shipping_method' => null,
                    'tax_amount' => 0,
                    'discount_amount' => 0,
                    'currency' => strtoupper(get_application_currency()->title),
                    'order_id' => $orderIds,
                    'description' => trans('plugins/payment::payment.payment_description', ['order_id' => Arr::first($orderIds), 'site_url' => request()->getHost()]),
                    'customer_id' => 0,
                    'customer_type' => Customer::class,
                    'return_url' => $request->input('return_url'),
                    'callback_url' => $request->input('callback_url'),
                    'products' => $products,
                    'orders' => [$booking],
                    'address' => $address,
                    'checkout_token' => session('checkout_token'),
                ];
            }, 120, 2);
        }

        if (defined('PAYMENT_FILTER_PAYMENT_INFO_DETAIL')) {
            add_filter(PAYMENT_FILTER_PAYMENT_INFO_DETAIL, function ($html, $payment) {
                if (! $payment->order_id) {
                    return $html;
                }

                $booking = $this->app->make(BookingInterface::class)->findById($payment->order_id);

                if (! $booking || ! $booking->address) {
                    return $html;
                }

                $html .= Html::tag('p', trans('plugins/payment::payment.payer_name') . ': ' . Html::tag('strong', $booking->address->first_name . ' ' . $booking->address->last_name))->toHtml();
                $html .= Html::tag('p', trans('plugins/payment::payment.email') . ': ' . Html::tag('strong', $booking->address->email))->toHtml();
                $html .= Html::tag('p', trans('plugins/payment::payment.phone') . ': ' . Html::tag('strong', $booking->address->phone ?: 'N/A'))->toHtml();

                return $html;
            }, 123, 2);
        }

        add_filter(BASE_FILTER_GET_LIST_DATA, function ($data, $model) {
            if (get_class($model) == Payment::class) {
                return $data
                    ->addColumn('customer_id', function ($item) {
                        if (! $item->order_id) {
                            return '&mdash;';
                        }

                        $booking = $this->app->make(BookingInterface::class)->findById($item->order_id);

                        if (! $booking) {
                            return '&mdash;';
                        }

                        return $booking->address->first_name . ' ' . $booking->address->last_name;
                    })
                    ->filter(function ($query) {
                        $keyword = request()->input('search.value');
                        if ($keyword) {
                            return $query
                                ->join('ht_bookings', 'ht_bookings.id', '=', 'payments.order_id')
                                ->join('ht_booking_addresses', 'ht_booking_addresses.booking_id', '=', 'ht_bookings.id')
                                ->where(function ($subQuery) use ($keyword) {
                                    return $subQuery
                                        ->where('ht_booking_addresses.first_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere('ht_booking_addresses.last_name', 'LIKE', '%' . $keyword . '%')
                                        ->orWhere(DB::raw('CONCAT(ht_booking_addresses.first_name, " ", ht_booking_addresses.last_name)'), 'LIKE', '%' . $keyword . '%')
                                        ->orWhere(DB::raw('CONCAT(ht_booking_addresses.last_name, " ", ht_booking_addresses.first_name)'), 'LIKE', '%' . $keyword . '%');
                                })
                                ->select('payments.*');
                        }

                        return $query;
                    });
            }

            return $data;
        }, 123, 2);

        add_filter(BASE_FILTER_TABLE_HEADINGS, function ($headings, $model) {
            if (get_class($model) == Payment::class) {
                return array_merge($headings, [
                    'customer_id' => [
                        'title' => trans('plugins/hotel::booking.customer'),
                        'class' => 'text-center no-sort',
                        'orderable' => false,
                        'searchable' => false,
                    ],
                ]);
            }

            return $headings;
        }, 123, 2);

        add_filter(BASE_FILTER_FOOTER_LAYOUT_TEMPLATE, function ($html) {
            return $html . Html::tag('script', 'window.trans = window.trans || {}; window.trans.room_availability = ' . Js::from(trans('plugins/hotel::room.form')) . ';');
        }, 120);
    }

    public function registerTopHeaderNotification(string|null $options): string
    {
        if (Auth::user()->hasPermission('booking.edit')) {
            $bookings = $this->app->make(BookingInterface::class)
                ->getPendingBookings(['id', 'created_at'], ['address']);

            if ($bookings->count() == 0) {
                return $options;
            }

            return $options . view('plugins/hotel::notification', compact('bookings'))->render();
        }

        return $options;
    }

    public function countPendingBookings(int|string|null $number, string $menuId): string|null
    {
        if ($menuId == 'cms-plugins-booking') {
            $attributes = [
                'class' => 'badge badge-success menu-item-count pending-bookings',
                'style' => 'display: none;',
            ];

            return Html::tag('span', '', $attributes)->toHtml();
        }

        return $number;
    }

    public function getMenuItemCount(array $data = []): array
    {
        if (Auth::user()->hasPermission('booking.index')) {
            $data[] = [
                'key' => 'pending-bookings',
                'value' => app(BookingInterface::class)->countPendingBookings(),
            ];
        }

        return $data;
    }
}
