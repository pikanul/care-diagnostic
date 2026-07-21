<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebVitalLog extends Model
{
    protected $fillable = [
        'metric',
        'value',
        'rating',
        'path',
        'url',
        'locale',
        'session_id',
        'device_type',
        'browser',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'value' => 'decimal:3',
            'metadata' => 'array',
        ];
    }
}
