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
        Schema::create('book_categories', function (Blueprint $table) {
            $table->id();
            $table->string('book_type');
            $table->string('category_code');
            $table->string('depth1');
            $table->string('depth2')->nullable();
            $table->string('depth3')->nullable();
            $table->string('depth4')->nullable();
            $table->string('depth5')->nullable();
            $table->string('depth6')->nullable();
            $table->timestamps();
            $table->unique(['book_type', 'category_code']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('book_categories');
    }
};
