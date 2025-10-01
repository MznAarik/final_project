<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
use App\Mail\SendTicket;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use SimpleSoftwareIO\QrCode\Facades\QrCode;
use Spatie\Browsershot\Browsershot;

class PaymentController extends Controller
{
    public function showPaymentForm()
    {
        $cartItems = session()->get('cart', []);
        if (empty($cartItems)) {
            return redirect()->route('cart.index')->with('error', 'Cart is empty.');
        }

        $totalAmount = array_sum(array_map(fn($event) => array_sum(array_map(fn($t) => $t['subtotal'], $event)), $cartItems));
        $currency = $cartItems[array_key_first($cartItems)][0]['eventCurrency'] ?? 'NPR';
        $transactionUuid = uniqid(mt_rand(), true);
        $productCode = 'EPAYTEST';
        $secretKey = "8gBm/:&EnhH.1/q";

        $data = "total_amount={$totalAmount},transaction_uuid={$transactionUuid},product_code={$productCode}";
        $signature = base64_encode(hash_hmac('sha256', $data, $secretKey, true));

        $paypalAmount = round($totalAmount / 133, 2); // NPR to USD
        $paypalCurrency = 'USD';

        $categories = array_merge(...array_map(fn($e) => array_column($e, 'category'), $cartItems));
        $eventNames = array_merge(...array_map(fn($e) => array_column($e, 'eventName'), $cartItems));

        session()->put('payment_data', [
            'transaction_uuid' => $transactionUuid,
            'total_amount' => $totalAmount,
            'paypal_amount' => $paypalAmount,
            'currency' => $currency,
        ]);

        return view('user.payment', compact('totalAmount', 'transactionUuid', 'productCode', 'signature', 'eventNames', 'categories', 'paypalAmount', 'paypalCurrency'));
    }


