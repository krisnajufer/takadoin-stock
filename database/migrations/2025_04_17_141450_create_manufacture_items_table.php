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
        Schema::create('manufacture_items', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('manufacture_id')->index();
            $table->string('item_id')->index();
            $table->string('bom_id')->index();
            $table->double('quantity');
            $table->timestamps();

            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('cascade');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
            $table->foreign('bom_id')->references('id')->on('boms')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_items');
    }
};
