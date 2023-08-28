<?php

namespace Botble\Hotel\Http\Controllers;

use Botble\Base\Enums\BaseStatusEnum;
use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\CalculateBookingAmountRequest;
use Botble\Hotel\Http\Requests\CheckoutRequest;
use Botble\Hotel\Http\Requests\InitBookingRequest;
use Botble\Hotel\Models\Place;
use Botble\Hotel\Models\Room;
use Botble\Hotel\Repositories\Interfaces\BookingAddressInterface;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Hotel\Repositories\Interfaces\BookingRoomInterface;
use Botble\Hotel\Repositories\Interfaces\CurrencyInterface;
use Botble\Hotel\Repositories\Interfaces\PlaceInterface;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Hotel\Repositories\Interfaces\ServiceInterface;
use Botble\Hotel\Services\BookingService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Supports\PaymentHelper;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\SeoHelper\SeoOpenGraph;
use Carbon\Carbon;
use Collective\Html\HtmlFacade as Html;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;
use Botble\Media\Facades\RvMedia;
use Botble\SeoHelper\Facades\SeoHelper;
use Botble\Slug\Facades\SlugHelper;
use Botble\Theme\Facades\Theme;

class PublicController extends Controller
{
    public function getRooms(Request $request, RoomInterface $roomRepository, BaseHttpResponse $response)
    {
        SeoHelper::setTitle(__('Rooms'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Rooms'), route('public.rooms'));

        if ($request->ajax() && $request->wantsJson()) {
            if ($request->input('start_date') && $request->input('end_date')) {
                $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'));
                $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'));
            } else {
                $startDate = Carbon::now();
                $endDate = Carbon::now()->addDay();
            }

            $filters = [
                'keyword' => $request->query('q'),
            ];

            $condition = [
                'start_date' => $startDate->format('Y-m-d'),
                'end_date' => $endDate->format('Y-m-d'),
                'adults' => (int)$request->input('adults', 1),
            ];

            $params = [
                'paginate' => [
                    'per_page' => 10,
                    'current_paged' => (int)$request->input('page', 1),
                ],
                'with' => [
                    'amenities',
                    'amenities.metadata',
                    'slugable',
                    'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->where(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('start_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('end_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '<=', $startDate)
                                    ->whereDate('end_date', '>=', $endDate);
                            })
                            ->orWhere(function ($query) use ($startDate, $endDate) {
                                return $query
                                    ->whereDate('start_date', '>=', $startDate)
                                    ->whereDate('end_date', '<=', $endDate);
                            });
                    },
                    'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                        return $query
                            ->whereDate('start_date', '>=', $startDate)
                            ->whereDate('end_date', '<=', $endDate)
                            ->take(40);
                    },
                ],
            ];

            $allRooms = $roomRepository->getRooms($filters, $params);

            $nights = $endDate->diffInDays($startDate);

            $rooms = [];
            foreach ($allRooms as $allRoom) {
                if ($allRoom->isAvailableAt($condition)) {
                    $allRoom->total_price = $allRoom->getRoomTotalPrice($startDate, $endDate, $nights);
                    $rooms[] = $allRoom;
                }
            }

            $data = null;

            foreach ($rooms as $room) {
                $data = view(
                    Theme::getThemeNamespace() . '::views.hotel.includes.room-item',
                    compact('room')
                )->render();
            }

            return $response->setData($data);
        }

