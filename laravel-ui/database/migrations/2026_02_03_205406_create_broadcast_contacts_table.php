<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_contacts', function (Blueprint $table) {
            $table->uuid('broadcast_uuid');
            $table->uuid('contact_uuid');
            $table->timestamps();
            
            $table->primary(['broadcast_uuid', 'contact_uuid']);
            $table->foreign('broadcast_uuid')->references('broadcast_uuid')->on('voice_broadcasts')->onDelete('cascade');
            $table->foreign('contact_uuid')->references('contact_uuid')->on('campaign_contacts')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_contacts');
    }
};
