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
        Schema::table('product_storage_locations', function (Blueprint $table) {
            $table->unique(['product_id', 'storage_location_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('product_storage_locations', function (Blueprint $table) {
            $table->dropForeign(['product_id']);
            $table->dropForeign(['storage_location_id']);
            $table->dropUnique(['product_id', 'storage_location_id']);

            $table->foreign('product_id')->references('id')->on('products')->onDelete('cascade');
            $table->foreign('storage_location_id')->references('id')->on('storage_locations')->onDelete('cascade');
        });
    }
};
