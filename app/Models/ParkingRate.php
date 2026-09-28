<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingRate extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_type',
        'first_hour_rate',
        'hourly_rate',
        'lost_ticket_penalty',
    ];

    protected $casts = [
        'first_hour_rate' => 'float',
        'hourly_rate' => 'float',
        'lost_ticket_penalty' => 'float',
    ];

    /**
     * Calculate parking fee based on vehicle type and duration in minutes
     */
    public static function calculateFee(string $vehicleType, int $durationMinutes, bool $isLost = false): array
    {
        $rate = self::where('vehicle_type', $vehicleType)->first();

        $firstHourRate = $rate ? (float) $rate->first_hour_rate : ($vehicleType === 'Mobil' ? 5000 : 2000);
        $hourlyRate = $rate ? (float) $rate->hourly_rate : ($vehicleType === 'Mobil' ? 5000 : 2000);
        $penalty = $rate ? (float) $rate->lost_ticket_penalty : 25000;

        // Billable hours: minimum 1 hour, then rounded up
        $hours = max(1, (int) ceil($durationMinutes / 60));

        if ($hours === 1) {
            $baseFee = $firstHourRate;
        } else {
            $baseFee = $firstHourRate + (($hours - 1) * $hourlyRate);
        }

        $totalFee = $baseFee + ($isLost ? $penalty : 0);

        return [
            'hours' => $hours,
            'hourly_rate' => $hourlyRate,
            'base_fee' => $baseFee,
            'penalty' => $isLost ? $penalty : 0,
            'total_fee' => $totalFee,
        ];
    }
}
