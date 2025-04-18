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
        Schema::create('purchase_receipts', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('purchase_order_id')->index();
            $table->dateTime('posting_date')->index();
            $table->string('status');
            $table->double('total_qty');
            $table->string('created_by')->index();
            $table->string('updated_by')->index()->nullable();
            $table->string('submitted_by')->index()->nullable();
            $table->string('cancelled_by')->index()->nullable();
            $table->timestamps();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();

            $table->foreign('purchase_order_id')->references('id')->on('purchase_orders')->onDelete('restrict');
            $table->foreign('created_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('updated_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('submitted_by')->references('id')->on('users')->onDelete('restrict');
            $table->foreign('cancelled_by')->references('id')->on('users')->onDelete('restrict');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_receipts');
    }
};
