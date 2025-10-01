@extends('layouts.app')

@section('title', 'Payment')

@section('content')
    <div class="flex items-center justify-center min-h-screen p-6">
        <div class="flex flex-col items-center shadow-xl rounded-xl p-8 w-full max-w-2xl bg-green-50 mt-[-50px]">
            @if (session('error'))
                <div class="mb-6 text-red-600 font-semibold">
                    {{ session('error') }}
                </div>
            @endif
            <h1 class="text-4xl font-bold text-gray-800 mb-4 text-center">Complete Your Payment</h1>
            <p class="text-center text-gray-600 mb-8 text-lg">
                Please proceed to pay using <span class="font-semibold text-green-600">eSewa</span> or <span
                    class="font-semibold text-blue-600">PayPal</span> to confirm your order.
            </p>

            <div class="grid gap-6 text-gray-700">
                <div class="grid grid-cols-2 gap-32">
                    <div>
                        <span class="font-semibold">Ticket Category:</span>
                        <p>{{ implode(', ', $categories) }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">Event Name:</span>
                        <p>{{ implode(', ', $eventNames) }}</p>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-32">
                    <div>
                        <span class="font-semibold">Product Code:</span>
                        <p>{{ $productCode }}</p>
                    </div>
                    <div>
                        <span class="font-semibold">Total Amount:</span>
                        <p class="text-2xl text-green-700 font-bold">Rs. {{ number_format($totalAmount, 2) }}</p>
                    </div>
                </div>

                <div>
                    <span class="font-semibold">Transaction ID:</span>
                    <p class="text-sm text-gray-500 break-all">{{ $transactionUuid }}</p>
                </div>
            </div>

            <div class="flex justify-center items-baseline gap-3 mt-8">
                <!-- eSewa Form -->
                <form action="https://rc-epay.esewa.com.np/api/epay/main/v2/form" method="POST" target="_blank">
                    <input type="hidden" name="amount" value="{{ $totalAmount }}">
                    <input type="hidden" name="tax_amount" value="0">
                    <input type="hidden" name="total_amount" value="{{ $totalAmount }}">
                    <input type="hidden" name="transaction_uuid" value="{{ $transactionUuid }}">
                    <input type="hidden" name="product_code" value="{{ $productCode }}">
                    <input type="hidden" name="product_service_charge" value="0">
                    <input type="hidden" name="product_delivery_charge" value="0">
                    <input type="hidden" name="success_url" value="{{ route('cart.checkout.success') }}">
                    <input type="hidden" name="failure_url" value="{{ route('cart.checkout.failure') }}">
                    <input type="hidden" name="signed_field_names" value="total_amount,transaction_uuid,product_code">
                    <input type="hidden" name="signature" value="{{ $signature }}">

                    <button type="submit"
                        class="bg-green-600 text-white text-lg font-semibold px-8 py-3 rounded-lg hover:bg-green-700 transition-all duration-200 shadow-md">
                        Pay with eSewa
                    </button>
                </form>

                <!-- PayPal Form -->
                <form action="https://www.sandbox.paypal.com/cgi-bin/webscr" method="post" target="_top">
                    <input type="hidden" name="cmd" value="_xclick">
                    <input type="hidden" name="business" value="sb-zwqzo29824330@business.example.com">
                    <input type="hidden" name="item_name" value="Event Tickets">
                    <input type="hidden" name="amount" value="{{ $paypalAmount }}">
                    <input type="hidden" name="currency_code" value="{{ $paypalCurrency }}">
                    <input type="hidden" name="custom" value="{{ $transactionUuid }}">
                    <input type="hidden" name="return" value="{{ route('cart.checkout.success') }}">
                    <input type="hidden" name="cancel_return" value="{{ route('cart.checkout.failure') }}">
                    <button type="submit"
                        class="bg-blue-600 text-white text-lg font-semibold px-8 py-3 rounded-lg hover:bg-blue-800 transition-all duration-200 shadow-md">
                        Pay with PayPal
                    </button>
                </form>
            </div>

            <p class="mt-6 text-sm text-center text-gray-500">
                You’ll be redirected to eSewa or PayPal for secure payment.
            </p>
        </div>
    </div>
@endsection