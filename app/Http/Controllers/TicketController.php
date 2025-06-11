<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class TicketController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $tickets = Auth::user()->tickets()
            ->where('delete_flag', 0)
            ->where('status', '!=', 'cancelled')->get();
        // dd($tickets->count());
        $totalPrice = Auth::user()->tickets()->sum('total_price');
        $ticketStatus = Auth::user()->tickets()->pluck('status')->all();
        // dd($totalPrice, $ticketStatus);
        return view('user.tickets.index', compact('tickets'));

    }

    /**
     * Store a newly created resource in storage.
     */

    public function generateQrCode($user_name, $ticket_id, $event_id)
    {
        try {
            $sensitiveData = json_encode([
                'user_name' => $user_name,
                'ticket_id' => $ticket_id,
                'event_id' => $event_id,
            ]);
            $encryptedData = Crypt::encryptString($sensitiveData);
            dump($encryptedData); // Debugging line to check the encrypted data
            $decryptedData = Crypt::decryptString($encryptedData);
            dd($decryptedData); // Debugging line to check the decrypted data


            $publicUrl = url('/') . '?data=' . urlencode($encryptedData);
            $qrCodeSvg = QrCode::size(200)->generate($publicUrl);

            $fileName = 'qrcodes/ticket_' . $ticket_id . '_event_' . $event_id . '.svg';
            Storage::disk('public')->put($fileName, $qrCodeSvg);

            $ticket = Ticket::findOrFail($ticket_id);
            Log::info('File path to store: ' . $fileName);
            $ticket->qr_code = $fileName;
            $ticket->save();

            $qrCodeUrl = asset('storage/' . $fileName);
            return $qrCodeUrl;
        } catch (\Exception $e) {
            Log::error('Error generating QR code: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error generating QR code: ' . $e->getMessage(),
            ]);
        }
    }

    public function store(Request $request)
    {
        try {

            $request->validate([
                'event_id' => 'required|exists:events,id',
                'quantity' => 'required|integer|min:1',
            ]);

            $event = Event::findOrFail($request->event_id);
            $totalCapacity = $event->capacity;
            $ticketsSold = $event->tickets()->where('status', '!=', 'cancelled')->sum('quantity');
            $availableTickets = $totalCapacity - $ticketsSold;
            $ticketPrice = $event->ticket_price;
            if ($ticketPrice == 0) {
                return redirect()->back()->with([
                    'status' => 0,
                    'message' => 'Ticket price not set for this event.',
                ]);
            }

            if ($request->quantity <= $availableTickets) {
                $ticket = new Ticket();
                $ticket->user_id = Auth::user()->id;
                $ticket->event_id = $request->event_id;
                $ticket->status = 'pending';
                $ticket->price = $ticketPrice;
                $ticket->quantity = $request->quantity;
                $ticket->total_price = $ticketPrice * $request->quantity;
                $ticket->deadline = now()->addDays(7);
                $ticket->cancellation_reason = null;
                $ticket->created_by = Auth::user()->id;
                $ticket->updated_by = Auth::user()->id;
                $ticket->qr_code = $this->generateQrCode(Auth::user()->name, $ticket->id, $ticket->event_id);
                $ticket->save();

                return view('user.tickets.qr-code', [
                    'ticket' => $ticket,
                ]);
            }
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Sorry! All seats reserved.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error purchasing ticket: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error purchasing ticket: ' . $e->getMessage(),
            ]);
        }
    }



    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        try {

            $userId = Auth::user()->id;
            $ticket = Ticket::with(['user', 'event'])
                ->where('id', $id)
                ->where('user_id', $userId)
                ->where('delete_flag', 0)
                ->where('status', '!=', 'cancelled')
                ->firstOrFail();
        } catch (\Exception $e) {
            return redirect()->route('user.tickets.index')->with([
                'status' => 0,
                'message' => 'Ticket not found.',
            ]);
        }

        return view('user.tickets.show', compact('ticket'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->update([
            'user_id' => Auth::user()->id,
            'event_id' => $request->event_id,
            'status' => 'pending',
            'price' => $request->price,
            'quantity' => $request->quantity,
            'total_price' => $request->price * $request->quantity,
            'deadline' => now()->addDays(7),
            'cancellation_reason' => null,
            'qr_code' => null,
            'updated_by' => Auth::user()->id,
        ]);
        $ticket->save();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->delete_flag = 1;
        $ticket->save();
        return redirect()->route('user.tickets.index')->with([
            'status' => 1,
            'message' => 'Ticket deleted successfully.',
        ]);
    }
}
