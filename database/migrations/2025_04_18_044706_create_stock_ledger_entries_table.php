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
        Schema::create('stock_ledger_entries', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->dateTime('posting_date');
            $table->string('transaction_type');
            $table->string('transaction_id')->index();
            $table->string('transaction_item_id')->index();
            $table->double('actual_quantity');
            $table->double('qty_after_transaction');
            $table->string('created_by')->index();
            $table->string('updated_by')->index()->nullable();
            $table->string('submitted_by')->index()->nullable();
            $table->string('cancelled_by')->index()->nullable();
            $table->timestamps();
            $table->dateTime('submitted_at')->nullable();
            $table->dateTime('cancelled_at')->nullable();

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
        Schema::dropIfExists('stock_ledger_entries');
    }
};
