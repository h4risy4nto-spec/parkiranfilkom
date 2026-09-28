<?php

namespace App\Http\Controllers;

use App\Models\ParkingArea;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use App\Models\ParkingTicket;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class CheckInController extends Controller
{
    /**
     * Show vehicle check-in form
     */
    public function index()
    {
        $areas = ParkingArea::with(['slots', 'activeTickets'])
            ->where('is_active', true)
            ->get();

        $rates = ParkingRate::all();

        // Recent entries
        $recentEntries = ParkingTicket::with(['area', 'slot'])
            ->where('status', 'active')
            ->latest('entry_time')
            ->take(5)
            ->get();

        return view('checkin.index', compact('areas', 'rates', 'recentEntries'));
    }

    /**
     * Process vehicle check-in and issue ticket
     */
    public function store(Request $request)
    {
        // 1. Validasi input
        $validated = $request->validate([
            'plate_number' => [
                'required',
                'string',
                'min:3',
                'max:15',
                'regex:/^[A-Z0-9\s]+$/i',
            ],
            'vehicle_type' => ['required', 'in:Motor,Mobil'],
            'parking_area_id' => ['nullable', 'exists:parking_areas,id'],
        ], [
            'plate_number.required' => 'Plat nomor wajib diisi.',
            'plate_number.min' => 'Plat nomor minimal 3 karakter.',
            'plate_number.max' => 'Plat nomor maksimal 15 karakter.',
            'plate_number.regex' => 'Format plat nomor hanya boleh huruf, angka, dan spasi.',
            'vehicle_type.required' => 'Jenis kendaraan wajib dipilih.',
            'vehicle_type.in' => 'Pilihan jenis kendaraan tidak valid.',
            'parking_area_id.exists' => 'Area parkir yang dipilih tidak ditemukan.',
        ]);

        $plate = strtoupper(trim(preg_replace('/\s+/', ' ', $validated['plate_number'])));
        $vehicleType = $validated['vehicle_type'];
        $areaId = $validated['parking_area_id'] ?? null;

        // 2. Cek apakah kendaraan ini sudah tercatat aktif di dalam parkiran
        $alreadyParked = ParkingTicket::where('plate_number', $plate)
            ->where('status', 'active')
            ->first();

        if ($alreadyParked) {
            return back()->withInput()->withErrors([
                'plate_number' => "Kendaraan dengan plat {$plate} sudah tercatat aktif di area {$alreadyParked->area->name} (Tiket: {$alreadyParked->ticket_code}).",
            ]);
        }

        // 3. Database Transaction untuk memastikan integritas dan menghindari race condition slot
        try {
            $ticket = DB::transaction(function () use ($plate, $vehicleType, $areaId) {
                // Cari area yang sesuai jika tidak dipilih secara spesifik
                $areaQuery = ParkingArea::where('is_active', true)
                    ->where('vehicle_type', $vehicleType);

                if ($areaId) {
                    $areaQuery->where('id', $areaId);
                }

                $candidateAreas = $areaQuery->get();

                $chosenArea = null;
                $chosenSlot = null;

                foreach ($candidateAreas as $area) {
                    // Cari slot pertama yang belum terisi di area ini
                    $freeSlot = ParkingSlot::where('parking_area_id', $area->id)
                        ->where('is_active', true)
                        ->where('is_occupied', false)
                        ->orderBy('slot_number')
                        ->lockForUpdate()
                        ->first();

                    if ($freeSlot) {
                        $chosenArea = $area;
                        $chosenSlot = $freeSlot;
                        break;
                    }
                }

                if (!$chosenArea || !$chosenSlot) {
                    throw new \Exception('Maaf, tidak ada slot parkir kosong yang tersedia untuk kendaraan jenis ini.');
                }

                // Ambil tarif per jam saat ini
                $rate = ParkingRate::where('vehicle_type', $vehicleType)->first();
                $hourlyRate = $rate ? $rate->hourly_rate : ($vehicleType === 'Mobil' ? 5000 : 2000);

                // Buat tiket baru
                $ticketCode = ParkingTicket::generateCode();

                $newTicket = ParkingTicket::create([
                    'ticket_code' => $ticketCode,
                    'plate_number' => $plate,
                    'vehicle_type' => $vehicleType,
                    'parking_area_id' => $chosenArea->id,
                    'parking_slot_id' => $chosenSlot->id,
                    'entry_time' => Carbon::now(),
                    'hourly_rate' => $hourlyRate,
                    'status' => 'active',
                    'operator_in_id' => Auth::id(),
                ]);

                // Update status slot menjadi terisi
                $chosenSlot->update(['is_occupied' => true]);

                return $newTicket;
            });

            return redirect()->route('checkin.show', $ticket->ticket_code)
                ->with('success', "Tiket parkir berhasil diterbitkan untuk {$ticket->plate_number} di Slot {$ticket->slot->slot_code}!");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    /**
     * Show issued ticket with QR Code
     */
    public function show($code)
    {
        $ticket = ParkingTicket::with(['area', 'slot', 'operatorIn'])
            ->where('ticket_code', $code)
            ->firstOrFail();

        return view('checkin.ticket', compact('ticket'));
    }

    /**
     * Printable ticket slip
     */
    public function printTicket($code)
    {
        $ticket = ParkingTicket::with(['area', 'slot'])
            ->where('ticket_code', $code)
            ->firstOrFail();

        return view('checkin.print', compact('ticket'));
    }
}
