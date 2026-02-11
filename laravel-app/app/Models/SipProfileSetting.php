<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SipProfileSetting Model
 * 
 * Settings for SIP profiles.
 * Configures profile behavior (codecs, ports, etc.).
 *
 * @property string $sip_profile_setting_uuid
 * @property string $sip_profile_uuid
 * @property string|null $sip_profile_setting_name
 * @property string|null $sip_profile_setting_value
 * @property string|null $sip_profile_setting_enabled
 * @property string|null $sip_profile_setting_description
 */
class SipProfileSetting extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_sip_profile_settings';
    protected $primaryKey = 'sip_profile_setting_uuid';
    
    // Not domain-scoped (system-wide)
    protected $usesDomainScoping = false;
    
    protected $fillable = [
        'sip_profile_uuid',
        'sip_profile_setting_name',
        'sip_profile_setting_value',
        'sip_profile_setting_enabled',
        'sip_profile_setting_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the SIP profile.
     */
    public function sipProfile(): BelongsTo
    {
        return $this->belongsTo(SipProfile::class, 'sip_profile_uuid', 'sip_profile_uuid');
    }

    /**
     * Check if the setting is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->sip_profile_setting_enabled === 'true';
    }

    /**
     * Scope for enabled settings.
     */
    public function scopeEnabled($query)
    {
        return $query->where('sip_profile_setting_enabled', 'true');
    }

    /**
     * Scope by setting name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('sip_profile_setting_name', $name);
    }
}
