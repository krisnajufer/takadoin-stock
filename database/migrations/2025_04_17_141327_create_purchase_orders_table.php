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
        Schema::create('purchase_orders', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->string('supplier_id')->index();
            $table->dateTime('posting_date')->index();
            $table->string('status');
            $table->double('total_qty');
            $table->double('grand_total');
            $table->string('created_by');
            $table->string('updated_by')->nullable();
            $table->string('submitted_by')->nullable();
            $table->string('cancelled_by')->nullable();
            $table->timestamps();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('purchase_orders');
    }
};
