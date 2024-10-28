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
        Schema::create('follows', function (Blueprint $table) {
            $table->id();
            $table->foreignId('follow_id')->comment('팔로우 하는 사람')->constrained('users', 'id')->onDelete('cascade');
            $table->foreignId('following_id')->comment('팔로우 받는 사람')->constrained('users', 'id')->onDelete('cascade');
            $table->unique(['follow_id', 'following_id']);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('follows');
    }
};
