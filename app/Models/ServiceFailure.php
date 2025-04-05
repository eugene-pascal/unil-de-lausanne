<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
            return '-';
        }
        return gmdate('H:i:s', $this->duration_seconds);
    }
}
