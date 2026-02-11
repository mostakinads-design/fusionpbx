<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * DeviceProfile Model
 * 
 * Device templates/profiles for provisioning.
 * Contains settings that can be applied to multiple devices.
 *
 * @property string $device_profile_uuid
 * @property string $domain_uuid
 * @property string|null $device_profile_name
 * @property string|null $device_profile_enabled
 * @property string|null $device_profile_description
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class DeviceProfile extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_profiles';
    protected $primaryKey = 'device_profile_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'device_profile_name',
        'device_profile_enabled',
        'device_profile_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
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
     * Get the devices using this profile.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'device_profile_uuid', 'device_profile_uuid');
    }

    /**
     * Check if the profile is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->device_profile_enabled === 'true';
    }

    /**
     * Scope for enabled profiles.
     */
    public function scopeEnabled($query)
    {
        return $query->where('device_profile_enabled', 'true');
    }
}
