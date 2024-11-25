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
        Schema::table('users', function (Blueprint $table) {
            $table->text('introduction')->nullable()->after('email')->comment('소개글');
            $table->string('link')->nullable()->after('email')->comment('회원링크');
            $table->boolean('is_secret')->default(false)->after('email')->comment('비공개여부');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('users', function (Blueprint $table) {
            $table->dropColumn('introduction');
            $table->dropColumn('link');
            $table->dropColumn('is_secret');
        });
    }
};
