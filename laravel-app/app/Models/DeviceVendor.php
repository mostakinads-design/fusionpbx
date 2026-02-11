<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DeviceVendor Model
 * 
 * Represents phone manufacturers/vendors.
 * Used for device provisioning templates.
 *
 * @property string $device_vendor_uuid
 * @property string|null $name
 * @property string|null $enabled
 * @property string|null $description
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class DeviceVendor extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_vendors';
    protected $primaryKey = 'device_vendor_uuid';
    
    // Device vendors are not domain-scoped (system-wide)
    protected $usesDomainScoping = false;
    
    protected $fillable = [
        'name',
        'enabled',
        'description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the devices for this vendor.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'device_vendor', 'name');
    }

    /**
     * Check if the vendor is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled === 'true';
    }

    /**
     * Scope for enabled vendors.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 'true');
    }

    /**
     * Scope by vendor name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('name', $name);
    }

    /**
     * Get popular vendors.
     */
    public function scopePopular($query)
    {
        return $query->whereIn('name', [
            'yealink',
            'poly',
            'polycom',
            'grandstream',
            'cisco',
            'snom',
            'fanvil',
        ]);
    }
}
