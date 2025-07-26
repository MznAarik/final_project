@php
    if (collect($recommendedEvents)->isEmpty())
        return;

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

                    <div class="event-card open-previewmodal-trigger" data-id="{{ $event->id }}" data-name="{{ $event->name }}"
                        data-image="{{ asset('storage/' . $event->img_path) }}" data-location="{{ $event->location }}"
                        data-venue="{{ $event->venue }}" data-organizer="{{ $event->organizer }}"
                        data-contact="{{ $event->contact_info }}" data-price="{{ $priceRange }}"
                        data-description="{{ $event->description }}" data-startdate="{{ $event->start_date }}"
                        data-enddate="{{ $event->end_date }}" data-status="{{ $event->status }}"
                        data-ticketdata='@json($ticketData)'>

                        <p class="ribbon {{ strtolower($event->status ?? '') }}">
                            <i class="fas fa-info-circle"></i> {{ ucfirst($event->status ?? 'Status not specified') }}
                        </p>
                        <img src="{{ asset('storage/' . $event->img_path) }}" alt="{{ $event->name }}">
                        <div class="card-content">
                            <h3 class="bold">{{ $event->name }}</h3>
                            <p class="event-date"><i class="fas fa-calendar-alt"></i>
                                {{ $event->start_date ?? 'Date not specified' }} - {{ $event->end_date ?? '' }}</p>
                            <p class="event-location"><i class="fas fa-map-marker-alt"></i> {{ $event->location }}</p>
                            <div class="price-button-container">
                                <p class="event-price"><strong>Rs. {{ $priceRange }}</strong></p>
                                <button class="small-button">Book Now</button>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif
    @endforeach
</section>

<!-- Modal Preview -->
<div id="previewModal" class="preview-modal" style="display: none;">
    <div class="preview-modal-content">
        <button class="preview-close-btn">×</button>
        <div class="preview-body">
            <div class="image-box">
                <div class="image-box-floating floating">
                    <img id="previewImage" src="" alt="Event Image">
                    <div class="image-footer">
                        <div class="controls">
                            <div class="share-wrapper">
                                <div class="share-container">
                                    <i class="material-icons share-icon">share</i>
                                    <div class="share-options">
                                        <a href="#" class="share-btn fb"><i class="fab fa-facebook-f"></i></a>
                                        <a href="#" class="share-btn ig"><i class="fab fa-instagram"></i></a>
                                        <a href="#" class="share-btn wa"><i class="fab fa-whatsapp"></i></a>
                                        <a href="#" class="share-btn tw"><i class="fab fa-twitter"></i></a>
                                        <a href="#" class="share-btn link"><i class="fas fa-link"></i></a>
                                    </div>
                                </div>
                            </div>
                            <i class="material-icons" id="favoriteIcon">favorite_border</i>
                            <i class="material-icons" id="fullscreenIcon">fullscreen</i>
                        </div>
                    </div>
                </div>
            </div>

            <div class="preview-details">
                <h2 id="previewTitle">Event Name</h2>

                <p><strong>Organizer:</strong> <span id="previewOrganizer">N/A</span></p>
                <p><strong>Contact:</strong> <span id="previewContact" class="lowercase">N/A</span></p>
                <p><strong>Venue:</strong> <span id="previewVenue">N/A</span></p>
                <p><strong>Status:</strong> <span id="previewStatus">N/A</span></p>
                <p><strong>Date:</strong> <span id="previewDate">N/A</span></p>
                <p id="previewLocation"> <strong> Location: </strong><span class="location-value"> N/A</span></p>

                <p><strong>Ticket Categories:</strong></p>
                <div id="previewTicketCategories" style="display:flex; gap:10px; margin: 7px 10px 0; flex-wrap: wrap;">
                </div>

                <form id="bookForm" action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" id="previewEventId" name="id">
                    <input type="hidden" id="ticketQuantities" name="ticketQuantities">
                    <button class="preview-action-btn" type="submit">Book Now</button>
                </form>

                <div class="fade-bottom">
                    <p id="previewDescription">Event Description</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Styles and Scripts -->
<link rel="stylesheet" href="{{ asset('css/preview.css') }}">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" />
<link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">

