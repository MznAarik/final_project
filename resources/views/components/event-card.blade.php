<div class="event-card">
    <p class="ribbon {{ strtolower($status ?? '') }}"><i class="fas fa-info-circle"></i>
        {{ ucfirst($status ?? 'Status not specified') }}</p>
    <img src="{{ $image }}" alt="{{ $name }}" class="open-previewmodal-trigger">
    <div class="card-content">
        <label for="id" name="id" hidden>{{$id}}</label>
        <h3 class="bold">{{ $name }}</h3>
        <p class="event-date"><i class="fas fa-calendar-alt"></i> {{ $date ?? 'Date not specified' }}</p>
        <p class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $location }}</p>
        <div class="price-button-container">
            <p class="event-price"><strong>Rs. {{ $price }}</strong></p>
            <button class="small-button open-previewmodal-trigger">{{ $button ?? 'Book Now' }}</button>
        </div>
    </div>
</div>