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
        Schema::create('manufacture_materials', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('manufacture_item_id')->index();
            $table->string('bom_id')->index();
            $table->string('item_id')->index();
            $table->double('required_quantity');
            $table->timestamps();

            $table->foreign('manufacture_item_id')->references('id')->on('manufacture_items')->onDelete('cascade');
            $table->foreign('bom_id')->references('id')->on('boms')->onDelete('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('manufacture_materials');
    }
};
