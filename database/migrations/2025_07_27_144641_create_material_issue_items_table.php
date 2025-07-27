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
        Schema::create('material_issue_items', function (Blueprint $table) {
            $table->id();
            $table->string('material_issue_id');
            $table->string('item_id');
            $table->integer('qty');
            $table->integer('price');
            $table->integer('amount');
            $table->timestamps();

            $table->foreign('material_issue_id')->references('id')->on('material_issues')->onDelete('restrict');
            $table->foreign('item_id')->references('id')->on('items')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_issue_items');
    }
};
