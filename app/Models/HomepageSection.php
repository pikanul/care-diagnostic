<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HomepageSection extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'section_key',
        'section_label_en',
        'section_label_bn',
        'is_active',
        'display_order',
        'section_type',
        'background_color',
        'background_image_path',
        'desktop_image_path',
        'mobile_image_path',
        'accent_image_path',
        'title_en',
        'title_bn',
        'subtitle_en',
        'subtitle_bn',
        'summary_en',
        'summary_bn',
        'content_en',
        'content_bn',
        'section_data',
        'related_doctor_refs',
        'related_service_refs',
        'related_test_refs',
        'related_post_refs',
        'auto_scroll',
        'carousel_speed',
        'display_limit',
        'preview_enabled',
        'created_by',
        'updated_by',
    ];

    protected function casts(): array
    {
        return [
            'is_active' => 'boolean',
            'auto_scroll' => 'boolean',
            'preview_enabled' => 'boolean',
            'section_data' => 'array',
            'related_doctor_refs' => 'array',
            'related_service_refs' => 'array',
            'related_test_refs' => 'array',
            'related_post_refs' => 'array',
        ];
    }
}
