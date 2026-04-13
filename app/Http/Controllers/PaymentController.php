<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Payment;
use App\Models\Quiz;
use App\Models\User;
use App\Models\Subscription;
use App\Models\ActivityLog;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class PaymentController extends Controller {
    
    /**
     * Constructor - register middleware
     * @return void
     */
    public function __construct() {
        $this->middleware('auth');
    }

    // ===================================
    // PAYMENT DISPLAY
    // ===================================

    /**
     * Show payment options page
     */
    public function showPaymentOptions(Request $request)
    {
        $amount = $request->query('amount', 100000); // Default 100k VND
        $type = $request->query('type', 'subscription');
        $itemId = $request->query('item_id', 0);

        return view('payment.index', compact('amount', 'type', 'itemId'));
    }

    // ===================================
    // PAYMENT INITIATION
    // ===================================

    public function initiatePayment(Request $request) {
        $gateway = $request->gateway;
        $amount = $request->amount;
        $type = $request->type; // 'quiz' or 'subscription'
        $itemId = $request->item_id;

        if (!in_array($gateway, ['vnpay', 'momo', 'stripe', 'paypal', '2checkout'])) {
            return back()->with('error', 'Cổng thanh toán không hợp lệ');
        }

        // Check if gateway is enabled
        $gateways = config('quiz.gateways');
        if (!isset($gateways[$gateway]['enabled']) || !$gateways[$gateway]['enabled']) {
            return back()->with('error', 'Cổng thanh toán này hiện không khả dụng');
        }

        $transactionId = 'TXN_' . Auth::id() . '_' . time() . '_' . Str::random(8);

        // Create pending payment record
        $payment = Payment::create([
            'user_id' => Auth::id(),
            'quiz_id' => $type === 'quiz' ? $itemId : null,
            'gateway' => $gateway,
            'transaction_id' => $transactionId,
            'amount' => $amount,
            'currency' => config('quiz.base_currency', 'VND'),
            'status' => 'pending'
        ]);

        // Redirect to appropriate gateway
        return match($gateway) {
            'vnpay' => $this->initiateVNPay($payment, $request),
            'momo' => $this->initiateMomo($payment, $request),
            'stripe' => $this->initiateStripe($payment, $request),
            'paypal' => $this->initiatePayPal($payment, $request),
            '2checkout' => $this->initiate2Checkout($payment, $request),
            default => back()->with('error', 'Cổng thanh toán không được hỗ trợ')
        };
    }

    // ===================================
    // VNPAY GATEWAY
    // ===================================

    private function initiateVNPay($payment, $request) {
        $vnp_Url = config('quiz.gateways.vnpay.url');
        $vnp_TmnCode = config('quiz.gateways.vnpay.tmncode');
        $vnp_HashSecret = config('quiz.gateways.vnpay.hashsecret');
        
        $vnp_TxnRef = $payment->transaction_id;
        $vnp_OrderInfo = "Thanh toan don hang " . $payment->id;
        $vnp_OrderType = "billpayment";
        $vnp_Amount = $payment->amount * 100; // VNPay expects amount in VND cents
        $vnp_Locale = "vn";
        $vnp_BankCode = $request->bank_code ? $request->bank_code : "";
        $vnp_IpAddr = $request->ip();

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
            "vnp_ReturnUrl" => route('payment.vnpay-return'),
            "vnp_TxnRef" => $vnp_TxnRef,
        );

        if (!empty($vnp_BankCode)) {
            $inputData['vnp_BankCode'] = $vnp_BankCode;
        }

        ksort($inputData);
        $query = "";
        $i = 0;
        $hashdata = "";
        foreach ($inputData as $key => $value) {
            if ($i == 1) {
                $hashdata .= "&" . urlencode($key) . "=" . urlencode($value);
            } else {
                if ($i == 0) {
                    $hashdata .= urlencode($key) . "=" . urlencode($value);
                } else {
                    $query .= "&" . urlencode($key) . "=" . urlencode($value);
                }
            }
            $i = 1;
        }

        $vnp_Url = $vnp_Url . "?" . $hashdata;
        $vnpay_hash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        $vnp_Url .= '&vnp_SecureHash=' . $vnpay_hash;

        return redirect($vnp_Url);
    }

    public function vnpayReturn(Request $request) {
        $vnp_HashSecret = config('quiz.gateways.vnpay.hashsecret');
        
        $inputData = array();
        foreach ($request->all() as $key => $value) {
            if (substr($key, 0, 4) == "vnp_") {
                $inputData[$key] = $value;
            }
        }

        unset($inputData['vnp_SecureHash']);
        ksort($inputData);
        $hashdata = http_build_query($inputData, '', '&');
        $secureHash = hash_hmac('sha512', $hashdata, $vnp_HashSecret);
        
        $payment = Payment::where('transaction_id', $request->vnp_TxnRef)->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Giao dịch không tồn tại');
        }

        if ($secureHash != $request->vnp_SecureHash) {
            $payment->update(['status' => 'failed']);
            return redirect('/')->with('error', 'Xác thực chữ ký thất bại');
        }

        if ($request->vnp_ResponseCode == '00') {
            $payment->update([
                'status' => 'success',
                'response_data' => json_encode($request->all())
            ]);
            
            $this->onPaymentSuccess($payment);
            return redirect('/dashboard')->with('success', 'Thanh toán thành công');
        } else {
            $payment->update(['status' => 'failed']);
            return redirect('/dashboard')->with('error', 'Thanh toán thất bại: ' . $request->vnp_ResponseCode);
        }
    }

    // ===================================
    // MOMO GATEWAY
    // ===================================

    private function initiateMomo($payment, $request) {
        $endpoint = config('quiz.gateways.momo.endpoint');
        $partnerCode = config('quiz.gateways.momo.partner_code');
        $accessKey = config('quiz.gateways.momo.access_key');
        $secretKey = config('quiz.gateways.momo.secret_key');

        $orderInfo = "Thanh toan don hang " . $payment->id;
        $amount = (int) $payment->amount;
        $orderId = $payment->transaction_id;
        $redirectUrl = route('payment.momo-return');
        $notifyUrl = route('payment.momo-notify');

        $requestId = time() . "";
        $requestType = "captureWallet";
        $extraData = "";
        $autoCapture = true;
        $lang = "vi";

        // Create signature
        $rawHash = "accessKey=" . $accessKey . "&amount=" . $amount . "&extraData=" . $extraData . "&ipAddress=" . $request->ip() . "&orderId=" . $orderId . "&orderInfo=" . $orderInfo . "&partnerCode=" . $partnerCode . "&redirectUrl=" . $redirectUrl . "&requestId=" . $requestId . "&requestType=" . $requestType;
        $signature = hash_hmac("sha256", $rawHash, $secretKey);

        $data = array(
            'partnerCode' => $partnerCode,
            'partnerName' => "Test",
            'partnerEmail' => "test@test.com",
            'storeId' => "MomoTestStore",
            'requestId' => $requestId,
            'amount' => $amount,
            'orderId' => $orderId,
            'orderLabel' => $orderId,
            'askingPrice' => $amount,
            'orderInfo' => $orderInfo,
            'redirectUrl' => $redirectUrl,
            'ipAddress' => $request->ip(),
            'requestType' => $requestType,
            'extraData' => $extraData,
            'autoCapture' => $autoCapture,
            'lang' => $lang,
            'signature' => $signature
        );

        $context = stream_context_create(array(
            'http' => array(
                'method' => 'POST',
                'header' => 'Content-Type: application/json',
                'content' => json_encode($data)
            )
        ));

        $response = @file_get_contents($endpoint, false, $context);
        $resultArr = json_decode($response, true);

        if ($resultArr['resultCode'] == 0) {
            return redirect($resultArr['payUrl']);
        } else {
            return back()->with('error', 'Lỗi khởi tạo thanh toán Momo: ' . $resultArr['message']);
        }
    }

    public function momoReturn(Request $request) {
        $payment = Payment::where('transaction_id', $request->orderId)->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Giao dịch không tồn tại');
        }

        if ($request->resultCode == 0) {
            $payment->update([
                'status' => 'success',
                'response_data' => json_encode($request->all())
            ]);
            
            $this->onPaymentSuccess($payment);
            return redirect('/dashboard')->with('success', 'Thanh toán thành công');
        } else {
            $payment->update(['status' => 'failed']);
            return redirect('/dashboard')->with('error', 'Thanh toán thất bại');
        }
    }

    // ===================================
    // STRIPE GATEWAY
    // ===================================

    private function initiateStripe($payment, $request) {
        try {
            $secretKey = config('quiz.gateways.stripe.secret_key');
            if (!$secretKey) {
                return back()->with('error', 'Stripe chưa được cấu hình');
            }

            // Use Stripe API to create checkout session
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ])->post('https://api.stripe.com/v1/checkout/sessions', [
                'payment_method_types[]' => 'card',
                'line_items[0][price_data][currency]' => strtolower(config('quiz.base_currency', 'usd')),
                'line_items[0][price_data][product_data][name]' => 'Quiz Payment',
                'line_items[0][price_data][unit_amount]' => (int) ($payment->amount * 100),
                'line_items[0][quantity]' => '1',
                'mode' => 'payment',
                'success_url' => route('payment.stripe-success') . '?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => route('payment.stripe-cancel'),
                'metadata[payment_id]' => $payment->id,
                'metadata[transaction_id]' => $payment->transaction_id
            ]);

            if ($response->successful()) {
                $session = $response->json();
                $payment->update([
                    'response_data' => json_encode(['stripe_session_id' => $session['id']])
                ]);
                return redirect($session['url']);
            }

            return back()->with('error', 'Lỗi tạo phiên Stripe: ' . $response->body());
        } catch (\Exception $e) {
            Log::error('Stripe initiate error: ' . $e->getMessage());
            return back()->with('error', 'Lỗi Stripe: ' . $e->getMessage());
        }
    }

    public function stripeSuccess(Request $request) {
        try {
            $secretKey = config('quiz.gateways.stripe.secret_key');
            if (!$secretKey || !$request->session_id) {
                return redirect('/')->with('error', 'Thông tin phiên không hợp lệ');
            }

            // Retrieve session from Stripe API
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $secretKey,
            ])->get('https://api.stripe.com/v1/checkout/sessions/' . $request->session_id);

            if (!$response->successful()) {
                return redirect('/')->with('error', 'Không thể xác minh phiên');
            }

            $session = $response->json();
            $payment = Payment::where('transaction_id', $session['metadata']['transaction_id'] ?? null)->first();

            if (!$payment) {
                return redirect('/')->with('error', 'Giao dịch không tồn tại');
            }

            if ($session['payment_status'] === 'paid') {
                $payment->update([
                    'status' => 'success',
                    'response_data' => json_encode($session)
                ]);
                
                $this->onPaymentSuccess($payment);
                return redirect('/dashboard')->with('success', 'Thanh toán thành công');
            }

            return redirect('/dashboard')->with('error', 'Thanh toán chưa hoàn thành');
        } catch (\Exception $e) {
            Log::error('Stripe success verify error: ' . $e->getMessage());
            return redirect('/dashboard')->with('error', 'Lỗi xác minh: ' . $e->getMessage());
        }
    }

    public function stripeCancel() {
        return redirect('/dashboard')->with('error', 'Thanh toán bị hủy');
    }

    // ===================================
    // PAYPAL GATEWAY
    // ===================================

    private function initiatePayPal($payment, $request) {
        $mode = config('quiz.gateways.paypal.mode', 'sandbox');
        $clientId = config('quiz.gateways.paypal.client_id');

        $returnUrl = route('payment.paypal-return');
        $cancelUrl = route('payment.paypal-cancel');
        $amount = (string) round($payment->amount, 2);
        $currency = config('quiz.base_currency', 'USD');

        $paypalUrl = $mode === 'sandbox' 
            ? 'https://www.sandbox.paypal.com/cgi-bin/webscr'
            : 'https://www.paypal.com/cgi-bin/webscr';

        $paypalData = array(
            'cmd' => '_xclick',
            'business' => config('quiz.gateways.paypal.merchant_email', 'merchant@example.com'),
            'item_name' => 'Quiz Payment',
            'item_number' => $payment->id,
            'amount' => $amount,
            'currency_code' => $currency,
            'return' => $returnUrl,
            'cancel_return' => $cancelUrl,
            'notify_url' => route('payment.paypal-notify'),
            'invoice' => $payment->transaction_id,
            'no_shipping' => '2'
        );

        $paypalUrl .= '?' . http_build_query($paypalData);

        return redirect($paypalUrl);
    }

    public function paypalReturn(Request $request) {
        $payment = Payment::where('transaction_id', $request->invoice)->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Giao dịch không tồn tại');
        }

        // Note: In production, verify with PayPal IPN
        $payment->update([
            'status' => 'success',
            'response_data' => json_encode($request->all())
        ]);

        $this->onPaymentSuccess($payment);
        return redirect('/dashboard')->with('success', 'Thanh toán thành công');
    }

    public function paypalCancel() {
        return redirect('/dashboard')->with('error', 'Thanh toán bị hủy');
    }

    // ===================================
    // 2CHECKOUT GATEWAY
    // ===================================

    private function initiate2Checkout($payment, $request) {
        $merchantCode = config('quiz.gateways.2checkout.merchant_code');
        $apiUrl = config('quiz.gateways.2checkout.api_url');
        $returnUrl = route('payment.2checkout-return');

        $postData = array(
            'merchant_code' => $merchantCode,
            'invoice_id' => $payment->transaction_id,
            'amount' => (string) $payment->amount,
            'currency' => config('quiz.base_currency', 'USD'),
            'return_url' => $returnUrl,
            'order_description' => 'Quiz Payment #' . $payment->id
        );

        $url = $apiUrl . '?merchant_code=' . urlencode($merchantCode) 
             . '&invoice_id=' . urlencode($postData['invoice_id'])
             . '&amount=' . urlencode($postData['amount'])
             . '&currency=' . urlencode($postData['currency'])
             . '&return_url=' . urlencode($returnUrl);

        return redirect($url);
    }

    public function twoCheckoutReturn(Request $request) {
        $payment = Payment::where('transaction_id', $request->invoice_id)->first();

        if (!$payment) {
            return redirect('/')->with('error', 'Giao dịch không tồn tại');
        }

        if ($request->status === 'success' || $request->status === 'approved') {
            $payment->update([
                'status' => 'success',
                'response_data' => json_encode($request->all())
            ]);
            
            $this->onPaymentSuccess($payment);
            return redirect('/dashboard')->with('success', 'Thanh toán thành công');
        } else {
            $payment->update(['status' => 'failed']);
            return redirect('/dashboard')->with('error', 'Thanh toán thất bại');
        }
    }

    // ===================================
    // PAYMENT SUCCESS HANDLER
    // ===================================

    private function onPaymentSuccess($payment) {
        try {
            $user = $payment->user;
            if (!$user) return false;

            // Mark payment as completed
            $payment->update(['status' => 'completed']);

            // Handle quiz purchase
            if ($payment->quiz_id) {
                $quiz = Quiz::find($payment->quiz_id);
                if ($quiz) {
                    // Store quiz purchase in Activity log
                    ActivityLog::create([
                        'user_id' => $user->id,
                        'action' => 'purchase_quiz',
                        'description' => 'Purchased quiz: ' . $quiz->title,
                        'data' => json_encode(['quiz_id' => $quiz->id])
                    ]);
                }
            }

            // Send payment success notification
            \App\Services\FirebaseService::notifyPaymentSuccess(
                $user->id,
                $payment->amount,
                $payment->currency
            );

            // Log activity
            ActivityLog::create([
                'user_id' => $user->id,
                'action' => 'payment_success',
                'description' => 'Payment completed: ' . $payment->transaction_id,
                'data' => json_encode(['payment_id' => $payment->id, 'amount' => $payment->amount])
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('Payment success handler error: ' . $e->getMessage());
            return false;
        }
    }
}
