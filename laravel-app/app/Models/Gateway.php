<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Gateway Model
 * 
 * Represents SIP trunks/providers for external calling.
 * Gateways allow routing calls to/from external networks.
 *
 * @property string $gateway_uuid
 * @property string $domain_uuid
 * @property string|null $gateway
 * @property string|null $username
 * @property string|null $password
 * @property string|null $distinct_to
 * @property string|null $auth_username
 * @property string|null $realm
 * @property string|null $from_user
 * @property string|null $from_domain
 * @property string|null $proxy
 * @property string|null $register_proxy
 * @property string|null $outbound_proxy
 * @property int|null $expire_seconds
 * @property string|null $register
 * @property string|null $register_transport
 * @property string|null $retry_seconds
 * @property string|null $extension
 * @property string|null $ping
 * @property int|null $channels
 * @property string|null $context
 * @property string|null $profile
 * @property string|null $hostname
 * @property string|null $enabled
 * @property string|null $description
 */
class Gateway extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_gateways';
    protected $primaryKey = 'gateway_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'gateway',
        'username',
        'password',
        'distinct_to',
        'auth_username',
        'realm',
        'from_user',
        'from_domain',
        'proxy',
        'register_proxy',
        'outbound_proxy',
        'expire_seconds',
        'register',
        'register_transport',
        'retry_seconds',
        'extension',
        'ping',
        'channels',
        'context',
        'profile',
        'hostname',
        'enabled',
        'description',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'expire_seconds' => 'integer',
        'channels' => 'integer',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Check if the gateway is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled === 'true';
    }

    /**
     * Check if registration is enabled.
     */
    public function hasRegistration(): bool
    {
        return $this->register === 'true';
    }

    /**
     * Check if ping is enabled.
     */
    public function hasPing(): bool
    {
        return $this->ping === 'true';
    }

    /**
     * Scope for enabled gateways.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 'true');
    }

    /**
     * Scope for gateways with registration.
     */
    public function scopeWithRegistration($query)
    {
        return $query->where('register', 'true');
    }

    /**
     * Scope by profile.
     */
    public function scopeByProfile($query, string $profile)
    {
        return $query->where('profile', $profile);
    }
}
