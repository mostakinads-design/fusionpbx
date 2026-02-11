<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;

/**
 * User Group Model
 * 
 * Pivot model for user-group relationships
 * 
 * @property string $user_group_uuid
 * @property string|null $domain_uuid
 * @property string|null $group_name
 * @property string $group_uuid
 * @property string $user_uuid
 */
class UserGroup extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_user_groups';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_group_uuid';

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
        'user_group_uuid',
        'domain_uuid',
        'group_name',
        'group_uuid',
        'user_uuid',
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
     * Get the user.
     */
    public function user()
    {
        return $this->belongsTo(FusionPbxUser::class, 'user_uuid', 'user_uuid');
    }

    /**
     * Get the group.
     */
    public function group()
    {
        return $this->belongsTo(Group::class, 'group_uuid', 'group_uuid');
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
        return ['user_group_uuid'];
    }
}
