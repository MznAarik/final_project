@extends('layouts.app')

@php
    $categories = json_decode($ticket->ticket_details, true) ?? [];
@endphp

@section('content')
    <div class="min-h-screen bg-gray-100 py-10 px-4">
        <div class="max-w-xl mx-auto bg-white rounded-2xl shadow-xl p-6 space-y-6">

            {{-- QR Code Section --}}
            <div class="flex justify-center">
                @if ($ticket->qr_code)
                    <img src="{{ Storage::url($ticket->qr_code) }}" alt="QR Code"
                        class="w-48 h-48 object-contain border border-gray-300 rounded-lg shadow-md hover:scale-105 transition-transform duration-300">
                @else
                    <p class="text-red-600 text-center font-semibold">No QR code available for this ticket.</p>
                @endif
            </div>

            {{-- Event Name --}}
            <div class="text-center">
                <h2 class="text-2xl font-bold text-gray-900">{{ $ticket->event->name }}</h2>
                <p class="text-gray-600 mt-1">Event Overview</p>
            </div>

            {{-- Ticket Categories --}}
            <div class="bg-gray-50 rounded-lg p-5 space-y-4">
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Purchased By: {{$ticket->user->name}}</h3>
                <h3 class="text-lg font-semibold text-gray-800 border-b pb-2">Ticket Categories</h3>
                @if (!empty($categories))
                    <ul class="space-y-3">
                        @foreach ($categories as $item)
                            <li class="bg-white p-4 rounded-md shadow-sm border">
                                <p><strong>Seat Category:</strong> {{ $item['category'] ?? 'N/A' }}</p>
                                <p><strong>Quantity:</strong> {{ $item['quantity'] ?? 0 }}</p>
                                <p><strong>Subtotal:</strong> {{ $ticket->event->currency }}
                                    {{ number_format(($item['price'] ?? 0) * ($item['quantity'] ?? 1), 2) }}
                                </p>
                            </li>
                        @endforeach
                    </ul>
                @else
                    <p class="text-gray-600">No category details available.</p>
                @endif
            </div>

            {{-- Ticket Summary --}}
            <div class="grid gap-4 text-gray-800 bg-gray-50 rounded-lg p-5">
                <p><strong>Total Price:</strong>
                    <span class="text-red-600 font-bold">{{ $ticket->event->currency }}
                        {{ number_format($ticket->total_price, 2) }}</span>
                </p>
                <p><strong>Deadline:</strong>
                    {{ $ticket->deadline ? \Carbon\Carbon::parse($ticket->deadline)->format('Y-m-d H:i:s') : 'N/A' }}
                </p>
                @if ($ticket->cancellation_reason)
                    <p><strong>Cancellation Reason:</strong> {{ $ticket->cancellation_reason }}</p>
                @endif
            </div>

            {{-- Back Button --}}
            <div class="text-center">
                <a href="{{ route('user.tickets.index') }}"
                    class="inline-flex items-center px-6 py-2 bg-red-600 text-white font-semibold rounded-lg hover:bg-red-700 transition">
                    ← Back to All Tickets
                </a>
            </div>
        </div>
    </div>
@endsection