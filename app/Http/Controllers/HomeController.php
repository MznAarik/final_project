<?php

namespace App\Http\Controllers;

use App\Models\Event;
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
            $recommendedEvents = [];

            if (Auth::check()) {
                $user = Auth::user();
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
                'title' => 'Welcome to EvenTickets - Discover What’s Happening Near You',
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
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(6)
            ->get();

        $partialMatch = Event::where('country_id', $countryId)
            ->where('province_id', $provinceId)
            ->whereNotIn('id', $exactMatch->pluck('id'))
            ->orderBy('popularity_score', 'desc')
            ->orderBy('created_at', 'desc')
            ->take(4)
            ->get();

        $broadMatch = Event::where('country_id', $countryId)
            ->whereNotIn('id', $exactMatch->pluck('id')->merge($partialMatch->pluck('id')))
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
            $events = Event::where('delete_flag', 0)
                ->where('status', '!=', 'cancelled')
                ->orderByDesc('created_at')
                ->paginate(10);
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
