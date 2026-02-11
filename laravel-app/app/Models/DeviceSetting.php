<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeviceSetting Model
 * 
 * Device-specific configuration settings.
 * Allows per-device customization.
 *
 * @property string $device_setting_uuid
 * @property string $device_uuid
 * @property string $domain_uuid
 * @property string|null $device_setting_category
 * @property string|null $device_setting_subcategory
 * @property string|null $device_setting_name
 * @property string|null $device_setting_value
 * @property string|null $device_setting_enabled
 * @property string|null $device_setting_description
 */
class DeviceSetting extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_settings';
    protected $primaryKey = 'device_setting_uuid';
    
    protected $fillable = [
        'device_uuid',
        'domain_uuid',
        'device_setting_category',
        'device_setting_subcategory',
        'device_setting_name',
        'device_setting_value',
        'device_setting_enabled',
        'device_setting_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Check if the setting is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->device_setting_enabled === 'true';
    }

    /**
     * Scope for enabled settings.
     */
    public function scopeEnabled($query)
    {
        return $query->where('device_setting_enabled', 'true');
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('device_setting_category', $category);
    }
}
