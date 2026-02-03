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
        Schema::create('agents', function (Blueprint $table) {
            $table->uuid('agent_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->uuid('user_uuid')->nullable();
            $table->uuid('extension_uuid')->nullable();
            $table->string('agent_name');
            $table->enum('agent_type', ['human', 'ai'])->default('human');
            $table->enum('agent_status', ['available', 'on_call', 'break', 'offline'])->default('offline');
            $table->integer('max_concurrent_calls')->default(1);
            $table->json('skills')->nullable();
            $table->boolean('is_available')->default(true);
            $table->timestamps();
            
            $table->index('domain_uuid');
            $table->index('agent_status');
            $table->index('agent_type');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('agents');
    }
};
