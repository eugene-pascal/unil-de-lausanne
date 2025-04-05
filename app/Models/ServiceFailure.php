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
        if (!$this->duration_seconds) {
            if ($this->started_at) {
                $duration = $this->started_at->diffInSeconds(now());
                return gmdate('H:i:s', $duration);
            }
            return '-//-';
        }
        return gmdate('H:i:s', $this->duration_seconds);
    }

    public function lastStatus(): HasOne
    {
        return $this->hasOne(ServiceStatus::class, 'service_name', 'service_name')
            ->latest('checked_at');
    }
}
