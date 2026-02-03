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
        Schema::create('campaigns', function (Blueprint $table) {
            $table->uuid('campaign_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('campaign_name');
            $table->text('campaign_description')->nullable();
            $table->enum('campaign_type', ['predictive', 'progressive', 'preview', 'manual'])->default('progressive');
            $table->enum('dialing_mode', ['power', 'predictive', 'preview'])->default('preview');
            $table->integer('max_attempts')->default(3);
            $table->integer('retry_delay')->default(3600); // seconds
            $table->integer('call_timeout')->default(30); // seconds
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->json('days_of_week')->nullable();
            $table->boolean('ai_agent_enabled')->default(false);
            $table->json('ai_agent_config')->nullable();
            $table->enum('campaign_status', ['draft', 'active', 'paused', 'completed', 'archived'])->default('draft');
            $table->string('caller_id_name')->nullable();
            $table->string('caller_id_number')->nullable();
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('campaign_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaigns');
    }
};
