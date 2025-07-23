<?php

namespace App\Http\Controllers;

use App\Helpers\AesHelper;
use App\Models\Ticket;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class AdminController extends Controller
{
    public function index()
    {
        return view('admin.dashboard');
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

            $ticket = Ticket::where('batch_code', $qrData['batch_code'])
                ->where('user_id', $qrData['user_id'])
                ->where('event_id', $qrData['event_id'])
                ->where('delete_flag', '!=', true)
                ->where('status', '!=', 'used')
                ->first();

            if ($ticket) {
                $batchCode = $ticket->batch_code;
                Log::debug('Decrypted batch code:', ['batch_code' => $batchCode]);

                if (
                    $batchCode === $qrData['batch_code'] &&
                    $ticket->delete_flag == false &&
                    now()->lessThan(Carbon::parse($ticket->deadline))
                ) {
                    $ticket->update([
                        'status' => 'used',
                        'deadline' => null,
                        'updated_by' => Auth::user()->id,
                        'updated_at' => now(),
                        'delete_flag' => true,
                    ]);
                    return response()->json([
                        'status' => 'valid',
                        'message' => 'Ticket validated for ' . $ticket->user->name . '  with event ' . $ticket->event->name,
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
}
