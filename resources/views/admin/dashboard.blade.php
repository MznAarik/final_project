@extends('admin.layouts.app')

@section('content')
    <style>
        .admin-dashboard {
            padding: 1rem;
        }

        .dashboard-header {
            margin-bottom: 2rem;
        }

        .dashboard-header h2 {
            font-size: 0.875rem;
            color: #71717a;
            margin-bottom: 0.5rem;
        }

        .dashboard-header h1 {
            font-size: 1.875rem;
            font-weight: 700;
            color: #e11d48;
            text-decoration: underline;
            margin: 0;
        }

        .overall-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 1.5rem;
            margin-bottom: 2rem;
        }

        .stat-item {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            text-align: left;
            transition: transform 0.2s ease;
        }

        .stat-item:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 15px -3px rgba(0, 0, 0, 0.1);
        }

        .stat-item h2 {
            font-size: 0.875rem;
            color: #6b7280;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .stat-item p {
            font-size: 1.5rem;
            font-weight: 700;
            color: #e11d48;
            margin: 0;
        }

        .charts-container {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: 2rem;
            margin-bottom: 2rem;
        }

        .chart-wrapper {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .chart-wrapper h3 {
            font-size: 1.125rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
            text-align: center;
        }

        .chart-wrapper canvas {
            max-height: 300px;
        }

        .latest-events {
            background: white;
            padding: 1.5rem;
            border-radius: 12px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        .latest-events h2 {
            font-size: 1.25rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 1rem;
        }

        .event-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 1rem;
            overflow: hidden;
        }

        .event-card {
            background: #f9fafb;
            padding: 1rem;
            border-radius: 8px;
            border-left: 4px solid #e11d48;
            transition: all 0.2s ease;
        }

        .event-card:hover {
            background: #f3f4f6;
            transform: translateX(4px);
        }

        .event-card h3 {
            font-size: 1rem;
            font-weight: 600;
            color: #374151;
            margin-bottom: 0.5rem;
        }

        .event-card .event-date {
            font-size: 0.875rem;
            color: #6b7280;
            margin: 0;
        }

        .event-card .event-stats {
            margin-top: 0.5rem;
            display: flex;
            gap: 1rem;
            font-size: 0.75rem;
            color: #6b7280;
        }

        .no-events {
            text-align: center;
            padding: 2rem;
            color: #6b7280;
            font-style: italic;
        }

        /* Responsive Design */
        @media (max-width: 1024px) {
            .charts-container {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .overall-details {
                grid-template-columns: 1fr;
            }

            .admin-dashboard {
                padding: 0.5rem;
            }

            .stat-item {
                padding: 1rem;
            }

            .chart-wrapper {
                padding: 1rem;
            }
        }
    </style>

    <div class="admin-dashboard">
        <!-- Dashboard Header -->
        <div class="dashboard-header">
            <h2>Welcome Back!</h2>
            <h1>{{ Auth::user()->name }}</h1>
        </div>

        <!-- Statistics Cards -->
        <div class="overall-details">
            <div class="stat-item">
                <h2>Net Sales</h2>
                <p>NPR {{ number_format($ticketRevenue->sum() ?? 0, 2) }}</p>
            </div>
            <div class="stat-item">
                <h2>Tickets Sold</h2>
                <p>{{ isset($events) ? $events->sum('tickets_sold') : 0 }}</p>
            </div>
            <div class="stat-item">
                <h2>Active Users</h2>
                <p>{{ $activeUsers ?? 0 }}</p>
            </div>
            <div class="stat-item">
                <h2>Total Events</h2>
                <p>{{ isset($events) ? $events->count() : 0 }}</p>
            </div>
        </div>

        <!-- Charts Section -->
        <div class="charts-container">
            <div class="chart-wrapper">
                <h3>Revenue per Event</h3>
                <canvas id="myLineChart"></canvas>
            </div>
            <div class="chart-wrapper">
                <h3>Event Distribution</h3>
                <canvas id="myPieChart"></canvas>
            </div>
        </div>

        <!-- Latest Events Section -->
        <div class="latest-events">
            <h2>Latest Events</h2>
            <div class="event-cards">
                @if (isset($events) && $events->isNotEmpty())
                    @foreach ($events->take(5) as $event)
                        <div class="event-card">
                            <h3>{{ $event->name }}</h3>

                            @php
                                $startDate = \Carbon\Carbon::now();
                                $eventDate = \Carbon\Carbon::parse($event->start_date);
                                $diff = $startDate->diff($eventDate);
                            @endphp

                            <p class="event-date">
                                <i class="fa-regular fa-calendar"></i>
                                Starts in
                                @if($diff->m > 0)
                                    {{ $diff->m }} month{{ $diff->m > 1 ? 's' : '' }}
                                @endif
                                @if($diff->d > 0)
                                    {{ $diff->m > 0 ? ' and ' : '' }}{{ $diff->d }} day{{ $diff->d > 1 ? 's' : '' }}
                                @endif
                            </p>

                            <div class="event-stats">
                                <span>
                                    <i class="fa-solid fa-ticket"></i>
                                    {{ $event->tickets_sold ?? 0 }} sold
                                </span>
                                <span>
                                    <i class="fa-solid fa-location-dot"></i>
                                    {{ $event->venue ?? 'TBA' }}
                                </span>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="no-events">
                        <i class="fa-regular fa-calendar-xmark" style="font-size: 3rem; margin-bottom: 1rem; opacity: 0.3;"></i>
                        <p>No events found</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const events = @json($events ?? collect());
            const ticketRevenue = @json($ticketRevenue ?? collect());
            const eventLabels = events.map(event => {
                const name = event.name || 'Unnamed Event';
                return name.length > 15 ? name.substring(0, 15) + '...' : name;
            });
            const ticketsSold = events.map(event => parseInt(event.tickets_sold) || 0);

            // Map revenue to event IDs
            const revenueData = events.map(event => ticketRevenue[event.id] || 0);

            // PIE CHART - Event Distribution (Tickets Sold)
            const pieCtx = document.getElementById('myPieChart').getContext('2d');
            new Chart(pieCtx, {
                type: 'doughnut',
                data: {
                    labels: eventLabels,
                    datasets: [{
                        data: ticketsSold,
                        backgroundColor: ['#f59e0b', '#8b5cf6', '#14b8a6', '#f43f5e', '#6366f1'],
                        borderWidth: 3,
                        borderColor: '#ffffff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: { padding: 20, usePointStyle: true }
                        },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    const label = context.label || '';
                                    const value = context.parsed;
                                    const total = context.dataset.data.reduce((a, b) => a + b, 0);
                                    const percentage = ((value / total) * 100).toFixed(1);
                                    return `${label}: ${value} (${percentage}%)`;
                                }
                            }
                        }
                    }
                }
            });

            // LINE CHART - Revenue per Event
            const lineCtx = document.getElementById('myLineChart').getContext('2d');
            new Chart(lineCtx, {
                type: 'line',
                data: {
                    labels: eventLabels.length > 0 ? eventLabels : ['No Data'],
                    datasets: [{
                        label: 'Revenue (NPR)',
                        data: revenueData,
                        fill: true,
                        borderColor: '#e11d48',
                        backgroundColor: 'rgba(225, 29, 72, 0.1)',
                        borderWidth: 2,
                        tension: 0.4,
                        pointBackgroundColor: '#e11d48',
                        pointBorderColor: '#ffffff',
                        pointBorderWidth: 2,
                        pointRadius: 5
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: { display: false },
                        tooltip: {
                            callbacks: {
                                label: function (context) {
                                    return `Revenue: NPR ${context.parsed.y.toLocaleString()}`;
                                }
                            }
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            ticks: {
                                callback: function (value) {
                                    return 'NPR ' + value.toLocaleString();
                                }
                            },
                            grid: {
                                color: 'rgba(0, 0, 0, 0.05)'
                            }
                        },
                        x: {
                            grid: { display: false }
                        }
                    }
                }
            });
        });
    </script>
@endsection