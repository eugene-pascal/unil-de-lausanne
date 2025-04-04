<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ServiceStatus extends Model
{
    protected $table = 'service_statuses';

    public $timestamps = false;

    protected $fillable = [
        'service_name',
        'type',
        'status',
        'full_response',
        'issues',
        'extra',
        'checked_at',
    ];

    protected $casts = [
        'full_response' => 'array',
        'issues' => 'array',
        'checked_at' => 'datetime',
    ];
}
