<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingArea extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'vehicle_type',
        'capacity',
        'location_description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'capacity' => 'integer',
    ];

    public function slots()
    {
        return $this->hasMany(ParkingSlot::class, 'parking_area_id');
    }

    public function tickets()
    {
        return $this->hasMany(ParkingTicket::class, 'parking_area_id');
    }

    public function activeTickets()
    {
        return $this->hasMany(ParkingTicket::class, 'parking_area_id')->where('status', 'active');
    }

    public function getOccupiedCountAttribute(): int
    {
        return $this->activeTickets()->count();
    }

    public function getAvailableCountAttribute(): int
    {
        return max(0, $this->capacity - $this->occupied_count);
    }

    public function getOccupancyPercentageAttribute(): int
    {
        if ($this->capacity == 0) {
            return 100;
        }
        return min(100, (int) round(($this->occupied_count / $this->capacity) * 100));
    }

    public function getStatusLabelAttribute(): string
    {
        $percent = $this->occupancy_percentage;
        if ($percent >= 100) {
            return 'Penuh';
        }
        if ($percent >= 80) {
            return 'Hampir Penuh';
        }
        return 'Tersedia';
    }

    public function getStatusColorAttribute(): string
    {
        $percent = $this->occupancy_percentage;
        if ($percent >= 100) {
            return 'danger'; // Red
        }
        if ($percent >= 80) {
            return 'warning'; // Orange / Amber
        }
        return 'success'; // Neon Green
    }
}
