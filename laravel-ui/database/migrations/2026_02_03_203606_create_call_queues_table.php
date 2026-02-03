<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void {
        Schema::create('call_queues', function (Blueprint $table) {
            $table->uuid('queue_uuid')->primary();
            $table->uuid('domain_uuid');
            $table->string('queue_name');
            $table->string('queue_extension');
            $table->text('queue_description')->nullable();
            $table->enum('strategy', ['ring-all', 'longest-idle-agent', 'round-robin', 'top-down', 'agent-with-least-talk-time', 'agent-with-fewest-calls', 'sequentially-by-agent-order', 'random'])->default('ring-all');
            $table->integer('timeout')->default(30);
            $table->integer('max_wait_time')->default(300);
            $table->integer('max_wait_time_with_no_agent')->default(60);
            $table->boolean('tier_rules_apply')->default(false);
            $table->integer('tier_rule_wait_second')->default(30);
            $table->boolean('tier_rule_no_agent_no_wait')->default(false);
            $table->integer('discard_abandoned_after')->default(60);
            $table->boolean('abandoned_resume_allowed')->default(true);
            $table->string('moh_sound')->nullable();
            $table->string('announce_sound')->nullable();
            $table->integer('announce_frequency')->default(30);
            $table->boolean('record_calls')->default(false);
            $table->boolean('is_active')->default(true);
            $table->string('caller_id_name_prefix')->nullable();
            $table->string('caller_id_number_prefix')->nullable();
            $table->timestamps();
            $table->index('domain_uuid');
        });
    }
    public function down(): void { Schema::dropIfExists('call_queues'); }
};
