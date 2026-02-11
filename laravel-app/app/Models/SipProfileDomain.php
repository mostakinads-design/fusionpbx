<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * SipProfileDomain Model
 * 
 * Associates domains with SIP profiles.
 * Allows profile to handle multiple domains.
 *
 * @property string $sip_profile_domain_uuid
 * @property string $sip_profile_uuid
 * @property string|null $sip_profile_domain_name
 * @property string|null $sip_profile_domain_alias
 * @property string|null $sip_profile_domain_parse
 */
class SipProfileDomain extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_sip_profile_domains';
    protected $primaryKey = 'sip_profile_domain_uuid';
    
    // Not domain-scoped (system-wide)
    protected $usesDomainScoping = false;
    
    protected $fillable = [
        'sip_profile_uuid',
        'sip_profile_domain_name',
        'sip_profile_domain_alias',
        'sip_profile_domain_parse',
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
     * Check if this is an alias.
     */
    public function isAlias(): bool
    {
        return $this->sip_profile_domain_alias === 'true';
    }

    /**
     * Check if parse is enabled.
     */
    public function shouldParse(): bool
    {
        return $this->sip_profile_domain_parse === 'true';
    }
}
