<?php

namespace App\Http\Controllers;
use App\Models\Province;
use App\Models\District;

use App\Http\Requests\EventsValidate;
use App\Models\Country;
use App\Models\Event;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{

    public function index()
    {
        $events = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->get();
        return view('admin.events.index', compact('events'));
    }
    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.events.createEvents');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(EventsValidate $request)
    {
        DB::beginTransaction();

        try {
            $country = Country::firstOrCreate(['name' => strtolower($request->input('country_name'))]);

            $province = $country->provinces()->firstOrCreate(
                ['name' => strtolower($request->input('province_name')), 'country_id' => $country->id]
            );

            $district = $province->districts()->firstOrCreate(
                ['name' => strtolower($request->input('district_name'))]
            );

            $events = new Event();
            $events->name = strtolower($request['name']);
            $events->venue = strtolower($request['venue']);
            $events->location = strtolower($request['location']);
            $events->event_category = strtolower($request['event_category']);
            $ticketCategoryPrice = $request['ticket_category_price'];
            foreach ($ticketCategoryPrice as &$category) {
                if (isset($category['category'])) {
                    $category['category'] = strtolower($category['category']);
                }
            }
            $events->ticket_category_price = json_encode($ticketCategoryPrice);
            $events->district_id = $district->id;
            $events->province_id = $province->id;
            $events->country_id = $country->id;
            $events->capacity = $request['capacity'];
            $events->description = $request['description'];
            $events->contact_info = $request['contact_info'];
            $events->start_date = $request['start_date'];
            $events->end_date = $request['end_date'];
            $events->status = $request['status'] ?? 'upcoming';
            $events->organizer = $request['organizer'];
            $events->tickets_sold = 0;
            $events->currency = strtolower($request['currency']);
            $events->created_by = Auth::id();
            $events->updated_by = Auth::id();

            if ($request->hasFile('image')) {
                $file = $request->file('image');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $img_path = $file->storeAs('images/events', $imageName, 'public');
                $events->img_path = $img_path;
            }
            $events->save();

            DB::commit();

            return redirect()->route('events.index')->with([
                'status' => 1,
                'message' => 'Event created successfully',
            ]);

        } catch (\Exception $e) {

            DB::rollBack();
            Log::error('Event creation failed: ' . $e->getMessage());
            return redirect()->route('events.index')->with([
                'status' => 0,
                'error' => $e->getMessage(),
            ]);

        }
    }

    /**
     * Display the specified resource.
     */
    // {
    //     $event = Event::where('id', $id)
    //         ->where('delete_flag', 0)
    //         ->where('status', '!=', 'cancelled')
    //         ->where('status', '!=', 'cancelled')
    //         ->firstOrFail();
    //     if (!$event) {
    //         return redirect()->route('events.index')->with([
    //             'status' => 0,
    //             'message' => 'Event not found or has been cancelled.',
    //         ]);
    //     }
    //     return view('components.preview', compact('event'));
    // }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.updateEvents', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */

    public function update(EventsValidate $request, $id)
    {
        try {
            $event = Event::findOrFail($id);
            $data = $request->validated();
            if ($request->hasFile('img_path')) {
                // Delete old image if it exists
                if ($event->img_path && Storage::disk('public')->exists($event->img_path)) {
                    Storage::disk('public')->delete($event->img_path);
                }

                // Store new image
                $file = $request->file('img_path');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $imgPath = $file->storeAs('images/events', $imageName, 'public');
                $data['img_path'] = $imgPath;
            }
            // Update the event
            $event->update($data);

            return redirect()->route('events.index')->with([
                'status' => 1,
                'message' => 'Event updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage());

            return redirect()->route('events.index')->with([
                'status' => 0,
                'error' => 'Failed to update event. ' . $e->getMessage(),
            ]);
        }
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        try {
            $event = Event::findOrFail($id);

            $event->delete_flag = true;
            $event->save();

            return response()->json([
                'status' => 1,
                'message' => 'Event deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error("Event deletion failed for ID {$id}: " . $e->getMessage());

            return response()->json([
                'status' => 0,
                'error' => 'Something went wrong while deleting the event.',
            ], 500);
        }
    }
    public function search(Request $request)
    {
        $query = $request->input('query');

        $events = Event::where('name', 'ILIKE', '%' . $query . '%')
            ->orWhere('location', 'ILIKE', '%' . $query . '%')
            ->orWhere('venue', 'ILIKE', '%' . $query . '%')
            ->orWhere('description', 'ILIKE', '%' . $query . '%')
            ->orWhere('contact_info', 'ILIKE', '%' . $query . '%')
            ->get();

        return view('search_results', compact('events', 'query'));
    }
    public function suggestions(Request $request)
    {
        $query = $request->query('q');

        $suggestions = Event::where('name', 'ILIKE', '%' . $query . '%')
            ->select('name')
            ->limit(10)
            ->get();

        return response()->json($suggestions);
    }

}
