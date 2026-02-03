<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('voice_broadcasts', function (Blueprint $table) {
            $table->uuid('broadcast_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('broadcast_name');
            $table->text('broadcast_description')->nullable();
            $table->string('audio_file_path')->nullable();
            $table->string('audio_url')->nullable();
            
            // Text-to-Speech Options
            $table->boolean('text_to_speech')->default(false);
            $table->text('tts_text')->nullable();
            $table->string('tts_voice')->nullable();
            $table->string('tts_language')->default('en-US');
            
            $table->string('caller_id_name')->nullable();
            $table->string('caller_id_number')->nullable();
            $table->integer('max_retries')->default(3);
            $table->integer('retry_delay')->default(300);
            $table->integer('call_timeout')->default(30);
            $table->enum('broadcast_status', ['draft', 'scheduled', 'active', 'paused', 'completed', 'cancelled'])->default('draft');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->timestamp('scheduled_at')->nullable();
            $table->integer('total_contacts')->default(0);
            $table->integer('called_count')->default(0);
            $table->integer('answered_count')->default(0);
            $table->integer('failed_count')->default(0);
            $table->decimal('completion_rate', 5, 2)->default(0);
            
            // AI Agent Integration
            $table->boolean('ai_enabled')->default(false);
            $table->json('ai_config')->nullable();
            $table->boolean('ai_conversation_mode')->default(false);
            $table->string('ai_model')->nullable();
            $table->text('ai_system_prompt')->nullable();
            $table->json('ai_voice_settings')->nullable();
            $table->boolean('ai_speech_recognition')->default(false);
            $table->boolean('ai_natural_language')->default(false);
            $table->integer('ai_max_conversation_turns')->default(5);
            
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('broadcast_status');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('voice_broadcasts');
    }
};
