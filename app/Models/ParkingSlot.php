<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingSlot extends Model
{
    use HasFactory;

    protected $fillable = [
        'parking_area_id',
        'slot_number',
        'slot_code',
        'is_occupied',
        'is_active',
    ];

    protected $casts = [
        'slot_number' => 'integer',
        'is_occupied' => 'boolean',
        'is_active' => 'boolean',
    ];

    public function area()
    {
        return $this->belongsTo(ParkingArea::class, 'parking_area_id');
    }

    public function tickets()
    {
        return $this->hasMany(ParkingTicket::class, 'parking_slot_id');
    }

    public function activeTicket()
    {
        return $this->hasOne(ParkingTicket::class, 'parking_slot_id')->where('status', 'active');
    }
}
