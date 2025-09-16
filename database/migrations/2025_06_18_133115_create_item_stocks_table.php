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
        Schema::create('item_stocks', function (Blueprint $table) {
            $table->id();
            $table->string('item_id');
            $table->integer('actual_qty')->default('0');
            $table->integer('issue_qty')->default('0');
            $table->integer('purchase_qty')->default('0');
            $table->integer('safety_stock')->default('0');
            $table->integer('min')->default('0');
            $table->integer('max')->default('0');
            $table->timestamps();

            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('item_stocks');
    }
};
