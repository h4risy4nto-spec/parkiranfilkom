<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingRate;
use Illuminate\Http\Request;

class AdminRateController extends Controller
{
    /**
     * Show rates configuration
     */
    public function index()
    {
        $rates = ParkingRate::all();
        return view('admin.rates.index', compact('rates'));
    }

    /**
     * Update parking rates
     */
    public function update(Request $request)
    {
        $validated = $request->validate([
            'rates' => ['required', 'array'],
            'rates.*.id' => ['required', 'exists:parking_rates,id'],
            'rates.*.first_hour_rate' => ['required', 'numeric', 'min:0'],
            'rates.*.hourly_rate' => ['required', 'numeric', 'min:0'],
            'rates.*.lost_ticket_penalty' => ['required', 'numeric', 'min:0'],
        ], [
            'rates.*.first_hour_rate.required' => 'Tarif jam pertama wajib diisi.',
            'rates.*.hourly_rate.required' => 'Tarif per jam wajib diisi.',
            'rates.*.lost_ticket_penalty.required' => 'Denda tiket hilang wajib diisi.',
        ]);

        foreach ($validated['rates'] as $data) {
            $rate = ParkingRate::find($data['id']);
            if ($rate) {
                $rate->update([
                    'first_hour_rate' => $data['first_hour_rate'],
                    'hourly_rate' => $data['hourly_rate'],
                    'lost_ticket_penalty' => $data['lost_ticket_penalty'],
                ]);
            }
        }

        return redirect()->route('admin.rates.index')
            ->with('success', 'Konfigurasi tarif parkir berhasil diperbarui!');
    }
}
