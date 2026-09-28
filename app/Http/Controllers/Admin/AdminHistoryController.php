<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingArea;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;

class AdminHistoryController extends Controller
{
    /**
     * Display transaction history with filters
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $startDate = $request->query('start_date');
        $endDate = $request->query('end_date');
        $type = $request->query('type');
        $status = $request->query('status');

        $query = ParkingTicket::with(['area', 'operatorIn', 'operatorOut'])
            ->whereIn('status', ['paid', 'lost']);

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('plate_number', 'like', "%{$search}%")
                  ->orWhere('ticket_code', 'like', "%{$search}%");
            });
        }

        if ($startDate) {
            $query->whereDate('exit_time', '>=', $startDate);
        }

        if ($endDate) {
            $query->whereDate('exit_time', '<=', $endDate);
        }

        if ($type) {
            $query->where('vehicle_type', $type);
        }

        if ($status) {
            $query->where('status', $status);
        }

        // Clone query for totals before pagination
        $totalRevenue = (clone $query)->sum('total_amount');
        $totalCount = (clone $query)->count();
        $totalMotor = (clone $query)->where('vehicle_type', 'Motor')->count();
        $totalMobil = (clone $query)->where('vehicle_type', 'Mobil')->count();

        $history = $query->latest('exit_time')->paginate(15)->withQueryString();

        return view('admin.history.index', compact(
            'history',
            'totalRevenue',
            'totalCount',
            'totalMotor',
            'totalMobil',
            'search',
            'startDate',
            'endDate',
            'type',
            'status'
        ));
    }

    /**
     * Printable summary report
     */
    public function report(Request $request)
    {
        $startDate = $request->query('start_date', Carbon::today()->format('Y-m-d'));
        $endDate = $request->query('end_date', Carbon::today()->format('Y-m-d'));
        $type = $request->query('type');

        $query = ParkingTicket::with(['area', 'operatorOut'])
            ->whereIn('status', ['paid', 'lost'])
            ->whereDate('exit_time', '>=', $startDate)
            ->whereDate('exit_time', '<=', $endDate);

        if ($type) {
            $query->where('vehicle_type', $type);
        }

        $records = $query->latest('exit_time')->get();
        $totalRevenue = $records->sum('total_amount');
        $totalCount = $records->count();

        return view('admin.history.report', compact('records', 'startDate', 'endDate', 'type', 'totalRevenue', 'totalCount'));
    }
}
