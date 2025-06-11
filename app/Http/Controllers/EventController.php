<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventsValidate;
use App\Models\Country;
use App\Models\Event;
use App\Models\Province;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $events = Event::all();
        $ticket_pricing = Event::select('ticket_pricing')->first();
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

            $country = Country::firstOrCreate(['name' => $request->input(strtolower('country_name'))]);
            $province = $country->provinces()->firstOrCreate(
                ['name' => $request->input(strtolower('province_name')), 'country_id' => $country->id]
            );

            $district = $province->districts()->firstOrCreate(
                ['name' => $request->input(strtolower('district_name'))]
            );

            $events = new Event();
            $events->name = strtolower($request['name']);
            $events->venue = strtolower($request['venue']);
            $events->location = strtolower($request['location']);
            $events->district_id = $district->id;  // Should be district_id if FK
            $events->province_id = $province->id;  // Same note
            $events->country_id = $country->id;    // Same note
            $events->capacity = $request['capacity'];
            $events->description = $request['description'];
            $events->contact_info = $request['contact_info'];
            $events->start_date = $request['start_date'];
            $events->end_date = $request['end_date'];
            $events->category = $request['category'];
            $events->status = $request['status'];
            $events->organizer = $request['organizer'];
            $events->tickets_sold = $request['tickets_sold'];
            $events->currency = $request['currency'];
            $events->created_by = Auth::id();
            $events->updated_by = Auth::id();

            $ticket_category = $request['ticket_category'];
            $ticket_price = $request['ticket_price'];
            $pricing = [];
            foreach ($ticket_category as $index => $category) {
                $pricing[] = [
                    'category' => $category,
                    'price' => $ticket_price[$index] ?? 0
                ];
            }
            $events->ticket_pricing = json_encode($pricing);

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
                'message' => 'Error Occured! Please try again',
                'error' => $e->getMessage(),
            ]);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.showEvents', compact('event'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $event = Event::findOrFail($id);
        return view('admin.events.createEvents', compact('event'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(EventsValidate $request, string $id)
    {
        try {
            $event = Event::findOrFail($id);
            $event->update($request->validated());

            return redirect()->route('events.index')->with([
                'status' => 1,
                'message' => 'Event updated successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Event update failed: ' . $e->getMessage());

            return redirect()->route('events.index')->with([
                'status' => 0,
                'message' => 'Error Occured! Please try again',
                'error' => $e->getMessage(),
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
            // $event->delete();
            $event->update(['delete_flag' => 1]);
            return redirect()->route('events.index')->with([
                'status' => 1,
                'message' => 'Event deleted successfully',
            ]);
        } catch (\Exception $e) {
            Log::error('Event deletion failed: ' . $e->getMessage());
            return redirect()->route('events.index')->with([
                'status' => 0,
                'message' => 'Error Occured! Please try again',
                'error' => $e->getMessage(),
            ]);
        }
    }
}
