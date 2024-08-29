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
        Schema::create('reading_processes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('book_user_id');
            $table->foreignId('user_id');
            $table->foreignId('book_id');
            $table->foreignId('parent_id');
            $table->integer('current_page');
            $table->text('note')->nullable();
            $table->string('photo')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('reading_processes');
    }
};
