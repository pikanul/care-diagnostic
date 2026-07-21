<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WebsiteErrorLog extends Model
{
    protected $fillable = [
        'status_code',
        'method',
        'path',
        'url',
        'referrer',
        'ip_address',
        'user_agent',
        'locale',
        'session_id',
        'metadata',
    ];

    protected function casts(): array
    {
        return [
            'metadata' => 'array',
        ];
    }
}
