<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
use App\Models\Event;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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
                'ticket_details' => 'required|array',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer',
            ]);

            $event = Event::findOrFail($request->event_id);
            if ($event->status == 'cancelled') {
                return redirect()->back()->with('error', 'Sorry, The event has be cancelled! Please stay tuned for more such events!');
            }

            $deadline = $event->start_date ? Carbon::parse($event->start_date)->subHours(24) : null;
            $categoryData = json_decode($event->ticket_category_price, true);
            $ticketsSold = $event->tickets_sold;
            $eventCapacity = $event->capacity ?? 0;

            $ticketDetails = [];
            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($request->ticket_details as $categoryInput) {
                $category = strtolower($categoryInput['category']);
                $quantity = $categoryInput['quantity'];
                $selected = collect($categoryData)->firstWhere('category', $category);
                if (!$selected) {
                    return redirect()->back()->with(['status' => false, 'message' => "Category '$category' not found."], 404);
                }
                $price = $selected['price'] ?? 0;
                if ($quantity > 0) {
                    $ticketDetails[] = [
                        'category' => $category,
                        'quantity' => $quantity,
                        'price' => $price
                    ];
                    $totalQuantity += $quantity;
                }
                $totalPrice += $price * $quantity;
            }

            if (empty($ticketDetails)) {
                return redirect()->back()->with(['status' => false, 'message' => 'Please select at least one category with a positive quantity.'], 400);
            }

            $newTicketsSold = $ticketsSold + $totalQuantity;
            if ($newTicketsSold > $eventCapacity) {
                return redirect()->back()->with(['status' => false, 'message' => 'Sorry! The tickets are sold.'], 400);
            }
            $event = Event::where('id', $event->id)->firstOrFail();
            $event->update(['tickets_sold' => $newTicketsSold]);

            $batchCode = uniqid('batch_') . '-' . $request->event_id;
            $sensitiveData = [
                'user_id' => Auth::user()->id,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'ticket_details' => $ticketDetails,
                'total_price' => $totalPrice
            ];

            $userId = Auth::user()->id;

            $encryptedData = AesHelper::encrypt($sensitiveData);
            $qrCodeSvg = QrCode::size(200)->generate(url('/') . '?ticket=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . $batchCode . '.svg';
            Storage::disk('public')->put($fileName, $qrCodeSvg);

            Ticket::create([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails),
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'deadline' => $deadline,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            return redirect()->back()->with(['status' => true, 'message' => 'Tickets created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error purchasing ticket: ' . $e->getMessage());
            return redirect()->back()->with(['status' => 0, 'message' => 'Error purchasing ticket: ' . $e->getMessage()]);
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
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_details' => 'required|array',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer',
            ]);

            $event = Event::findOrFail($request->event_id);
            $deadline = $event->start_date ? Carbon::parse($event->start_date)->subHours(24) : null;
            $categoryData = json_decode($event->ticket_category_price, true);
            $ticketsSold = $event->tickets_sold ?? 0;
            $eventCapacity = $event->capacity ?? 0;

            $ticket = Ticket::where('batch_code', $batch_code)
                ->where('status', '!=', 'cancelled')->firstOrFail();
            $previousTicketDetails = json_decode($ticket->ticket_details, true) ?? [];
            $previousTotalQuantity = $ticket->total_quantity ?? array_sum(array_column($previousTicketDetails, 'quantity'));

            $ticketDetails = [];
            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($request->ticket_details as $categoryInput) {
                $category = strtolower($categoryInput['category']);
                $quantity = $categoryInput['quantity'];
                $selected = collect($categoryData)->firstWhere('category', $category);
                if (!$selected) {
                    return redirect()->back()->with(['status' => false, 'message' => "Category '$category' not found."], 404);
                }
                $price = $selected['price'] ?? 0;
                if ($quantity > 0) {
                    $ticketDetails[] = [
                        'category' => $category,
                        'quantity' => $quantity,
                        'price' => $price
                    ];
                    $totalQuantity += $quantity;
                }
                $totalPrice += $price * $quantity;
            }

            if (empty($ticketDetails)) {
                return redirect()->back()->with(['status' => false, 'message' => 'Please select at least one category with a positive quantity.'], 400);
            }

            $quantityChange = $totalQuantity - $previousTotalQuantity;
            $newTicketsSold = $ticketsSold + $quantityChange;
            if ($newTicketsSold > $eventCapacity) {
                return redirect()->back()->with(['status' => false, 'message' => 'Sorry! The tickets are sold.'], 400);
            }

            $event->update(['tickets_sold' => $newTicketsSold]);

            $batchCode = $batch_code;
            $sensitiveData = [
                'user_id' => Auth::user()->id,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'ticket_details' => $ticketDetails,
                'total_price' => $totalPrice
            ];
            $userId = Auth::user()->id;

            $encryptedData = AesHelper::encrypt($sensitiveData);
            $qrCodeSvg = QrCode::size(200)->generate(url('/') . '?ticket=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . $batch_code . '.svg';

            if ($ticket->qr_code != 0 && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            Storage::disk('public')->put($fileName, $qrCodeSvg);

            $ticket->update([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails),
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'status' => 'pending',
                'deadline' => $deadline,
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

            return redirect()->back()->with(['status' => true, 'message' => 'Tickets updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            return redirect()->back()->with(['status' => 0, 'message' => 'Error updating ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $batch_code)
    {
        try {
            $ticket = Ticket::where('batch_code', $batch_code)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $event = Event::findOrFail($ticket->event_id);

            // Delete QR code if it exists
            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            $ticket->delete();

            $newTicketSold = Ticket::where('event_id', $event->id)
                ->where('batch_code', '!=', $batch_code)
                ->where('status', '!=', 'cancelled') // Adjust based on your logic
                ->where('delete_flag', 0) // Adjust based on your logic
                ->sum('total_quantity');

            $event->update(['tickets_sold' => $newTicketSold]);

            return redirect()->back()->with([
                'status' => true,
                'message' => 'Ticket deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ticket: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error deleting ticket: ' . $e->getMessage()
            ]);
        }
    }
}