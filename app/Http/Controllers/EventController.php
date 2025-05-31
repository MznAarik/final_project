<?php

namespace App\Http\Controllers;

use App\Http\Requests\EventsValidate;
use App\Models\Event;
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
            $events = new Event();
            $events->name = strtolower($request['name']);
            $events->venue = $request['venue'];
            $events->location = $request['location'];
            $events->district = $request['district'];
            $events->province = $request['province'];
            $events->country = $request['country'];
            $events->capacity = $request['capacity'];
            $events->ticket_price = $request['ticket_price'];
            $events->description = $request['description'];
            $events->contact_info = $request['contact_info'];
            $events->start_date = $request['start_date'];
            $events->end_date = $request['end_date'];
            $events->category = $request['category'];
            $events->status = $request['status'];
            $events->organizer = $request['organizer'];

            if ($request->has('image')) {
                $file = $request->file('image');
                $imageName = time() . '.' . $file->getClientOriginalExtension();
                $img_path = $file->storeAs('images/events', $imageName, 'public');
                $events->img_path = $img_path;
            }

            $events->tickets_sold = $request['tickets_sold'];
            $events->currency = $request['currency'];
            $events->created_by = Auth::id();
            $events->updated_by = Auth::id();
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
            $event->delete();
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
