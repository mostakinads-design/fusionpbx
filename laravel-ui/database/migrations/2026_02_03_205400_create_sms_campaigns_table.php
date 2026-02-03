<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_campaigns', function (Blueprint $table) {
            $table->uuid('sms_campaign_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('campaign_name');
            $table->text('campaign_description')->nullable();
            $table->text('sms_template');
            $table->string('sender_id');
            $table->integer('sending_rate')->default(10);
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->enum('campaign_status', ['draft', 'scheduled', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->integer('total_contacts')->default(0);
            $table->integer('sent_count')->default(0);
            $table->integer('delivered_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->integer('opt_out_count')->default(0);
            $table->enum('schedule_type', ['immediate', 'scheduled', 'recurring'])->default('immediate');
            $table->timestamp('scheduled_at')->nullable();
            
            // AI Agent Integration
            $table->boolean('ai_enabled')->default(false);
            $table->json('ai_config')->nullable();
            $table->boolean('ai_reply_handling')->default(false);
            $table->string('ai_model')->nullable();
            $table->text('ai_personality')->nullable();
            $table->json('ai_conversation_context')->nullable();
            
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('campaign_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_campaigns');
    }
};
