
<section class="event-section"> 
    <h2>{{ $title }}</h2>    
    <div class="event-cards">
        @foreach ([1, 2, 3, 4] as $id)
            <div class="event-card">
                <x-event-card 
                    :image="asset('images/event1.jpg')" 
                    :name="$title . ' ' . $id" 
                    location="Kathmandu City Hall" 
                    :price="300 + ($id * 50)" 
                    button="Book Now" 
                />
            </div>
        @endforeach
    </div>
       @if (!empty($seeMoreLink))
        <div class="see-more-container">
            <a href="{{ $seeMoreLink }}" class="see-more-link">See More →</a>
        </div>
    @endif
</section>
