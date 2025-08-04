@extends('admin.layouts.app')

@section('title', 'All Tickets Information')

@section('content')
    <style>
        .tickets-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            overflow: hidden;
        }

        .tickets-header {
            background: linear-gradient(135deg, #991b1b 0%, #dc2626 100%);
            color: white;
            padding: 2rem;
            position: relative;
            overflow: hidden;
        }

        .tickets-header::before {
            content: '';
            position: absolute;
            top: 0;
            right: 0;
            width: 100px;
            height: 100px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            transform: translate(30px, -30px);
        }

        .tickets-header h1 {
            font-size: 2rem;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .tickets-stats {
            display: flex;
            gap: 2rem;
            margin-top: 1rem;
            flex-wrap: wrap;
        }

        .stat-item {
            display: flex;
            flex-direction: column;
            align-items: center;
            text-align: center;
        }

        .stat-number {
            font-size: 1.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .stat-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 0.25rem;
        }

        .tickets-controls {
            padding: 1.5rem;
            background: #f9fafb;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .search-filters {
            display: flex;
            gap: 0.75rem;
            flex-wrap: wrap;
        }

        .search-input, .filter-select {
            padding: 0.5rem 0.75rem;
            border: 1px solid #d1d5db;
            border-radius: 6px;
            font-size: 0.875rem;
            min-width: 150px;
        }

        .search-input:focus, .filter-select:focus {
            outline: none;
            border-color: #991b1b;
            box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
        }

        .export-btn {
            background: #059669;
            color: white;
            padding: 0.5rem 1rem;
            border: none;
            border-radius: 6px;
            font-size: 0.875rem;
            font-weight: 500;
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 0.5rem;
            transition: all 0.2s;
        }

        .export-btn:hover {
            background: #047857;
            transform: translateY(-1px);
        }

        .table-container {
            overflow-x: auto;
        }

        .tickets-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 0.875rem;
        }

        .tickets-table th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            padding: 1rem 0.75rem;
            text-align: left;
            font-weight: 600;
            color: #475569;
            white-space: nowrap;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .tickets-table td {
            border-bottom: 1px solid #e2e8f0;
            padding: 1rem 0.75rem;
            vertical-align: middle;
        }

        .tickets-table tbody tr {
            transition: all 0.2s;
        }

        .tickets-table tbody tr:hover {
            background: #f8fafc;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .ticket-id {
            font-weight: 600;
            color: #1e40af;
            font-family: 'Courier New', monospace;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 0.75rem;
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            background: linear-gradient(135deg, #991b1b, #dc2626);
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: 600;
            font-size: 0.875rem;
        }

        .user-details h4 {
            margin: 0;
            font-weight: 600;
            color: #374151;
            font-size: 0.875rem;
        }

        .user-details p {
            margin: 0;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .event-info {
            max-width: 200px;
        }

        .event-name {
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.25rem;
            line-height: 1.2;
        }

        .event-date {
            font-size: 0.75rem;
            color: #6b7280;
            display: flex;
            align-items: center;
            gap: 0.25rem;
        }

        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.25rem;
            padding: 0.25rem 0.75rem;
            border-radius: 20px;
            font-size: 0.75rem;
            font-weight: 500;
            text-transform: capitalize;
        }

        .status-confirmed {
            background: #dcfce7;
            color: #166534;
        }

        .status-pending {
            background: #fef3c7;
            color: #92400e;
        }

        .status-cancelled {
            background: #fee2e2;
            color: #991b1b;
        }

        .status-used {
            background: #e0e7ff;
            color: #3730a3;
        }

        .category-tags {
            display: flex;
            flex-direction: column;
            gap: 0.25rem;
        }

        .category-tag {
            background: #f1f5f9;
            color: #475569;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-size: 0.75rem;
            font-weight: 500;
            border-left: 3px solid #991b1b;
        }

        .quantity-info {
            text-align: center;
        }

        .quantity-number {
            font-size: 1.125rem;
            font-weight: 700;
            color: #991b1b;
            margin-bottom: 0.25rem;
        }

        .quantity-label {
            font-size: 0.75rem;
            color: #6b7280;
        }

        .actions-cell {
            white-space: nowrap;
        }

        .action-buttons {
            display: flex;
            gap: 0.25rem;
        }

        .action-btn {
            width: 32px;
            height: 32px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 0.75rem;
            transition: all 0.2s;
        }

        .btn-view {
            background: #3b82f6;
            color: white;
        }

        .btn-view:hover {
            background: #2563eb;
            transform: scale(1.1);
        }

        .btn-edit {
            background: #f59e0b;
            color: white;
        }

        .btn-edit:hover {
            background: #d97706;
            transform: scale(1.1);
        }

        .no-tickets {
            text-align: center;
            padding: 4rem 2rem;
            color: #6b7280;
        }

        .no-tickets i {
            font-size: 4rem;
            margin-bottom: 1rem;
            opacity: 0.3;
        }

        .pagination-wrapper {
            padding: 1.5rem;
            background: #f9fafb;
            border-top: 1px solid #e5e7eb;
            display: flex;
            justify-content: between;
            align-items: center;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .tickets-controls {
                flex-direction: column;
                align-items: stretch;
            }
            
            .search-filters {
                justify-content: center;
            }
        }

        @media (max-width: 768px) {
            .tickets-header {
                padding: 1.5rem;
            }
            
            .tickets-header h1 {
                font-size: 1.5rem;
            }
            
            .tickets-stats {
                gap: 1rem;
            }
            
            .tickets-table th,
            .tickets-table td {
                padding: 0.75rem 0.5rem;
            }
            
            .user-info {
                flex-direction: column;
                text-align: center;
            }
            
            .action-buttons {
                flex-direction: column;
            }
        }

        /* Loading and Empty States */
        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.8);
            display: none;
            align-items: center;
            justify-content: center;
            z-index: 100;
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid #f3f4f6;
            border-top: 4px solid #991b1b;
            border-radius: 50%;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }
    </style>

    <div class="tickets-container">
        <!-- Header Section -->
        <div class="tickets-header">
            <h1>
                <i class="fa-solid fa-ticket"></i>
                Tickets Management
            </h1>
            <div class="tickets-stats">
                <div class="stat-item">
                    <div class="stat-number">{{ $tickets->count() }}</div>
                    <div class="stat-label">Total Tickets</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $tickets->where('status', 'confirmed')->count() }}</div>
                    <div class="stat-label">Confirmed</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $tickets->where('status', 'used')->count() }}</div>
                    <div class="stat-label">Used</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number">{{ $tickets->sum(function($ticket) { 
                        return collect(json_decode($ticket->ticket_details, true))->sum('quantity'); 
                    }) }}</div>
                    <div class="stat-label">Total Quantity</div>
                </div>
            </div>
        </div>

        <!-- Controls Section -->
        <div class="tickets-controls">
            <div class="search-filters">
                <input type="text" class="search-input" placeholder="Search tickets..." id="searchInput">
                <select class="filter-select" id="statusFilter">
                    <option value="">All Status</option>
                    <option value="confirmed">Confirmed</option>
                    <option value="pending">Pending</option>
                    <option value="cancelled">Cancelled</option>
                    <option value="used">Used</option>
                </select>
                <select class="filter-select" id="eventFilter">
                    <option value="">All Events</option>
                    @foreach($tickets->unique('event.id') as $ticket)
                        <option value="{{ $ticket->event->id }}">{{ $ticket->event->name }}</option>
                    @endforeach
                </select>
            </div>
            <button class="export-btn" onclick="exportTickets()">
                <i class="fa-solid fa-download"></i>
                Export
            </button>
        </div>

        <!-- Table Section -->
        <div class="table-container">
            <div class="loading-overlay" id="loadingOverlay">
                <div class="spinner"></div>
            </div>
            
            @if($tickets->isNotEmpty())
                <table class="tickets-table" id="ticketsTable">
                    <thead>
                        <tr>
                            <th>Ticket ID</th>
                            <th>User</th>
                            <th>Event</th>
                            <th>Status</th>
                            <th>Categories</th>
                            <th>Quantity</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($tickets as $ticket)
                            @php
                                $ticketDetails = json_decode($ticket->ticket_details, true) ?? [];
                                $totalQuantity = collect($ticketDetails)->sum('quantity');
                            @endphp
                            <tr data-ticket-id="{{ $ticket->id }}" data-status="{{ $ticket->status }}" data-event-id="{{ $ticket->event->id }}">
                                <td>
                                    <span class="ticket-id">#{{ $ticket->id }}</span>
                                </td>
                                
                                <td>
                                    <div class="user-info">
                                        <div class="user-avatar">
                                            {{ strtoupper(substr($ticket->user->name, 0, 1)) }}
                                        </div>
                                        <div class="user-details">
                                            <h4>{{ $ticket->user->name }}</h4>
                                            <p>{{ $ticket->user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="event-info">
                                        <div class="event-name">{{ $ticket->event->name }}</div>
                                        <div class="event-date">
                                            <i class="fa-regular fa-calendar"></i>
                                            {{ \Carbon\Carbon::parse($ticket->event->date)->format('M d, Y') }}
                                        </div>
                                    </div>
                                </td>
                                
                                <td>
                                    <span class="status-badge status-{{ $ticket->status }}">
                                        @switch($ticket->status)
                                            @case('confirmed')
                                                <i class="fa-solid fa-check-circle"></i>
                                                @break
                                            @case('pending')
                                                <i class="fa-solid fa-clock"></i>
                                                @break
                                            @case('cancelled')
                                                <i class="fa-solid fa-times-circle"></i>
                                                @break
                                            @case('used')
                                                <i class="fa-solid fa-ticket-simple"></i>
                                                @break
                                        @endswitch
                                        {{ ucfirst($ticket->status) }}
                                    </span>
                                </td>
                                
                                <td>
                                    <div class="category-tags">
                                        @forelse($ticketDetails as $detail)
                                            <span class="category-tag">
                                                {{ $detail['category'] ?? 'Unknown' }}
                                            </span>
                                        @empty
                                            <span class="category-tag">No Category</span>
                                        @endforelse
                                    </div>
                                </td>
                                
                                <td>
                                    <div class="quantity-info">
                                        <div class="quantity-number">{{ $totalQuantity }}</div>
                                        <div class="quantity-label">
                                            {{ $totalQuantity == 1 ? 'ticket' : 'tickets' }}
                                        </div>
                                    </div>
                                </td>
                                
                                <td class="actions-cell">
                                    <div class="action-buttons">
                                        <button class="action-btn btn-view" onclick="viewTicket({{ $ticket->id }})" title="View Details">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="action-btn btn-edit" onclick="editTicket({{ $ticket->id }})" title="Edit Ticket">
                                            <i class="fa-solid fa-edit"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @else
                <div class="no-tickets">
                    <i class="fa-regular fa-ticket"></i>
                    <h3>No Tickets Found</h3>
                    <p>There are currently no tickets in the system.</p>
                </div>
            @endif
        </div>

        <!-- Pagination Section -->
        @if(isset($tickets) && method_exists($tickets, 'links'))
            <div class="pagination-wrapper">
                {{ $tickets->links() }}
            </div>
        @endif
    </div>

    <script>
        // Search and Filter Functionality
        document.getElementById('searchInput').addEventListener('input', function() {
            filterTickets();
        });

        document.getElementById('statusFilter').addEventListener('change', function() {
            filterTickets();
        });

        document.getElementById('eventFilter').addEventListener('change', function() {
            filterTickets();
        });

        function filterTickets() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const statusFilter = document.getElementById('statusFilter').value;
            const eventFilter = document.getElementById('eventFilter').value;
            const rows = document.querySelectorAll('#ticketsTable tbody tr');

            rows.forEach(row => {
                const ticketId = row.querySelector('.ticket-id').textContent.toLowerCase();
                const userName = row.querySelector('.user-details h4').textContent.toLowerCase();
                const eventName = row.querySelector('.event-name').textContent.toLowerCase();
                const status = row.dataset.status;
                const eventId = row.dataset.eventId;

                const matchesSearch = ticketId.includes(searchTerm) || 
                                    userName.includes(searchTerm) || 
                                    eventName.includes(searchTerm);
                const matchesStatus = !statusFilter || status === statusFilter;
                const matchesEvent = !eventFilter || eventId === eventFilter;

                if (matchesSearch && matchesStatus && matchesEvent) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        }

        // Action Functions
        function viewTicket(ticketId) {
            // window.location.href = `/admin/tickets/${ticketId}`;
            showNotification('View ticket functionality would be implemented here', 'info');
        }

        function editTicket(ticketId) {
            // window.location.href = `/admin/tickets/${ticketId}/edit`;
            showNotification('Edit ticket functionality would be implemented here', 'info');
        }

        function exportTickets() {
            showLoading(true);
            // Simulate export
            setTimeout(() => {
                showLoading(false);
                showNotification('Tickets exported successfully!', 'success');
            }, 2000);
        }

        // Utility Functions
        function showLoading(show) {
            document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
        }

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
                background: ${type === 'success' ? '#059669' : type === 'error' ? '#ef4444' : '#3b82f6'};
            `;
            notification.textContent = message;
            
            document.body.appendChild(notification);
            
            setTimeout(() => {
                notification.remove();
            }, 3000);
        }

        // Add animation styles
        const style = document.createElement('style');
        style.textContent = `
            @keyframes slideIn {
                from { transform: translateX(100%); opacity: 0; }
                to { transform: translateX(0); opacity: 1; }
            }
        `;
        document.head.appendChild(style);
    </script>
@endsection