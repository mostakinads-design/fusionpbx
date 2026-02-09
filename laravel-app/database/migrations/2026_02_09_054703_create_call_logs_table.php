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
        Schema::create('call_logs', function (Blueprint $table) {
            $table->id();
            $table->string('caller_id');
            $table->string('destination');
            $table->integer('duration')->default(0); // in seconds
            $table->enum('status', ['answered', 'missed', 'busy', 'failed'])->default('answered');
            $table->string('call_type')->nullable(); // inbound, outbound
            $table->timestamp('call_date')->useCurrent();
            $table->timestamps();
            
            $table->index('caller_id');
            $table->index('destination');
            $table->index('status');
            $table->index('call_date');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('call_logs');
    }
};
