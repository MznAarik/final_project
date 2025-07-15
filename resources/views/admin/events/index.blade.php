@extends('admin.layouts.app')

<style>
    .add-event {
        margin: 2rem;
        position: relative;
        display: flex;
        align-items: center;
        justify-content: end;
    }

    #add-event {
        padding: 0.5rem 1rem;
        cursor: pointer;
        background-color: red;
        color: white;
        border: none;
        transition: all 200ms ease-in-out;
        font-size: 1.2rem;
        font-weight: 600;
        border-radius: 4px;
    }

    #add-event:hover {
        transform: scale(1.05);
    }

    .event-cards {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 1.5rem;
        margin: 2rem;
    }

    .event-card {
        border: 1px solid #ccc;
        overflow: hidden;
        background: #fff;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.1);
        transition: transform 0.2s;
    }

    .event-card:hover {
        transform: scale(1.02);
    }

    .ribbon {
        padding: 0.4rem 1rem;
        background-color: #ccc;
        color: #fff;
        font-weight: bold;
        text-transform: capitalize;
    }

    .ribbon.upcoming {
        background-color: #28a745;
    }

    .ribbon.active {
        background-color: #007bff;
    }

    .ribbon.completed {
        background-color: #6c757d;
    }

    .ribbon.cancelled {
        background-color: #dc3545;
    }

    .card-content {
        padding: 1rem;
    }

    .card-content h3 {
        margin: 0.5rem 0;
    }

    .event-date,
    .event-location {
        font-size: 0.9rem;
        color: #555;
    }

    .event-price {
        font-size: 1rem;
    }

    .price-button-container {
        margin-top: 1rem;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .small-button {
        background-color: #007bff;
        border: none;
        padding: 0.4rem 0.8rem;
        color: white;
        font-weight: bold;
        cursor: pointer;
        border-radius: 4px;
    }

    .small-button:hover {
        background-color: #0056b3;
    }
</style>

@section('content')
    <div class="add-event">
        <a id="add-event" href="{{ route('events.create') }}">
            <i class="fa-solid fa-square-plus"></i> Add Event
        </a>
    </div>

    <div class="event-cards">
        @foreach ($events as $event)
            @php
                $ticketData = is_array($event->ticket_category_price)
                    ? $event->ticket_category_price
                    : json_decode($event->ticket_category_price, true);

                $prices = collect($ticketData)->pluck('price')->filter();
                $minPrice = $prices->min();
                $maxPrice = $prices->max();
                $priceRange = $prices->isEmpty() ? 'N/A' : ($minPrice == $maxPrice ? $minPrice : "$minPrice - $maxPrice");
            @endphp

            <div class="event-card">
                <p class="ribbon {{ strtolower($event->status ?? '') }}">
                    <i class="fas fa-info-circle"></i> {{ ucfirst($event->status ?? 'Status not specified') }}
                </p>
                <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->name }}" class="open-previewmodal-trigger"
                    style="width: 100%; height: 180px; object-fit: cover;">
                <div class="card-content">
                    <input type="hidden" name="id" value="{{ $event->id }}">
                    <h3 class="bold">{{ $event->name }}</h3>
                    <p class="event-date">
                        <i class="fas fa-calendar-alt"></i> {{ $event->start_date ?? 'Date not specified' }} -
                        {{ $event->end_date ?? '' }}
                    </p>
                    <p class="event-location">
                        <i class="fas fa-map-marker-alt"></i> {{ $event->location }}
                    </p>
                    <div class="price-button-container">
                        <p class="event-price"><strong>Rs. {{ $priceRange }}</strong></p>
                        <a href="{{ route('events.edit', $event->id) }}" class="small-button">Edit Event</a>
                    </div>
                </div>
            </div>
        @endforeach
    </div>
@endsection