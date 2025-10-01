@extends('admin.layouts.app')

@section('title', 'All Tickets Information')

@section('content')
<style>
    .tickets-container {
        background: white;
        border-radius: 12px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        overflow: hidden;
        margin-bottom: 2rem;
    }

    .table-container {
        overflow-x: auto;
        position: relative;
    }

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

    /* =================================
       HEADER STYLES
    ================================= */
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
        position: relative;
        z-index: 1;
    }

    .tickets-stats {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
        gap: 2rem;
        margin-top: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .stat-item {
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        padding: 0.5rem;
        background: rgba(255, 255, 255, 0.1);
        border-radius: 8px;
        border: 1px solid rgba(255, 255, 255, 0.2);
        transition: all 0.3s ease;
    }

    .stat-item:hover {
        background: rgba(255, 255, 255, 0.15);
        transform: translateY(-2px);
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

    /* =================================
       CONTROLS & FILTERS STYLES
    ================================= */
    .tickets-controls {
        padding: 1.5rem;
        background: #f9fafb;
        border-bottom: 1px solid #e5e7eb;
    }

    .controls-grid {
        display: grid;
        grid-template-columns: 1fr auto;
        gap: 1.5rem;
        align-items: end;
    }

    .filters-section {
        display: grid;
        gap: 1rem;
    }

    .filters-row {
        display: flex;
        gap: 0.75rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .filter-label {
        font-size: 0.875rem;
        font-weight: 500;
        color: #374151;
        min-width: 80px;
    }

    .search-input, .filter-select {
        padding: 0.5rem 0.75rem;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        font-size: 0.875rem;
        min-width: 150px;
        background: white;
        transition: all 0.2s ease;
    }

    .search-input:focus, .filter-select:focus {
        outline: none;
        border-color: #991b1b;
        box-shadow: 0 0 0 3px rgba(153, 27, 27, 0.1);
    }

    .export-actions {
        display: flex;
        gap: 0.5rem;
    }

    .export-btn, .clear-btn {
        padding: 0.5rem 1rem;
        border: none;
        border-radius: 6px;
        font-size: 0.875rem;
        font-weight: 500;
        cursor: pointer;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.2s ease;
    }

    .export-btn {
        background: #059669;
        color: white;
    }

    .export-btn:hover {
        background: #047857;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(5, 150, 105, 0.2);
    }

    .clear-btn {
        background: #6b7280;
        color: white;
    }

    .clear-btn:hover {
        background: #4b5563;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(107, 114, 128, 0.2);
    }

    /* =================================
       TABLE STYLES
    ================================= */
    .tickets-table {
        width: 100%;
        border-collapse: collapse;
        font-size: 0.875rem;
        min-width: 1200px;
        background: white;
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
        transition: all 0.2s ease;
    }

    .tickets-table tbody tr:hover {
        background: #f8fafc;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    /* =================================
       TABLE CELL CONTENT STYLES
    ================================= */
    .ticket-id {
        font-weight: 600;
        color: #1e40af;
        font-family: 'Courier New', monospace;
    }

    .user-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
        min-width: 180px;
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
        flex-shrink: 0;
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

    /* =================================
       STATUS & PAYMENT STYLES
    ================================= */
    .status-container {
        position: relative;
        display: inline-block;
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
        cursor: pointer;
        transition: all 0.2s ease;
        min-width: 90px;
        justify-content: center;
    }

    .status-badge:hover {
        transform: scale(1.05);
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);
    }

    .status-confirmed {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #16a34a;
    }

    .status-pending {
        background: #fef3c7;
        color: #92400e;
        border: 1px solid #f59e0b;
    }

    .status-cancelled {
        background: #fee2e2;
        color: #991b1b;
        border: 1px solid #ef4444;
    }

    .status-used {
        background: #e0e7ff;
        color: #3730a3;
        border: 1px solid #6366f1;
    }

    .status-dropdown {
        position: absolute;
        top: 100%;
        left: 0;
        right: 0;
        background: white;
        border: 1px solid #d1d5db;
        border-radius: 6px;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        z-index: 50;
        display: none;
        animation: slideDown 0.2s ease;
    }

    .status-dropdown.show {
        display: block;
    }

    @keyframes slideDown {
        from {
            opacity: 0;
            transform: translateY(-4px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

    .status-option {
        padding: 0.5rem 0.75rem;
        cursor: pointer;
        font-size: 0.75rem;
        font-weight: 500;
        transition: all 0.2s ease;
        border-bottom: 1px solid #f3f4f6;
    }

    .status-option:last-child {
        border-bottom: none;
    }

    .status-option:hover {
        background: #f3f4f6;
    }

    .status-option.confirmed {
        color: #166534;
    }

    .status-option.pending {
        color: #92400e;
    }

    .status-option.cancelled {
        color: #991b1b;
    }

    .payment-info {
        min-width: 140px;
    }

    .payment-method {
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        padding: 0.25rem 0.5rem;
        border-radius: 12px;
        font-size: 0.75rem;
        font-weight: 500;
        text-transform: capitalize;
        margin-bottom: 0.25rem;
    }

    .payment-paypal {
        background: #e0f2fe;
        color: #0277bd;
    }

    .payment-esewa {
        background: #e0f2fe;
        color: #11ac0b;
    }

    .payment-stripe {
        background: #f3e8ff;
        color: #7c3aed;
    }

    .payment-bank {
        background: #ecfdf5;
        color: #059669;
    }

    .payment-cash {
        background: #fef3c7;
        color: #92400e;
    }

    .payment-amount {
        font-weight: 700;
        color: #374151;
        font-size: 0.875rem;
        display: flex;
        flex-direction: column;
    }

    .payment-amount-usd {
        font-size: 0.75rem;
        color: #6b7280;
        font-weight: 400;
    }

    .payment-id {
        font-size: 0.75rem;
        color: #6b7280;
        font-family: 'Courier New', monospace;
    }

    .category-tags {
        display: flex;
        flex-direction: column;
        gap: 0.25rem;
        max-width: 120px;
    }

    .category-tag {
        background: #f1f5f9;
        color: #475569;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
        border-left: 3px solid #991b1b;
        text-align: center;
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

    /* =================================
       ACTION BUTTONS STYLES
    ================================= */
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
        transition: all 0.2s ease;
    }

    .btn-view {
        background: #3b82f6;
        color: white;
    }

    .btn-view:hover {
        background: #2563eb;
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(59, 130, 246, 0.3);
    }

    .btn-edit {
        background: #f59e0b;
        color: white;
    }

    .btn-edit:hover {
        background: #d97706;
        transform: scale(1.1);
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.3);
    }

    /* =================================
       NOTIFICATION & MESSAGE STYLES
    ================================= */
    .flash-message {
        position: fixed;
        top: 20px;
        right: 20px;
        padding: 1rem 1.5rem;
        border-radius: 8px;
        color: white;
        font-weight: 500;
        z-index: 9999;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        animation: slideIn 0.3s ease;
    }

    .flash-success {
        background: #059669;
    }

    .flash-error {
        background: #ef4444;
    }

    @keyframes slideIn {
        from { transform: translateX(100%); opacity: 0; }
        to { transform: translateX(0); opacity: 1; }
    }

    @keyframes slideOut {
        from { transform: translateX(0); opacity: 1; }
        to { transform: translateX(100%); opacity: 0; }
    }

    /* =================================
       EMPTY STATE STYLES
    ================================= */
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

    .no-tickets h3 {
        margin: 0 0 0.5rem 0;
        color: #374151;
        font-size: 1.25rem;
        font-weight: 600;
    }

    .no-tickets p {
        margin: 0;
        font-size: 0.875rem;
    }

    /* =================================
       RESPONSIVE STYLES
    ================================= */
    @media (max-width: 1024px) {
        .controls-grid {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
        .export-actions {
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
            grid-template-columns: repeat(2, 1fr);
            gap: 1rem;
        }
        
        .filters-row {
            flex-direction: column;
            align-items: stretch;
        }
        
        .search-input, .filter-select {
            width: 100%;
            min-width: unset;
        }
        
        .tickets-table {
            min-width: 900px;
        }
        
        .tickets-table th, .tickets-table td {
            padding: 0.75rem 0.5rem;
        }
        
        .user-info {
            flex-direction: column;
            text-align: center;
            min-width: 120px;
        }
        
        .action-buttons {
            flex-direction: column;
        }
    }

    @media (max-width: 480px) {
        .tickets-container {
            border-radius: 8px;
            margin: 0.5rem;
        }
        
        .tickets-header {
            padding: 1rem;
        }
        
        .tickets-controls {
            padding: 1rem;
        }
        
        .tickets-stats {
            grid-template-columns: 1fr;
            gap: 0.75rem;
        }
        
        .stat-item {
            padding: 0.75rem;
        }
    }
</style>

<div class="tickets-container">
    <!-- Flash Messages -->
    @if (session('success'))
        <div class="flash-message flash-success">
            {{ session('success') }}
        </div>
    @endif
    @if (session('error'))
        <div class="flash-message flash-error">
            {{ session('error') }}
        </div>
    @endif

    <!-- Header Section -->
    <div class="tickets-header">
        <h1>
            <i class="fa-solid fa-ticket"></i>
            Tickets Management
        </h1>
        <div class="tickets-stats">
            <div class="stat-item">
                <div class="stat-number" id="totalTickets">{{ $tickets->count() }}</div>
                <div class="stat-label">Total Tickets</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="confirmedTickets">{{ $tickets->where('status', 'confirmed')->count() }}</div>
                <div class="stat-label">Confirmed</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="usedTickets">{{ $tickets->where('status', 'used')->count() }}</div>
                <div class="stat-label">Used</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="totalQuantity">{{ $tickets->sum(function($ticket) { 
                    return collect(json_decode($ticket->ticket_details, true))->sum('quantity'); 
                }) }}</div>
                <div class="stat-label">Total Quantity</div>
            </div>
            <div class="stat-item">
                <div class="stat-number" id="totalRevenue">NPR {{ $totalSumNpr ?? '0.00' }}</div>
                <div class="stat-label">Total Revenue (NPR)</div>
            </div>
        </div>
    </div>

    <!-- Controls Section -->
    <div class="tickets-controls">
        <div class="controls-grid">
            <div class="filters-section">
                <div class="filters-row">
                    <span class="filter-label">Search:</span>
                    <input type="text" class="search-input" placeholder="Search tickets, users, events..." id="searchInput">
                    <span class="filter-label">Status:</span>
                    <select class="filter-select" id="statusFilter">
                        <option value="">All Status</option>
                        <option value="confirmed">Confirmed</option>
                        <option value="pending">Pending</option>
                        <option value="cancelled">Cancelled</option>
                        <option value="used">Used</option>
                    </select>
                </div>
                <div class="filters-row">
                    <span class="filter-label">Event:</span>
                    <select class="filter-select" id="eventFilter">
                        <option value="">All Events</option>
                        @foreach($tickets->unique('event_id') as $ticket)
                            <option value="{{ $ticket->event->id }}">{{ $ticket->event->name }}</option>
                        @endforeach
                    </select>
                    <span class="filter-label">Payment:</span>
                    <select class="filter-select" id="paymentFilter">
                        <option value="">All Methods</option>
                        <option value="paypal">PayPal</option>
                        <option value="esewa">Esewa</option>
                        <option value="bank">Bank Transfer</option>
                        <option value="cash">Cash</option>
                    </select>
                </div>
            </div>
            <div class="export-actions">
                <button class="clear-btn" onclick="clearFilters()">
                    <i class="fa-solid fa-filter-circle-xmark"></i> Clear
                </button>
                <button class="export-btn" onclick="exportTickets()">
                    <i class="fa-solid fa-download"></i> Export
                </button>
            </div>
        </div>
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
                        <th>ID</th>
                        <th>User</th>
                        <th>Event</th>
                        <th>Status</th>
                        <th>Payment</th>
                        <th>Categories</th>
                        <th>Quantity</th>
                    </tr>
                </thead>
                <tbody id="ticketsTableBody">
                    @foreach($tickets as $ticket)
                        @php
                            $ticketDetails = json_decode($ticket->ticket_details, true) ?? [];
                            $totalQuantity = collect($ticketDetails)->sum('quantity');
                            $payment = collect($ticket->payments)->first();
                        @endphp
                        <tr data-ticket-id="{{ $ticket->id }}"
                            data-status="{{ $ticket->status }}"
                            data-event-id="{{ $ticket->event_id }}"
                            data-payment-method="{{ $payment['payment_method'] ?? '' }}"
                            data-search-text="{{ strtolower($ticket->user->name . ' ' . $ticket->user->email . ' ' . $ticket->event->name . ' #' . $ticket->id) }}">
                            <td><span class="ticket-id">#{{ $ticket->id }}</span></td>
                            <td>
                                <div class="user-info">
                                    <div class="user-avatar">{{ strtoupper(substr($ticket->user->name, 0, 1)) }}</div>
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
                                        {{ \Carbon\Carbon::parse($ticket->event->start_date)->format('M d, Y') }}
                                    </div>
                                </div>
                            </td>
                         <td>
                                    <div class="status-container">
                                        @if ($ticket->status === 'used')
                                            <span title="Update not available" class="status-badge status-{{ strtolower($ticket->status) }}">
                                                <i class="fa-solid fa-ticket-simple"></i>
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        @else
                                            <span title="Click to update" class="status-badge status-{{ strtolower($ticket->status) }}" onclick="toggleStatusDropdown({{ $ticket->id }})">
                                                @switch(strtolower($ticket->status))
                                                    @case('confirmed') <i class="fa-solid fa-check-circle"></i> @break
                                                    @case('pending') <i class="fa-solid fa-clock"></i> @break
                                                    @case('cancelled') <i class="fa-solid fa-times-circle"></i> @break
                                                @endswitch
                                                {{ ucfirst($ticket->status) }}
                                            </span>
                                        @endif
                                        
                                        <div style="width:max-content" class="status-dropdown" id="statusDropdown_{{ $ticket->id }}">
                                            <div class="status-option confirmed" onclick="updateTicketStatus({{ $ticket->id }}, 'confirmed')">
                                                <i class="fa-solid fa-check-circle"></i> Confirmed
                                            </div>
                                            <div class="status-option pending" onclick="updateTicketStatus({{ $ticket->id }}, 'pending')">
                                                <i class="fa-solid fa-clock"></i> Pending
                                            </div>
                                            <div class="status-option cancelled" onclick="updateTicketStatus({{ $ticket->id }}, 'cancelled')">
                                                <i class="fa-solid fa-times-circle"></i> Cancelled
                                            </div>
                                        </div>
                                    </div>
                                </td>

                            <td>
                                <div class="payment-info">
                                    @if($payment)
                                        <div class="payment-method payment-{{ $payment['payment_method'] }}">
                                            @switch($payment['payment_method'])
                                                @case('paypal') <i class="fa-brands fa-paypal"></i> @break
                                                @case('esewa') <i class="fa-solid fa-money-bills"></i> @break
                                                @case('bank') <i class="fa-solid fa-university"></i> @break
                                                @case('cash') <i class="fa-solid fa-money-bills"></i> @break
                                                @default <i class="fa-solid fa-credit-card"></i>
                                            @endswitch
                                            {{ ucfirst($payment['payment_method']) }}
                                        </div>
                                        <div class="payment-amount">
                                            @php
                                                $amount = $payment->amount ?? 0;
                                                if (($payment['payment_method'] ?? '') === 'paypal') {
                                                    $amount = $amount * 133;
                                                }
                                            @endphp
                                            <span>NPR {{ number_format($amount, 2) }}</span>
                                        </div>
                                    @else
                                        <span style="color: #6b7280; font-style: italic;">No payment</span>
                                    @endif
                                </div>
                            </td>
                            <td>
                                <div class="category-tags">
                                    @forelse($ticketDetails as $detail)
                                        <span class="category-tag">{{ $detail['category'] ?? 'Unknown' }}</span>
                                    @empty
                                        <span class="category-tag">No Category</span>
                                    @endforelse
                                </div>
                            </td>
                            <td>
                                <div class="quantity-info">
                                    <div class="quantity-number">{{ $totalQuantity }}</div>
                                    <div class="quantity-label">{{ $totalQuantity == 1 ? 'ticket' : 'tickets' }}</div>
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
</div>

<script>
(function() {
    const USD_TO_NPR_RATE = 133;

    // Status update functionality
    function updateTicketStatus(ticketId, newStatus) {
        showLoading(true);
        
        // Create form data
        const formData = new FormData();
        formData.append('status', newStatus);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '');
        formData.append('_method', 'PUT');

        // Send AJAX request
        fetch(`/admin/tickets/${ticketId}/status`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            showLoading(false);
            if (data.success) {
                // Update the status badge
                const statusBadge = document.querySelector(`tr[data-ticket-id="${ticketId}"] .status-badge`);
                const statusIcon = getStatusIcon(newStatus);
                
                // Update badge classes and content
                statusBadge.className = `status-badge status-${newStatus}`;
                statusBadge.innerHTML = `${statusIcon} ${capitalizeFirst(newStatus)}`;
                
                // Update row data attribute
                const row = document.querySelector(`tr[data-ticket-id="${ticketId}"]`);
                row.setAttribute('data-status', newStatus);
                
                // Hide dropdown
                hideAllStatusDropdowns();
                
                // Update stats
                updateStatsAfterStatusChange();
                
                // Show success notification
                showNotification(`Ticket #${ticketId} status updated to ${newStatus}`, 'success');
            } else {
                showNotification(data.message || 'Failed to update ticket status', 'error');
            }
        })
        .catch(error => {
            showLoading(false);
            console.error('Error:', error);
            showNotification('An error occurred while updating the ticket status', 'error');
        });
    }

    function getStatusIcon(status) {
        const icons = {
            'confirmed': '<i class="fa-solid fa-check-circle"></i>',
            'pending': '<i class="fa-solid fa-clock"></i>',
            'cancelled': '<i class="fa-solid fa-times-circle"></i>',
            'used': '<i class="fa-solid fa-ticket-simple"></i>'
        };
        return icons[status] || '<i class="fa-solid fa-circle"></i>';
    }

    function capitalizeFirst(str) {
        return str.charAt(0).toUpperCase() + str.slice(1);
    }

    function toggleStatusDropdown(ticketId) {
        // Hide all other dropdowns first
        hideAllStatusDropdowns();
        
        // Toggle the clicked dropdown
        const dropdown = document.getElementById(`statusDropdown_${ticketId}`);
        dropdown.classList.toggle('show');
        
        // Add event listener to close dropdown when clicking outside
        document.addEventListener('click', function closeDropdown(e) {
            if (!e.target.closest('.status-container')) {
                hideAllStatusDropdowns();
                document.removeEventListener('click', closeDropdown);
            }
        });
    }

    function hideAllStatusDropdowns() {
        document.querySelectorAll('.status-dropdown').forEach(dropdown => {
            dropdown.classList.remove('show');
        });
    }

    function updateStatsAfterStatusChange() {
        const rows = document.querySelectorAll('#ticketsTableBody tr:not([style*="none"])');
        let stats = { total: 0, confirmed: 0, used: 0, pending: 0, cancelled: 0 };

        rows.forEach(row => {
            const status = row.dataset.status;
            stats.total++;
            if (stats[status] !== undefined) {
                stats[status]++;
            }
        });

        document.getElementById('totalTickets').textContent = stats.total;
        document.getElementById('confirmedTickets').textContent = stats.confirmed;
        document.getElementById('usedTickets').textContent = stats.used;
    }

    // Filter functionality
    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value;
        const eventFilter = document.getElementById('eventFilter').value;
        const paymentFilter = document.getElementById('paymentFilter').value;
        const rows = document.querySelectorAll('#ticketsTableBody tr');
        
        let visibleCount = 0;
        let stats = { total: 0, confirmed: 0, used: 0, quantity: 0, revenue: 0 };

        rows.forEach(row => {
            const searchText = row.dataset.searchText;
            const status = row.dataset.status;
            const eventId = row.dataset.eventId;
            const paymentMethod = row.dataset.paymentMethod;

            const matchesSearch = !searchTerm || searchText.includes(searchTerm);
            const matchesStatus = !statusFilter || status === statusFilter;
            const matchesEvent = !eventFilter || eventId === eventFilter;
            const matchesPayment = !paymentFilter || paymentMethod === paymentFilter;

            if (matchesSearch && matchesStatus && matchesEvent && matchesPayment) {
                row.style.display = '';
                visibleCount++;
                
                stats.total++;
                if (status === 'confirmed') stats.confirmed++;
                if (status === 'used') stats.used++;
                
                const quantityElement = row.querySelector('.quantity-number');
                if (quantityElement) {
                    stats.quantity += parseInt(quantityElement.textContent) || 0;
                }
                
                // Calculate revenue from NPR amount displayed
                const amountElement = row.querySelector('.payment-amount span');
                if (amountElement) {
                    const nprAmount = parseFloat(amountElement.textContent.replace('NPR ', '').replace(',', '')) || 0;
                    stats.revenue += nprAmount;
                }
            } else {
                row.style.display = 'none';
            }
        });

        document.getElementById('totalTickets').textContent = stats.total;
        document.getElementById('confirmedTickets').textContent = stats.confirmed;
        document.getElementById('usedTickets').textContent = stats.used;
        document.getElementById('totalQuantity').textContent = stats.quantity;
        document.getElementById('totalRevenue').textContent = `NPR ${stats.revenue.toLocaleString('en-US', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;

        const noTicketsElement = document.querySelector('.no-tickets');
        const tableElement = document.querySelector('.tickets-table');
        if (noTicketsElement && tableElement) {
            noTicketsElement.style.display = visibleCount === 0 ? 'block' : 'none';
            tableElement.style.display = visibleCount === 0 ? 'none' : 'table';
        }
    }

    function clearFilters() {
        document.getElementById('searchInput').value = '';
        document.getElementById('statusFilter').value = '';
        document.getElementById('eventFilter').value = '';
        document.getElementById('paymentFilter').value = '';
        applyFilters();
    }

    function debounce(func, wait) {
        let timeout;
        return function executedFunction(...args) {
            const later = () => {
                clearTimeout(timeout);
                func(...args);
            };
            clearTimeout(timeout);
            timeout = setTimeout(later, wait);
        };
    }

    function viewTicket(ticketId) {
        window.location.href = `/admin/tickets/${ticketId}`;
    }

    function editTicket(ticketId) {
        window.location.href = `/admin/tickets/${ticketId}/edit`;
    }

    function exportTickets() {
        showLoading(true);
        const visibleRows = document.querySelectorAll('#ticketsTableBody tr:not([style*="none"])');
        
        if (visibleRows.length === 0) {
            showLoading(false);
            showNotification('No tickets to export with current filters', 'error');
            return;
        }

        let csvContent = "data:text/csv;charset=utf-8,";
        csvContent += "Ticket ID,User Name,User Email,Event Name,Event Date,Status,Payment Method,Payment Amount (NPR),Categories,Quantity\n";

        visibleRows.forEach(row => {
            const ticketId = row.querySelector('.ticket-id')?.textContent || 'N/A';
            const userName = row.querySelector('.user-details h4')?.textContent || 'N/A';
            const userEmail = row.querySelector('.user-details p')?.textContent || 'N/A';
            const eventName = row.querySelector('.event-name')?.textContent || 'N/A';
            const eventDate = row.querySelector('.event-date')?.textContent?.replace(/.*\s/, '').trim() || 'N/A';
            const status = row.querySelector('.status-badge')?.textContent?.trim() || 'N/A';
            const paymentMethod = row.querySelector('.payment-method')?.textContent?.trim() || 'N/A';
            const paymentAmount = row.querySelector('.payment-amount span')?.textContent?.trim() || 'NPR 0.00';
            const categories = Array.from(row.querySelectorAll('.category-tag')).map(tag => tag.textContent.trim()).join('; ') || 'N/A';
            const quantity = row.querySelector('.quantity-number')?.textContent || '0';

            const rowData = [
                `"${ticketId}"`,
                `"${userName}"`,
                `"${userEmail}"`,
                `"${eventName}"`,
                `"${eventDate}"`,
                `"${status}"`,
                `"${paymentMethod}"`,
                `"${paymentAmount}"`,
                `"${categories}"`,
                `"${quantity}"`
            ];
            csvContent += rowData.join(",") + "\n";
        });

        setTimeout(() => {
            const encodedUri = encodeURI(csvContent);
            const link = document.createElement("a");
            link.setAttribute("href", encodedUri);
            link.setAttribute("download", `tickets_export_${new Date().toISOString().split('T')[0]}.csv`);
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            showLoading(false);
            showNotification(`Exported ${visibleRows.length} tickets successfully!`, 'success');
        }, 1000);
    }

    function showLoading(show) {
        document.getElementById('loadingOverlay').style.display = show ? 'flex' : 'none';
    }

    function showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `flash-message flash-${type}`;
        notification.textContent = message;
        document.body.appendChild(notification);
        
        setTimeout(() => {
            notification.style.animation = 'slideOut 0.3s ease';
            setTimeout(() => notification.remove(), 300);
        }, 4000);
    }

    // Event Listeners
    document.getElementById('searchInput').addEventListener('input', debounce(applyFilters, 300));
    document.getElementById('statusFilter').addEventListener('change', applyFilters);
    document.getElementById('eventFilter').addEventListener('change', applyFilters);
    document.getElementById('paymentFilter').addEventListener('change', applyFilters);
    
    // Initialize filters on page load
    document.addEventListener('DOMContentLoaded', function() {
        applyFilters();
        
        // Auto-hide flash messages
        const flashMessages = document.querySelectorAll('.flash-message');
        flashMessages.forEach(message => {
            setTimeout(() => {
                message.style.animation = 'slideOut 0.3s ease';
                setTimeout(() => message.remove(), 300);
            }, 4000);
        });
    });

    // Make functions globally available
    window.clearFilters = clearFilters;
    window.exportTickets = exportTickets;
    window.viewTicket = viewTicket;
    window.editTicket = editTicket;
    window.updateTicketStatus = updateTicketStatus;
    window.toggleStatusDropdown = toggleStatusDropdown;
})();
</script>
@endsection