@php
    if (collect($recommendedEvents)->isEmpty()) {
        return;
    }
    $groupedEvents = $recommendedEvents->groupBy('status');
    $prioritizedStatuses = ['upcoming', 'active', 'completed', 'cancelled'];
@endphp

<section class="event-section">
    <h2 class="text-2xl font-bold mb-6 capitalize">{{ $title }}</h2>
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
                        <p class="ribbon {{ strtolower($event->status ?? '') }}"><i class="fas fa-info-circle"></i>
                            {{ ucfirst($event->status ?? 'Status not specified') }}</p>
                        <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->name }}"
                            class="open-previewmodal-trigger">
                        <div class="card-content">
                            <label for="id" name="id" hidden>{{ $event->id }}</label>
                            <h3 class="bold">{{ $event->name }}</h3>
                            <p class="event-date"><i class="fas fa-calendar-alt"></i>
                                {{ $event->start_date ?? 'Date not specified' }} - {{ $event->end_date ?? '' }}</p>
                            <p class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</p>
                            <div class="price-button-container">
                                <p class="event-price"><strong>Rs. {{ $priceRange }}</strong></p>
                                <button class="small-button open-previewmodal-trigger">{{ $button ?? 'Book Now' }}</button>
                            </div>
                        </div>
                    </div>
                    <x-preview :id="$event->id" :status="$event->status" :image="asset('storage/' . $event->img_path)"
                        :name="$event->name" :start-date="$event->start_date" :end-date="$event->end_date"
                        :ticket-data="$ticketData" />
                @endforeach
            </div>
        @endif
    @endforeach
</section>

<link rel="stylesheet" href="{{ asset('css/preview.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
<script src="{{ asset('js/preview.js') }}" defer></script>