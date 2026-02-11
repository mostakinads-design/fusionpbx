<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Device Model
 * 
 * Represents a phone/device in FusionPBX
 * 
 * @property string $device_uuid
 * @property string|null $domain_uuid
 * @property string|null $device_profile_uuid
 * @property string|null $device_address
 * @property string|null $device_label
 * @property string|null $device_vendor
 * @property string|null $device_model
 * @property string|null $device_firmware_version
 * @property string|null $device_enabled
 * @property string|null $device_template
 * @property string|null $device_username
 * @property string|null $device_password
 * @property string|null $device_description
 */
class Device extends FpbxBaseModel
{
    use HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_devices';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'device_uuid';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'device_uuid',
        'domain_uuid',
        'device_profile_uuid',
        'device_address',
        'device_label',
        'device_vendor',
        'device_location',
        'device_serial_number',
        'device_model',
        'device_firmware_version',
        'device_enabled',
        'device_enabled_date',
        'device_template',
        'device_user_uuid',
        'device_username',
        'device_password',
        'device_uuid_alternate',
        'device_description',
        'device_provisioned_date',
        'device_provisioned_method',
        'device_provisioned_ip',
        'device_provisioned_agent',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'device_enabled_date' => 'datetime',
        'device_provisioned_date' => 'datetime',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'device_password',
    ];

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the device profile.
     */
    public function profile(): BelongsTo
    {
        return $this->belongsTo(DeviceProfile::class, 'device_profile_uuid', 'device_profile_uuid');
    }

    /**
     * Get the device vendor.
     */
    public function vendor(): BelongsTo
    {
        return $this->belongsTo(DeviceVendor::class, 'device_vendor', 'name');
    }

    /**
     * Get the device lines.
     */
    public function lines(): HasMany
    {
        return $this->hasMany(DeviceLine::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get the device keys.
     */
    public function keys(): HasMany
    {
        return $this->hasMany(DeviceKey::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get the device settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(DeviceSetting::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get the device logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(DeviceLog::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get new UUID for the model
     */
    public function newUniqueId(): string
    {
        return static::generateUuid();
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['device_uuid'];
    }

    /**
     * Check if device is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->device_enabled === 'true';
    }

    /**
     * Check if device has been provisioned.
     */
    public function isProvisioned(): bool
    {
        return !is_null($this->device_provisioned_date);
    }

    /**
     * Scope to get only enabled devices.
     */
    public function scopeEnabled($query)
    {
        return $query->where('device_enabled', 'true');
    }

    /**
     * Scope to get devices by vendor.
     */
    public function scopeByVendor($query, string $vendor)
    {
        return $query->where('device_vendor', $vendor);
    }

    /**
     * Scope to get devices by model.
     */
    public function scopeByModel($query, string $model)
    {
        return $query->where('device_model', $model);
    }
}
