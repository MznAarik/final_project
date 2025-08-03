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
    // public function index()
    // {
    //     return view('admin.dashboard')->with(['status' => 1, 'message' => 'Welcome Admin']);
    // }

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
        $events = Event::where('start_date', '>=', now())
            ->orderBy('start_date', 'asc')
            ->take(5)
            ->get();

        $ticketData = Ticket::select(DB::raw('event_id, SUM(total_price) as total_price'))
            ->where('status', '!=', 'cancelled')
            ->where('delete_flag', false)
            ->groupBy('event_id')
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
