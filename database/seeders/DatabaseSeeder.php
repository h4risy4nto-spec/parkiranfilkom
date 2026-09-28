<?php

namespace Database\Seeders;

use App\Models\ParkingArea;
use App\Models\ParkingRate;
use App\Models\ParkingSlot;
use App\Models\ParkingTicket;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Users (Admin, Operator, dan 4 Anggota Kelompok)
        $admin = User::firstOrCreate(
            ['email' => 'admin@filkom.ub.ac.id'],
            [
                'name' => 'Administrator Parkir FILKOM',
                'password' => Hash::make('password123'),
                'role' => 'admin',
                'phone' => '081234567890',
                'is_active' => true,
            ]
        );

        $operator = User::firstOrCreate(
            ['email' => 'operator@filkom.ub.ac.id'],
            [
                'name' => 'Petugas Gerbang Utama',
                'password' => Hash::make('password123'),
                'role' => 'operator',
                'phone' => '081234567891',
                'is_active' => true,
            ]
        );

        // Akun 4 Anggota Kelompok 1 PTI D
        $members = [
            ['name' => 'Anggota 1 - Dashboard Lead', 'email' => 'anggota1@filkom.ub.ac.id', 'role' => 'admin'],
            ['name' => 'Anggota 2 - Check-In Gate', 'email' => 'anggota2@filkom.ub.ac.id', 'role' => 'operator'],
            ['name' => 'Anggota 3 - Check-Out & QR', 'email' => 'anggota3@filkom.ub.ac.id', 'role' => 'operator'],
            ['name' => 'Anggota 4 - Database & Admin', 'email' => 'anggota4@filkom.ub.ac.id', 'role' => 'admin'],
        ];

        foreach ($members as $m) {
            User::firstOrCreate(
                ['email' => $m['email']],
                [
                    'name' => $m['name'],
                    'password' => Hash::make('password123'),
                    'role' => $m['role'],
                    'phone' => '08987654321',
                    'is_active' => true,
                ]
            );
        }

        // 2. Seed Parking Rates
        ParkingRate::updateOrCreate(
            ['vehicle_type' => 'Motor'],
            [
                'first_hour_rate' => 2000,
                'hourly_rate' => 2000,
                'lost_ticket_penalty' => 25000,
            ]
        );

        ParkingRate::updateOrCreate(
            ['vehicle_type' => 'Mobil'],
            [
                'first_hour_rate' => 5000,
                'hourly_rate' => 5000,
                'lost_ticket_penalty' => 50000,
            ]
        );

        // 3. Seed Parking Areas & Slots
        $areasData = [
            [
                'name' => 'Area A - Gedung F',
                'code' => 'AREA-A',
                'vehicle_type' => 'Motor',
                'capacity' => 20,
                'location_description' => 'Samping Gedung F (Pascasarjana & Lab FILKOM)',
            ],
            [
                'name' => 'Area B - Gedung G',
                'code' => 'AREA-B',
                'vehicle_type' => 'Motor',
                'capacity' => 15,
                'location_description' => 'Belakang Gedung G (Kantin & Gedung Kuliah Bersama)',
            ],
            [
                'name' => 'Area C - Lapangan Depan',
                'code' => 'AREA-C',
                'vehicle_type' => 'Mobil',
                'capacity' => 10,
                'location_description' => 'Depan Gedung Utama FILKOM UB',
            ],
        ];

        $createdAreas = [];
        foreach ($areasData as $areaInfo) {
            $area = ParkingArea::firstOrCreate(
                ['code' => $areaInfo['code']],
                [
                    'name' => $areaInfo['name'],
                    'vehicle_type' => $areaInfo['vehicle_type'],
                    'capacity' => $areaInfo['capacity'],
                    'location_description' => $areaInfo['location_description'],
                    'is_active' => true,
                ]
            );
            $createdAreas[$areaInfo['code']] = $area;

            // Generate Slot untuk setiap area
            for ($i = 1; $i <= $area->capacity; $i++) {
                $codePrefix = substr($areaInfo['code'], 5); // 'A', 'B', 'C'
                $slotCode = sprintf('%s-%02d', $codePrefix, $i);

                ParkingSlot::firstOrCreate(
                    [
                        'parking_area_id' => $area->id,
                        'slot_number' => $i,
                    ],
                    [
                        'slot_code' => $slotCode,
                        'is_occupied' => false,
                        'is_active' => true,
                    ]
                );
            }
        }

        // 4. Seed Beberapa Tiket Aktif (Kendaraan sedang parkir)
        $areaA = $createdAreas['AREA-A'];
        $areaB = $createdAreas['AREA-B'];
        $areaC = $createdAreas['AREA-C'];

        $slotA1 = ParkingSlot::where('parking_area_id', $areaA->id)->where('slot_number', 1)->first();
        $slotA2 = ParkingSlot::where('parking_area_id', $areaA->id)->where('slot_number', 2)->first();
        $slotB1 = ParkingSlot::where('parking_area_id', $areaB->id)->where('slot_number', 1)->first();
        $slotC1 = ParkingSlot::where('parking_area_id', $areaC->id)->where('slot_number', 1)->first();

        // Tiket Aktif 1: Motor N 4567 AA
        if ($slotA1) {
            $slotA1->update(['is_occupied' => true]);
            ParkingTicket::firstOrCreate(
                ['ticket_code' => 'TKT-20260928-MTR01'],
                [
                    'plate_number' => 'N 4567 AA',
                    'vehicle_type' => 'Motor',
                    'parking_area_id' => $areaA->id,
                    'parking_slot_id' => $slotA1->id,
                    'entry_time' => Carbon::now()->subHours(2)->subMinutes(15),
                    'hourly_rate' => 2000,
                    'status' => 'active',
                    'operator_in_id' => $operator->id,
                ]
            );
        }

        // Tiket Aktif 2: Motor N 1234 XY
        if ($slotA2) {
            $slotA2->update(['is_occupied' => true]);
            ParkingTicket::firstOrCreate(
                ['ticket_code' => 'TKT-20260928-MTR02'],
                [
                    'plate_number' => 'N 1234 XY',
                    'vehicle_type' => 'Motor',
                    'parking_area_id' => $areaA->id,
                    'parking_slot_id' => $slotA2->id,
                    'entry_time' => Carbon::now()->subMinutes(45),
                    'hourly_rate' => 2000,
                    'status' => 'active',
                    'operator_in_id' => $operator->id,
                ]
            );
        }

        // Tiket Aktif 3: Motor B 6789 UB
        if ($slotB1) {
            $slotB1->update(['is_occupied' => true]);
            ParkingTicket::firstOrCreate(
                ['ticket_code' => 'TKT-20260928-MTR03'],
                [
                    'plate_number' => 'B 6789 UB',
                    'vehicle_type' => 'Motor',
                    'parking_area_id' => $areaB->id,
                    'parking_slot_id' => $slotB1->id,
                    'entry_time' => Carbon::now()->subHours(1)->subMinutes(10),
                    'hourly_rate' => 2000,
                    'status' => 'active',
                    'operator_in_id' => $operator->id,
                ]
            );
        }

        // Tiket Aktif 4: Mobil L 9999 AB
        if ($slotC1) {
            $slotC1->update(['is_occupied' => true]);
            ParkingTicket::firstOrCreate(
                ['ticket_code' => 'TKT-20260928-MBL01'],
                [
                    'plate_number' => 'L 9999 AB',
                    'vehicle_type' => 'Mobil',
                    'parking_area_id' => $areaC->id,
                    'parking_slot_id' => $slotC1->id,
                    'entry_time' => Carbon::now()->subHours(3)->subMinutes(20),
                    'hourly_rate' => 5000,
                    'status' => 'active',
                    'operator_in_id' => $operator->id,
                ]
            );
        }

        // 5. Seed Riwayat Parkir Selesai (Completed Transactions)
        $completed = [
            [
                'ticket_code' => 'TKT-20260928-HIS01',
                'plate_number' => 'N 2234 BC',
                'vehicle_type' => 'Motor',
                'parking_area_id' => $areaA->id,
                'entry_time' => Carbon::now()->subHours(5),
                'exit_time' => Carbon::now()->subHours(3),
                'duration_minutes' => 120,
                'hourly_rate' => 2000,
                'total_amount' => 4000,
            ],
            [
                'ticket_code' => 'TKT-20260928-HIS02',
                'plate_number' => 'W 8877 KL',
                'vehicle_type' => 'Mobil',
                'parking_area_id' => $areaC->id,
                'entry_time' => Carbon::now()->subHours(4),
                'exit_time' => Carbon::now()->subHours(2),
                'duration_minutes' => 120,
                'hourly_rate' => 5000,
                'total_amount' => 10000,
            ],
            [
                'ticket_code' => 'TKT-20260928-HIS03',
                'plate_number' => 'AG 1122 ZZ',
                'vehicle_type' => 'Motor',
                'parking_area_id' => $areaB->id,
                'entry_time' => Carbon::now()->subHours(3)->subMinutes(30),
                'exit_time' => Carbon::now()->subMinutes(30),
                'duration_minutes' => 180,
                'hourly_rate' => 2000,
                'total_amount' => 6000,
            ],
        ];

        foreach ($completed as $c) {
            ParkingTicket::firstOrCreate(
                ['ticket_code' => $c['ticket_code']],
                [
                    'plate_number' => $c['plate_number'],
                    'vehicle_type' => $c['vehicle_type'],
                    'parking_area_id' => $c['parking_area_id'],
                    'parking_slot_id' => null,
                    'entry_time' => $c['entry_time'],
                    'exit_time' => $c['exit_time'],
                    'duration_minutes' => $c['duration_minutes'],
                    'hourly_rate' => $c['hourly_rate'],
                    'total_amount' => $c['total_amount'],
                    'status' => 'paid',
                    'operator_in_id' => $operator->id,
                    'operator_out_id' => $operator->id,
                ]
            );
        }
    }
}
