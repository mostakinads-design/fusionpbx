<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sms_campaign_contacts', function (Blueprint $table) {
            $table->uuid('sms_campaign_uuid');
            $table->uuid('contact_uuid');
            $table->timestamps();
            
            $table->primary(['sms_campaign_uuid', 'contact_uuid']);
            $table->foreign('sms_campaign_uuid')->references('sms_campaign_uuid')->on('sms_campaigns')->onDelete('cascade');
            $table->foreign('contact_uuid')->references('contact_uuid')->on('campaign_contacts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sms_campaign_contacts');
    }
};
