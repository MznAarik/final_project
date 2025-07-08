<?php

namespace App\Http\Controllers;

use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminTicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        try {
            $ticket = Ticket::where('delete_flag', 0);
            dd($ticket->get());
        } catch (\Exception $e) {
            Log::error('Failed to fetch ticket: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to fetch ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('admin.ticket.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
            $ticket = new Ticket();
            $ticket->event_id = $request->event_id;
            $ticket->name = $request->name;
            $ticket->category = $request->category;
            dd($ticket->toArray());
            $ticket = new User();
            $ticket->user_id = $request->user_id;
            $ticket->quantity = $request->quantity;
            $ticket->total_price = $request->total_price;
            $ticket->status = 'pending'; // Default status
            $ticket->delete_flag = 0; // Default delete flag
            dd($ticket);
            $ticket->save();

        } catch (\Exception $e) {
            Log::error('Failed to create ticket: ' . $e->getMessage());
            return redirect()->back()->withErrors(['error' => 'Failed to create ticket: ' . $e->getMessage()]);
        }
        return redirect()->route('admin.ticket.index')->with('success', 'Ticket created successfully.');
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

    public function OpenDecryptQrCode(Request $request)
    {

    }
}
