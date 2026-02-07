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
        Schema::create('v_campaign_contacts', function (Blueprint $table) {
            $table->uuid('campaign_contact_uuid')->primary();
            $table->uuid('campaign_uuid')->index();
            $table->uuid('domain_uuid')->index();
            $table->string('contact_name');
            $table->string('contact_phone');
            $table->string('contact_email')->nullable();
            $table->enum('contact_status', ['pending', 'calling', 'answered', 'no_answer', 'busy', 'failed', 'completed'])->default('pending');
            $table->integer('call_attempts')->default(0);
            $table->timestamp('last_call_time')->nullable();
            $table->boolean('sms_sent')->default(false);
            $table->boolean('sms_delivered')->default(false);
            $table->timestamp('insert_date')->nullable();
            $table->timestamp('update_date')->nullable();
            $table->uuid('insert_user')->nullable();
            $table->uuid('update_user')->nullable();
            
            $table->foreign('campaign_uuid')->references('campaign_uuid')->on('v_campaigns')->onDelete('cascade');
            $table->foreign('domain_uuid')->references('domain_uuid')->on('v_domains')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_campaign_contacts');
    }
};
