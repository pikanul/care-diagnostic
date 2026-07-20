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
        Schema::create('global_settings', function (Blueprint $table) {
            $table->id();
            $table->string('hospital_name_en');
            $table->string('hospital_name_bn');
            $table->string('logo_path')->nullable();
            $table->string('logo_mobile_path')->nullable();
            $table->string('favicon_path')->nullable();
            $table->text('address_en')->nullable();
            $table->text('address_bn')->nullable();
            $table->string('phone_primary')->nullable();
            $table->string('phone_secondary')->nullable();
            $table->string('emergency_number')->nullable();
            $table->string('email')->nullable();
            $table->text('opening_hours_en')->nullable();
            $table->text('opening_hours_bn')->nullable();
            $table->json('social_links')->nullable();
            $table->string('whatsapp_link')->nullable();
            $table->text('google_map_embed')->nullable();
            $table->string('default_language', 5)->default('en')->index();
            $table->boolean('contact_buttons_visible')->default(true);
            $table->boolean('header_top_bar_visible')->default(true);
            $table->boolean('newsletter_visible')->default(false);
            $table->string('book_appointment_button_label_en')->nullable();
            $table->string('book_appointment_button_label_bn')->nullable();
            $table->string('book_appointment_button_url')->nullable();
            $table->string('emergency_button_label_en')->nullable();
            $table->string('emergency_button_label_bn')->nullable();
            $table->string('emergency_button_url')->nullable();
            $table->text('footer_description_en')->nullable();
            $table->text('footer_description_bn')->nullable();
            $table->text('copyright_text_en')->nullable();
            $table->text('copyright_text_bn')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('navigation_items', function (Blueprint $table) {
            $table->id();
            $table->string('label_en');
            $table->string('label_bn');
            $table->string('url');
            $table->foreignId('parent_id')->nullable()->constrained('navigation_items')->nullOnDelete();
            $table->string('location', 30)->default('header')->index();
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('open_in_new_tab')->default(false);
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('footer_sections', function (Blueprint $table) {
            $table->id();
            $table->string('title_en');
            $table->string('title_bn');
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->foreignId('updated_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('footer_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('footer_section_id')->constrained()->cascadeOnDelete();
            $table->string('label_en');
            $table->string('label_bn');
            $table->string('url');
            $table->unsignedInteger('display_order')->default(0)->index();
            $table->boolean('is_active')->default(true)->index();
            $table->boolean('open_in_new_tab')->default(false);
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
        Schema::dropIfExists('footer_links');
        Schema::dropIfExists('footer_sections');
        Schema::dropIfExists('navigation_items');
        Schema::dropIfExists('global_settings');
    }
};
