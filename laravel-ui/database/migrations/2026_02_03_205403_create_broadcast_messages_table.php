<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('broadcast_messages', function (Blueprint $table) {
            $table->uuid('broadcast_message_uuid')->primary();
            $table->uuid('broadcast_uuid');
            $table->uuid('contact_uuid')->nullable();
            $table->string('phone_number');
            $table->enum('call_status', ['pending', 'calling', 'ringing', 'answered', 'completed', 'failed', 'busy', 'no_answer'])->default('pending');
            $table->integer('call_duration')->default(0);
            $table->boolean('call_answered')->default(false);
            $table->integer('attempts')->default(0);
            $table->timestamp('last_attempt_at')->nullable();
            $table->timestamp('completed_at')->nullable();
            $table->string('recording_url')->nullable();
            $table->text('failure_reason')->nullable();
            
            // AI Conversation Data
            $table->boolean('ai_conversation_occurred')->default(false);
            $table->json('ai_conversation_log')->nullable();
            $table->text('ai_transcription')->nullable();
            $table->json('ai_sentiment_analysis')->nullable();
            $table->json('ai_intent_detection')->nullable();
            $table->json('ai_key_phrases')->nullable();
            $table->integer('ai_conversation_turns')->default(0);
            $table->enum('ai_conversation_outcome', ['positive', 'neutral', 'negative', 'unknown'])->nullable();
            
            $table->timestamps();
            
            $table->foreign('broadcast_uuid')->references('broadcast_uuid')->on('voice_broadcasts')->onDelete('cascade');
            $table->index('phone_number');
            $table->index('call_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('broadcast_messages');
    }
};
