<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DomainSetting Model
 * 
 * Domain-specific settings and configuration.
 * Allows per-domain customization of system behavior.
 *
 * @property string $domain_setting_uuid
 * @property string $domain_uuid
 * @property string|null $app_uuid
 * @property string|null $domain_setting_category
 * @property string|null $domain_setting_subcategory
 * @property string|null $domain_setting_name
 * @property string|null $domain_setting_value
 * @property int|null $domain_setting_order
 * @property bool $domain_setting_enabled
 * @property string|null $domain_setting_description
 */
class DomainSetting extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_domain_settings';
    protected $primaryKey = 'domain_setting_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'app_uuid',
        'domain_setting_category',
        'domain_setting_subcategory',
        'domain_setting_name',
        'domain_setting_value',
        'domain_setting_order',
        'domain_setting_enabled',
        'domain_setting_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'domain_setting_order' => 'integer',
        'domain_setting_enabled' => 'boolean',
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
     * Scope for enabled settings.
     */
    public function scopeEnabled($query)
    {
        return $query->where('domain_setting_enabled', true);
    }

    /**
     * Scope by category.
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('domain_setting_category', $category);
    }

    /**
     * Scope by name.
     */
    public function scopeByName($query, string $name)
    {
        return $query->where('domain_setting_name', $name);
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('domain_setting_order');
    }
}
