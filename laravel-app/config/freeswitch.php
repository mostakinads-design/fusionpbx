<?php

return [

    /*
    |--------------------------------------------------------------------------
    | FreeSwitch ESL Connection Settings
    |--------------------------------------------------------------------------
    |
    | Configure the connection to FreeSwitch Event Socket Library (ESL)
    |
    */

    'esl' => [
        'host' => env('FREESWITCH_ESL_HOST', '127.0.0.1'),
        'port' => env('FREESWITCH_ESL_PORT', 8021),
        'password' => env('FREESWITCH_ESL_PASSWORD', 'ClueCon'),
        'timeout' => env('FREESWITCH_ESL_TIMEOUT', 10),
    ],

    /*
    |--------------------------------------------------------------------------
    | FreeSwitch Configuration Paths
    |--------------------------------------------------------------------------
    |
    | Paths to FreeSwitch configuration directories
    |
    */

    'paths' => [
        'base' => env('FREESWITCH_BASE_PATH', '/etc/freeswitch'),
        'scripts' => env('FREESWITCH_SCRIPTS_PATH', '/usr/share/freeswitch/scripts'),
        'sounds' => env('FREESWITCH_SOUNDS_PATH', '/usr/share/freeswitch/sounds'),
        'recordings' => env('FREESWITCH_RECORDINGS_PATH', '/var/lib/freeswitch/recordings'),
        'storage' => env('FREESWITCH_STORAGE_PATH', '/var/lib/freeswitch/storage'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Database Settings
    |--------------------------------------------------------------------------
    |
    | FreeSwitch database configuration
    |
    */

    'database' => [
        'connection' => env('FREESWITCH_DB_CONNECTION', 'mysql'),
        'host' => env('FREESWITCH_DB_HOST', '127.0.0.1'),
        'port' => env('FREESWITCH_DB_PORT', '3306'),
        'database' => env('FREESWITCH_DB_DATABASE', 'fusionpbx'),
        'username' => env('FREESWITCH_DB_USERNAME', 'fusionpbx'),
        'password' => env('FREESWITCH_DB_PASSWORD', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Domain Settings
    |--------------------------------------------------------------------------
    |
    | Multi-tenant domain configuration
    |
    */

    'multi_tenant' => env('FREESWITCH_MULTI_TENANT', true),
    'default_domain' => env('FREESWITCH_DEFAULT_DOMAIN', 'default'),

    /*
    |--------------------------------------------------------------------------
    | SIP Settings
    |--------------------------------------------------------------------------
    |
    | Default SIP profile settings
    |
    */

    'sip' => [
        'default_profile' => env('FREESWITCH_SIP_PROFILE', 'internal'),
        'port' => env('FREESWITCH_SIP_PORT', 5060),
        'tls_port' => env('FREESWITCH_SIP_TLS_PORT', 5061),
        'ws_port' => env('FREESWITCH_SIP_WS_PORT', 5062),
        'wss_port' => env('FREESWITCH_SIP_WSS_PORT', 7443),
    ],

    /*
    |--------------------------------------------------------------------------
    | Extension Settings
    |--------------------------------------------------------------------------
    |
    | Default extension configuration
    |
    */

    'extensions' => [
        'default_password_length' => 15,
        'default_voicemail_enabled' => true,
        'default_call_timeout' => 30,
        'default_call_group' => 'default',
    ],

    /*
    |--------------------------------------------------------------------------
    | Call Recording Settings
    |--------------------------------------------------------------------------
    */

    'recording' => [
        'enabled' => env('FREESWITCH_RECORDING_ENABLED', false),
        'format' => env('FREESWITCH_RECORDING_FORMAT', 'wav'),
        'sample_rate' => env('FREESWITCH_RECORDING_SAMPLE_RATE', 8000),
    ],

    /*
    |--------------------------------------------------------------------------
    | Voicemail Settings
    |--------------------------------------------------------------------------
    */

    'voicemail' => [
        'enabled' => env('FREESWITCH_VOICEMAIL_ENABLED', true),
        'email_notifications' => env('FREESWITCH_VOICEMAIL_EMAIL', true),
        'max_message_length' => env('FREESWITCH_VOICEMAIL_MAX_LENGTH', 300),
        'max_messages' => env('FREESWITCH_VOICEMAIL_MAX_MESSAGES', 100),
    ],

    /*
    |--------------------------------------------------------------------------
    | XML Configuration
    |--------------------------------------------------------------------------
    |
    | XML config generation settings
    |
    */

    'xml_config' => [
        'cache_enabled' => env('FREESWITCH_XML_CACHE', true),
        'cache_ttl' => env('FREESWITCH_XML_CACHE_TTL', 3600),
    ],

    /*
    |--------------------------------------------------------------------------
    | Event Settings
    |--------------------------------------------------------------------------
    |
    | FreeSwitch event subscription configuration
    |
    */

    'events' => [
        'enabled' => env('FREESWITCH_EVENTS_ENABLED', true),
        'subscribe_to' => [
            'CHANNEL_CREATE',
            'CHANNEL_ANSWER',
            'CHANNEL_HANGUP',
            'CHANNEL_HANGUP_COMPLETE',
            'CUSTOM',
            'PRESENCE_IN',
            'PRESENCE_OUT',
            'MESSAGE_WAITING',
            'NOTIFY',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | WebRTC Softphone Settings
    |--------------------------------------------------------------------------
    */

    'webrtc' => [
        'enabled' => env('FREESWITCH_WEBRTC_ENABLED', false),
        'stun_server' => env('FREESWITCH_STUN_SERVER', 'stun:stun.freeswitch.org'),
        'turn_server' => env('FREESWITCH_TURN_SERVER', null),
    ],

    /*
    |--------------------------------------------------------------------------
    | Call Center Settings
    |--------------------------------------------------------------------------
    */

    'call_center' => [
        'enabled' => env('FREESWITCH_CALL_CENTER_ENABLED', true),
        'max_wait_time' => env('FREESWITCH_CC_MAX_WAIT', 300),
        'tier_rule' => env('FREESWITCH_CC_TIER_RULE', 'longest-idle-agent'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Fax Settings
    |--------------------------------------------------------------------------
    */

    'fax' => [
        'enabled' => env('FREESWITCH_FAX_ENABLED', true),
        'ident' => env('FREESWITCH_FAX_IDENT', 'FusionPBX'),
        'header' => env('FREESWITCH_FAX_HEADER', 'FusionPBX'),
        'resolution' => env('FREESWITCH_FAX_RESOLUTION', 'fine'),
    ],

];
