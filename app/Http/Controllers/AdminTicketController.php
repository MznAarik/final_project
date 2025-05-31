<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AdminTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.tickets.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {

        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $tickets = new Ticket();
            $tickets->event_id = $request->event_id;
            $tickets = new User();
            dd($tickets->toArray());
            $tickets->user_id = $request->user_id;
            $tickets->quantity = $request->quantity;
            $tickets->total_price = $request->total_price;
            $tickets->status = 'pending'; // Default status
            $tickets->delete_flag = 0; // Default delete flag
            dd($tickets);
            $tickets->save();

        } catch (\Exception $e) {
            Log::error('Failed to create ticket: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to create ticket: ' . $e->getMessage()]);
        }
        return redirect()->route('admin.tickets.index')->with('success', 'Ticket created successfully.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
