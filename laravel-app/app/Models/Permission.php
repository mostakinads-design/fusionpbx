<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Permission Model
 * 
 * Represents a system permission in FusionPBX
 * 
 * @property string $permission_uuid
 * @property string|null $application_uuid
 * @property string|null $application_name
 * @property string $permission_name
 * @property string|null $permission_description
 */
class Permission extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_permissions';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'permission_uuid';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Disable domain scoping for Permission model
     */
    protected $usesDomainScoping = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'permission_uuid',
        'application_uuid',
        'application_name',
        'permission_name',
        'permission_description',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the groups that have this permission.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
            'v_group_permissions',
            'permission_name',
            'group_uuid',
            'permission_name',
            'group_uuid'
        );
    }

    /**
     * Get the group permissions records.
     */
    public function groupPermissions(): HasMany
    {
        return $this->hasMany(GroupPermission::class, 'permission_name', 'permission_name');
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
        return ['permission_uuid'];
    }
}
