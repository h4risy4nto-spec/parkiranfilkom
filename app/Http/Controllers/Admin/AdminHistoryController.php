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

        // Totals for top stat cards
        $totalCount = (clone $query)->count();
        $totalMasuk = ParkingTicket::whereNotNull('entry_time')->count();
        $totalKeluar = (clone $query)->count();
        $totalKendaraan = ParkingTicket::distinct('plate_number')->count('plate_number');
        $totalMotor = (clone $query)->where('vehicle_type', 'Motor')->count();
        $totalMobil = (clone $query)->where('vehicle_type', 'Mobil')->count();

        // Defaults for baseline demo presentation if numbers are 0
        if ($totalCount === 0) $totalCount = 120;
        if ($totalMasuk === 0) $totalMasuk = 78;
        if ($totalKeluar === 0) $totalKeluar = 42;
        if ($totalKendaraan === 0) $totalKendaraan = 56;
        if ($totalMobil === 0 && $totalMotor === 0) {
            $totalMobil = 70;
            $totalMotor = 50;
        }

        // 4-day transaction trend for line chart
        $chartLabels = ['06 Okt', '07 Okt', '08 Okt', '09 Okt'];
        $chartMasuk = [50, 65, 58, 78];
        $chartKeluar = [30, 42, 38, 42];

        $history = $query->latest('exit_time')->paginate(10)->withQueryString();

        return view('admin.history.index', compact(
            'history',
            'totalCount',
            'totalMasuk',
            'totalKeluar',
            'totalKendaraan',
            'totalMotor',
            'totalMobil',
            'chartLabels',
            'chartMasuk',
            'chartKeluar',
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
        $totalCount = $records->count();
        $totalMotor = $records->where('vehicle_type', 'Motor')->count();
        $totalMobil = $records->where('vehicle_type', 'Mobil')->count();

        return view('admin.history.report', compact('records', 'startDate', 'endDate', 'type', 'totalCount', 'totalMotor', 'totalMobil'));
    }
}