        return Theme::scope('hotel.rooms')->render();
    }

    public function getRoom(string $key, RoomInterface $roomRepository)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Room::class));

        if (!$slug) {
            abort(404);
        }

        $room = $roomRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
            ['amenities', 'currency', 'category']
        );

        if (!$room) {
            abort(404);
        }

        SeoHelper::setTitle($room->name)->setDescription(Str::words($room->description, 120));

        $meta = new SeoOpenGraph();
        if ($room->image) {
            $meta->setImage(RvMedia::getImageUrl($room->image));
        }
        $meta->setDescription($room->description);
        $meta->setUrl($room->url);
        $meta->setTitle($room->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add($room->name, $room->url);

        if (function_exists('admin_bar')) {
            admin_bar()->registerLink(__('Edit this room'), route('room.edit', $room->id));
        }

        $startDate = Carbon::now()->format('d-m-Y');
        $endDate = Carbon::now()->addDay()->format('d-m-Y');

        $condition = [
            'start_date' => $startDate,
            'end_date' => $endDate,
        ];

        $relatedRooms = $roomRepository->getRelatedRooms(
            $room->id,
            (int)theme_option('number_of_related_rooms', 2),
            [
                'with' => [
                    'amenities',
                    'slugable',
                    'activeBookingRooms' => function ($query) use ($condition) {
                        return $query
                            ->whereDate('start_date', '<=', date('Y-m-d', strtotime($condition['start_date'])))
                            ->whereDate('end_date', '>=', date('Y-m-d', strtotime($condition['end_date'])));
                    },
                    'activeRoomDates' => function ($query) use ($condition) {
                        return $query
                            ->whereDate('start_date', '>=', date('Y-m-d', strtotime($condition['start_date'])))
                            ->whereDate('end_date', '<=', date('Y-m-d', strtotime($condition['end_date'])))
                            ->take(40);
                    },
                ],
            ]
        );

        foreach ($relatedRooms as &$relatedRoom) {
            if ($relatedRoom->isAvailableAt($condition)) {
                $relatedRoom->total_price = $relatedRoom->getRoomTotalPrice($startDate, $endDate);
            }
        }

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, ROOM_MODULE_SCREEN_NAME, $room);

        $images = [];
        foreach ($room->images as $image) {
            $images[] = RvMedia::getImageUrl($image, null, false, RvMedia::getDefaultImage());
        }

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate);

        Theme::asset()->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');

        $room->content = Html::tag('div', (string)$room->content, ['class' => 'ck-content'])->toHtml();

        return Theme::scope('hotel.room', compact('room', 'images', 'relatedRooms'))->render();
    }

    public function getPlace(string $key, PlaceInterface $placeRepository)
    {
        $slug = SlugHelper::getSlug($key, SlugHelper::getPrefix(Place::class));

        if (!$slug) {
            abort(404);
        }

        $place = $placeRepository->getFirstBy(
            ['id' => $slug->reference_id],
            ['*'],
            ['slugable']
        );

        if (!$place) {
            abort(404);
        }

        SeoHelper::setTitle($place->name)->setDescription(Str::words($place->description, 120));

        $meta = new SeoOpenGraph();
        if ($place->image) {
            $meta->setImage(RvMedia::getImageUrl($place->image));
        }
        $meta->setDescription($place->description);
        $meta->setUrl($place->url);
        $meta->setTitle($place->name);
        $meta->setType('article');

        SeoHelper::setSeoOpenGraph($meta);

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add($place->name, $place->url);

        $relatedPlaces = $placeRepository->getRelatedPlaces($place->id, 3);

        do_action(BASE_ACTION_PUBLIC_RENDER_SINGLE, PLACE_MODULE_SCREEN_NAME, $place);

        Theme::asset()->add('ckeditor-content-styles', 'vendor/core/core/base/libraries/ckeditor/content-styles.css');

        $place->content = Html::tag('div', (string)$place->content, ['class' => 'ck-content'])->toHtml();

        return Theme::scope('hotel.place', compact('place', 'relatedPlaces'))->render();
    }

    public function postBooking(InitBookingRequest $request, RoomInterface $roomRepository, BaseHttpResponse $response)
    {
        $room = $roomRepository->getFirstBy(
            ['id' => $request->input('room_id')],
            ['*'],
            ['currency', 'category']
        );

        if (!$room) {
            abort(404);
        }

        $token = md5(Str::random(40));

        session([
            $token => $request->except(['_token']),
            'checkout_token' => $token,
        ]);

        return $response->setNextUrl(route('public.booking.form', $token));
    }

    public function getBooking(
        string           $token,
        RoomInterface    $roomRepository,
        ServiceInterface $serviceRepository,
        BaseHttpResponse $response
    )
    {
        SeoHelper::setTitle(__('Booking'));

        $sessionData = [];
        if (session()->has($token)) {
            $sessionData = session($token);
        }

        if (empty($sessionData)) {
            abort(404);
        }

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Booking'), route('public.booking'));

        $startDate = Carbon::createFromFormat('d-m-Y', Arr::get($sessionData, 'start_date'));
        $endDate = Carbon::createFromFormat('d-m-Y', Arr::get($sessionData, 'end_date'));
        $nights = $endDate->diffInDays($startDate);
        $adults = Arr::get($sessionData, 'adults');

        $room = $roomRepository->getFirstBy(
            ['id' => Arr::get($sessionData, 'room_id')],
            ['*'],
            [
                'currency',
                'category',
                'activeBookingRooms' => function ($query) use ($startDate, $endDate) {
                    return $query
                        ->where(function ($query) use ($startDate, $endDate) {
                            return $query
                                ->whereDate('start_date', '>=', $startDate)
                                ->whereDate('start_date', '<=', $endDate);
                        })
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            return $query
                                ->whereDate('end_date', '>=', $startDate)
                                ->whereDate('end_date', '<=', $endDate);
                        })
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            return $query
                                ->whereDate('start_date', '<=', $startDate)
                                ->whereDate('end_date', '>=', $endDate);
                        })
                        ->orWhere(function ($query) use ($startDate, $endDate) {
                            return $query
                                ->whereDate('start_date', '>=', $startDate)
                                ->whereDate('end_date', '<=', $endDate);
                        });
                },
                'activeRoomDates' => function ($query) use ($startDate, $endDate) {
                    return $query
                        ->whereDate('start_date', '>=', $startDate)
                        ->whereDate('end_date', '<=', $endDate)
                        ->take(40);
                },
            ]
        );

        if (!$room) {
            abort(404);
        }

        if (!$room->isAvailableAt(['start_date' => $startDate, 'end_date' => $endDate])) {
            return $response
                ->setError()
                ->setMessage(__(
                    'This room is not available for booking from :start_date to :end_date!',
                    ['start_date' => $startDate->toDateString(), 'end_date' => $endDate->toDateString()]
                ))
                ->withInput();
        }

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate, $nights);

        $taxAmount = $room->tax->percentage * $room->total_price / 100;

        $total = $room->total_price + $taxAmount;

        $services = $serviceRepository->allBy(['status' => BaseStatusEnum::PUBLISHED]);

        return Theme::scope(
            'hotel.booking',
            compact(
                'room',
                'services',
                'startDate',
                'endDate',
                'adults',
                'total',
                'taxAmount',
                'token'
            )
        )->render();
    }

    public function postCheckout(
        CheckoutRequest         $request,
        BookingInterface        $bookingRepository,
        RoomInterface           $roomRepository,
        BookingAddressInterface $bookingAddressRepository,
        BookingRoomInterface    $bookingRoomRepository,
        ServiceInterface        $serviceRepository,
        BookingService          $bookingService,
        BaseHttpResponse        $response
    )
    {
        $room = $roomRepository->findOrFail($request->input('room_id'));

        $booking = $bookingRepository->getModel();
        $booking->fill($request->input());

        $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'));
        $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'));
        $nights = $endDate->diffInDays($startDate);

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate, $nights);

        $taxAmount = $room->tax->percentage * $room->total_price / 100;

        $booking->amount = $room->total_price + $taxAmount;
        $booking->tax_amount = $taxAmount;

        $booking->transaction_id = Str::upper(Str::random(32));

        $serviceIds = $request->input('services');

        if ($serviceIds) {
            $services = $serviceRepository->getModel()
                ->whereIn('id', $serviceIds)
                ->get();

            foreach ($services as $service) {
                $booking->amount += $service->price;
            }
        }

        $booking = $bookingRepository->createOrUpdate($booking);

        if ($serviceIds) {
            $booking->services()->attach($serviceIds);
        }

        session()->put('booking_transaction_id', $booking->transaction_id);

        $bookingRoomRepository->createOrUpdate([
            'room_id' => $room->id,
            'booking_id' => $booking->id,
            'price' => $room->total_price,
            'currency_id' => $room->currency_id,
            'number_of_rooms' => 1,
            'start_date' => $startDate->format('Y-m-d'),
            'end_date' => $endDate->format('Y-m-d'),
        ]);

        $bookingAddress = $bookingAddressRepository->getModel();
        $bookingAddress->fill($request->input());
        $bookingAddress->booking_id = $booking->id;
        $bookingAddressRepository->createOrUpdate($bookingAddress);

        $request->merge([
            'order_id' => $booking->id,
        ]);

        $data = [
            'error' => false,
            'message' => false,
            'amount' => $booking->amount,
            'currency' => strtoupper(get_application_currency()->title),
            'type' => $request->input('payment_method'),
            'charge_id' => null,
        ];

        session()->put('selected_payment_method', $data['type']);

        $paymentData = apply_filters(PAYMENT_FILTER_PAYMENT_DATA, [], $request);

        switch ($request->input('payment_method')) {
            case PaymentMethodEnum::COD:
                $codPaymentService = app(CodPaymentService::class);
                $data['charge_id'] = $codPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');

                break;

            case PaymentMethodEnum::BANK_TRANSFER:
                $bankTransferPaymentService = app(BankTransferPaymentService::class);
                $data['charge_id'] = $bankTransferPaymentService->execute($paymentData);
                $data['message'] = trans('plugins/payment::payment.payment_pending');

                break;

            default:
                $data = apply_filters(PAYMENT_FILTER_AFTER_POST_CHECKOUT, $data, $request);

                break;
        }

        if ($checkoutUrl = Arr::get($data, 'checkoutUrl')) {
            return $response
                ->setError($data['error'])
                ->setNextUrl($checkoutUrl)
                ->withInput()
                ->setMessage($data['message']);
        }

        if ($data['error'] || !$data['charge_id']) {
            return $response
                ->setError()
                ->setNextUrl(PaymentHelper::getCancelURL())
                ->withInput()
                ->setMessage($data['message'] ?: __('Checkout error!'));
        }

        $bookingService->processBooking($booking->id, $data['charge_id']);

        $redirectUrl = PaymentHelper::getRedirectURL();

        if ($request->input('token')) {
            session()->forget($request->input('token'));
            session()->forget('checkout_token');
        }
        if ($request->input('payment_method') != 'cod'){
            $vnp_Url = config('vnpayService.vnp_Url');
            $vnp_Returnurl = $redirectUrl;
            $vnp_TmnCode =  config('vnpayService.vnp_TmnCode');//Mã website tại VNPAY
            $vnp_HashSecret =  config('vnpayService.vnp_HashSecret');; //Chuỗi bí mật
            $vnp_TxnRef = $booking->id; //Mã đơn hàng. Trong thực tế Merchant cần insert đơn hàng vào DB và gửi mã này sang VNPAY
            $vnp_OrderInfo = 'Test thanh toán vnpay';
            $vnp_OrderType = 'billpayment';
            $vnp_Amount = $booking->amount * 100; //giá
            $vnp_Locale = 'vn';
            $vnp_BankCode = ''; // code ngân hàng
            $vnp_IpAddr = $_SERVER['REMOTE_ADDR'];
            //Add Params of 2.0.1 Version

            //Billing
            $inputData = array(
                "vnp_Version" => "2.1.0",
                "vnp_TmnCode" => $vnp_TmnCode,
                "vnp_Amount" => $vnp_Amount,
                "vnp_Command" => "pay",
                "vnp_CreateDate" => date('YmdHis'),
                "vnp_CurrCode" => "VND",
                "vnp_IpAddr" => $vnp_IpAddr,
                "vnp_Locale" => $vnp_Locale,
                "vnp_OrderInfo" => $vnp_OrderInfo,
                "vnp_OrderType" => $vnp_OrderType,
                "vnp_ReturnUrl" => $vnp_Returnurl,
                "vnp_TxnRef" => $vnp_TxnRef

            );

            if (isset($vnp_BankCode) && $vnp_BankCode != "") {
                $inputData['vnp_BankCode'] = $vnp_BankCode;
            }
            if (isset($vnp_Bill_State) && $vnp_Bill_State != "") {
                $inputData['vnp_Bill_State'] = $vnp_Bill_State;
            }

//        var_dump($inputData);
            ksort($inputData);
            $query = "";
            $i = 0;
            $hashdata = "";
            foreach ($inputData as $key => $value) {
                if ($i == 1) {
                    $hashdata .= '&' . urlencode($key) . "=" . urlencode($value);
                } else {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                    $i = 1;
                }
                $query .= urlencode($key) . "=" . urlencode($value) . '&';
            }

            $vnp_Url = $vnp_Url . "?" . $query;
            if (isset($vnp_HashSecret)) {
                $vnpSecureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);//
                $vnp_Url .= 'vnp_SecureHash=' . $vnpSecureHash;
            }
            $returnData = array('code' => '00'
            , 'message' => 'success'
            , 'data' => $vnp_Url);
            if (isset($_POST['payment_method'])) {
                return redirect($vnp_Url);
            } else {
                echo json_encode($returnData);
            }
        }
