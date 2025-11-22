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
    public function index()
    {
        $tickets = Ticket::with(['user:id,name', 'event:id,name'])
            ->where('delete_flag', 0)
            ->orderBy('created_at', 'desc')->get();
        // dd($tickets->toArray());
        return view('user.tickets.index', compact('tickets'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_details' => 'required|array',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $event = Event::findOrFail($request->event_id);

            $deadline = $event->end_date ? Carbon::parse($event->end_date) : null;
            dd($deadline);
            $ticketDetails = [];
            $totalPrice = 0;
            $totalQuantity = 0;

            foreach ($request->ticket_details as $categoryInput) {
                $category = strtolower($categoryInput['category']);
                $quantity = $categoryInput['quantity'];
                $ticketDetails[] = [
                    'category' => $category,
                    'quantity' => $quantity,
                    'price' => $categoryInput['price'] ?? 0, // Price already validated in CartController
                ];
                $totalQuantity += $quantity;
                $totalPrice += ($categoryInput['price'] ?? 0) * $quantity;
            }

            $batchCode = uniqid('batch_') . '-' . $request->event_id;
            $userId = Auth::user()->id;
            $sensitiveData = [
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
            ];
            $encryptedData = AesHelper::encrypt(json_encode($sensitiveData));
            $qrCodeUrl = route('admin.verify-ticket') . '?data=' . urlencode($encryptedData);
            $qrCode = QrCode::format('svg')->size(200)->generate($qrCodeUrl);
            $fileName = 'qrcodes/ticket_' . time() . '_' . Str::random(8) . '.svg';
            Storage::disk('public')->put($fileName, $qrCode);

            $ticket = Ticket::create([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails),
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'status' => 'completed', // Set to completed as payment is handled in PaymentController
                'deadline' => $deadline,
                'created_by' => $userId,
                'updated_by' => $userId,
            ]);

            DB::commit();

            Mail::to(Auth::user()->email)->send(new SendTicket($ticket, $fileName));

            return redirect()->route('user.tickets.show')->with(['status' => true, 'message' => 'Tickets created successfully.']);
        } catch (\Exception $e) {
            Log::error('Error purchasing ticket: ' . $e->getMessage());
            DB::rollback();
            return redirect()->back()->with(['status' => 0, 'message' => 'Error purchasing ticket: ' . $e->getMessage()]);
        }
    }

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

    public function update(Request $request, string $batch_code)
    {
        try {
            $request->validate([
                'event_id' => 'required|exists:events,id',
                'ticket_details' => 'required|array',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer|min:1',
            ]);

            DB::beginTransaction();

            $event = Event::findOrFail($request->event_id);
            $deadline = $event->start_date ? Carbon::parse($event->start_date)->subHours(24) : null;
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
                $ticketDetails[] = [
                    'category' => $category,
                    'quantity' => $quantity,
                    'price' => $categoryInput['price'] ?? 0, // Price validated in CartController
                ];
                $totalQuantity += $quantity;
                $totalPrice += ($categoryInput['price'] ?? 0) * $quantity;
            }

            $batchCode = $batch_code;
            $userId = Auth::user()->id;
            $sensitiveData = [
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
            ];

            $encryptedData = AesHelper::encrypt(json_encode($sensitiveData));
            $qrCodeUrl = route('admin.verify-ticket') . '?data=' . urlencode($encryptedData);
            $qrCode = QrCode::format('svg')->size(200)->generate($qrCodeUrl);
            $fileName = 'qrcodes/ticket_' . time() . '_' . Str::random(8) . '.svg';

            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            Storage::disk('public')->put($fileName, $qrCode);

            $ticket->update([
                'user_id' => $userId,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails),
                'total_quantity' => $totalQuantity,
                'total_price' => $totalPrice,
                'status' => 'completed', // Set to completed as payment is handled elsewhere
                'deadline' => $deadline,
                'updated_by' => $userId,
                'updated_at' => now(),
            ]);

            DB::commit();

            Mail::to(Auth::user()->email)->send(new SendTicket($ticket, $fileName));

            return redirect()->route('user.tickets.index')->with(['status' => true, 'message' => 'Tickets updated successfully.']);
        } catch (\Exception $e) {
            Log::error('Error updating ticket: ' . $e->getMessage());
            DB::rollback();
            return redirect()->back()->with(['status' => 0, 'message' => 'Error updating ticket: ' . $e->getMessage()]);
        }
    }

    public function destroy(string $batch_code)
    {
        try {
            $ticket = Ticket::where('batch_code', $batch_code)
                ->where('user_id', Auth::user()->id)
                ->firstOrFail();

            DB::beginTransaction();

            $event = Event::findOrFail($ticket->event_id);

            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            $ticket->delete();

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