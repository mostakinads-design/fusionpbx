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
        Schema::create('v_campaign_calls', function (Blueprint $table) {
            $table->uuid('campaign_call_uuid')->primary();
            $table->uuid('campaign_uuid')->index();
            $table->uuid('campaign_contact_uuid')->index();
            $table->uuid('domain_uuid')->index();
            $table->uuid('call_uuid')->nullable()->index();
            $table->string('call_status')->nullable(); // initiated, ringing, answered, completed, failed
            $table->integer('call_duration')->default(0);
            $table->boolean('call_answered')->default(false);
            $table->boolean('sms_sent')->default(false);
            $table->string('sms_status')->nullable(); // queued, sent, delivered, failed
            $table->json('ai_conversation')->nullable();
            $table->json('ai_extracted_data')->nullable();
            $table->timestamp('insert_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->uuid('insert_user')->nullable();
            $table->uuid('update_user')->nullable();
            
            $table->foreign('campaign_uuid')->references('campaign_uuid')->on('v_campaigns')->onDelete('cascade');
            $table->foreign('campaign_contact_uuid')->references('campaign_contact_uuid')->on('v_campaign_contacts')->onDelete('cascade');
            $table->foreign('domain_uuid')->references('domain_uuid')->on('v_domains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_campaign_calls');
    }
};
