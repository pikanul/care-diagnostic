<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('web_vital_logs', function (Blueprint $table): void {
            $table->id();
            $table->string('metric', 40)->index();
            $table->decimal('value', 12, 3);
            $table->string('rating', 40)->nullable()->index();
            $table->string('path', 512)->index();
            $table->string('url', 2048);
            $table->string('locale', 5)->nullable()->index();
            $table->string('session_id', 120)->nullable()->index();
            $table->string('device_type', 40)->nullable()->index();
            $table->string('browser', 120)->nullable();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['metric', 'created_at']);
            $table->index(['path', 'created_at']);
        });

        Schema::create('website_error_logs', function (Blueprint $table): void {
            $table->id();
            $table->unsignedSmallInteger('status_code')->index();
            $table->string('method', 10)->nullable();
            $table->string('path', 512)->index();
            $table->string('url', 2048);
            $table->string('referrer', 2048)->nullable();
            $table->string('ip_address', 45)->nullable()->index();
            $table->string('user_agent', 1024)->nullable();
            $table->string('locale', 5)->nullable()->index();
            $table->string('session_id', 120)->nullable()->index();
            $table->json('metadata')->nullable();
            $table->timestamps();

            $table->index(['status_code', 'created_at']);
            $table->index(['path', 'status_code']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('website_error_logs');
        Schema::dropIfExists('web_vital_logs');
    }
};
