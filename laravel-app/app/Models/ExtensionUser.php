<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * ExtensionUser Model
 * 
 * Represents the relationship between extensions and users in FusionPBX.
 * Allows multiple users to be assigned to an extension.
 *
 * @property string $extension_user_uuid
 * @property string $domain_uuid
 * @property string $extension_uuid
 * @property string $user_uuid
 * @property \Illuminate\Support\Carbon $insert_date
 * @property string|null $insert_user
 * @property \Illuminate\Support\Carbon|null $update_date
 * @property string|null $update_user
 */
class ExtensionUser extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_extension_users';
    protected $primaryKey = 'extension_user_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'extension_uuid',
        'user_uuid',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain this assignment belongs to.
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
     * Get the user.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(FusionPbxUser::class, 'user_uuid', 'user_uuid');
    }
}
