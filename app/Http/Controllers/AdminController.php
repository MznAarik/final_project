<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function viewAllTickets()
    {
        $tickets = Ticket::with(['user:id,name', 'event:id,name', 'payments:id,ticket_id,payment_method,amount'])
            ->where('delete_flag', 0)
            ->orderBy('created_at', 'desc')
            ->get();

        $usdToNprRate = 133;
        $totalSumNpr = 0;

        foreach ($tickets as &$ticket) {
            $ticketTotal = 0;

            foreach ($ticket['payments'] as $payment) {
                $amount = (float) $payment['amount'];

                if (strtolower($payment['payment_method']) === 'paypal') {
                    $amount *= $usdToNprRate;
                }

                $ticketTotal += $amount;
            }

            $ticket['total_payment_npr'] = $ticketTotal;

            $totalSumNpr += $ticketTotal;
        }

        return view('admin.tickets.index', compact('tickets', 'totalSumNpr'));

    }

    public function updateStatus(Request $request, Ticket $ticket)
    {
        $request->validate([
            'status' => 'required|in:confirmed,pending,cancelled'
        ]);

        $ticket->update([
            'status' => $request->status
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Ticket status updated successfully'
        ]);
    }

    public function showScanQrPage()
    {
        return view('admin.scan-qr');
    }

    public function verifyTicket(Request $request)
    {
        try {
            $encryptedData = urldecode($request->query('data'));

            $qrData = AesHelper::decrypt($encryptedData);

            $decodedQr = json_decode($qrData, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                Log::error('Invalid QR code format', ['error' => json_last_error_msg()]);
                return response()->json(['success' => false, 'message' => 'Invalid QR code format']);
            }

            $ticket = Ticket::where('batch_code', $decodedQr['batch_code'])
                ->where('user_id', $decodedQr['user_id'])
                ->where('event_id', $decodedQr['event_id'])
                ->where('delete_flag', '!=', true)
                ->where('status', '!=', 'used')
                ->where('status', '!=', 'cancelled')
                ->first();

            if ($ticket) {
                $batchCode = $ticket->batch_code;
                Log::debug('Decrypted batch code:', ['batch_code' => $batchCode]);

                if (
                    $batchCode === $decodedQr['batch_code'] &&
                    $ticket->delete_flag == false &&
                    now()->lessThan(Carbon::parse($ticket->deadline))
                ) {
                    $ticket->update([
                        'status' => 'used',
                        'deadline' => null,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => now(),
                        // 'delete_flag' => true,
                    ]);
                    $ticketDetails = json_decode($ticket->ticket_details, true) ?: [];
                    $categories = !empty($ticketDetails) ? array_column($ticketDetails, 'quantity', 'category') : [];

                    return response()->json([
                        'status' => 'valid',
                        'message' => 'Ticket validated for ' . $ticket->user->name . ' of categories ' . implode(', ', array_keys($categories)) . ' with quantities ' . implode(', ', $categories) . ' respectively' . ' of event ' . $ticket->event->name,
                    ]);
                }
            }

            Log::warning('Ticket validation failed: No matching ticket or invalid conditions');
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid! Ticket already verified or expired',
            ], 400);
        } catch (\Exception $e) {
            Log::error('QR validation error:', [
                'message' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'status' => 'invalid',
                'message' => 'Invalid QR code data: ' . $e->getMessage(),
            ], 400);
        }
    }

    public function adminDashboard()
    {

        // Auto updating status and  deleting finished event after 24hours
        Event::where('end_date', '<=', Carbon::now())
            ->where('status', '!=', 'completed')
            ->update(['status' => 'completed']);

        $cutoffTime = Carbon::now()->subHours(24);
        Event::whereDate('end_date', '<=', $cutoffTime)
            ->where('delete_flag', false)
            ->update(['delete_flag' => true]);

        $ticketData = Ticket::select(DB::raw('event_id, SUM(total_price) as total_price'))
            ->where('status', '!=', 'cancelled')
            ->where('delete_flag', false)
            ->groupBy('event_id')
            ->take(5)
            ->get();

        $eventIds = $ticketData->pluck('event_id')->toArray();
        $events = Event::whereIn('id', $eventIds)
            ->where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $totalPrice = $ticketData->pluck('total_price');
        $activeUsers = User::where('delete_flag', 'false')->count();

        $ticketRevenue = $events->mapWithKeys(function ($item) use ($ticketData) {
            $total = $ticketData->where('event_id', $item->id)->sum('total_price');
            return [$item->id => $total];
        });

        return view('admin.dashboard', compact('ticketRevenue', 'totalPrice', 'activeUsers', 'events'));

    }

}
