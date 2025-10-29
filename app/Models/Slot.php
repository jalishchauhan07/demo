<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Slot extends Model
{
    protected $fillable = ['slot_date', 'slot_time', 'pickup_point', 'appointment'];

    protected $casts = [
        'appointment' => 'array',
        'slot_date' => 'date',
    ];

    /**
     * Scope to filter records from a specific date
     */
    public function scopeFromDate($query, $date)
    {
        if ($date) {
            return $query->whereDate('slot_date', '>=', $date);
        }
        return $query;
    }

    /**
     * Scope to apply sorting (by date or time)
     */
    public function scopeApplySort($query, $sort)
    {
        switch ($sort) {
            case 'date_desc':
                return $query->orderBy('slot_date', 'desc')->orderBy('slot_time', 'desc');
            case 'time_asc':
                return $query->orderBy('slot_time', 'asc');
            case 'time_desc':
                return $query->orderBy('slot_time', 'desc');
            default:
                // Default sort: earliest date/time first
                return $query->orderBy('slot_date', 'asc')->orderBy('slot_time', 'asc');
        }
    }
}
