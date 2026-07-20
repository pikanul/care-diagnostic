<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('homepage_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section_key')->unique();
            $table->string('section_label_en');
            $table->string('section_label_bn');
            $table->boolean('is_active')->default(true)->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->string('section_type')->default('content')->index();
            $table->string('background_color')->nullable();
            $table->string('background_image_path')->nullable();
            $table->string('desktop_image_path')->nullable();
            $table->string('mobile_image_path')->nullable();
            $table->string('accent_image_path')->nullable();
            $table->string('title_en')->nullable();
            $table->string('title_bn')->nullable();
            $table->string('subtitle_en')->nullable();
            $table->string('subtitle_bn')->nullable();
            $table->text('summary_en')->nullable();
            $table->text('summary_bn')->nullable();
            $table->longText('content_en')->nullable();
            $table->longText('content_bn')->nullable();
            $table->json('section_data')->nullable();
            $table->json('related_doctor_refs')->nullable();
            $table->json('related_service_refs')->nullable();
            $table->json('related_test_refs')->nullable();
            $table->json('related_post_refs')->nullable();
            $table->boolean('auto_scroll')->default(false);
            $table->unsignedSmallInteger('carousel_speed')->default(4000);
            $table->unsignedSmallInteger('display_limit')->nullable();
            $table->boolean('preview_enabled')->default(true);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('homepage_sections');
    }
};
