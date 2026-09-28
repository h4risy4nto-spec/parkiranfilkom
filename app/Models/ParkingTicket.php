<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use SimpleSoftwareIO\QrCode\Facades\QrCode;

class ParkingTicket extends Model
{
    use HasFactory;

    protected $fillable = [
        'ticket_code',
        'plate_number',
        'vehicle_type',
        'parking_area_id',
        'parking_slot_id',
        'entry_time',
        'exit_time',
        'duration_minutes',
        'hourly_rate',
        'total_amount',
        'status',
        'operator_in_id',
        'operator_out_id',
        'notes',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
        'duration_minutes' => 'integer',
        'hourly_rate' => 'float',
        'total_amount' => 'float',
    ];

    public function area()
    {
        return $this->belongsTo(ParkingArea::class, 'parking_area_id');
    }

    public function slot()
    {
        return $this->belongsTo(ParkingSlot::class, 'parking_slot_id');
    }

    public function operatorIn()
    {
        return $this->belongsTo(User::class, 'operator_in_id');
    }

    public function operatorOut()
    {
        return $this->belongsTo(User::class, 'operator_out_id');
    }

    /**
     * Generate unique ticket code
     */
    public static function generateCode(): string
    {
        $prefix = 'TKT-' . date('Ymd');
        $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
        $code = "{$prefix}-{$random}";

        while (self::where('ticket_code', $code)->exists()) {
            $random = strtoupper(substr(bin2hex(random_bytes(4)), 0, 5));
            $code = "{$prefix}-{$random}";
        }

        return $code;
    }

    /**
     * Generate SVG QR Code for ticket
     */
    public function getQrCodeSvgAttribute(): string
    {
        try {
            return QrCode::size(160)
                ->margin(1)
                ->generate($this->ticket_code);
        } catch (\Throwable $e) {
            // Fallback SVG QR placeholder if needed
            return '<div class="p-3 bg-dark text-center border border-success rounded text-success fw-bold font-monospace">' . e($this->ticket_code) . '</div>';
        }
    }

    /**
     * Calculate current duration string (e.g. "1 jam 25 mnt")
     */
    public function getFormattedDurationAttribute(): string
    {
        $start = $this->entry_time;
        $end = $this->exit_time ?? now();

        $diffMinutes = max(1, $start->diffInMinutes($end));
        $hours = floor($diffMinutes / 60);
        $minutes = $diffMinutes % 60;

        if ($hours > 0) {
            return "{$hours} jam {$minutes} mnt";
        }

        return "{$minutes} mnt";
    }

    /**
     * Get live fee calculation before exiting
     */
    public function calculateLiveFee(bool $isLost = false): array
    {
        $minutes = max(1, $this->entry_time->diffInMinutes(now()));
        return ParkingRate::calculateFee($this->vehicle_type, $minutes, $isLost);
    }
}
