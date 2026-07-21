<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitorLog extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'locale',
        'ip_address',
        'hashed_ip',
        'method',
        'url',
        'path',
        'query_string',
        'referrer',
        'landing_page',
        'user_agent',
        'browser',
        'platform',
        'device_type',
        'is_bot',
        'utm_source',
        'utm_medium',
        'utm_campaign',
        'utm_term',
        'utm_content',
        'country',
        'country_code',
        'region',
        'city',
        'timezone',
        'latitude',
        'longitude',
        'headers',
        'last_seen_at',
    ];

    protected function casts(): array
    {
        return [
            'headers' => 'array',
            'is_bot' => 'boolean',
            'latitude' => 'decimal:7',
            'longitude' => 'decimal:7',
            'last_seen_at' => 'datetime',
        ];
    }
}
