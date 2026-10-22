<?php

namespace App\Http\Controllers;

use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckOutController extends Controller
{
    /**
     * Show vehicle check-out page with QR code scanner and search
     */
    public function index(Request $request)
    {
        $query = $request->query('q');
        $foundTicket = null;
        $feeDetails = null;

        if ($query) {
            $cleaned = strtoupper(trim(preg_replace('/\s+/', ' ', $query)));

            $foundTicket = ParkingTicket::with(['area', 'slot', 'operatorIn'])
                ->where('status', 'active')
                ->where(function ($q) use ($cleaned) {
                    $q->where('ticket_code', $cleaned)
                      ->orWhere('plate_number', $cleaned);
                })
                ->first();

            if ($foundTicket) {
                $isLost = $request->boolean('is_lost');
                $feeDetails = $foundTicket->calculateLiveFee($isLost);
            }
        }

        // Recent checkouts
        $recentCheckouts = ParkingTicket::with(['area'])
            ->whereIn('status', ['paid', 'lost'])
            ->latest('exit_time')
            ->take(5)
            ->get();

        return view('checkout.index', compact('foundTicket', 'feeDetails', 'recentCheckouts', 'query'));
    }

    /**
     * API endpoint to lookup ticket by QR Code scan
     */
    public function lookup(Request $request)
    {
        $code = strtoupper(trim($request->input('code', '')));

        if (!$code) {
            return response()->json(['success' => false, 'message' => 'Kode tiket kosong.'], 400);
        }

        $ticket = ParkingTicket::with(['area', 'slot'])
            ->where('status', 'active')
            ->where(function ($q) use ($code) {
                $q->where('ticket_code', $code)
                  ->orWhere('plate_number', $code);
            })
            ->first();

        if (!$ticket) {
            return response()->json([
                'success' => false,
                'message' => 'Tiket aktif tidak ditemukan untuk kode atau plat: ' . $code,
            ], 404);
        }

        $now = Carbon::now();
        $durationMinutes = max(1, $ticket->entry_time->diffInMinutes($now));

        return response()->json([
            'success' => true,
            'ticket' => [
                'code' => $ticket->ticket_code,
                'plate' => $ticket->plate_number,
                'type' => $ticket->vehicle_type,
                'area' => $ticket->area->name ?? '-',
                'slot' => $ticket->slot->slot_code ?? '-',
                'entry_time' => $ticket->entry_time->translatedFormat('d M Y, H:i'),
                'exit_time' => $now->translatedFormat('d M Y, H:i'),
                'duration' => $ticket->formatted_duration,
            ],
        ]);
    }

    /**
     * Process checkout without billing (campus free parking)
     */
    public function process(Request $request)
    {
        $validated = $request->validate([
            'ticket_code' => ['required', 'string', 'exists:parking_tickets,ticket_code'],
            'is_lost' => ['nullable', 'boolean'],
            'notes' => ['nullable', 'string', 'max:255'],
        ], [
            'ticket_code.required' => 'Kode tiket wajib ada.',
            'ticket_code.exists' => 'Tiket tidak ditemukan dalam sistem.',
        ]);

        $ticketCode = $validated['ticket_code'];
        $isLost = $request->boolean('is_lost');
        $notes = $validated['notes'] ?? null;

        try {
            $ticket = DB::transaction(function () use ($ticketCode, $isLost, $notes) {
                $ticket = ParkingTicket::where('ticket_code', $ticketCode)
                    ->where('status', 'active')
                    ->lockForUpdate()
                    ->first();

                if (!$ticket) {
                    throw new \Exception('Tiket sudah diproses keluar atau tidak valid.');
                }

                $now = Carbon::now();
                $diffMinutes = max(1, $ticket->entry_time->diffInMinutes($now));

                // Update status tiket (Free parking - no monetary charge)
                $ticket->update([
                    'exit_time' => $now,
                    'duration_minutes' => $diffMinutes,
                    'hourly_rate' => 0,
                    'total_amount' => 0,
                    'status' => $isLost ? 'lost' : 'paid',
                    'operator_out_id' => Auth::id(),
                    'notes' => $notes ?: ($isLost ? 'Tiket Hilang (Diverifikasi Operator)' : null),
                ]);

                // Kosongkan kembali slot parkir terkait
                if ($ticket->parking_slot_id) {
                    $slot = ParkingSlot::find($ticket->parking_slot_id);
                    if ($slot) {
                        $slot->update(['is_occupied' => false]);
                    }
                }

                return $ticket;
            });

            return redirect()->route('checkout.receipt', $ticket->ticket_code)
                ->with('success', "Kendaraan {$ticket->plate_number} berhasil keluar! Durasi: {$ticket->formatted_duration}. Slot telah dikosongkan kembali.");
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * Show exit receipt
     */
    public function receipt($code)
    {
        $ticket = ParkingTicket::with(['area', 'slot', 'operatorIn', 'operatorOut'])
            ->where('ticket_code', $code)
            ->whereIn('status', ['paid', 'lost'])
            ->firstOrFail();

        return view('checkout.receipt', compact('ticket'));
    }
}
