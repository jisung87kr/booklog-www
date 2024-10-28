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
        Schema::create('quotations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('original_reading_process_id')->comment('원본 독서 과정')->constrained('reading_processes', 'id')->onDelete('cascade');
            $table->foreignId('quoting_user_id')->comment('인용한 사용자')->constrained('users', 'id')->onDelete('cascade');
            $table->text('content')->comment('인용 내용');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('quotations');
    }
};
