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
</style>

@php
    if (collect($events)->isEmpty()) {
        return;
    }
    $groupedEvents = $events->groupBy('status');
    $prioritizedStatuses = ['upcoming', 'active', 'completed', 'cancelled'];
@endphp

@section('content')
    <div class="add-event">
        <a id="add-event" href="{{ route('events.create') }}">
            <i class="fa-solid fa-square-plus"></i> Add Event
        </a>
    </div>

    @foreach ($prioritizedStatuses as $status)
        @if ($groupedEvents->has($status) && $groupedEvents[$status]->isNotEmpty())

            <div class="event-cards">
                @foreach ($groupedEvents[$status] as $event)
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
                        <x-event-card :id="$event->id" :image="url('storage/' . $event->img_path)" :name="$event->name"
                            :location="$event->location" :price="$priceRange" :status="$event->status" :date="$event->start_date"
                            button="Edit Event" />
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
@endsection