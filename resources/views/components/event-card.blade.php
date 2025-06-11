<div class="event-card">
    <img src="{{ $image }}" alt="{{ $name }}" class="open-previewmodal-trigger">
    <div class="card-content">
        <h3>{{ $name }}</h3>
        <p class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $location }}</p>
        <div class="price-button-container">
            <p class="event-price"><strong>Rs. {{ $price }}</strong></p>
            <button class="small-button open-previewmodal-trigger">{{ $button ?? 'Book Now' }}</button>
        </div>
    </div>
</div>
