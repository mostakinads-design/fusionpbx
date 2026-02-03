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
        Schema::create('campaign_calls', function (Blueprint $table) {
            $table->uuid('call_uuid')->primary();
            $table->uuid('campaign_uuid');
            $table->uuid('contact_uuid');
            $table->uuid('agent_uuid')->nullable();
            $table->string('call_sid')->nullable();
            $table->string('phone_number');
            $table->enum('call_status', ['initiated', 'ringing', 'answered', 'completed', 'failed', 'busy', 'no_answer'])->default('initiated');
            $table->enum('disposition', ['answer', 'no_answer', 'busy', 'failed', 'callback', 'interested', 'not_interested', 'do_not_call'])->nullable();
            $table->integer('call_duration')->default(0);
            $table->timestamp('answered_at')->nullable();
            $table->timestamp('ended_at')->nullable();
            $table->string('recording_url')->nullable();
            $table->text('notes')->nullable();
            $table->boolean('is_ai_handled')->default(false);
            $table->json('ai_transcript')->nullable();
            $table->string('ai_sentiment')->nullable();
            $table->timestamps();
            
            $table->foreign('campaign_uuid')->references('campaign_uuid')->on('campaigns')->onDelete('cascade');
            $table->foreign('contact_uuid')->references('contact_uuid')->on('campaign_contacts')->onDelete('cascade');
            $table->index('campaign_uuid');
            $table->index('contact_uuid');
            $table->index('agent_uuid');
            $table->index('call_status');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_calls');
    }
};