    public function processPayment(Request $request, $gateway = 'esewa')
    {
        $paymentData = session()->get('payment_data', []);
        $cartItems = session()->get('cart', []);
        if (empty($cartItems) || empty($paymentData)) {
            Log::error('Payment processing failed: Cart or payment data missing');
            return redirect()->route('cart.index')->with('error', 'Cart or payment data is missing.');
        }

        $transactionUuid = $paymentData['transaction_uuid'];
        $totalAmount = $paymentData['total_amount'];
        $paypalAmount = $paymentData['paypal_amount'];
        $paymentStatus = 'FAILED';

        if ($gateway === 'esewa') {
            if ($request->has('data')) {
                try {
                    $decryptedData = base64_decode($request->data);
                    $data = json_decode($decryptedData, true);
                    if (is_array($data) && isset($data['status'], $data['transaction_uuid']) && $data['transaction_uuid'] === $transactionUuid) {
                        $paymentStatus = $data['status'];
                    } else {
                        Log::warning('eSewa verification failed: Invalid data or transaction UUID mismatch', ['data' => $data]);
                    }
                } catch (\Exception $e) {
                    Log::error('eSewa verification error: ' . $e->getMessage());
                }
            } else {
                Log::warning('eSewa verification failed: Missing data parameter');
            }
        } elseif ($gateway === 'paypal') {
            Log::info('PayPal request parameters: ', $request->all());
            $requestData = $request->query();
            if (isset($requestData['PayerID'])) {
                Log::info('PayPal(success) callback: Payment verified with PayerID', ['PayerID' => $requestData['PayerID']]);
                if (!isset($requestData['custom']) || $requestData['custom'] !== $transactionUuid) {
                    Log::warning('PayPal custom field mismatch or missing', ['custom' => $requestData['custom'] ?? 'none', 'expected' => $transactionUuid]);
                }
                $paymentStatus = 'COMPLETE';
            } else {
                Log::warning('PayPal success callback failed: Missing PayerID', ['params' => $requestData]);
            }
        }

        if ($paymentStatus !== 'COMPLETE') {
            Log::warning("Payment not completed for {$gateway}: Status = {$paymentStatus}");
            return redirect()->route('cart.index')->with('error', 'Payment was not successful.');
        }

        try {
            DB::beginTransaction();

            $userId = Auth::id();
            $createdTickets = [];

            foreach ($cartItems as $eventId => $tickets) {
                $event = Event::findOrFail($eventId);
                if ($event->status == 'cancelled') {
                    DB::rollBack();
                    Log::error("Event {$event->name} (ID: {$eventId}) is cancelled");
                    return redirect()->route('cart.index')->with('error', "Event {$event->name} has been cancelled!");
                }

                $categoryData = is_array($event->ticket_category_price) ? $event->ticket_category_price : json_decode($event->ticket_category_price, true);
                $ticketsSold = $event->tickets_sold ?? 0;
                $eventCapacity = $event->capacity ?? 0;
                $totalQuantity = array_sum(array_column($tickets, 'quantity'));
                $totalPrice = array_sum(array_column($tickets, 'subtotal'));

                $newTicketsSold = $ticketsSold + $totalQuantity;
                if ($newTicketsSold > $eventCapacity) {
                    DB::rollBack();
                    Log::error("Event {$event->name} (ID: {$eventId}) capacity exceeded: {$newTicketsSold}/{$eventCapacity}");
                    return redirect()->route('cart.index')->with('error', "Sorry! All tickets for {$event->name} are sold out.");
                }

                $deadline = $event->start_date ? Carbon::parse($event->start_date)->subHours(24) : null;
                $popularityScore = ($event->tickets_sold * 5) + (10 / (now()->diffInDays($event->created_at) + 1));

                $event->update([
                    'tickets_sold' => $newTicketsSold,
                    'popularity_score' => $popularityScore,
                ]);

                $batchCode = uniqid('batch_') . '-' . $eventId;
                try {
                    $encryptedData = AesHelper::encrypt(json_encode([
                        'user_id' => $userId,
                        'event_id' => $eventId,
                        'batch_code' => $batchCode,
                    ]));
                } catch (\Exception $e) {
                    Log::error('QR code encryption failed: ' . $e->getMessage());
                    throw new \Exception('Failed to generate secure ticket data');
                }

                $qrCodeUrl = route('admin.verify-ticket') . '?data=' . urlencode($encryptedData);
                Log::info('Generating QR code for URL: ' . $qrCodeUrl);

                $svg = QrCode::format('svg')->size(200)->generate($qrCodeUrl);

                $svgFileName = 'qrcodes/ticket_' . time() . '_' . Str::random(8) . '.svg';
                $pngFileName = str_replace('.svg', '.png', $svgFileName);

                $svgPath = Storage::disk('public')->path($svgFileName);
                $pngPath = Storage::disk('public')->path($pngFileName);

                Storage::disk('public')->put($svgFileName, $svg);

                $canvasWidth = 512;
                $canvasHeight = 512;
                $qrWidth = 217;
                $qrHeight = 217;
                $x = intval(($canvasWidth - $qrWidth) / 2); // 81
                $y = intval(($canvasHeight - $qrHeight) / 2); // 81

                // Wrap SVG in centered HTML
                $html = '<div style="width: ' . $canvasWidth . 'px; height: ' . $canvasHeight . 'px; display: flex; justify-content: center; align-items: center;">';
                $html .= '<svg width="' . $qrWidth . '" height="' . $qrHeight . '">' . $svg . '</svg>';
                $html .= '</div>';

                Browsershot::html($html)
                    ->windowSize($canvasWidth, $canvasHeight)
                    ->clip($x, $y, $qrWidth, $qrHeight)
                    ->save($pngPath);

                Storage::disk('public')->delete($svgFileName);

                $ticket = Ticket::create([
                    'user_id' => $userId,
                    'event_id' => $eventId,
                    'batch_code' => $batchCode,
                    'qr_code' => $pngFileName,
                    'ticket_details' => json_encode($tickets),
                    'total_quantity' => $totalQuantity,
                    'total_price' => $totalPrice,
                    'status' => 'pending',
                    'deadline' => $deadline,
                    'created_by' => $userId,
                    'updated_by' => $userId,
                ]);

                if (class_exists('App\Models\Payment')) {
                    Payment::create([
                        'user_id' => $userId,
                        'ticket_id' => $ticket->id,
                        'transaction_id' => $transactionUuid,
                        'amount' => $gateway === 'paypal' ? $paypalAmount : $totalPrice,
                        'payment_method' => $gateway,
                        'status' => 'succeeded',
                        'payment_date' => now(),
                        'created_by' => $userId,
                        'updated_by' => $userId,
                        'delete_flag' => 0,
                        'gateway' => $gateway,
                        'event_id' => $eventId,
                    ]);
                } else {
                    Log::error('Payment model not found');
                    DB::rollBack();
                    return redirect()->route('cart.index')->with('error', 'Payment model not found.');
                }

                $createdTickets[] = $ticket;
                Mail::to(Auth::user()->email)->send(new SendTicket($ticket));
            }

            session()->forget('cart');
            session()->forget('payment_data');
            DB::commit();

            $successMessage = $gateway === 'esewa' ? 'Payment via eSewa completed successfully!' : 'Payment via PayPal completed successfully!';
            return redirect()->route('user.tickets.index')->with(['status' => true, 'message' => $successMessage]);
        } catch (\Exception $e) {
            Log::error('Error processing payment: ' . $e->getMessage());
            DB::rollBack();
            return redirect()->route('cart.index')->with(['status' => 0, 'message' => 'Error during payment processing: ' . $e->getMessage()]);
        }
    }


    public function successCallback(Request $request)
    {
        $gateway = $request->has('data') ? 'esewa' : 'paypal';
        Log::info("Success callback received for gateway: {$gateway}", $request->all());
        return $this->processPayment($request, $gateway);
    }

    public function failureCallback(Request $request)
    {
        Log::info('Failure callback received', $request->all());
        session()->forget('payment_data');
        return redirect()->route('cart.index')->with('error', 'Payment failed or was cancelled.');
    }
}