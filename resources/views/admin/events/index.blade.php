@extends('admin.layouts.app')

@section('title', 'Event Management')

@section('content')
    <style>
        .events-container {
            padding: 1rem;
        }

        .events-header {
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
            color: white;
            padding: 2rem;
            border-radius: 12px;
            margin-bottom: 2rem;
            position: relative;
            overflow: hidden;
        }

        .events-header::before {
            content: '';
            position: absolute;
            top: -50px;
            right: -50px;
            width: 150px;
            height: 150px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
        }

        .events-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
            position: relative;
            z-index: 2;
        }

        .events-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 2rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }

        .controls-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 2rem;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .search-input, .filter-select {
            padding: 0.75rem 1rem;
            border: 1px solid #d1d5db;
            border-radius: 8px;
            font-size: 0.875rem;
            min-width: 180px;
            transition: all 0.2s;
        }

        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: #991b1b;
            box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
        }

        .add-event-btn {
            background: linear-gradient(135deg, #059669, #047857);
            color: white;
            padding: 0.75rem 1.5rem;
            border: none;
            border-radius: 8px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            text-decoration: none;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.3s;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .add-event-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.2);
            color: white;
            text-decoration: none;
        }

        .events-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .event-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: all 0.3s;
            position: relative;
        }

        .event-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .event-image {
            position: relative;
            overflow: hidden;
            height: 200px;
        }

        .event-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.3s;
        }

        .event-card:hover .event-image img {
            transform: scale(1.05);
        }

        .status-ribbon {
            position: absolute;
            top: 12px;
            left: 12px;
            padding: 0.5rem 1rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            backdrop-filter: blur(10px);
            z-index: 10;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-upcoming {
            background: #E3342F;
            color: white;
        }

        .status-active {
            background: rgba(34, 197, 94, 0.9);
            color: white;
        }

        .status-completed {
            background: rgba(59, 130, 246, 0.9);
            color: white;
        }

        .status-cancelled {
            background: rgba(107, 114, 128, 0.9);
            color: white;
        }

        .image-overlay {
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            background: linear-gradient(transparent, rgba(0, 0, 0, 0.7));
            padding: 1rem;
            color: white;
        }

        .card-content {
            padding: 1.5rem;
        }

        .event-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: #374151;
            margin-bottom: 1rem;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .event-details {
            display: flex;
            flex-direction: column;
            gap: 0.75rem;
            margin-bottom: 1.5rem;
        }

        .event-detail {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            font-size: 0.875rem;
            color: #6b7280;
        }

        .event-detail i {
            width: 16px;
            color: #991b1b;
        }

        .price-section {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            margin-bottom: 1rem;
        }

        .price-label {
            font-size: 0.75rem;
            color: #6b7280;
            text-transform: uppercase;
            font-weight: 600;
            margin-bottom: 0.25rem;
        }

        .price-value {
            font-size: 1.25rem;
            font-weight: 700;
            color: #991b1b;
        }

        .card-actions {
            display: flex;
            gap: 0.5rem;
        }

        .action-btn {
            flex: 1;
            padding: 0.75rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s;
            text-decoration: none;
            text-align: center;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.25rem;
        }

        .btn-edit {
            background: #fbbf24;
            color: #92400e;
        }

        .btn-edit:hover {
            background: #f59e0b;
            color: white;
            text-decoration: none;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-view:hover {
            background: #2563eb;
            text-decoration: none;
        }

        .btn-delete {
            background: #ef4444;
            color: white;
            min-width: 40px;
        }

        .btn-delete:hover {
            background: #dc2626;
        }

        .no-events {
            grid-column: 1 / -1;
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .no-events i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .no-events h3 {
            font-size: 1.5rem;
            margin-bottom: 0.5rem;
            color: #374151;
        }

        .event-stats-mini {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0.75rem;
            background: #f8fafc;
            border-top: 1px solid #e2e8f0;
            font-size: 0.75rem;
            color: #64748b;
        }

        .tickets-info {
            display: flex;
            gap: 1rem;
        }

        /* Modal Styles */
        .preview-modal {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.8);
            z-index: 1000;
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            max-width: 90%;
            max-height: 90%;
            border-radius: 8px;
            overflow: hidden;
        }

        .modal-content img {
            width: 100%;
            height: auto;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .events-header {
                padding: 1.5rem;
            }

            .events-header h1 {
                font-size: 1.5rem;
            }

            .events-stats {
                grid-template-columns: repeat(2, 1fr);
                gap: 1rem;
            }

            .controls-section {
                flex-direction: column;
                align-items: stretch;
            }

            .search-filters {
                justify-content: center;
            }

            .events-grid {
                grid-template-columns: 1fr;
            }

            .card-actions {
                flex-direction: column;
            }
        }

        /* Loading Animation */
        .loading-skeleton {
            background: linear-gradient(90deg, #f0f0f0 25%, #e0e0e0 50%, #f0f0f0 75%);
            background-size: 200% 100%;
            animation: loading 1.5s infinite;
        }

        @keyframes loading {
            0% { background-position: 200% 0; }
            100% { background-position: -200% 0; }
        }
    </style>

    <div class="events-container">
        <!-- Header Section with Stats -->
        <div class="events-header">
            <h1>
                <i class="fa-solid fa-calendar-days"></i>
                Events Management
            </h1>
            <div class="events-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $events->count() }}</div>
                    <div class="stat-label">Total Events</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $events->where('status', 'active')->count() }}</div>
                    <div class="stat-label">Active</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $events->where('status', 'upcoming')->count() }}</div>
                    <div class="stat-label">Upcoming</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $events->where('status', 'completed')->count() }}</div>
                    <div class="stat-label">Completed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $events->where('status', 'cancelled')->count() }}</div>
                    <div class="stat-label">Cancelled</div>
                </div>
                @if(isset($tickets))
                    <div class="stat-item">
                        <div class="stat-number">{{ $tickets->count() }}</div>
                        <div class="stat-label">Total Tickets</div>
                    </div>
                @endif
            </div>
        </div>

        <!-- Controls Section -->
        <div class="controls-section">
            <div class="search-filters">
                <input type="text" class="search-input" placeholder="Search events..." id="searchEvents">
                <select class="filter-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="upcoming">Upcoming</option>
                    <option value="active">Active</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                </select>
                <input type="date" class="search-input" id="dateFilter" placeholder="Filter by date">
            </div>
            <a href="{{ route('events.create') }}" class="add-event-btn">
                <i class="fa-solid fa-plus"></i>
                Add New Event
            </a>
        </div>

        <!-- Events Grid -->
        <div class="events-grid" id="eventsGrid">
            @if ($events->isEmpty())
                <div class="no-events">
                    <i class="fa-regular fa-calendar-xmark"></i>
                    <h3>No Events Found</h3>
                    <p>Start by creating your first event to manage your ticketing system.</p>
                </div>
            @else
                @foreach ($events as $event)
                    @php
                        $ticketData = is_array($event->ticket_category_price)
                            ? $event->ticket_category_price
                            : json_decode($event->ticket_category_price, true);

                        $prices = collect($ticketData)->pluck('price')->filter();
                        $minPrice = $prices->min();
                        $maxPrice = $prices->max();
                        $priceRange = $prices->isEmpty() ? 'Free' : ($minPrice == $maxPrice ? "Rs. " . number_format($minPrice) : "Rs. " . number_format($minPrice) . " - " . number_format($maxPrice));
                        
                        // Calculate tickets sold for this event
                        $ticketsSold = isset($tickets) ? $tickets->where('event_id', $event->id)->sum(function($ticket) {
                            return collect(json_decode($ticket->ticket_details, true))->sum('quantity');
                        }) : 0;
                    @endphp

                    <div class="event-card" data-event-id="{{ $event->id }}" data-status="{{ strtolower($event->status ?? '') }}" data-name="{{ strtolower($event->name) }}" data-date="{{ $event->start_date }}">
                        <div class="event-image">
                            <span class="status-ribbon status-{{ strtolower($event->status ?? 'upcoming') }}">
                                @switch(strtolower($event->status ?? ''))
                                    @case('active')
                                        <i class="fa-solid fa-play"></i>
                                        @break
                                    @case('completed')
                                        <i class="fa-solid fa-check"></i>
                                        @break
                                    @case('cancelled')
                                        <i class="fa-solid fa-times"></i>
                                        @break
                                    @default
                                        <i class="fa-solid fa-clock"></i>
                                @endswitch
                                {{ ucfirst($event->status ?? 'Upcoming') }}
                            </span>
                            
                            @if($event->img_path)
                                <img src="{{ asset('storage/' . $event->img_path) }}" 
                                     alt="{{ $event->name }}" 
                                     class="event-preview-trigger"
                                     onclick="openImagePreview('{{ asset('storage/' . $event->img_path) }}', '{{ $event->name }}')">
                            @else
                                <div style="background: linear-gradient(135deg, #e5e7eb, #d1d5db); height: 100%; display: flex; align-items: center; justify-content: center; color: #6b7280;">
                                    <i class="fa-solid fa-image" style="font-size: 2rem;"></i>
                                </div>
                            @endif
                        </div>

                        <div class="card-content">
                            <h3 class="event-title">{{ $event->name }}</h3>
                            
                            <div class="event-details">
                                <div class="event-detail">
                                    <i class="fa-regular fa-calendar"></i>
                                    <span>
                                        {{ $event->start_date ? \Carbon\Carbon::parse($event->start_date)->format('M d, Y') : 'Date TBA' }}
                                        @if($event->end_date && $event->end_date != $event->start_date)
                                            - {{ \Carbon\Carbon::parse($event->end_date)->format('M d, Y') }}
                                        @endif
                                    </span>
                                </div>
                                
                                <div class="event-detail">
                                    <i class="fa-solid fa-location-dot"></i>
                                    <span>{{ $event->location ?? 'Location TBA' }}</span>
                                </div>
                                
                                <div class="event-detail">
                                    <i class="fa-solid fa-ticket"></i>
                                    <span>{{ $ticketsSold }} tickets sold</span>
                                </div>
                            </div>

                            <div class="price-section">
                                <div class="price-label">Price Range</div>
                                <div class="price-value">{{ $priceRange }}</div>
                            </div>

                            <div class="card-actions">
                                <a href="{{ route('events.show', $event->id) }}" class="action-btn btn-view">
                                    <i class="fa-solid fa-eye"></i>
                                    View
                                </a>
                                <a href="{{ route('events.edit', $event->id) }}" class="action-btn btn-edit">
                                    <i class="fa-solid fa-edit"></i>
                                    Edit
                                </a>
                                <button onclick="deleteEvent({{ $event->id }}, '{{ addslashes($event->name) }}')" class="action-btn btn-delete">
                                    <i class="fa-solid fa-trash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="event-stats-mini">
                            <div class="tickets-info">
                                <span><i class="fa-solid fa-ticket"></i> {{ $ticketsSold }} sold</span>
                                <span><i class="fa-solid fa-eye"></i> Views: N/A</span>
                            </div>
                            <span>ID: #{{ str_pad($event->id, 4, '0', STR_PAD_LEFT) }}</span>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
    </div>

    <!-- Image Preview Modal -->
    <div id="imagePreviewModal" class="preview-modal" onclick="closeImagePreview()">
        <div class="modal-content" onclick="event.stopPropagation()">
            <img id="previewImage" src="" alt="">
        </div>
    </div>

    <script>
        // Search and Filter Functionality
        document.getElementById('searchEvents').addEventListener('input', filterEvents);
        document.getElementById('statusFilter').addEventListener('change', filterEvents);
        document.getElementById('dateFilter').addEventListener('change', filterEvents);

        function filterEvents() {
            const searchTerm = document.getElementById('searchEvents').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
            const dateFilter = document.getElementById('dateFilter').value;
            const eventCards = document.querySelectorAll('.event-card');

            eventCards.forEach(card => {
                const eventName = card.dataset.name;
                const eventStatus = card.dataset.status;
                const eventDate = card.dataset.date;

                const matchesSearch = !searchTerm || eventName.includes(searchTerm);
                const matchesStatus = !statusFilter || eventStatus === statusFilter;
                const matchesDate = !dateFilter || eventDate === dateFilter;

                if (matchesSearch && matchesStatus && matchesDate) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        }

        // Image Preview Functions
        function openImagePreview(imageSrc, eventName) {
            const modal = document.getElementById('imagePreviewModal');
            const previewImg = document.getElementById('previewImage');
            
            previewImg.src = imageSrc;
            previewImg.alt = eventName;
            modal.style.display = 'flex';
        }

        function closeImagePreview() {
            document.getElementById('imagePreviewModal').style.display = 'none';
        }

        // Delete Event Function
        function deleteEvent(eventId, eventName) {
            if (!confirm(`Are you sure you want to delete the event "${eventName}"? This action will soft delete the event and may affect associated tickets.`)) {
                return;
            }

            const card = document.querySelector(`[data-event-id="${eventId}"]`);
            if (card) {
                card.style.opacity = '0.5';
                card.style.pointerEvents = 'none';
            }

            
            fetch(`/events/destroy/${eventId}`, {
                method: 'DELETE',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'Content-Type': 'application/json',
                    'Accept': 'application/json'
                }
            })
            .then(response => {
                
                if (!response.ok) {
                    return response.text().then(text => {
                        throw new Error(text || 'Failed to soft delete event');
                    });
                }
                
                return response.json();
                
            })
            .then(data => {
                showNotification('Event soft deleted successfully!', 'success');
                if (card) card.remove();
                location.reload();
            })
            .catch(error => {
                console.error('Error soft deleting event:', error);
                showNotification(`Failed to soft delete event: ${error.message}`, 'error');
                if (card) {
                    card.style.opacity = '1';
                    card.style.pointerEvents = 'auto';
                }
            });
        }

        // Notification Function
        function showNotification(message, type) {
            const notification = document.createElement('div');
            notification.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                padding: 1rem 1.5rem;
                border-radius: 8px;
                color: white;
                font-weight: 500;
                z-index: 9999;
                animation: slideIn 0.3s ease;
                background: ${type === 'success' ? '#059669' : '#ef4444'};
                box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => notification.remove(), 300);
            }, 3000);
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
            @keyframes slideOut {
                from { transform: translateX(0); opacity: 1; }
                to { transform: translateX(100%); opacity: 0; }
            }
        `;
        document.head.appendChild(style);

        // Close modal with escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImagePreview();
            }
        });
    </script>
@endsection