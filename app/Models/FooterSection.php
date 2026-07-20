<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class FooterSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'title_en',
        'title_bn',
        'display_order',
        'is_active',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
        ];
    }

    public function links(): HasMany
    {
        return $this->hasMany(FooterLink::class);
    }
}
