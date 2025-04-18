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
        Schema::create('sales_orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('customer_id')->index();
            $table->dateTime('posting_date')->index();
            $table->string('order_status')->index();
            $table->string('payment_status')->index();
            $table->double('total_quantity');
            $table->double('total_amount');
            $table->double('grand_total');
            $table->double('outstanding_amount');
            $table->string('created_by')->index();
            $table->string('updated_by')->index()->nullable();
            $table->string('submitted_by')->index()->nullable();
            $table->string('cancelled_by')->index()->nullable();
            $table->timestamps();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();

            $table->foreign('customer_id')->references('id')->on('customers')->onDelete('restrict');
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
        Schema::dropIfExists('sales_orders');
    }
};
