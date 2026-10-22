<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingArea;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminTicketController extends Controller
{
    /**
     * List all currently active parked vehicles
     */
    public function activeTickets(Request $request)
    {
        $search = $request->query('search');
        $areaId = $request->query('area_id');
        $type = $request->query('type');

        $query = ParkingTicket::with(['area', 'slot', 'operatorIn'])
            ->where('status', 'active');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%");
            });
        }

        if ($areaId) {
            $query->where('parking_area_id', $areaId);
        }

        if ($type) {
            $query->where('vehicle_type', $type);
        }

        $tickets = $query->latest('entry_time')->paginate(15)->withQueryString();
        $areas = ParkingArea::where('is_active', true)->get();

        return view('admin.tickets.active', compact('tickets', 'areas', 'search', 'areaId', 'type'));
    }

    /**
     * Force exit an active vehicle (Admin override)
     */
    public function forceExit(Request $request, $id)
    {
        $ticket = ParkingTicket::where('id', $id)->where('status', 'active')->firstOrFail();

        $now = Carbon::now();
        $diffMinutes = max(1, $ticket->entry_time->diffInMinutes($now));

        DB::transaction(function () use ($ticket, $now, $diffMinutes) {
            $ticket->update([
                'exit_time' => $now,
                'duration_minutes' => $diffMinutes,
                'hourly_rate' => 0,
                'total_amount' => 0,
                'status' => 'paid',
                'operator_out_id' => Auth::id(),
                'notes' => 'Keluar paksa oleh Administrator',
            ]);

            if ($ticket->parking_slot_id) {
                $slot = ParkingSlot::find($ticket->parking_slot_id);
                if ($slot) {
                    $slot->update(['is_occupied' => false]);
                }
            }
        });

        return back()->with('success', "Kendaraan {$ticket->plate_number} berhasil dikeluarkan secara manual oleh Admin.");
    }
}
