<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class ServiceFailure extends Model
{
    protected $table = 'service_failures';

    public $timestamps = false;

    protected $fillable = [
        'service_name',
        'type',
        'started_at',
        'ended_at',
        'duration_seconds',
    ];

    protected $casts = [
        'started_at' => 'datetime',
        'ended_at' => 'datetime',
    ];

    public function getDurationFormattedAttr(): string
    {
        $seconds = $this->duration_seconds;

        if (!$seconds) {
            if ($this->started_at) {
                $seconds = $this->started_at->diffInSeconds(now());
            } else {
                return '-//-';
            }
        }

        $days = floor($seconds / 86400);
        $hours = floor(($seconds % 86400) / 3600);
        $minutes = floor(($seconds % 3600) / 60);
        $remainingSeconds = $seconds % 60;

        if ($days > 0) {
            return sprintf('%dd %02d:%02d:%02d', $days, $hours, $minutes, $remainingSeconds);
        }

        return sprintf('%02d:%02d:%02d', $hours, $minutes, $remainingSeconds);
    }


    public function lastStatus(): HasOne
    {
        return $this->hasOne(ServiceStatus::class, 'service_name', 'service_name')
            ->latest('checked_at');
    }
}
