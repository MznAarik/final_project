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
                'ticket_details' => 'required|array|min:1',
                'ticket_details.*.category' => 'required|string',
                'ticket_details.*.quantity' => 'required|integer',
            ]);

            $event = Event::findOrFail($request->event_id);
            $deadline = $event->start_date;
            $categoryData = json_decode($event->ticket_category_price, true);

            $ticketDetails = [];
            $totalPrice = 0;
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
                }
                $totalPrice += $price * $quantity;
            }
            if (empty($ticketDetails)) {
                return redirect()->back()->with(['status' => false, 'message' => 'Please select atleast one category.'], 400);
            }

            $batchCode = uniqid('batch_') . '-' . $request->event_id;
            $sensitiveData = [
                'user_id' => Auth::user()->id,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'ticket_details' => $ticketDetails,
                'total_price' => $totalPrice
            ];
            $encryptedData = AesHelper::encrypt($sensitiveData);
            $qrCodeSvg = QrCode::size(200)->generate(url('/') . '?ticket=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . $batchCode . '.svg';
            Storage::disk('public')->put($fileName, $qrCodeSvg);
            Ticket::create([
                'user_id' => Auth::user()->id,
                'event_id' => $request->event_id,
                'batch_code' => $batchCode,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails), // Ensure this is saved
                'total_price' => $totalPrice,
                'status' => 'pending',
                'deadline' => $deadline,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
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
            $categoryData = json_decode($event->ticket_category_price, true);

            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid ticket_category_price JSON: ' . json_last_error_msg());
                return redirect()->back()->withErrors(['message' => 'Invalid ticket_category_price data in event.']);
            }
            $ticket = Ticket::where('batch_code', $batch_code)->firstOrFail();

            $ticketDetails = [];
            $totalPrice = 0;
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
                }
                $totalPrice += $price * $quantity;
            }
            if (empty($ticketDetails)) {
                return redirect()->back()->with(['status' => false, 'message' => 'Please select atleast one category.'], 400);
            }

            $sensitiveData = [
                'user_id' => Auth::user()->id,
                'event_id' => $request->event_id,
                'batch_code' => $batch_code,
                'ticket_details' => $ticketDetails,
                'total_price' => $totalPrice
            ];
            $encryptedData = AesHelper::encrypt($sensitiveData);

            // Delete the old QR code if it exists
            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }

            // Generate new QR code
            $qrCodeSvg = QrCode::size(200)->generate(url('/') . '?ticket=' . urlencode($encryptedData));
            $fileName = 'qrcodes/' . time() . '_' . $batch_code . '.svg';
            Storage::disk('public')->put($fileName, $qrCodeSvg);

            // Update the ticket record
            $ticket->update([
                'event_id' => $request->event_id,
                'qr_code' => $fileName,
                'ticket_details' => json_encode($ticketDetails),
                'total_price' => $totalPrice,
                'status' => 'pending',
                'updated_by' => Auth::user()->id,
                'updated_at' => now(),
            ]);

            return redirect()->back()->with(['status' => true, 'message' => 'Ticket updated successfully.']);
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
            $ticket = Ticket::where('batch_code', $batch_code)->firstOrFail();
            $ticket->delete();
            if ($ticket->qr_code && Storage::disk('public')->exists($ticket->qr_code)) {
                Storage::disk('public')->delete($ticket->qr_code);
            }
            return redirect()->back()->with([
                'status' => 1,
                'message' => 'Ticket deleted successfully.',
            ]);
        } catch (\Exception $e) {
            Log::error('Error deleting ticket: ' . $e->getMessage());
            return redirect()->back()->with([
                'status' => 0,
                'message' => 'Error deleting ticket: ',
            ]);
        }
    }
}