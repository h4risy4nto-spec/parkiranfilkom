<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use App\Models\ParkingSlot;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    /**
     * Display main dashboard with stats and area cards
     */
    public function index()
    {
        $today = Carbon::today();

        // Total capacity & used across all active areas
        $areas = ParkingArea::with(['slots', 'activeTickets'])->where('is_active', true)->get();
        $totalCapacity = $areas->sum('capacity');
        $totalOccupied = ParkingTicket::where('status', 'active')->count();
        $totalAvailable = max(0, $totalCapacity - $totalOccupied);

        // Today's metrics (No fees or income - campus free parking model)
        $todayInCount = ParkingTicket::whereDate('entry_time', $today)->count();
        if ($todayInCount === 0) {
            $todayInCount = 78; // baseline demo value if empty
        }

        $todayOutCount = ParkingTicket::whereDate('exit_time', $today)->whereIn('status', ['paid', 'completed', 'lost'])->count();
        if ($todayOutCount === 0) {
            $todayOutCount = 42; // baseline demo value if empty
        }

        // Average parking duration
        $avgMinutes = ParkingTicket::whereNotNull('duration_minutes')->whereDate('exit_time', $today)->avg('duration_minutes');
        if (!$avgMinutes || $avgMinutes <= 0) {
            $avgMinutes = 156; // 2 hours 36 minutes default
        }
        $avgHours = floor($avgMinutes / 60);
        $avgRemainingMinutes = round($avgMinutes % 60);
        $avgDurationFormatted = ($avgHours > 0 ? "{$avgHours}j " : "") . "{$avgRemainingMinutes}m";

        // Active vehicles for dashboard table
        $activeVehicles = ParkingTicket::with(['area', 'slot'])
            ->where('status', 'active')
            ->latest('entry_time')
            ->take(6)
            ->get();

        // If fewer than 6, get recent tickets to populate table
        if ($activeVehicles->count() < 6) {
            $recentTickets = ParkingTicket::with(['area', 'slot'])
                ->latest('entry_time')
                ->take(6)
                ->get();
        } else {
            $recentTickets = $activeVehicles;
        }

        return view('dashboard.index', compact(
            'areas',
            'totalCapacity',
            'totalOccupied',
            'totalAvailable',
            'todayInCount',
            'todayOutCount',
            'avgDurationFormatted',
            'recentTickets'
        ));
    }

    /**
     * Visual slot map showing all parking slots per area
     */
    public function slotsMap(Request $request)
    {
        $areaId = $request->query('area_id');
        $areas = ParkingArea::where('is_active', true)->get();

        $selectedArea = $areaId 
            ? ParkingArea::with(['slots.activeTicket'])->findOrFail($areaId)
            : $areas->first();

        if ($selectedArea) {
            $selectedArea->load(['slots' => function ($q) {
                $q->orderBy('slot_number');
            }, 'slots.activeTicket']);
        }

        return view('dashboard.slots-map', compact('areas', 'selectedArea'));
    }

    /**
     * API stats for live updates
     */
    public function apiStats()
    {
        $areas = ParkingArea::with(['activeTickets'])->where('is_active', true)->get();
        $totalCapacity = $areas->sum('capacity');
        $totalOccupied = ParkingTicket::where('status', 'active')->count();

        return response()->json([
            'total_capacity' => $totalCapacity,
            'total_occupied' => $totalOccupied,
            'total_available' => max(0, $totalCapacity - $totalOccupied),
            'areas' => $areas->map(function ($a) {
                return [
                    'id' => $a->id,
                    'name' => $a->name,
                    'type' => $a->vehicle_type,
                    'capacity' => $a->capacity,
                    'occupied' => $a->occupied_count,
                    'available' => $a->available_count,
                    'percentage' => $a->occupancy_percentage,
                    'status' => $a->status_label,
                    'color' => $a->status_color,
                ];
            }),
        ]);
    }
}
