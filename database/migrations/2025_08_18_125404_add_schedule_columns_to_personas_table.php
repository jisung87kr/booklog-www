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
        Schema::table('personas', function (Blueprint $table) {
            $table->boolean('auto_publish_enabled')->default(false)->after('is_active');
            $table->string('publish_frequency')->nullable()->after('auto_publish_enabled'); // daily, weekly, hourly
            $table->json('publish_schedule')->nullable()->after('publish_frequency'); // 스케줄 상세 설정
            $table->timestamp('last_published_at')->nullable()->after('publish_schedule');
            $table->timestamp('next_publish_at')->nullable()->after('last_published_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('personas', function (Blueprint $table) {
            $table->dropColumn([
                'auto_publish_enabled',
                'publish_frequency', 
                'publish_schedule',
                'last_published_at',
                'next_publish_at'
            ]);
        });
    }
};
