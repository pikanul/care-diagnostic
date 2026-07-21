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
        Schema::table('global_settings', function (Blueprint $table): void {
            $table->json('seo_settings')->nullable()->after('google_map_embed');
            $table->json('marketing_tools')->nullable()->after('seo_settings');
            $table->json('sms_settings')->nullable()->after('marketing_tools');
            $table->json('email_integration_settings')->nullable()->after('sms_settings');
            $table->json('visitor_tracking_settings')->nullable()->after('email_integration_settings');
        });

        Schema::create('visitor_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('session_id', 120)->nullable()->index();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('locale', 5)->nullable()->index();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('hashed_ip', 64)->nullable()->index();
            $table->string('method', 10)->nullable();
            $table->string('url', 2048);
            $table->string('path', 1024)->index();
            $table->string('query_string', 2048)->nullable();
            $table->string('referrer', 2048)->nullable();
            $table->string('landing_page', 2048)->nullable();
            $table->string('user_agent', 1024)->nullable();
            $table->string('browser', 120)->nullable();
            $table->string('platform', 120)->nullable();
            $table->string('device_type', 40)->nullable()->index();
            $table->boolean('is_bot')->default(false)->index();
            $table->string('utm_source')->nullable()->index();
            $table->string('utm_medium')->nullable()->index();
            $table->string('utm_campaign')->nullable()->index();
            $table->string('utm_term')->nullable();
            $table->string('utm_content')->nullable();
            $table->string('country', 120)->nullable()->index();
            $table->string('country_code', 10)->nullable()->index();
            $table->string('region', 120)->nullable();
            $table->string('city', 120)->nullable()->index();
            $table->string('timezone', 80)->nullable();
            $table->decimal('latitude', 10, 7)->nullable();
            $table->decimal('longitude', 10, 7)->nullable();
            $table->json('headers')->nullable();
            $table->timestamp('last_seen_at')->nullable()->index();
            $table->timestamps();

            $table->index(['created_at', 'is_bot']);
            $table->index(['session_id', 'created_at']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('visitor_logs');

        Schema::table('global_settings', function (Blueprint $table): void {
            $table->dropColumn([
                'seo_settings',
                'marketing_tools',
                'sms_settings',
                'email_integration_settings',
                'visitor_tracking_settings',
            ]);
        });
    }
};
