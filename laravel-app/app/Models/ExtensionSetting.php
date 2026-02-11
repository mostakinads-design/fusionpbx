<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExtensionSetting Model
 * 
 * Stores extension-specific settings and configuration.
 *
 * @property string $extension_setting_uuid
 * @property string $domain_uuid
 * @property string $extension_uuid
 * @property string|null $extension_setting_type
 * @property string|null $extension_setting_name
 * @property string|null $extension_setting_value
 * @property bool $extension_setting_enabled
 * @property string|null $extension_setting_description
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class ExtensionSetting extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_extension_settings';
    protected $primaryKey = 'extension_setting_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'extension_uuid',
        'extension_setting_type',
        'extension_setting_name',
        'extension_setting_value',
        'extension_setting_enabled',
        'extension_setting_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'extension_setting_enabled' => 'boolean',
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
     * Get the extension.
     */
    public function extension(): BelongsTo
    {
        return $this->belongsTo(FusionPbxExtension::class, 'extension_uuid', 'extension_uuid');
    }

    /**
     * Scope for enabled settings.
     */
    public function scopeEnabled($query)
    {
        return $query->where('extension_setting_enabled', true);
    }

    /**
     * Scope by setting type.
     */
    public function scopeByType($query, string $type)
    {
        return $query->where('extension_setting_type', $type);
    }
}
