<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('outbound_routes', function (Blueprint $table) {
            $table->uuid('route_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('route_name');
            $table->text('route_description')->nullable();
            $table->integer('route_order')->default(0);
            $table->string('dial_prefix')->nullable();
            $table->integer('prefix_strip')->default(0);
            $table->string('destination_pattern');
            $table->uuid('gateway_uuid')->nullable();
            $table->string('gateway_name')->nullable();
            $table->boolean('is_active')->default(true);
            $table->boolean('is_emergency')->default(false);
            $table->enum('route_type', ['voice', 'sms', 'both'])->default('voice');
            $table->string('caller_id_name')->nullable();
            $table->string('caller_id_number')->nullable();
            $table->integer('limit_max')->nullable();
            $table->string('account_code')->nullable();
            
            // AI-Powered Routing
            $table->boolean('ai_routing_enabled')->default(false);
            $table->json('ai_routing_rules')->nullable();
            $table->boolean('ai_cost_optimization')->default(false);
            $table->boolean('ai_quality_optimization')->default(false);
            $table->json('ai_routing_weights')->nullable();
            
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('route_order');
            $table->index('is_active');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('outbound_routes');
    }
};
