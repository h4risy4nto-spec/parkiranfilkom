<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ParkingArea;
use App\Models\ParkingSlot;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminAreaController extends Controller
{
    /**
     * List all parking areas and their slots
     */
    public function index()
    {
        $areas = ParkingArea::withCount(['slots', 'activeTickets'])->get();
        return view('admin.areas.index', compact('areas'));
    }

    /**
     * Store new parking area and generate its slots
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'code' => ['required', 'string', 'max:20', 'unique:parking_areas,code'],
            'vehicle_type' => ['required', 'in:Motor,Mobil'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'location_description' => ['nullable', 'string', 'max:255'],
        ], [
            'name.required' => 'Nama area wajib diisi.',
            'code.required' => 'Kode area wajib diisi.',
            'code.unique' => 'Kode area sudah digunakan.',
            'vehicle_type.required' => 'Jenis kendaraan wajib dipilih.',
            'capacity.required' => 'Kapasitas slot wajib diisi.',
            'capacity.min' => 'Kapasitas minimal 1 slot.',
        ]);

        DB::transaction(function () use ($validated) {
            $code = strtoupper(trim($validated['code']));
            $area = ParkingArea::create([
                'name' => $validated['name'],
                'code' => $code,
                'vehicle_type' => $validated['vehicle_type'],
                'capacity' => $validated['capacity'],
                'location_description' => $validated['location_description'] ?? null,
                'is_active' => true,
            ]);

            // Auto-generate slots
            $prefix = preg_replace('/[^A-Z0-9]/', '', $code);
            for ($i = 1; $i <= $area->capacity; $i++) {
                ParkingSlot::create([
                    'parking_area_id' => $area->id,
                    'slot_number' => $i,
                    'slot_code' => sprintf('%s-%02d', $prefix, $i),
                    'is_occupied' => false,
                    'is_active' => true,
                ]);
            }
        });

        return redirect()->route('admin.areas.index')
            ->with('success', "Area {$validated['name']} berhasil ditambahkan dengan {$validated['capacity']} slot!");
    }

    /**
     * Update parking area capacity and info
     */
    public function update(Request $request, $id)
    {
        $area = ParkingArea::findOrFail($id);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'capacity' => ['required', 'integer', 'min:1', 'max:500'],
            'location_description' => ['nullable', 'string', 'max:255'],
        ]);

        $newCapacity = (int) $validated['capacity'];
        $currentOccupied = $area->activeTickets()->count();

        if ($newCapacity < $currentOccupied) {
            return back()->with('error', "Kapasitas tidak boleh lebih kecil dari jumlah kendaraan yang sedang parkir ({$currentOccupied}).");
        }

        DB::transaction(function () use ($area, $validated, $newCapacity) {
            $oldCapacity = $area->capacity;

            $area->update([
                'name' => $validated['name'],
                'capacity' => $newCapacity,
                'location_description' => $validated['location_description'] ?? null,
            ]);

            $prefix = preg_replace('/[^A-Z0-9]/', '', $area->code);

            if ($newCapacity > $oldCapacity) {
                // Tambah slot baru
                for ($i = $oldCapacity + 1; $i <= $newCapacity; $i++) {
                    ParkingSlot::firstOrCreate(
                        ['parking_area_id' => $area->id, 'slot_number' => $i],
                        ['slot_code' => sprintf('%s-%02d', $prefix, $i), 'is_occupied' => false, 'is_active' => true]
                    );
                }
            } elseif ($newCapacity < $oldCapacity) {
                // Hapus slot kosong yang berlebih di urutan akhir
                ParkingSlot::where('parking_area_id', $area->id)
                    ->where('slot_number', '>', $newCapacity)
                    ->where('is_occupied', false)
                    ->delete();
            }
        });

        return redirect()->route('admin.areas.index')
            ->with('success', "Area {$area->name} berhasil diperbarui.");
    }

    /**
     * Delete parking area safely
     */
    public function destroy($id)
    {
        $area = ParkingArea::findOrFail($id);

        if ($area->activeTickets()->count() > 0) {
            return back()->with('error', "Tidak dapat menghapus area {$area->name} karena masih ada kendaraan yang sedang parkir!");
        }

        $area->delete();

        return redirect()->route('admin.areas.index')
            ->with('success', "Area {$area->name} beserta seluruh slotnya telah dihapus.");
    }
}
