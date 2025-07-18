@extends('layouts.app')

@section('title', 'Cart')

@section('content')
    <div class="flex flex-col items-center min-h-screen p-6">
        <div class="w-full max-w-4xl">
            <h1 class="text-4xl font-bold text-gray-900 mb-6">Your Cart</h1>
            <p class="text-gray-600 mb-8 text-lg">View and manage your selected tickets before checkout.</p>

            @if ($cartItems && !empty($cartItems))
                @foreach ($cartItems as $eventId => $tickets)
                    <div class="cart-item mb-6 p-6 border border-gray-200 rounded-lg bg-white shadow-lg">
                        <div class="flex flex-col md:flex-row items-start gap-6">
                            <div class="w-full md:w-1/4">
                                <img src="{{ asset('storage/' . ($tickets[0]['eventImage'] ?? 'images/default.jpg')) }}"
                                    alt="{{ $tickets[0]['eventName'] ?? 'Event Image' }}"
                                    class="w-40 h-40 object-cover rounded-lg shadow-md">
                            </div>

                            <div class="w-full md:w-2/4">
                                <h2 class="text-xl font-semibold text-gray-800 mb-4">Event: {{ $tickets[0]['eventName'] }} (ID:
                                    {{ $eventId }})
                                </h2>

                                {{-- Form to update ticket quantities --}}
                                <form action="{{ route('cart.update') }}" method="POST" class="space-y-3">
                                    @csrf
                                    @method('PUT') <!-- Match the PUT route -->
                                    @foreach ($tickets as $index => $item)
                                        <div class="flex items-center justify-between border-b border-gray-200 py-3">
                                            <div class="flex-1">
                                                <span class="text-gray-700 font-medium">
                                                    {{ $item['category'] }} -
                                                    {{ $item['eventCurrency'] }} {{ $item['price'] }} x {{ $item['quantity'] }} =
                                                    {{ $item['eventCurrency'] }} {{ $item['subtotal'] }}
                                                </span>
                                            </div>
                                            <div class="flex items-center space-x-3">
                                                <input type="number" name="quantity[{{ $eventId }}][{{ $index }}]"
                                                    value="{{ $item['quantity'] }}" min="1"
                                                    class="w-20 p-1 border border-gray-300 rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            </div>
                                        </div>
                                    @endforeach

                                    <div class="flex justify-between items-center mt-4">
                                        <button type="submit" name="update[{{ $eventId }}]"
                                            class="bg-blue-600 text-white px-4 py-2 rounded-lg hover:bg-blue-700 transition duration-200">
                                            Update All
                                        </button>

                                        <p class="text-lg font-bold text-gray-800">
                                            Total: {{ $tickets[0]['eventCurrency'] }}
                                            {{ array_sum(array_map(fn($t) => $t['subtotal'], $tickets)) }}
                                        </p>
                                    </div>
                                </form>

                                {{-- Separate form for "Remove All" --}}
                                <form action="{{ route('cart.removeSingle', ['eventId' => $eventId, 'index' => 0]) }}" method="POST"
                                    class="mt-2">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit"
                                        class="bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                                        Remove All
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach

                {{-- Cart Summary --}}
                <div class="mt-8 p-4 bg-white rounded-lg shadow-lg border border-gray-200">
                    <h3 class="text-xl font-semibold text-gray-800 mb-4">Cart Summary</h3>
                    <p class="text-gray-700 mb-4">
                        Total for all events:
                        {{ array_sum(array_map(fn($event) => array_sum(array_map(fn($t) => $t['subtotal'], $event)), $cartItems)) }}
                        {{ $cartItems[array_key_first($cartItems)][0]['eventCurrency'] ?? 'NPR' }}
                    </p>

                    {{-- Checkout Form --}}
                    <form action="{{ route('cart.checkout') }}" method="POST" class="text-right">
                        @csrf
                        <button type="submit"
                            class="bg-red-600 text-white px-6 py-3 rounded-lg hover:bg-red-700 transition duration-200 text-lg font-semibold">
                            Proceed to Checkout
                        </button>
                    </form>
                </div>
            @else
                <div class="text-center p-6 bg-white rounded-lg shadow-lg border border-gray-200">
                    <p class="text-gray-600 text-xl">Your cart is empty.</p>
                    <a href="{{ route('home') }}"
                        class="mt-4 inline-block bg-red-600 text-white px-4 py-2 rounded-lg hover:bg-red-700 transition duration-200">
                        Continue Shopping
                    </a>
                </div>
            @endif
        </div>
    </div>
@endsection