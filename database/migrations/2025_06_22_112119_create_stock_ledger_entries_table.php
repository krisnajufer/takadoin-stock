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
            $table->string('transaction_type');
            $table->string('transaction_id');
            $table->string('item_id');
            $table->date('posting_date');
            $table->time('posting_time');
            $table->integer('qty_change');
            $table->integer('qty_after_transaction');
            $table->timestamps();
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
