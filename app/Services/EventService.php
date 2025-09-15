<?php

namespace App\Services;

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class EventService
{
    /**
     * Update event statuses based on start and end dates.
     */
    public function updateEventStatuses(): void
    {
        $now = now();

        // Active events (currently happening)
        Event::where('start_date', '<=', $now)
            ->where('end_date', '>=', $now)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'active')
            ->update(['status' => 'active']);
            
            // Completed events
            Event::where('end_date', '<', $now)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);
            
            // Upcoming events
            Event::where('start_date', '>', $now)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'upcoming')
            ->update(['status' => 'upcoming']);
    }

    /**
     * Fetch active/upcoming events for home page (users and admin)
     */
    public function getActiveEvents(?User $user = null, int $limit = 10)
    {
        $now = now();

        $query = Event::where('delete_flag', 0)
            ->where(function ($q) use ($now) {
                $q->where('start_date', '>', $now) // upcoming
                    ->orWhere(function ($q2) use ($now) { // active
                        $q2->where('start_date', '<=', $now)
                            ->where('end_date', '>=', $now);
                    });
            });

        if ($user) {
            $query->where(function ($q) use ($user) {
                $q->where('country_id', $user->country_id)
                    ->orWhere('province_id', $user->province_id)
                    ->orWhere('district_id', $user->district_id);
            });
        }

        return $query->orderBy('start_date', 'asc')
            ->orderBy('popularity_score', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Fetch all events for admin dashboard
     */
    public function getAllEventsForAdmin(int $limit = 20)
    {
        return Event::where('delete_flag', 0)
            ->orderBy('start_date', 'asc')
            ->take($limit)
            ->get();
    }

    /**
     * Prepare dashboard stats for admin
     */
    public function getDashboardStats()
    {
        $ticketData = Ticket::select(DB::raw('event_id, SUM(total_price) as total_price'))
            ->where('status', '!=', 'cancelled')
            ->where('delete_flag', false)
            ->groupBy('event_id')
            ->get();

        $eventIds = $ticketData->pluck('event_id')->toArray();

        $now = now();
        $events = Event::whereIn('id', $eventIds)
            ->where('end_date', '>=', $now) // active/upcoming events
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $ticketRevenue = $events->mapWithKeys(function ($event) use ($ticketData) {
            $total = $ticketData->firstWhere('event_id', $event->id)->total_price ?? 0;
            return [$event->id => $total];
        });

        $activeUsers = User::where('delete_flag', false)->count();

        return compact('ticketRevenue', 'events', 'activeUsers');
    }

    /**
     * Check if tickets are available for a given event
     */
    public function isTicketAvailable(Event $event): bool
    {
        return in_array($event->status, ['upcoming', 'active']);
    }
}