//        return $response
//            ->setNextUrl($redirectUrl)
//            ->setMessage(__('Booking successfully!'));
    }

    public function checkoutSuccess(string $transactionId, BookingInterface $bookingRepository)
    {
        $vnp_HashSecret = config('vnpayService.vnp_HashSecret'); //Secret key
        $booking = $bookingRepository->getFirstBy(['transaction_id' => $transactionId]);
        $vnp_SecureHash = $_GET['vnp_SecureHash'];
        $inputData = array();
        foreach ($_GET as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);

        $i = 0;
        $hashData = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashData = $hashData . '&' . urlencode($key) . "=" . urlencode($value);

            } else {
                $hashData = $hashData . urlencode($key) . "=" . urlencode($value);
                $i = 1;
            }
        }


        $secureHash = hash_hmac('sha512', $hashData, $vnp_HashSecret);
        if (!$booking) {
            abort(404);
        }
        if ($secureHash == $vnp_SecureHash) {
            if ($_GET['vnp_ResponseCode'] == '00') {
                $booking->update([
                    'status' => 'completed',
                ]);
            } else {
                $booking->update([
                    'status' => 'processing',
                ]);
            }
        } else {
            $booking->update([
                'status' => 'cancelled',
            ]);
        }
        SeoHelper::setTitle(__('Booking Information'));

        Theme::breadcrumb()
            ->add(__('Home'), route('public.index'))
            ->add(__('Booking'), route('public.booking.information', $transactionId));

        return Theme::scope('hotel.booking-information', compact('booking'))->render();
    }

    public function ajaxCalculateBookingAmount(
        CalculateBookingAmountRequest $request,
        BaseHttpResponse              $response,
        RoomInterface                 $roomRepository,
        ServiceInterface              $serviceRepository
    )
    {
        $request->validate([
            'start_date' => 'required:date_format:d-m-Y',
            'end_date' => 'required:date_format:d-m-Y',
            'room_id' => 'required',
        ]);

        $startDate = Carbon::createFromFormat('d-m-Y', $request->input('start_date'));
        $endDate = Carbon::createFromFormat('d-m-Y', $request->input('end_date'));
        $nights = $endDate->diffInDays($startDate);

        $room = $roomRepository->findOrFail($request->input('room_id'));

        $room->total_price = $room->getRoomTotalPrice($startDate, $endDate, $nights);

        $taxAmount = $room->tax->percentage * $room->total_price / 100;

        $amount = $room->total_price + $taxAmount;

        $serviceIds = $request->input('services');

        if ($serviceIds) {
            $services = $serviceRepository->getModel()
                ->whereIn('id', $serviceIds)
                ->get();

            foreach ($services as $service) {
                $amount += $service->price;
            }
        }

        return $response->setData([
            'amount' => format_price($amount),
            'amount_raw' => $amount,
        ]);
    }

    public function changeCurrency(
        Request           $request,
        CurrencyInterface $currencyRepository,
        BaseHttpResponse  $response,
                          $title = null
    )
    {
        if (empty($title)) {
            $title = $request->input('currency');
        }

        if (!$title) {
            return $response;
        }

        $currency = $currencyRepository->getFirstBy(['title' => $title]);

        if ($currency) {
            cms_currency()->setApplicationCurrency($currency);
        }

        return $response;
    }
}
