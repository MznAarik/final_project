<section class="ticket-section">
    <div class="ticket-cards">
        @foreach ([1, 2, 3, 4, 5, 6, 7, 8] as $id)
            <div class="ticket-card">
                <x-event-card
                    :image="asset('images/event1.jpg')"
                    :name="'Event ' . $id"
                    location="Bhaktapur Mall"
                    :price="300 + ($id * 50)"
                    button="Buy Now"
                />
            </div>
        @endforeach
    </div>
</section>
