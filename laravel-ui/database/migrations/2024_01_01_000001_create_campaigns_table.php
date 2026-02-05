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
        Schema::create('v_campaigns', function (Blueprint $table) {
            $table->uuid('campaign_uuid')->primary();
            $table->uuid('domain_uuid')->index();
            $table->string('campaign_name');
            $table->enum('campaign_type', ['predictive', 'progressive', 'preview', 'manual'])->default('progressive');
            $table->enum('broadcast_type', ['voice', 'sms', 'voice_sms'])->default('voice');
            $table->boolean('ai_enabled')->default(false);
            $table->string('ai_provider')->nullable(); // openai, anthropic, google, custom
            $table->string('ai_model')->nullable(); // gpt-4, claude-3-opus, gemini-pro
            $table->text('ai_prompt')->nullable();
            $table->text('voice_message')->nullable();
            $table->text('sms_message')->nullable();
            $table->string('caller_id_number')->nullable();
            $table->string('caller_id_name')->nullable();
            $table->timestamp('scheduled_start')->nullable();
            $table->integer('max_retry_attempts')->default(3);
            $table->enum('status', ['draft', 'scheduled', 'running', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_contacts')->default(0);
            $table->integer('calls_made')->default(0);
            $table->integer('calls_answered')->default(0);
            $table->integer('calls_failed')->default(0);
            $table->integer('sms_sent')->default(0);
            $table->integer('sms_delivered')->default(0);
            $table->timestamp('insert_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->uuid('insert_user')->nullable();
            $table->uuid('update_user')->nullable();
            
            $table->foreign('domain_uuid')->references('domain_uuid')->on('v_domains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_campaigns');
    }
};
