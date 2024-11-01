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
        Schema::create('posts', function (Blueprint $table) {
            $table->id();
            $table->string('type')->default(\App\Enums\PostTypeEnum::POST);
            $table->foreignId('user_id')->nullable();
            $table->string('title')->nullable();
            $table->text('content');
            $table->string('status')->default(\App\Enums\PostStatusEnum::DRAFT);
            $table->foreignId('parent_id')->nullable();
            $table->foreignId('original_parent_id')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('posts');
    }
};
