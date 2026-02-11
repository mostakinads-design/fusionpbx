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
        // v_gateways - SIP Trunks/Gateways
        Schema::create('v_gateways', function (Blueprint $table) {
            $table->string('gateway_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('gateway')->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('distinct_to')->nullable();
            $table->text('auth_username')->nullable();
            $table->text('realm')->nullable();
            $table->text('from_user')->nullable();
            $table->text('from_domain')->nullable();
            $table->text('proxy')->nullable();
            $table->text('register_proxy')->nullable();
            $table->text('outbound_proxy')->nullable();
            $table->decimal('expire_seconds', 10, 0)->nullable();
            $table->text('register')->nullable();
            $table->text('register_transport')->nullable();
            $table->text('contact_params')->nullable();
            $table->decimal('retry_seconds', 10, 0)->nullable();
            $table->text('extension')->nullable();
            $table->text('ping')->nullable();
            $table->text('ping_min')->nullable();
            $table->text('ping_max')->nullable();
            $table->text('contact_in_ping')->nullable();
            $table->text('caller_id_in_from')->nullable();
            $table->text('supress_cng')->nullable();
            $table->text('sip_cid_type')->nullable();
            $table->text('codec_prefs')->nullable();
            $table->decimal('channels', 10, 0)->nullable();
            $table->text('extension_in_contact')->nullable();
            $table->text('context')->nullable();
            $table->text('profile')->nullable();
            $table->text('hostname')->nullable();
            $table->text('enabled')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('gateway');
        });

        // v_sip_profiles - SIP Profile configuration
        Schema::create('v_sip_profiles', function (Blueprint $table) {
            $table->string('sip_profile_uuid', 36)->primary();
            $table->text('sip_profile_name')->nullable();
            $table->text('sip_profile_hostname')->nullable();
            $table->text('sip_profile_enabled')->nullable();
            $table->text('sip_profile_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
        });

        // v_sip_profile_domains - SIP Profile domain assignments
        Schema::create('v_sip_profile_domains', function (Blueprint $table) {
            $table->string('sip_profile_domain_uuid', 36)->primary();
            $table->string('sip_profile_uuid', 36)->nullable();
            $table->text('sip_profile_domain_name')->nullable();
            $table->text('sip_profile_domain_alias')->nullable();
            $table->text('sip_profile_domain_parse')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('sip_profile_uuid');
        });

        // v_sip_profile_settings - SIP Profile settings
        Schema::create('v_sip_profile_settings', function (Blueprint $table) {
            $table->string('sip_profile_setting_uuid', 36)->primary();
            $table->string('sip_profile_uuid', 36)->nullable();
            $table->text('sip_profile_setting_name')->nullable();
            $table->text('sip_profile_setting_value')->nullable();
            $table->text('sip_profile_setting_enabled')->nullable();
            $table->text('sip_profile_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('sip_profile_uuid');
        });

        // v_dialplans - Dialplan entries
        Schema::create('v_dialplans', function (Blueprint $table) {
            $table->string('dialplan_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('app_uuid', 36)->nullable();
            $table->text('hostname')->nullable();
            $table->text('dialplan_context')->nullable();
            $table->text('dialplan_name')->nullable();
            $table->text('dialplan_number')->nullable();
            $table->text('dialplan_destination')->nullable();
            $table->text('dialplan_continue')->nullable();
            $table->text('dialplan_xml')->nullable();
            $table->decimal('dialplan_order', 10, 0)->nullable();
            $table->text('dialplan_enabled')->nullable();
            $table->text('dialplan_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('dialplan_context');
        });

        // v_dialplan_details - Dialplan detail conditions and actions
        Schema::create('v_dialplan_details', function (Blueprint $table) {
            $table->string('dialplan_detail_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('dialplan_uuid', 36)->nullable();
            $table->text('dialplan_detail_tag')->nullable();
            $table->text('dialplan_detail_type')->nullable();
            $table->text('dialplan_detail_data')->nullable();
            $table->text('dialplan_detail_break')->nullable();
            $table->text('dialplan_detail_inline')->nullable();
            $table->decimal('dialplan_detail_group', 10, 0)->nullable();
            $table->decimal('dialplan_detail_order', 10, 0)->nullable();
            $table->boolean('dialplan_detail_enabled')->default(true);
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('dialplan_uuid');
        });

        // v_xml_cdr - Call Detail Records
        Schema::create('v_xml_cdr', function (Blueprint $table) {
            $table->string('xml_cdr_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('provider_uuid', 36)->nullable();
            $table->string('extension_uuid', 36)->nullable();
            $table->text('sip_call_id')->nullable();
            $table->text('domain_name')->nullable();
            $table->text('accountcode')->nullable();
            $table->text('direction')->nullable();
            $table->text('default_language')->nullable();
            $table->text('context')->nullable();
            $table->text('caller_id_name')->nullable();
            $table->text('caller_id_number')->nullable();
            $table->text('caller_destination')->nullable();
            $table->text('source_number')->nullable();
            $table->text('destination_number')->nullable();
            $table->decimal('start_epoch', 10, 0)->nullable();
            $table->dateTime('start_stamp')->nullable();
            $table->dateTime('answer_stamp')->nullable();
            $table->decimal('answer_epoch', 10, 0)->nullable();
            $table->decimal('end_epoch', 10, 0)->nullable();
            $table->dateTime('end_stamp')->nullable();
            $table->decimal('duration', 10, 0)->nullable();
            $table->decimal('mduration', 10, 0)->nullable();
            $table->decimal('billsec', 10, 0)->nullable();
            $table->decimal('billmsec', 10, 0)->nullable();
            $table->decimal('hold_accum_seconds', 10, 0)->nullable();
            $table->text('bridge_uuid')->nullable();
            $table->text('read_codec')->nullable();
            $table->text('read_rate')->nullable();
            $table->text('write_codec')->nullable();
            $table->text('write_rate')->nullable();
            $table->text('remote_media_ip')->nullable();
            $table->text('network_addr')->nullable();
            $table->text('record_path')->nullable();
            $table->text('record_name')->nullable();
            $table->decimal('record_length', 10, 0)->nullable();
            $table->text('record_transcription')->nullable();
            $table->char('leg', 1)->nullable();
            $table->string('originating_leg_uuid', 36)->nullable();
            $table->decimal('pdd_ms', 10, 0)->nullable();
            $table->decimal('rtp_audio_in_mos', 10, 2)->nullable();
            $table->text('last_app')->nullable();
            $table->text('last_arg')->nullable();
            $table->boolean('voicemail_message')->default(false);
            $table->boolean('missed_call')->default(false);
            $table->string('call_center_queue_uuid', 36)->nullable();
            $table->text('cc_side')->nullable();
            $table->string('cc_member_uuid', 36)->nullable();
            $table->decimal('cc_queue_joined_epoch', 10, 0)->nullable();
            $table->text('cc_queue')->nullable();
            $table->string('cc_member_session_uuid', 36)->nullable();
            $table->string('cc_agent_uuid', 36)->nullable();
            $table->text('cc_agent')->nullable();
            $table->text('cc_agent_type')->nullable();
            $table->text('cc_agent_bridged')->nullable();
            $table->decimal('cc_queue_answered_epoch', 10, 0)->nullable();
            $table->decimal('cc_queue_terminated_epoch', 10, 0)->nullable();
            $table->decimal('cc_queue_canceled_epoch', 10, 0)->nullable();
            $table->text('cc_cancel_reason')->nullable();
            $table->text('cc_cause')->nullable();
            $table->decimal('waitsec', 10, 0)->nullable();
            $table->text('conference_name')->nullable();
            $table->string('conference_uuid', 36)->nullable();
            $table->text('conference_member_id')->nullable();
            $table->text('digits_dialed')->nullable();
            $table->text('pin_number')->nullable();
            $table->text('status')->nullable();
            $table->text('hangup_cause')->nullable();
            $table->decimal('hangup_cause_q850', 10, 0)->nullable();
            $table->text('sip_hangup_disposition')->nullable();
            $table->string('ring_group_uuid', 36)->nullable();
            $table->string('ivr_menu_uuid', 36)->nullable();
            $table->json('call_flow')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('extension_uuid');
            $table->index(['direction', 'start_stamp']);
            $table->index('caller_id_number');
            $table->index('destination_number');
            $table->index('hangup_cause');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_xml_cdr');
        Schema::dropIfExists('v_dialplan_details');
        Schema::dropIfExists('v_dialplans');
        Schema::dropIfExists('v_sip_profile_settings');
        Schema::dropIfExists('v_sip_profile_domains');
        Schema::dropIfExists('v_sip_profiles');
        Schema::dropIfExists('v_gateways');
    }
};
