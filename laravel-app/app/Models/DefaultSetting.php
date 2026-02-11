<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * DefaultSetting Model
 * 
 * System-wide default settings.
 * Provides fallback values when domain settings are not defined.
 *
 * @property string $default_setting_uuid
 * @property string|null $app_uuid
 * @property string|null $default_setting_category
 * @property string|null $default_setting_subcategory
 * @property string|null $default_setting_name
 * @property string|null $default_setting_value
 * @property int|null $default_setting_order
 * @property bool $default_setting_enabled
 * @property string|null $default_setting_description
 */
class DefaultSetting extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_default_settings';
    protected $primaryKey = 'default_setting_uuid';
    
    // Default settings are not domain-scoped (system-wide)
    protected $usesDomainScoping = false;
    
    protected $fillable = [
        'app_uuid',
        'default_setting_category',
        'default_setting_subcategory',
        'default_setting_name',
        'default_setting_value',
        'default_setting_order',
        'default_setting_enabled',
        'default_setting_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'default_setting_order' => 'integer',
        'default_setting_enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Scope for enabled settings.
     */
    public function scopeEnabled($query)
    {
        return $query->where('default_setting_enabled', true);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('default_setting_category', $category);
    }

    /**
     * Scope by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('default_setting_name', $name);
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('default_setting_order');
    }
}
