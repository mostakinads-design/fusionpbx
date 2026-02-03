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
        Schema::create('dids', function (Blueprint $table) {
            $table->uuid('did_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('did_number')->unique();
            $table->text('did_description')->nullable();
            $table->enum('destination_type', ['extension', 'ivr', 'queue', 'external', 'voicemail'])->default('extension');
            $table->string('destination_number');
            $table->uuid('extension_uuid')->nullable();
            $table->uuid('ivr_uuid')->nullable();
            $table->uuid('queue_uuid')->nullable();
            $table->enum('routing_type', ['voice_only', 'sms_only', 'voice_and_sms'])->default('voice_only');
            $table->boolean('voice_enabled')->default(true);
            $table->boolean('sms_enabled')->default(false);
            $table->string('country_code', 5)->nullable();
            $table->string('area_code', 10)->nullable();
            $table->boolean('is_active')->default(true);
            $table->string('caller_id_name')->nullable();
            $table->string('caller_id_number')->nullable();
            $table->boolean('record_calls')->default(false);
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('did_number');
            $table->index('is_active');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('dids');
    }
};
