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
        Schema::table('books', function (Blueprint $table) {
            $table->integer('price')->nullable()->after('isbn')->comment('정가');
            $table->integer('sale_price')->nullable()->after('isbn')->comment('판매가');
            $table->string('link')->nullable()->after('isbn')->comment('링크');
            $table->string('type')->nullable()->after('isbn')->comment('타입(교보문고...등)');
            $table->string('product_id')->nullable()->after('isbn')->comment('상품 아이디');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('books', function (Blueprint $table) {
            $table->dropColumn('price');
            $table->dropColumn('sale_price');
            $table->dropColumn('link');
            $table->dropColumn('type');
            $table->dropColumn('product_id');
        });
    }
};
