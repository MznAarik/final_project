<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
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
        $totalPrice = Auth::user()->tickets()->sum('total_price');
        $ticketStatus = Auth::user()->tickets()->pluck('status')->all();
        // dd($totalPrice, $ticketStatus);
        return view('user.tickets.index', compact('tickets'));

    }

    /**
     * Store a newly created resource in storage.
     */

    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_category' => 'required|array|min:1',
                'ticket_category.*.category' => 'required|string',
                'ticket_category.*.quantity' => 'required|integer|min:1',
            ]);

            $event = Event::findOrFail($request->event_id);
            $categoryData = json_decode($event->ticket_category_price, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid ticket_category_price JSON: ' . json_last_error_msg());
                return redirect()->back()->with([
                    'status' => false,
                    'message' => 'Invalid ticket category & price.',
                ], 500);
            }

            $ticketsCreated = [];

            $batchCode = uniqid('batch_') . '-' . $request->event_id;
            foreach ($request->ticket_category as $categoryInput) {
                $selected = null;
                $category = $categoryInput['category'];
                $quantity = $categoryInput['quantity'];

                foreach ($categoryData as $index => $cat) {
                    Log::info("Checking index $index: " . json_encode($cat));
                    if (isset($cat['category']) && strtolower($cat['category']) === strtolower($category)) {
                        $selected = $cat;
                        $totalPrice = ($cat['price'] ?? 0) * $quantity;
                        Log::info('Match found: ' . json_encode($selected));
                        break;
                    }
                }

                if (!$selected || !isset($selected['category'])) {
                    Log::error('Category not found or invalid for: ' . $category);
                    Log::error('Available categories: ' . json_encode($categoryData));
                    return redirect()->back()->with([
                        'status' => false,
                        'message' => "Ticket category '$category' not found or invalid.",
                    ], 404);
                }

                $ticketData = [
                    'user_id' => Auth::user()->id,
                    'event_id' => $request->event_id,
                    'batch_code' => $batchCode,
                    'status' => 'pending',
                    'price' => $selected['price'] ?? 0,
                    'category' => strtolower($selected['category']),
                    'quantity' => $quantity,
                    'deadline' => now()->addDays(7),
                    'cancellation_reason' => null,
                    'total_price' => $totalPrice,
                    'description' => $selected['description'] ?? null,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ];

                Log::info('Ticket data before create: ', $ticketData);

                try {
                    $ticket = Ticket::create($ticketData);
                    $qrCode = $this->generateQrCode(Auth::user()->name, $ticket->id, $request->event_id);
                    $ticket->update(['qr_code' => $qrCode]);
                    $ticketsCreated[] = $ticket;
                } catch (\Exception $e) {
                    Log::error('Error creating ticket: ' . $e->getMessage());
                    return redirect()->back()->with([
                        'status' => false,
                        'message' => 'Error creating ticket: ' . $e->getMessage(),
                    ]);
                }
            }

            return redirect()->back()->with([
                'status' => true,
                'message' => 'Tickets created successfully.',
                'tickets' => $ticketsCreated,
            ]);

        } catch (\Exception $e) {
            Log::error('Error purchasing ticket: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error purchasing ticket: ' . $e->getMessage(),
            ]);
        }
    }

    public function generateQrCode($user_name, $ticket_id, $event_id)
    {
        try {
            $sensitiveData = json_encode([
                'user_name' => $user_name,
                'ticket_id' => $ticket_id,
                'event_id' => $event_id,
            ]);

            //implemented AesHelper for AES-256 encryption
            $encryptedData = AesHelper::encrypt($sensitiveData);
            $publicUrl = url('/') . '?data=' . urlencode($encryptedData);
            $qrCodeSvg = QrCode::size(200)->generate($publicUrl);
            $fileName = 'qrcodes/ticket_' . time() . $ticket_id . '_event_' . $event_id . '.svg';
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



    /**
     * Display the specified resource.
     */
    public function show(string $batch_code)
    {
        try {
            $userId = Auth::user()->id;
            $ticket = Ticket::with(['user', 'event'])
                ->where('batch_code', $batch_code)
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

    public function update(Request $request, string $batch_code)
    {

        //implement batch code uuid for ticket
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_category' => 'required|array|min:1',
                'ticket_category.*.category' => 'required|string',
                'ticket_category.*.quantity' => 'required|integer|min:1',
            ]);
            $event = Event::findOrFail($request->event_id);
            $categoryData = json_decode($event->ticket_category_price, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid ticket_category_price JSON: ' . json_last_error_msg());
                return redirect()->back()->withErrors(['message' => 'Invalid ticket_category_price data in event.']);
            }

            $ticket = Ticket::where('batch_code', $batch_code)->firstOrFail();
            $ticketsUpdated = [];

            foreach ($request->ticket_category as $categoryInput) {
                $selected = null;
                $category = $categoryInput['category'];
                $quantity = $categoryInput['quantity'];

                foreach ($categoryData as $index => $cat) {
                    Log::info("Checking index $index: " . json_encode($cat));
                    if (isset($cat['category']) && strtolower($cat['category']) === strtolower($category)) {
                        $selected = $cat;
                        $totalPrice = ($cat['price'] ?? 0) * $quantity;
                        Log::info('Match found: ' . json_encode($selected));
                        break;
                    }
                }

                if (!$selected || !isset($selected['category'])) {
                    Log::error('Category not found or invalid for: ' . $category);
                    Log::error('Available categories: ' . json_encode($categoryData));
                    return redirect()->back()->withErrors(['message' => "Ticket category '$category' not found or invalid."]);
                }

                $ticketData = [
                    'user_id' => Auth::user()->id,
                    'event_id' => $request->event_id,
                    'status' => 'pending',
                    'price' => $selected['price'] ?? 0,
                    'category' => strtolower($selected['category']),
                    'quantity' => $quantity,
                    'deadline' => now()->addDays(7),
                    'cancellation_reason' => null,
                    'total_price' => $totalPrice,
                    'description' => $selected['description'] ?? null,
                    'updated_by' => Auth::id(),
                ];

                Log::info('Ticket data before update: ', $ticketData);

                if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                    Storage::disk('public')->delete($ticket->qr_code);
                }

                $qrCode = $this->generateQrCode(Auth::user()->name, $ticket->id, $request->event_id);
                $ticket->update(['qr_code' => $qrCode]);
                $ticket->update($ticketData);
                $ticketsUpdated[] = $ticket->fresh(); // Refresh to get updated data
                dd($quantity, $category, $selected['price'], $selected, $ticketData);
            }

            return redirect()->back()->with(['success' => 'Ticket updated successfully.'])->with('tickets', $ticketsUpdated);

        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            return redirect()->back()->with(['error' => 'Error updating ticket: ' . $e->getMessage()])->withInput();
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $ticket = Ticket::findOrFail($id);
        $ticket->where('delete_flag', 0);
        dd($ticket->toArray());
        $ticket->delete();
        return redirect()->route('user.tickets.index')->with([
            'status' => 1,
            'message' => 'Ticket deleted successfully.',
        ]);
    }
}
