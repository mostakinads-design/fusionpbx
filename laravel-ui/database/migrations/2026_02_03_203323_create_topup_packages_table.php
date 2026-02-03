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
        Schema::create('topup_packages', function (Blueprint $table) {
            $table->uuid('package_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('package_name');
            $table->text('package_description')->nullable();
            $table->decimal('amount', 10, 2);
            $table->decimal('bonus_amount', 10, 2)->default(0.00);
            $table->decimal('price', 10, 2);
            $table->string('currency', 3)->default('USD');
            $table->integer('validity_days')->nullable();
            $table->boolean('is_active')->default(true);
            $table->integer('display_order')->default(0);
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('topup_packages');
    }
};