<script>
    document.addEventListener('DOMContentLoaded', () => {
        const modal = document.getElementById('previewModal');
        const closeBtn = modal.querySelector('.preview-close-btn');
        const fullscreenIcon = document.getElementById('fullscreenIcon');
        const favIcon = document.getElementById('favoriteIcon');
        const img = document.getElementById('previewImage');
        const userLoggedIn = true; // replace with actual auth check
        const bookForm = document.getElementById('bookForm');
        const previewTicketCategories = document.getElementById('previewTicketCategories');
        const previewEventId = document.getElementById('previewEventId');
        const ticketQuantitiesInput = document.getElementById('ticketQuantities');

        // Preview modal content elements
        const previewTitle = document.getElementById('previewTitle');
        const previewOrganizer = document.getElementById('previewOrganizer');
        const previewContact = document.getElementById('previewContact');
        const previewLocation = document.getElementById('previewLocation').querySelector('.location-value');
        const previewVenue = document.getElementById('previewVenue');
        const previewStatus = document.getElementById('previewStatus');
        const previewDate = document.getElementById('previewDate');
        const previewDesc = document.getElementById('previewDescription');

        let ticketData = []; // Moved to global scope within the event listener

        document.querySelectorAll('.open-previewmodal-trigger').forEach(trigger => {
            trigger.addEventListener('click', (e) => {
                const card = e.target.closest('.event-card');
                if (!card) return;

                img.src = card.dataset.image || '';
                previewTitle.textContent = card.dataset.name || 'N/A';
                previewOrganizer.textContent = card.dataset.organizer || 'N/A';
                previewContact.textContent = card.dataset.contact || 'N/A';
                previewLocation.textContent = card.dataset.location || 'N/A';
                previewVenue.textContent = card.dataset.venue || 'N/A';
                previewStatus.textContent = card.dataset.status || 'N/A';

                const start = new Date(card.dataset.startdate);
                const end = new Date(card.dataset.enddate);
                previewDate.textContent = (isNaN(start) || isNaN(end))
                    ? 'N/A'
                    : `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`;

                previewDesc.textContent = card.dataset.description || 'No description';
                previewEventId.value = card.dataset.id || '';

                // Parse and store ticket data
                try {
                    ticketData = JSON.parse(card.dataset.ticketdata || '[]');
                } catch (error) {
                    ticketData = [];
                }

                // Clear previous ticket categories
                previewTicketCategories.innerHTML = '';

                // Render ticket categories with quantity controls
                ticketData.forEach((ticket, index) => {
                    const div = document.createElement('div');
                    div.style.border = '1px solid #ccc';
                    div.style.padding = '10px';
                    div.style.borderRadius = '4px';
                    div.style.minWidth = '100px';
                    div.innerHTML = `
                    <strong>${ticket.category.toUpperCase()}</strong><br>Rs. ${ticket.price}
                    <div class="quantity flex mt-2" data-index="${index}">
                        <button class="sub-btn m-2 text-2xl" type="button">-</button>
                        <input type="text" class="text-quantity" oninput="this.value = this.value.replace(/[^0-9]/g, '')" min="0" value="0" style="width: 40px; text-align: center;">
                        <button class="add-btn m-2 text-2xl" type="button">+</button>
                    </div>
                `;
                    previewTicketCategories.appendChild(div);
                });

                modal.style.display = 'flex';
            });
        });

        // Quantity control event listeners
        previewTicketCategories.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-btn, .sub-btn');
            if (!btn) return;

            const quantityDiv = btn.closest('.quantity');
            const input = quantityDiv.querySelector('.text-quantity');
            let value = parseInt(input.value) || 0;

            if (btn.classList.contains('add-btn')) {
                value++;
            } else if (btn.classList.contains('sub-btn') && value > 0) {
                value--;
            }

            input.value = value;
        });

        // Form submission with validation
        bookForm.addEventListener('submit', (e) => {
            const quantities = Array.from(previewTicketCategories.querySelectorAll('.text-quantity'))
                .map(input => parseInt(input.value) || 0);
            const totalQuantity = quantities.reduce((sum, qty) => sum + qty, 0);

            if (totalQuantity === 0) {
                e.preventDefault();
                alert('Please select at least one ticket.');
                return;
            }

            const selectedTickets = [];
            previewTicketCategories.querySelectorAll('.quantity').forEach((div, index) => {
                const quantity = parseInt(div.querySelector('.text-quantity').value) || 0;
                if (quantity > 0 && ticketData[index]) {
                    selectedTickets.push({
                        category: ticketData[index].category,
                        quantity: quantity
                    });
                }
            });
            ticketQuantitiesInput.value = JSON.stringify(selectedTickets);
        });

        // Close modal
        closeBtn.addEventListener('click', () => {
            modal.style.display = 'none';
        });

        // Close modal when clicking outside content
        modal.addEventListener('click', (e) => {
            if (e.target === modal) {
                modal.style.display = 'none';
            }
        });

        // Fullscreen toggle on image
        fullscreenIcon.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                if (img.requestFullscreen) {
                    img.requestFullscreen();
                } else if (img.webkitRequestFullscreen) {
                    img.webkitRequestFullscreen();
                } else if (img.msRequestFullscreen) {
                    img.msRequestFullscreen();
                }
            } else {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                }
            }
        });

        // Load favorite status
        if (userLoggedIn) {
            const isFav = localStorage.getItem('favoriteActive') === 'true';
            if (isFav) {
                favIcon.textContent = 'favorite';
                favIcon.classList.add('fav-active');
            }
        }

        // Toggle favorite icon
        favIcon.addEventListener('click', () => {
            if (!userLoggedIn) {
                alert('Please log in to favorite items.');
                return;
            }

            if (favIcon.textContent === 'favorite_border') {
                favIcon.textContent = 'favorite';
                favIcon.classList.add('fav-active');
                localStorage.setItem('favoriteActive', 'true');
            } else {
                favIcon.textContent = 'favorite_border';
                favIcon.classList.remove('fav-active');
                localStorage.setItem('favoriteActive', 'false');
            }
        });

        // Share buttons toggle
        document.querySelectorAll('.share-container').forEach((shareWrapper) => {
            const shareIcon = shareWrapper.querySelector('.share-icon');
            const shareOptions = shareWrapper.querySelector('.share-options');
            const card = shareWrapper.closest('.image-box-floating');

            shareIcon.addEventListener('click', (e) => {
                e.stopPropagation();
                const isExpanded = shareWrapper.classList.toggle('expanded');
                shareOptions.classList.toggle('active', isExpanded);
                card.classList.toggle('share-active', isExpanded);
            });

            document.addEventListener('click', (e) => {
                if (!shareWrapper.contains(e.target)) {
                    shareWrapper.classList.remove('expanded');
                    shareOptions.classList.remove('active');
                    card.classList.remove('share-active');
                }
            });
        });

        // Thumbnail click swap
        document.querySelectorAll('.photo-thumbnails .thumb').forEach(thumb => {
            thumb.addEventListener('click', function () {
                img.src = this.src;

                document.querySelectorAll('.photo-thumbnails .thumb').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>