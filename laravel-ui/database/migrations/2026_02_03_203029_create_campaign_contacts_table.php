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
        Schema::create('campaign_contacts', function (Blueprint $table) {
            $table->uuid('contact_uuid')->primary();
            $table->uuid('campaign_uuid');
            $table->string('first_name');
            $table->string('last_name')->nullable();
            $table->string('phone_number');
            $table->string('email')->nullable();
            $table->json('custom_data')->nullable();
            $table->enum('status', ['new', 'dialing', 'connected', 'no_answer', 'busy', 'failed', 'completed', 'do_not_call'])->default('new');
            $table->integer('priority')->default(5);
            $table->integer('attempts')->default(0);
            $table->timestamp('last_call_at')->nullable();
            $table->timestamp('next_call_at')->nullable();
            $table->timestamps();
            
            $table->foreign('campaign_uuid')->references('campaign_uuid')->on('campaigns')->onDelete('cascade');
            $table->index('campaign_uuid');
            $table->index('status');
            $table->index('next_call_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('campaign_contacts');
    }
};
