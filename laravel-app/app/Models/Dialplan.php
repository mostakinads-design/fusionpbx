<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Dialplan Model
 * 
 * Call routing rules for FreeSwitch.
 * Defines how calls are processed.
 *
 * @property string $dialplan_uuid
 * @property string $domain_uuid
 * @property string|null $app_uuid
 * @property string|null $hostname
 * @property string|null $dialplan_context
 * @property string|null $dialplan_name
 * @property string|null $dialplan_number
 * @property string|null $dialplan_destination
 * @property string|null $dialplan_continue
 * @property string|null $dialplan_xml
 * @property int|null $dialplan_order
 * @property string|null $dialplan_enabled
 * @property string|null $dialplan_description
 */
class Dialplan extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_dialplans';
    protected $primaryKey = 'dialplan_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'app_uuid',
        'hostname',
        'dialplan_context',
        'dialplan_name',
        'dialplan_number',
        'dialplan_destination',
        'dialplan_continue',
        'dialplan_xml',
        'dialplan_order',
        'dialplan_enabled',
        'dialplan_description',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dialplan_order' => 'integer',
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
     * Get the dialplan details (conditions and actions).
     */
    public function details(): HasMany
    {
        return $this->hasMany(DialplanDetail::class, 'dialplan_uuid', 'dialplan_uuid')
            ->orderBy('dialplan_detail_order');
    }

    /**
     * Check if the dialplan is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->dialplan_enabled === 'true';
    }

    /**
     * Check if continue flag is set.
     */
    public function shouldContinue(): bool
    {
        return $this->dialplan_continue === 'true';
    }

    /**
     * Scope for enabled dialplans.
     */
    public function scopeEnabled($query)
    {
        return $query->where('dialplan_enabled', 'true');
    }

    /**
     * Scope by context.
     */
    public function scopeByContext($query, string $context)
    {
        return $query->where('dialplan_context', $context);
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('dialplan_order');
    }
}
