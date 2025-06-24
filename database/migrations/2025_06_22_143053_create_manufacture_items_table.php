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
            $table->id();
            $table->string('manufacture_id');
            $table->string('item_id');
            $table->string('bom_id');
            $table->integer('qty');
            $table->timestamps();


            $table->foreign('manufacture_id')->references('id')->on('manufactures')->onDelete('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
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
