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
        // v_domains - Core domain/tenant table
        Schema::create('v_domains', function (Blueprint $table) {
            $table->string('domain_uuid', 36)->primary();
            $table->string('domain_parent_uuid', 36)->nullable();
            $table->text('domain_name')->nullable();
            $table->boolean('domain_enabled')->default(true);
            $table->text('domain_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_name');
        });

        // v_users - User accounts table
        Schema::create('v_users', function (Blueprint $table) {
            $table->string('user_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('contact_uuid', 36)->nullable();
            $table->text('username')->nullable();
            $table->text('password')->nullable();
            $table->text('salt')->nullable();
            $table->text('user_email')->nullable();
            $table->text('user_status')->nullable();
            $table->text('api_key')->nullable();
            $table->text('user_totp_secret')->nullable();
            $table->text('user_type')->nullable();
            $table->text('user_enabled')->nullable();
            $table->text('add_user')->nullable();
            $table->text('add_date')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('username');
            $table->index('user_email');
        });

        // v_groups - User groups for permissions
        Schema::create('v_groups', function (Blueprint $table) {
            $table->string('group_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('group_name')->nullable();
            $table->text('group_protected')->nullable();
            $table->decimal('group_level', 10, 0)->nullable();
            $table->text('group_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('group_name');
        });

        // v_user_groups - User to group assignments
        Schema::create('v_user_groups', function (Blueprint $table) {
            $table->string('user_group_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('group_name')->nullable();
            $table->string('group_uuid', 36)->nullable();
            $table->string('user_uuid', 36)->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('group_uuid');
            $table->index('user_uuid');
        });

        // v_group_permissions - Group permissions
        Schema::create('v_group_permissions', function (Blueprint $table) {
            $table->string('group_permission_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('permission_name')->nullable();
            $table->text('permission_protected')->nullable();
            $table->text('permission_assigned')->nullable();
            $table->text('group_name')->nullable();
            $table->string('group_uuid', 36)->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('group_uuid');
        });

        // v_permissions - Available permissions
        Schema::create('v_permissions', function (Blueprint $table) {
            $table->string('permission_uuid', 36)->primary();
            $table->string('application_uuid', 36)->nullable();
            $table->text('application_name')->nullable();
            $table->text('permission_name')->nullable();
            $table->text('permission_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('permission_name');
        });

        // v_user_settings - User-specific settings
        Schema::create('v_user_settings', function (Blueprint $table) {
            $table->string('user_setting_uuid', 36)->primary();
            $table->string('user_uuid', 36)->nullable();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('user_setting_category')->nullable();
            $table->text('user_setting_subcategory')->nullable();
            $table->text('user_setting_name')->nullable();
            $table->text('user_setting_value')->nullable();
            $table->decimal('user_setting_order', 10, 0)->nullable();
            $table->boolean('user_setting_enabled')->default(true);
            $table->text('user_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('user_uuid');
            $table->index('domain_uuid');
        });

        // v_user_logs - User activity logs
        Schema::create('v_user_logs', function (Blueprint $table) {
            $table->string('user_log_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->text('hostname')->nullable();
            $table->dateTime('timestamp')->nullable();
            $table->string('user_uuid', 36)->nullable();
            $table->text('username')->nullable();
            $table->text('type')->nullable();
            $table->text('result')->nullable();
            $table->text('remote_address')->nullable();
            $table->text('user_agent')->nullable();
            $table->text('session_id')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
            $table->index('user_uuid');
            $table->index(['timestamp', 'type']);
        });

        // v_domain_settings - Domain-specific settings
        Schema::create('v_domain_settings', function (Blueprint $table) {
            $table->string('domain_setting_uuid', 36)->primary();
            $table->string('domain_uuid', 36)->nullable();
            $table->string('app_uuid', 36)->nullable();
            $table->text('domain_setting_category')->nullable();
            $table->text('domain_setting_subcategory')->nullable();
            $table->text('domain_setting_name')->nullable();
            $table->text('domain_setting_value')->nullable();
            $table->decimal('domain_setting_order', 10, 0)->nullable();
            $table->boolean('domain_setting_enabled')->default(true);
            $table->text('domain_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
            
            $table->index('domain_uuid');
        });

        // v_default_settings - Default system settings
        Schema::create('v_default_settings', function (Blueprint $table) {
            $table->string('default_setting_uuid', 36)->primary();
            $table->string('app_uuid', 36)->nullable();
            $table->text('default_setting_category')->nullable();
            $table->text('default_setting_subcategory')->nullable();
            $table->text('default_setting_name')->nullable();
            $table->text('default_setting_value')->nullable();
            $table->decimal('default_setting_order', 10, 0)->nullable();
            $table->boolean('default_setting_enabled')->default(true);
            $table->text('default_setting_description')->nullable();
            $table->dateTime('insert_date')->nullable();
            $table->string('insert_user', 36)->nullable();
            $table->dateTime('update_date')->nullable();
            $table->string('update_user', 36)->nullable();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('v_default_settings');
        Schema::dropIfExists('v_domain_settings');
        Schema::dropIfExists('v_user_logs');
        Schema::dropIfExists('v_user_settings');
        Schema::dropIfExists('v_permissions');
        Schema::dropIfExists('v_group_permissions');
        Schema::dropIfExists('v_user_groups');
        Schema::dropIfExists('v_groups');
        Schema::dropIfExists('v_users');
        Schema::dropIfExists('v_domains');
    }
};
