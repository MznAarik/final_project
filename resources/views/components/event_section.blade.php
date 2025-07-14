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
                        <x-event-card :id="$event->id" :image="url('storage/' . $event->img_path)" :name="$event->name"
                            :location="$event->location" :price="$priceRange" :status="$event->status" :date="$event->start_date"
                            button="Book Now" />
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</section>