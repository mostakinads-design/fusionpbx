<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeviceLine Model
 * 
 * Represents SIP lines configured on a device.
 * Each line can register an extension to the device.
 *
 * @property string $device_line_uuid
 * @property string $domain_uuid
 * @property string $device_uuid
 * @property string|null $line_number
 * @property string|null $server_address
 * @property string|null $server_address_primary
 * @property string|null $server_address_secondary
 * @property string|null $outbound_proxy_primary
 * @property string|null $outbound_proxy_secondary
 * @property string|null $label
 * @property string|null $display_name
 * @property string|null $user_id
 * @property string|null $auth_id
 * @property string|null $password
 * @property int|null $sip_port
 * @property string|null $sip_transport
 * @property int|null $register_expires
 * @property string|null $shared_line
 * @property string|null $enabled
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class DeviceLine extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_lines';
    protected $primaryKey = 'device_line_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'device_uuid',
        'line_number',
        'server_address',
        'server_address_primary',
        'server_address_secondary',
        'outbound_proxy_primary',
        'outbound_proxy_secondary',
        'label',
        'display_name',
        'user_id',
        'auth_id',
        'password',
        'sip_port',
        'sip_transport',
        'register_expires',
        'shared_line',
        'enabled',
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
        'sip_port' => 'integer',
        'register_expires' => 'integer',
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
     * Get the device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Check if the line is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled === 'true';
    }

    /**
     * Check if this is a shared line.
     */
    public function isSharedLine(): bool
    {
        return $this->shared_line === 'true';
    }

    /**
     * Scope for enabled lines.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 'true');
    }
}
