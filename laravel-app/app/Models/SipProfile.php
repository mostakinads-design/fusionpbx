<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * SipProfile Model
 * 
 * SIP profiles define how FreeSwitch handles SIP traffic.
 * Internal and External profiles are common.
 *
 * @property string $sip_profile_uuid
 * @property string|null $sip_profile_name
 * @property string|null $sip_profile_hostname
 * @property string|null $sip_profile_enabled
 * @property string|null $sip_profile_description
 */
class SipProfile extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_sip_profiles';
    protected $primaryKey = 'sip_profile_uuid';
    
    // SIP profiles are not domain-scoped (system-wide)
    protected $usesDomainScoping = false;
    
    protected $fillable = [
        'sip_profile_name',
        'sip_profile_hostname',
        'sip_profile_enabled',
        'sip_profile_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domains for this profile.
     */
    public function domains(): HasMany
    {
        return $this->hasMany(SipProfileDomain::class, 'sip_profile_uuid', 'sip_profile_uuid');
    }

    /**
     * Get the settings for this profile.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(SipProfileSetting::class, 'sip_profile_uuid', 'sip_profile_uuid');
    }

    /**
     * Check if the profile is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->sip_profile_enabled === 'true';
    }

    /**
     * Scope for enabled profiles.
     */
    public function scopeEnabled($query)
    {
        return $query->where('sip_profile_enabled', 'true');
    }

    /**
     * Scope by profile name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('sip_profile_name', $name);
    }

    /**
     * Get internal profile.
     */
    public function scopeInternal($query)
    {
        return $query->where('sip_profile_name', 'internal');
    }

    /**
     * Get external profile.
     */
    public function scopeExternal($query)
    {
        return $query->where('sip_profile_name', 'external');
    }
}
