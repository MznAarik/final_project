@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 to-gray-100 py-8 px-4 sm:px-6 lg:px-8">
        <div class="max-w-4xl mx-auto">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-red-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5-5M17 13v6a2 2 0 01-2 2H9a2 2 0 01-2-2v-6">
                        </path>
                    </svg>
                </div>
                <h1
                    class="text-4xl font-bold bg-gradient-to-r from-gray-900 to-gray-700 bg-clip-text text-transparent mb-3">
                    Your Cart
                </h1>
                <p class="text-gray-600 text-lg max-w-2xl mx-auto">
                    Review your selected tickets and complete your purchase
                </p>
            </div>

            @if ($cartItems && !empty($cartItems))
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Cart Items Column -->
                    <div class="lg:col-span-2 space-y-6">
                        @foreach ($cartItems as $eventId => $tickets)
                            <div
                                class="group relative overflow-hidden rounded-2xl bg-white shadow-sm border border-gray-100 hover:shadow-lg transition-all duration-300">
                                <!-- Event Header -->
                                <div class="relative h-48 overflow-hidden">
                                    <img src="{{ asset('storage/' . ($tickets[0]['eventImage'] ?? 'images/default.jpg')) }}"
                                        alt="{{ $tickets[0]['eventName'] ?? 'Event Image' }}"
                                        class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                    <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent">
                                    </div>
                                    <div class="absolute bottom-4 left-6 right-6">
                                        <h2 class="text-xl font-bold text-white mb-1">
                                            {{ $tickets[0]['eventName'] }}
                                        </h2>
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-white/20 backdrop-blur-sm text-white border border-white/30">
                                            Event ID: {{ $eventId }}
                                        </span>
                                    </div>
                                </div>

                                <!-- Tickets Section -->
                                <div class="p-6">
                                    <form action="{{ route('cart.update') }}" method="POST" class="space-y-4">
                                        @csrf
                                        @method('PUT')

                                        @foreach ($tickets as $index => $item)
                                            <div
                                                class="flex items-center justify-between p-4 rounded-xl bg-gray-50 border border-gray-100 hover:bg-gray-100 transition-colors">
                                                <div class="flex-1">
                                                    <div class="flex items-center space-x-3">
                                                        <div class="w-3 h-3 bg-red-500 rounded-full"></div>
                                                        <span class="font-semibold text-gray-900">{{ $item['category'] }}</span>
                                                    </div>
                                                    <div class="mt-1 text-sm text-gray-600">
                                                        {{ $item['eventCurrency'] }} {{ number_format($item['price'], 2) }} each
                                                    </div>
                                                </div>

                                                <div class="flex items-center space-x-4">
                                                    <!-- Quantity Input -->
                                                    <div class="flex items-center space-x-2">
                                                        <label class="text-sm font-medium text-gray-700">Qty:</label>
                                                        <div class="relative">
                                                            <input type="number" name="quantity[{{ $eventId }}][{{ $index }}]"
                                                                value="{{ $item['quantity'] }}" min="1"
                                                                class="w-20 h-10 px-3 text-center border border-gray-200 rounded-lg focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all">
                                                        </div>
                                                    </div>

                                                    <!-- Subtotal -->
                                                    <div class="text-right">
                                                        <div class="font-bold text-gray-900">
                                                            {{ $item['eventCurrency'] }} {{ number_format($item['subtotal'], 2) }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach

                                        <!-- Action Buttons -->
                                        <div
                                            class="flex flex-col sm:flex-row justify-between items-center pt-4 border-t border-gray-100 space-y-3 sm:space-y-0 sm:space-x-3">
                                            <div class="flex space-x-3">
                                                <button type="submit" name="update[{{ $eventId }}]"
                                                    class="inline-flex items-center px-4 py-2 text-sm font-medium text-indigo-700 bg-indigo-50 border border-indigo-200 rounded-lg hover:bg-indigo-100 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition-colors">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                            d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15">
                                                        </path>
                                                    </svg>
                                                    Update
                                                </button>
                                            </div>

                                            <div class="font-bold text-lg text-gray-900">
                                                Total: {{ $tickets[0]['eventCurrency'] }}
                                                {{ number_format(array_sum(array_map(fn($t) => $t['subtotal'], $tickets)), 2) }}
                                            </div>
                                        </div>
                                    </form>

                                    <!-- Remove Button -->
                                    <form action="{{ route('cart.removeSingle', ['eventId' => $eventId, 'index' => 0]) }}"
                                        method="POST" class="mt-4">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                            onclick="return confirm('Are you sure you want to remove all tickets for this event?')"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded-lg hover:bg-red-100 focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                            Remove
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Cart Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="sticky top-8">
                            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                                <h3 class="text-xl font-bold text-gray-900 mb-6">Order Summary</h3>

                                <!-- Summary Details -->
                                <div class="space-y-4 mb-6">
                                    @php
                                        $totalItems = array_sum(array_map(fn($event) => array_sum(array_map(fn($t) => $t['quantity'], $event)), $cartItems));
                                        $grandTotal = array_sum(array_map(fn($event) => array_sum(array_map(fn($t) => $t['subtotal'], $event)), $cartItems));
                                        $currency = $cartItems[array_key_first($cartItems)][0]['eventCurrency'] ?? 'NPR';
                                    @endphp

                                    <div class="flex justify-between text-gray-600">
                                        <span>Items ({{ $totalItems }})</span>
                                        <span>{{ $currency }} {{ number_format($grandTotal, 2) }}</span>
                                    </div>

                                    <div class="flex justify-between text-gray-600">
                                        <span>Processing Fee</span>
                                        <span>{{ $currency }} 0.00</span>
                                    </div>

                                    <div class="border-t border-gray-200 pt-4">
                                        <div class="flex justify-between text-lg font-bold text-gray-900">
                                            <span>Total</span>
                                            <span>{{ $currency }} {{ number_format($grandTotal, 2) }}</span>
                                        </div>
                                    </div>
                                </div>

                                <!-- Checkout Button -->
                                <form action="{{ route('cart.checkout') }}" method="GET">
                                    @csrf
                                    <div class="flex justify-center ">
                                        <button type="submit"
                                            class="inline-flex items-center px-4 py-2 text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded-lg hover:bg-green-100 focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors">
                                            <svg class="w-5 h-5 inline mr-2" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                                </path>
                                            </svg>
                                            Secure Checkout
                                        </button>
                                    </div>
                                </form>



                                <!-- Security Note -->
                                <div class="mt-4 flex items-center justify-center text-xs text-gray-500">
                                    <svg class="w-4 h-4 mr-1 text-green-500" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd"
                                            d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z"
                                            clip-rule="evenodd"></path>
                                    </svg>
                                    SSL Encrypted & Secure
                                </div>
                            </div>

                            <!-- Continue Shopping Link -->
                            <div class="mt-6 text-center">
                                <a href="{{ route('home') }}"
                                    class="inline-flex items-center text-indigo-600 hover:text-indigo-700 font-medium transition-colors ">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                                    </svg>
                                    Continue Shopping
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @else
                <!-- Empty Cart State -->
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-12">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gray-100 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                            </svg>
                        </div>

                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Your cart is empty</h3>
                        <p class="text-gray-600 mb-8">Looks like you haven't added any tickets yet. Discover amazing events and
                            start planning!</p>

                        <a href="{{ route('home') }}"
                            class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-indigo-600 to-purple-600 text-white font-semibold rounded-lg hover:from-indigo-700 hover:to-purple-700 focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transform hover:scale-105 transition-all duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Explore Events
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>
@endsection