<?php

namespace App\Http\Controllers;

use Botble\Base\Http\Responses\BaseHttpResponse;
use Botble\Hotel\Http\Requests\CheckoutRequest;
use Botble\Hotel\Models\Booking;
use Botble\Hotel\Repositories\Interfaces\BookingAddressInterface;
use Botble\Hotel\Repositories\Interfaces\BookingInterface;
use Botble\Hotel\Repositories\Interfaces\BookingRoomInterface;
use Botble\Hotel\Repositories\Interfaces\RoomInterface;
use Botble\Hotel\Repositories\Interfaces\ServiceInterface;
use Botble\Hotel\Services\BookingService;
use Botble\Payment\Enums\PaymentMethodEnum;
use Botble\Payment\Services\Gateways\BankTransferPaymentService;
use Botble\Payment\Services\Gateways\CodPaymentService;
use Botble\Payment\Supports\PaymentHelper;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Str;

class PaymentController extends Controller
{
    public function execPostRequest($url, $data)
    {
        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($data))
        );
        curl_setopt($ch, CURLOPT_TIMEOUT, 5);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 5);
        //execute post
        $result = curl_exec($ch);
        //close connection
        curl_close($ch);
        return $result;
    }

    public function store(CheckoutRequest         $request,
                          BookingInterface        $bookingRepository,
                          RoomInterface           $roomRepository,
                          BookingAddressInterface $bookingAddressRepository,
                          BookingRoomInterface    $bookingRoomRepository,
                          ServiceInterface        $serviceRepository,
                          BookingService          $bookingService,
                          BaseHttpResponse        $response,
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

        $redirectUrlSuccess = PaymentHelper::getRedirectURL();

        if ($request->input('token')) {
            session()->forget($request->input('token'));
            session()->forget('checkout_token');
        }
//        return $response
//            ->setNextUrl($redirectUrl)
//            ->setMessage(__('Booking successfully!'));
        //momo
        if ($request->input('payment_method') == 'cod') {
            $endpoint = "https://test-payment.momo.vn/v2/gateway/api/create";
            $partnerCode = 'MOMOBKUN20180529';
            $accessKey = 'klm05TvNBzhg7h7j';
            $secretKey = 'at67qH6mk8w5Y1nAyMoYKMWACiEi2bsa';

            $orderInfo = "Thanh toan MoMo";
            $amount =$booking->amount;
            $orderId = time() . "";
            $redirectUrl = $redirectUrlSuccess;// thanhf coong
            $ipnUrl = $redirectUrlSuccess;// truy van ket qua
            $extraData = "";
            $accessKey = $accessKey;
            $serectkey = $secretKey;
            $requestId = time() . "";
            $requestType = "payWithATM";
//            $extraData = ($_POST["extraData"] ? $_POST["extraData"] : "");
            //before sign HMAC SHA256 signature
            $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipnUrl=" . $ipnUrl . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
            $signature = hash_hmac("sha256", $rawHash, $serectkey);
            $data = array('partnerCode' => $partnerCode,
                'partnerName' => "Test",
                "storeId" => "MomoTestStore",
                'requestId' => $requestId,
                'amount' => $amount,
                'orderId' => $orderId,
                'orderInfo' => $orderInfo,
                'redirectUrl' => $redirectUrl,
                'ipnUrl' => $ipnUrl,
                'lang' => 'vi',
                'extraData' => $extraData,
                'requestType' => $requestType,
                'signature' => $signature);
            $result = $this->execPostRequest($endpoint, json_encode($data));
            $jsonResult = json_decode($result, true);  // decode json
            //Just a example, please check more in there
            return redirect($jsonResult['payUrl']);
//            header('Location: ' . $jsonResult['payUrl']);

        } else { //vnpay
            $vnp_Url = config('vnpayService.vnp_Url');
            $vnp_Returnurl = $redirectUrlSuccess;
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

    }

}
