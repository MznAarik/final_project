@extends('layouts.app')

@section('title', 'Your Tickets')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-slate-50 via-cyan-50 to-indigo-50">
        <div class="container mx-auto px-4 py-12">
            <!-- Header Section -->
            <div class="text-center mb-12">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-cyan-100 rounded-full mb-4">
                    <svg class="w-8 h-8 text-cyan-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                        </path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-900 mb-3">Your Event Tickets</h1>
                <p class="text-lg text-gray-600 max-w-2xl mx-auto">Manage and view all your purchased event tickets in one
                    place</p>
            </div>

            @if ($tickets->isEmpty())
                <!-- Empty State -->
                <div class="max-w-md mx-auto text-center">
                    <div class="bg-white rounded-2xl shadow-lg p-12">
                        <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                            <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                </path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-3">No tickets yet</h3>
                        <p class="text-gray-500 mb-6">You haven't purchased any tickets yet. Start exploring amazing events!</p>
                        <a href="{{ route('buy_tickets') }}"
                            class="inline-flex items-center px-6 py-3 bg-red-600 text-white font-medium rounded-lg hover:bg-red-700 transition-colors duration-200">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                            </svg>
                            Browse Events
                        </a>
                    </div>
                </div>
            @else
                <!-- Tickets Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-8">
                    @foreach ($tickets as $ticket)
                        <div
                            class="group bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 overflow-hidden border border-gray-100">
                            <!-- Ticket Header with Event Info -->
                            <div class="relative p-6 pb-4 bg-gradient-to-r from-red-700 to-cyan-600">
                                <div class="flex items-start justify-between">
                                    <div class="flex-1">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span
                                                class="px-2 py-1 bg-white/20 rounded-md text-xs font-medium text-white backdrop-blur-sm">
                                                BooKets
                                            </span>
                                        </div>
                                        <h2 class="text-xl font-bold text-white mb-1 line-clamp-2">
                                            {{ $ticket->event->name }}
                                        </h2>
                                        <p class="text-red-100 text-sm">
                                            Purchased {{ $ticket->created_at->diffForHumans() }}
                                        </p>
                                    </div>
                                    <div class="flex-shrink-0 ml-4">
                                        <div
                                            class="w-12 h-12 bg-white/20 rounded-xl flex items-center justify-center backdrop-blur-sm">
                                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
                                                </path>
                                            </svg>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <!-- Ticket Details -->
                            <div class="p-6">
                                <div class="space-y-4 mb-6">
                                    <!-- Batch Code -->
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-500">Batch Code</span>
                                        <span
                                            class="font-mono text-sm bg-gray-100 px-2 py-1 rounded">{{ $ticket->batch_code }}</span>
                                    </div>

                                    <!-- Quantity -->
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-500">Quantity</span>
                                        <div class="flex items-center space-x-2">
                                            <span
                                                class="w-6 h-6 bg-red-100 text-red-600 rounded-full flex items-center justify-center text-xs font-bold">{{ $ticket->total_quantity }}</span>
                                            <span
                                                class="text-sm text-gray-600">{{ Str::plural('ticket', $ticket->total_quantity) }}</span>
                                        </div>
                                    </div>

                                    <!-- Total Price -->
                                    <div class="flex items-center justify-between py-2 border-b border-gray-100">
                                        <span class="text-sm font-medium text-gray-500">Total Price</span>
                                        <span class="text-lg font-bold text-green-600" style="text-transform: uppercase;">
                                            {{ $ticket->total_price }} {{ $ticket->event->currency }}
                                        </span>
                                    </div>

                                    <!-- Purchase Date -->
                                    <div class="flex items-center justify-between py-2">
                                        <span class="text-sm font-medium text-gray-500">Purchase Date</span>
                                        <span class="text-sm text-gray-600">{{ $ticket->created_at->format('M d, Y') }}</span>
                                    </div>
                                </div>

                                <!-- Action Button -->
                                <div class="pt-2">
                                    <a href="{{ route('user.tickets.show', $ticket->batch_code) }}"
                                        class="w-full inline-flex items-center justify-center px-6 py-3 bg-gradient-to-r from-red-600 to-cyan-600 text-white font-semibold rounded-xl hover:from-red-700 hover:to-cyan-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transform hover:-translate-y-0.5 transition-all duration-200 group"
                                        aria-label="View tickets for batch {{ $ticket->batch_code }}">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                            </path>
                                        </svg>
                                        View Tickets
                                        <svg class="w-4 h-4 ml-2 group-hover:translate-x-1 transition-transform duration-200"
                                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </div>

                            <!-- Decorative bottom border -->
                            <div class="h-1 bg-gradient-to-r from-red-500 to-cyan-500"></div>
                        </div>
                    @endforeach
                </div>

                <!-- Tickets Summary (if multiple tickets exist) -->
                @if ($tickets->count() > 1)
                    <div class="mt-12 bg-white rounded-2xl shadow-lg p-8">
                        <div class="flex items-center justify-between">
                            <div>
                                <h3 class="text-lg font-semibold text-gray-900 mb-1">Tickets Summary</h3>
                                <p class="text-gray-600">You have {{ $tickets->count() }} ticket
                                    {{ Str::plural('batch', $tickets->count()) }} for
                                    {{ $tickets->pluck('event.name')->unique()->count() }}
                                    {{ Str::plural('event', $tickets->pluck('event.name')->unique()->count()) }}
                                </p>
                            </div>
                            <div class="border-l border-gray-200 pl-8 text-right">
                                <div class="text-2xl font-bold text-gray-900">{{ $tickets->sum('total_quantity') }}</div>
                                <div class="text-sm text-gray-500">Total Tickets</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endif
        </div>
    </div>

    @push('styles')
        <style>
            .line-clamp-2 {
                display: -webkit-box;
                -webkit-box-orient: vertical;
                overflow: hidden;
            }
        </style>
    @endpush
@endsection