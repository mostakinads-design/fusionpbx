<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User Setting Model
 * 
 * Stores user-specific settings
 * 
 * @property string $user_setting_uuid
 * @property string $user_uuid
 * @property string|null $domain_uuid
 * @property string|null $user_setting_category
 * @property string|null $user_setting_subcategory
 * @property string|null $user_setting_name
 * @property string|null $user_setting_value
 * @property int|null $user_setting_order
 * @property bool $user_setting_enabled
 * @property string|null $user_setting_description
 */
class UserSetting extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_user_settings';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_setting_uuid';

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
        'user_setting_uuid',
        'user_uuid',
        'domain_uuid',
        'user_setting_category',
        'user_setting_subcategory',
        'user_setting_name',
        'user_setting_value',
        'user_setting_order',
        'user_setting_enabled',
        'user_setting_description',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'user_setting_order' => 'integer',
        'user_setting_enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(FusionPbxUser::class, 'user_uuid', 'user_uuid');
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
        return ['user_setting_uuid'];
    }
}
