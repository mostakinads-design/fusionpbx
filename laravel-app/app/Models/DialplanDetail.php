<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DialplanDetail Model
 * 
 * Individual conditions and actions within a dialplan.
 * Defines the logic for call routing.
 *
 * @property string $dialplan_detail_uuid
 * @property string $domain_uuid
 * @property string $dialplan_uuid
 * @property string|null $dialplan_detail_tag
 * @property string|null $dialplan_detail_type
 * @property string|null $dialplan_detail_data
 * @property string|null $dialplan_detail_break
 * @property string|null $dialplan_detail_inline
 * @property int|null $dialplan_detail_group
 * @property int|null $dialplan_detail_order
 * @property bool $dialplan_detail_enabled
 */
class DialplanDetail extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_dialplan_details';
    protected $primaryKey = 'dialplan_detail_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'dialplan_uuid',
        'dialplan_detail_tag',
        'dialplan_detail_type',
        'dialplan_detail_data',
        'dialplan_detail_break',
        'dialplan_detail_inline',
        'dialplan_detail_group',
        'dialplan_detail_order',
        'dialplan_detail_enabled',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'dialplan_detail_group' => 'integer',
        'dialplan_detail_order' => 'integer',
        'dialplan_detail_enabled' => 'boolean',
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
     * Get the dialplan.
     */
    public function dialplan(): BelongsTo
    {
        return $this->belongsTo(Dialplan::class, 'dialplan_uuid', 'dialplan_uuid');
    }

    /**
     * Check if this is a condition.
     */
    public function isCondition(): bool
    {
        return $this->dialplan_detail_tag === 'condition';
    }

    /**
     * Check if this is an action.
     */
    public function isAction(): bool
    {
        return $this->dialplan_detail_tag === 'action';
    }

    /**
     * Check if this is an anti-action.
     */
    public function isAntiAction(): bool
    {
        return $this->dialplan_detail_tag === 'anti-action';
    }

    /**
     * Check if break is enabled.
     */
    public function shouldBreak(): bool
    {
        return $this->dialplan_detail_break === 'true' || $this->dialplan_detail_break === 'on-true';
    }

    /**
     * Check if inline execution.
     */
    public function isInline(): bool
    {
        return $this->dialplan_detail_inline === 'true';
    }

    /**
     * Scope for enabled details.
     */
    public function scopeEnabled($query)
    {
        return $query->where('dialplan_detail_enabled', true);
    }

    /**
     * Scope for conditions.
     */
    public function scopeConditions($query)
    {
        return $query->where('dialplan_detail_tag', 'condition');
    }

    /**
     * Scope for actions.
     */
    public function scopeActions($query)
    {
        return $query->where('dialplan_detail_tag', 'action');
    }

    /**
     * Scope by group.
     */
    public function scopeByGroup($query, int $group)
    {
        return $query->where('dialplan_detail_group', $group);
    }

    /**
     * Scope ordered.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('dialplan_detail_order');
    }
}
