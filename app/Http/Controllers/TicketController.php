<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
use App\Mail\SendTicket;
use App\Models\Event;
use App\Models\Ticket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
        DB::beginTransaction();
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_details' => 'required|array',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer',
            ]);
            $event = Event::findOrFail($request->event_id);
            if ($event->status == 'cancelled') {
                return redirect()->back()->with('error', 'Sorry, The event has been cancelled! Please stay tuned for more such events!');
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
            $userId = Auth::user()->id;
            $sensitiveData = [
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
            ];
            $encryptedData = AesHelper::encrypt($sensitiveData);
            $qrCodeSvg = QrCode::size(200)->generate(url('admin/verify-ticket') . '?data=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . Str::random(8) . uniqid() . '.svg';
            Storage::disk('public')->put($fileName, $qrCodeSvg);

            $ticket = Ticket::create([
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

            Mail::to(Auth::user()->email)->send(new SendTicket($ticket));

            DB::commit();

            return redirect()->route('user.tickets.show')->with(['status' => true, 'message' => 'Tickets created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error purchasing ticket: ' . $e->getMessage());
            DB::rollback();
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
        DB::beginTransaction();
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
                ->where('status', '!=', 'cancelled')
                ->where('delete_flag', 0)->firstOrFail();
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
            $userId = Auth::user()->id;
            $sensitiveData = [
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
            ];

            $encryptedData = AesHelper::encrypt($sensitiveData);
            $qrCodeSvg = QrCode::size(200)->generate(url('admin/verify-ticket') . '?data=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . Str::random(8) . uniqid() . '.svg';

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

            Mail::to(Auth::user()->email)->send(new SendTicket($ticket));

            DB::commit();

            return redirect()->route('user.tickets.show')->with(['status' => true, 'message' => 'Tickets updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            DB::rollback();
            return redirect()->back()->with(['status' => 0, 'message' => 'Error updating ticket: ' . $e->getMessage()]);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $batch_code)
    {
        try {
            DB::beginTransaction();
            $ticket = Ticket::where('batch_code', $batch_code)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            $event = Event::findOrFail($ticket->event_id);

            // Delete QR code if it exists
            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            $ticket->delete();

            // to update the total tickets sold required for the event
            $newTicketSold = Ticket::where('event_id', $event->id)
                ->where('batch_code', '!=', $batch_code)
                ->where('status', '!=', 'cancelled')
                ->where('delete_flag', 0)
                ->sum('total_quantity');

            $event->update(['tickets_sold' => $newTicketSold]);

            DB::commit();

            return redirect()->back()->with([
                'status' => true,
                'message' => 'Ticket deleted successfully.'
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ticket: ' . $e->getMessage());
            DB::rollback();
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error deleting ticket: ' . $e->getMessage()
            ]);
        }
    }
}