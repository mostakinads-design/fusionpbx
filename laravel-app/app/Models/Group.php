<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * Group Model
 * 
 * Represents a permission group in FusionPBX
 * 
 * @property string $group_uuid
 * @property string|null $domain_uuid
 * @property string $group_name
 * @property string|null $group_protected
 * @property int|null $group_level
 * @property string|null $group_description
 */
class Group extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_groups';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'group_uuid';

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
        'group_uuid',
        'domain_uuid',
        'group_name',
        'group_protected',
        'group_level',
        'group_description',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'group_level' => 'integer',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain that owns the group.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the users for the group.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            FusionPbxUser::class,
            'v_user_groups',
            'group_uuid',
            'user_uuid',
            'group_uuid',
            'user_uuid'
        );
    }

    /**
     * Get the permissions for the group.
     */
    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(
            Permission::class,
            'v_group_permissions',
            'group_uuid',
            'permission_name',
            'group_uuid',
            'permission_name'
        );
    }

    /**
     * Get the group permissions records.
     */
    public function groupPermissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class, 'group_uuid', 'group_uuid');
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
        return ['group_uuid'];
    }

    /**
     * Check if group is protected.
     */
    public function isProtected(): bool
    {
        return $this->group_protected === 'true';
    }
}
