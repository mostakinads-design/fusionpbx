<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * Group Permission Model
 * 
 * Pivot model for group-permission relationships
 * 
 * @property string $group_permission_uuid
 * @property string|null $domain_uuid
 * @property string $permission_name
 * @property string|null $permission_protected
 * @property string|null $permission_assigned
 * @property string|null $group_name
 * @property string $group_uuid
 */
class GroupPermission extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_group_permissions';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'group_permission_uuid';

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
        'group_permission_uuid',
        'domain_uuid',
        'permission_name',
        'permission_protected',
        'permission_assigned',
        'group_name',
        'group_uuid',
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
     * Get the group that owns the permission.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_uuid', 'group_uuid');
    }

    /**
     * Get the permission.
     */
    public function permission()
    {
        return $this->belongsTo(Permission::class, 'permission_name', 'permission_name');
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
        return ['group_permission_uuid'];
    }
}
