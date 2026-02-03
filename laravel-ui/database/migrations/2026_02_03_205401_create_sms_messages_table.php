<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_messages', function (Blueprint $table) {
            $table->uuid('sms_message_uuid')->primary();
            $table->uuid('sms_campaign_uuid')->nullable();
            $table->uuid('contact_uuid')->nullable();
            $table->string('phone_number');
            $table->text('message_content');
            $table->enum('status', ['pending', 'sent', 'delivered', 'failed', 'opt_out'])->default('pending');
            $table->timestamp('sent_at')->nullable();
            $table->timestamp('delivered_at')->nullable();
            $table->timestamp('failed_at')->nullable();
            $table->text('failure_reason')->nullable();
            $table->string('provider_message_id')->nullable();
            $table->string('provider_name')->nullable();
            $table->decimal('cost', 10, 4)->default(0);
            $table->enum('direction', ['inbound', 'outbound'])->default('outbound');
            
            // AI Response Handling
            $table->boolean('ai_processed')->default(false);
            $table->text('ai_response')->nullable();
            $table->json('ai_sentiment')->nullable();
            $table->json('ai_intent')->nullable();
            $table->json('ai_entities')->nullable();
            
            $table->timestamps();
            
            $table->foreign('sms_campaign_uuid')->references('sms_campaign_uuid')->on('sms_campaigns')->onDelete('cascade');
            $table->index('phone_number');
            $table->index('status');
            $table->index('direction');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_messages');
    }
};
