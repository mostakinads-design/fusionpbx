<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeviceKey Model
 * 
 * Programmable function keys on IP phones.
 * Supports BLF, speed dial, line keys, etc.
 *
 * @property string $device_key_uuid
 * @property string $domain_uuid
 * @property string $device_uuid
 * @property int|null $device_key_id
 * @property string|null $device_key_category
 * @property string|null $device_key_vendor
 * @property string|null $device_key_type
 * @property string|null $device_key_subtype
 * @property int|null $device_key_line
 * @property string|null $device_key_value
 * @property string|null $device_key_extension
 * @property string|null $device_key_protected
 * @property string|null $device_key_label
 * @property string|null $device_key_icon
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class DeviceKey extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_keys';
    protected $primaryKey = 'device_key_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'device_uuid',
        'device_key_id',
        'device_key_category',
        'device_key_vendor',
        'device_key_type',
        'device_key_subtype',
        'device_key_line',
        'device_key_value',
        'device_key_extension',
        'device_key_protected',
        'device_key_label',
        'device_key_icon',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'device_key_id' => 'integer',
        'device_key_line' => 'integer',
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
     * Check if the key is protected.
     */
    public function isProtected(): bool
    {
        return $this->device_key_protected === 'true';
    }

    /**
     * Scope by key type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('device_key_type', $type);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('device_key_category', $category);
    }

    /**
     * Scope for line keys.
     */
    public function scopeLineKeys($query)
    {
        return $query->where('device_key_category', 'line');
    }

    /**
     * Scope for BLF keys.
     */
    public function scopeBlf Keys($query)
    {
        return $query->where('device_key_type', 'blf');
    }
}
