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
        Schema::create('balance_transactions', function (Blueprint $table) {
            $table->uuid('transaction_uuid')->primary();
            $table->uuid('balance_uuid');
            $table->uuid('user_uuid');
            $table->enum('transaction_type', ['credit', 'debit'])->default('credit');
            $table->decimal('amount', 10, 2);
            $table->decimal('balance_before', 10, 2);
            $table->decimal('balance_after', 10, 2);
            $table->text('description')->nullable();
            $table->string('reference_id')->nullable();
            $table->string('payment_method')->nullable();
            $table->string('payment_gateway')->nullable();
            $table->string('gateway_transaction_id')->nullable();
            $table->enum('status', ['pending', 'completed', 'failed', 'refunded'])->default('pending');
            $table->json('metadata')->nullable();
            $table->timestamps();
            
            $table->foreign('balance_uuid')->references('balance_uuid')->on('user_balances')->onDelete('cascade');
            $table->index('user_uuid');
            $table->index('transaction_type');
            $table->index('status');
            $table->index('created_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('balance_transactions');
    }
};
