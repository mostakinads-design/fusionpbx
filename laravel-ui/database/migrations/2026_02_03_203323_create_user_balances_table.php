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
        Schema::create('user_balances', function (Blueprint $table) {
            $table->uuid('balance_uuid')->primary();
            $table->uuid('user_uuid');
            $table->uuid('domain_uuid');
            $table->decimal('balance', 10, 2)->default(0.00);
            $table->string('currency', 3)->default('USD');
            $table->decimal('credit_limit', 10, 2)->default(0.00);
            $table->boolean('auto_recharge_enabled')->default(false);
            $table->decimal('auto_recharge_amount', 10, 2)->nullable();
            $table->decimal('auto_recharge_threshold', 10, 2)->nullable();
            $table->decimal('low_balance_alert_threshold', 10, 2)->default(10.00);
            $table->timestamps();
            
            $table->index('user_uuid');
            $table->index('domain_uuid');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('user_balances');
    }
};
