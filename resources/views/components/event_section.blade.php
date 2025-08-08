@php
    if (collect($recommendedEvents)->isEmpty())
        return;

    $groupedEvents = $recommendedEvents->groupBy('status');
    $prioritizedStatuses = ['upcoming','exclusive', 'active', 'completed', 'cancelled'];
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

                <div style="max-height: 250px; overflow-y: auto; border: 1px solid #ccc; padding: 8px;">
                    <table style="width: 100%; border-collapse: collapse; font-size: 14px;">
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Organizer:</td>
                            <td style="padding: 2px 4px;"><span id="previewOrganizer">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Contact:</td>
                            <td style="padding: 2px 4px;"><span id="previewContact" class="lowercase">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Venue:</td>
                            <td style="padding: 2px 4px;"><span id="previewVenue">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Date:</td>
                            <td style="padding: 2px 4px;"><span id="previewDate">N/A</span></td>
                        </tr>
                        <tr>
                            <td style="padding: 2px 4px; font-weight: bold;">Location:</td>
                            <td style="padding: 2px 4px;"><span id="previewLocation" class="location-value">N/A</span>
                            </td>
                        </tr>
                    </table>
                </div>

                <!-- Fade effect for description -->
                <div class="fade-bottom">
                    <!-- Add style white-space: pre-line to preserve line breaks -->
                    <p id="previewDescription" style="white-space: pre-line;">Event Description</p>

                </div>

                <div style="flex-shrink: 0; padding: 15px 20px; text-align: center;">
                    <p style="margin: 8px 0;"><strong>Ticket Categories</strong></p>

                    <div id="previewTicketCategories" class="ticket-categories">
                        <!-- Ticket categories will be inserted here -->
                    </div>

                    <form id="bookForm" action="{{ route('cart.add') }}" method="POST" style="text-align: center;">
                        @csrf
                        <input type="hidden" id="previewEventId" name="id">
                        <input type="hidden" id="ticketQuantities" name="ticketQuantities">
                        <button class="preview-action-btn" type="submit"
                            style="padding: 8px 20px; font-weight: bold;">Book Now</button>
                    </form>
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

        const previewTitle = document.getElementById('previewTitle');
        const previewOrganizer = document.getElementById('previewOrganizer');
        const previewContact = document.getElementById('previewContact');
        const previewLocation = document.getElementById('previewLocation');
        const previewVenue = document.getElementById('previewVenue');
        const previewDate = document.getElementById('previewDate');
        const previewDesc = document.getElementById('previewDescription');

        let ticketData = [];

        function renderTicketCategories() {
            previewTicketCategories.innerHTML = '';

            ticketData.forEach((ticket, index) => {
                const div = document.createElement('div');
                div.className = 'ticket-box';
                div.style.border = '1px solid #ccc';
                div.style.padding = '8px';
                div.style.borderRadius = '4px';
                div.style.width = '100px';         // Force consistent narrow width
                div.style.margin = '0 5px';        // Optional horizontal gap
                div.style.boxSizing = 'border-box';
                div.style.textAlign = 'center';
                div.innerHTML = `
                <strong>${ticket.category.toUpperCase()}</strong><br>
                <p class="price" id="price-${index}">Rs. ${ticket.price}</p>
                <div class="quantity flex mt-2" data-index="${index}">
                    <button class="sub-btn m-2 text-2xl" type="button">-</button>
                    <input type="text" class="text-quantity" value="0" oninput="this.value = this.value.replace(/[^0-9]/g, '')" style="width: 40px; text-align: center;">
                    <button class="add-btn m-2 text-2xl" type="button">+</button>
                </div>
            `;
                previewTicketCategories.appendChild(div);
            });
        }

        function updatePrice(index, quantity) {
            const priceElement = document.getElementById(`price-${index}`);
            if (!priceElement || !ticketData[index]) return;
            const unitPrice = ticketData[index].price || 0;
            const totalPrice = quantity > 0 ? unitPrice * quantity : unitPrice;
            priceElement.textContent = `Rs. ${totalPrice}`;
        }

        previewTicketCategories.addEventListener('click', (e) => {
            const btn = e.target.closest('.add-btn, .sub-btn');
            if (!btn) return;

            const quantityDiv = btn.closest('.quantity');
            const input = quantityDiv.querySelector('.text-quantity');
            const index = parseInt(quantityDiv.dataset.index);
            const ticketBox = btn.closest('.ticket-box');

            let value = parseInt(input.value) || 0;

            if (btn.classList.contains('add-btn')) {
                if (value === 0) {
                    // Enforce exclusive selection
                    previewTicketCategories.querySelectorAll('.text-quantity').forEach(i => {
                        if (i !== input) i.value = '0';
                    });
                    previewTicketCategories.querySelectorAll('.ticket-box').forEach(box => box.classList.remove('active'));
                    ticketBox.classList.add('active');
                }
                value += 1;
            } else if (btn.classList.contains('sub-btn') && value > 0) {
                value -= 1;
                if (value === 0) ticketBox.classList.remove('active');
            }

            input.value = value;
            updatePrice(index, value);
        });

        previewTicketCategories.addEventListener('input', (e) => {
            if (!e.target.classList.contains('text-quantity')) return;

            const input = e.target;
            const quantityDiv = input.closest('.quantity');
            const index = parseInt(quantityDiv.dataset.index);
            let value = parseInt(input.value) || 0;

            if (value > 0) {
                previewTicketCategories.querySelectorAll('.text-quantity').forEach(i => {
                    if (i !== input) i.value = '0';
                });
                previewTicketCategories.querySelectorAll('.ticket-box').forEach(box => box.classList.remove('active'));
                input.closest('.ticket-box').classList.add('active');
            } else {
                input.closest('.ticket-box').classList.remove('active');
            }

            input.value = value;
            updatePrice(index, value);
        });

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

                const start = new Date(card.dataset.startdate);
                const end = new Date(card.dataset.enddate);
                previewDate.textContent = (isNaN(start) || isNaN(end))
                    ? 'N/A'
                    : `${start.toLocaleDateString()} - ${end.toLocaleDateString()}`;

                previewDesc.textContent = card.dataset.description || 'No description';
                previewEventId.value = card.dataset.id || '';

                try {
                    ticketData = JSON.parse(card.dataset.ticketdata || '[]');
                } catch {
                    ticketData = [];
                }

                renderTicketCategories();
                modal.style.display = 'flex';
            });
        });

        closeBtn.addEventListener('click', () => modal.style.display = 'none');
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.style.display = 'none';
        });

        fullscreenIcon.addEventListener('click', () => {
            if (!document.fullscreenElement) {
                img.requestFullscreen?.();
                img.webkitRequestFullscreen?.();
                img.msRequestFullscreen?.();
            } else {
                document.exitFullscreen?.();
            }
        });

        if (userLoggedIn) {
            const isFav = localStorage.getItem('favoriteActive') === 'true';
            if (isFav) {
                favIcon.textContent = 'favorite';
                favIcon.classList.add('fav-active');
            }
        }

        favIcon.addEventListener('click', () => {
            if (!userLoggedIn) {
                alert('Please log in to favorite items.');
                return;
            }
            const isFavorite = favIcon.textContent === 'favorite';
            favIcon.textContent = isFavorite ? 'favorite_border' : 'favorite';
            favIcon.classList.toggle('fav-active', !isFavorite);
            localStorage.setItem('favoriteActive', String(!isFavorite));
        });

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

        document.querySelectorAll('.photo-thumbnails .thumb').forEach(thumb => {
            thumb.addEventListener('click', function () {
                img.src = this.src;
                document.querySelectorAll('.photo-thumbnails .thumb').forEach(t => t.classList.remove('active'));
                this.classList.add('active');
            });
        });
    });
</script>