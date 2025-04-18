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
        Schema::create('stock_management_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('stock_management_id')->index();
            $table->string('item_id')->index();
            $table->double('quantity');
            $table->timestamps();

            $table->foreign('stock_management_id')->references('id')->on('stock_managements')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('stock_management_items');
    }
};
