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
        Schema::create('campaign_agents', function (Blueprint $table) {
            $table->uuid('campaign_uuid');
            $table->uuid('agent_uuid');
            $table->timestamps();
            
            $table->primary(['campaign_uuid', 'agent_uuid']);
            $table->foreign('campaign_uuid')->references('campaign_uuid')->on('campaigns')->onDelete('cascade');
            $table->foreign('agent_uuid')->references('agent_uuid')->on('agents')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_agents');
    }
};
