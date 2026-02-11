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
        // v_extensions - Phone extensions
        Schema::create('v_extensions', function (Blueprint $table) {
            $table->string('extension_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('extension')->nullable();
            $table->text('number_alias')->nullable();
            $table->text('password')->nullable();
            $table->text('accountcode')->nullable();
            $table->text('effective_caller_id_name')->nullable();
            $table->text('effective_caller_id_number')->nullable();
            $table->text('outbound_caller_id_name')->nullable();
            $table->text('outbound_caller_id_number')->nullable();
            $table->text('emergency_caller_id_name')->nullable();
            $table->text('emergency_caller_id_number')->nullable();
            $table->text('directory_first_name')->nullable();
            $table->text('directory_last_name')->nullable();
            $table->text('directory_visible')->nullable();
            $table->text('directory_exten_visible')->nullable();
            $table->text('max_registrations')->nullable();
            $table->text('limit_max')->nullable();
            $table->text('limit_destination')->nullable();
            $table->text('missed_call_app')->nullable();
            $table->text('missed_call_data')->nullable();
            $table->text('user_context')->nullable();
            $table->text('toll_allow')->nullable();
            $table->decimal('call_timeout', 10, 0)->nullable();
            $table->text('call_group')->nullable();
            $table->text('call_screen_enabled')->nullable();
            $table->text('user_record')->nullable();
            $table->text('hold_music')->nullable();
            $table->text('auth_acl')->nullable();
            $table->text('sip_force_contact')->nullable();
            $table->text('nibble_account')->nullable();
            $table->decimal('sip_force_expires', 10, 0)->nullable();
            $table->text('mwi_account')->nullable();
            $table->text('sip_bypass_media')->nullable();
            $table->decimal('unique_id', 10, 0)->nullable();
            $table->text('dial_string')->nullable();
            $table->text('dial_user')->nullable();
            $table->text('dial_domain')->nullable();
            $table->text('do_not_disturb')->nullable();
            $table->text('forward_all_destination')->nullable();
            $table->text('forward_all_enabled')->nullable();
            $table->text('forward_busy_destination')->nullable();
            $table->text('forward_busy_enabled')->nullable();
            $table->text('forward_no_answer_destination')->nullable();
            $table->text('forward_no_answer_enabled')->nullable();
            $table->text('forward_user_not_registered_destination')->nullable();
            $table->text('forward_user_not_registered_enabled')->nullable();
            $table->string('follow_me_uuid', 36)->nullable();
            $table->text('follow_me_enabled')->nullable();
            $table->text('follow_me_destinations')->nullable();
            $table->text('extension_language')->nullable();
            $table->text('extension_dialect')->nullable();
            $table->text('extension_voice')->nullable();
            $table->text('extension_type')->nullable();
            $table->text('enabled')->nullable();
            $table->text('description')->nullable();
            $table->text('absolute_codec_string')->nullable();
            $table->text('force_ping')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('extension');
        });

        // v_extension_users - Extension to user assignments
        Schema::create('v_extension_users', function (Blueprint $table) {
            $table->string('extension_user_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('extension_uuid', 36)->nullable();
            $table->string('user_uuid', 36)->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('extension_uuid');
            $table->index('user_uuid');
        });

        // v_extension_settings - Extension settings
        Schema::create('v_extension_settings', function (Blueprint $table) {
            $table->string('extension_setting_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('extension_uuid', 36)->nullable();
            $table->text('extension_setting_type')->nullable();
            $table->text('extension_setting_name')->nullable();
            $table->text('extension_setting_value')->nullable();
            $table->boolean('extension_setting_enabled')->default(true);
            $table->text('extension_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('extension_uuid');
        });

        // v_devices - Phone devices
        Schema::create('v_devices', function (Blueprint $table) {
            $table->string('device_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('device_profile_uuid', 36)->nullable();
            $table->text('device_address')->nullable();
            $table->text('device_label')->nullable();
            $table->text('device_vendor')->nullable();
            $table->text('device_location')->nullable();
            $table->text('device_serial_number')->nullable();
            $table->text('device_model')->nullable();
            $table->text('device_firmware_version')->nullable();
            $table->text('device_enabled')->nullable();
            $table->dateTime('device_enabled_date')->nullable();
            $table->text('device_template')->nullable();
            $table->string('device_user_uuid', 36)->nullable();
            $table->text('device_username')->nullable();
            $table->text('device_password')->nullable();
            $table->string('device_uuid_alternate', 36)->nullable();
            $table->text('device_description')->nullable();
            $table->dateTime('device_provisioned_date')->nullable();
            $table->text('device_provisioned_method')->nullable();
            $table->text('device_provisioned_ip')->nullable();
            $table->text('device_provisioned_agent')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('device_address');
        });

        // v_device_lines - Device line configuration
        Schema::create('v_device_lines', function (Blueprint $table) {
            $table->string('device_line_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('device_uuid', 36)->nullable();
            $table->text('line_number')->nullable();
            $table->text('server_address')->nullable();
            $table->text('server_address_primary')->nullable();
            $table->text('server_address_secondary')->nullable();
            $table->text('outbound_proxy_primary')->nullable();
            $table->text('outbound_proxy_secondary')->nullable();
            $table->text('label')->nullable();
            $table->text('display_name')->nullable();
            $table->text('user_id')->nullable();
            $table->text('auth_id')->nullable();
            $table->text('password')->nullable();
            $table->decimal('sip_port', 10, 0)->nullable();
            $table->text('sip_transport')->nullable();
            $table->decimal('register_expires', 10, 0)->nullable();
            $table->text('shared_line')->nullable();
            $table->text('enabled')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('device_uuid');
        });

        // v_device_keys - Device programmable keys
        Schema::create('v_device_keys', function (Blueprint $table) {
            $table->string('device_key_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('device_uuid', 36)->nullable();
            $table->decimal('device_key_id', 10, 0)->nullable();
            $table->text('device_key_category')->nullable();
            $table->text('device_key_vendor')->nullable();
            $table->text('device_key_type')->nullable();
            $table->text('device_key_subtype')->nullable();
            $table->decimal('device_key_line', 10, 0)->nullable();
            $table->text('device_key_value')->nullable();
            $table->text('device_key_extension')->nullable();
            $table->text('device_key_protected')->nullable();
            $table->text('device_key_label')->nullable();
            $table->text('device_key_icon')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('device_uuid');
        });

        // v_device_settings - Device settings
        Schema::create('v_device_settings', function (Blueprint $table) {
            $table->string('device_setting_uuid', 36)->primary();
            $table->string('device_uuid', 36)->nullable();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('device_setting_category')->nullable();
            $table->text('device_setting_subcategory')->nullable();
            $table->text('device_setting_name')->nullable();
            $table->text('device_setting_value')->nullable();
            $table->text('device_setting_enabled')->nullable();
            $table->text('device_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('device_uuid');
            $table->index('domain_uuid');
        });

        // v_device_vendors - Device vendor information
        Schema::create('v_device_vendors', function (Blueprint $table) {
            $table->string('device_vendor_uuid', 36)->primary();
            $table->text('name')->nullable();
            $table->text('enabled')->nullable();
            $table->text('description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
        });

        // v_device_profiles - Device profile templates
        Schema::create('v_device_profiles', function (Blueprint $table) {
            $table->string('device_profile_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('device_profile_name')->nullable();
            $table->text('device_profile_enabled')->nullable();
            $table->text('device_profile_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
        });

        // v_device_logs - Device provisioning logs
        Schema::create('v_device_logs', function (Blueprint $table) {
            $table->string('device_log_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('device_uuid', 36)->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->text('device_address')->nullable();
            $table->text('request_scheme')->nullable();
            $table->text('http_host')->nullable();
            $table->text('server_port')->nullable();
            $table->text('server_protocol')->nullable();
            $table->text('query_string')->nullable();
            $table->text('remote_address')->nullable();
            $table->text('http_user_agent')->nullable();
            $table->text('http_status')->nullable();
            $table->text('http_status_code')->nullable();
            $table->text('http_content_body')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('device_uuid');
            $table->index('timestamp');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_device_logs');
        Schema::dropIfExists('v_device_profiles');
        Schema::dropIfExists('v_device_vendors');
        Schema::dropIfExists('v_device_settings');
        Schema::dropIfExists('v_device_keys');
        Schema::dropIfExists('v_device_lines');
        Schema::dropIfExists('v_devices');
        Schema::dropIfExists('v_extension_settings');
        Schema::dropIfExists('v_extension_users');
        Schema::dropIfExists('v_extensions');
    }
};
