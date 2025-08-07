<?php

namespace App\Http\Controllers;

use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class HomeController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            //Auto updating status and  deleting finished event after 24hours
            Event::where('end_date', '<=', Carbon::now())
                ->where('status', '!=', 'completed')
                ->update(['status' => 'completed']);

            $cutoffTime = Carbon::now()->subHours(24);
            Event::whereDate('end_date', '<=', $cutoffTime)
                ->where('delete_flag', false)
                ->update(['delete_flag' => true]);

            $recommendedEvents = [];

            if (Auth::check()) {
                $user = Auth::user();

                //Auto redirect admin to admin dashboard
                if ($user->role === 'admin') {
                    return redirect()->route('admin.dashboard')->with([
                        'status' => 1,
                        'message' => 'Welcome Admin!'
                    ]);
                }

                $countryId = $user->country_id;
                $provinceId = $user->province_id;
                $districtId = $user->district_id;

                $recommendedEvents = $this->getRecommendedEvents($countryId, $provinceId, $districtId);

                if ($recommendedEvents->isEmpty()) {
                    $recommendedEvents = Event::where('delete_flag', 0)
                        ->where('status', '!=', 'cancelled')
                        ->orderBy('popularity_score', 'desc')
                        ->orderBy('created_at', 'desc')
                        ->take(4)
                        ->get();
                }
            } else {
                $recommendedEvents = Event::where('delete_flag', 0)
                    ->where('status', '!=', 'cancelled')
                    ->orderBy('popularity_score', 'desc')
                    ->orderBy('created_at', 'desc')
                    ->take(4)
                    ->get();
            }
            return view('home', [
                'recommendedEvents' => $recommendedEvents,
                'title' => '- Welcome to Ticket Booking System -',
                'sectionType' => 'recommended'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching recommended events: ' . $e->getMessage());
            return view('home', ['recommendedEvents' => []])
                ->with(['error' => 'No recommended events available. Please stay tuned.']);
        }
    }

    /**
     * Fetch recommended events based on user location.
     */
    private function getRecommendedEvents($countryId, $provinceId, $districtId)
    {
        $exactMatch = Event::where('country_id', $countryId)
            ->where('province_id', $provinceId)
            ->where('district_id', $districtId)
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $partialMatch = Event::where('country_id', $countryId)
            ->where('province_id', $provinceId)
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $broadMatch = Event::where('country_id', $countryId)
            ->whereNotIn('id', $exactMatch->pluck('id')->merge($partialMatch->pluck('id')))
            ->where('status', '!=', 'cancelled')
            ->where('status', '!=', 'completed')
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(2)
            ->get();

        return $exactMatch
            ->merge($partialMatch)
            ->merge($broadMatch)
            ->take(8);
    }

    public function showAllEvents()
    {
        try {
            $events = Event::orderByDesc('created_at')->paginate(10);
            return view('home', [
                'recommendedEvents' => $events,
                'sectionType' => 'all',
                'title' => 'All Events - Discover What’s Happening'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching events: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch events at this time.');
        }
    }

    public function showUpcomingEvents()
    {
        try {
            $upcomingEvents = Event::where('status', 'upcoming')
                ->where('delete_flag', '!=', true)
                ->where('status', '!=', 'cancelled')
                ->orderBy('start_date', 'asc')
                ->paginate(10);

            return view('home', [
                'recommendedEvents' => $upcomingEvents,
                'sectionType' => 'upcoming',
                'title' => 'Upcoming Events - Book Your Spot Early'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching upcoming events: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch upcoming events at this time.');
        }
    }

    public function showPopularEvents()
    {
        try {
            $popularEvents = Event::where('status', '!=', 'cancelled')
                ->where('delete_flag', '!=', true)
                ->orderBy('popularity_score', 'desc')
                ->paginate(10);
            return view('home', [
                'recommendedEvents' => $popularEvents,
                'sectionType' => 'popular',
                'title' => 'Popular Events - Join the Crowd'
            ]);

        } catch (\Exception $e) {
            Log::error('Error fetching popular events: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Unable to fetch popular events at this time.');
        }
    }

}
