<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

/**
 * FusionPBX User Model
 * 
 * Represents a user account in FusionPBX
 * 
 * @property string $user_uuid
 * @property string|null $domain_uuid
 * @property string|null $contact_uuid
 * @property string $username
 * @property string $password
 * @property string|null $salt
 * @property string|null $user_email
 * @property string|null $user_status
 * @property string|null $api_key
 * @property string|null $user_enabled
 */
class FusionPbxUser extends Authenticatable implements FilamentUser
{
    use HasApiTokens, Notifiable, HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_users';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_uuid';

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
        'user_uuid',
        'domain_uuid',
        'contact_uuid',
        'username',
        'password',
        'salt',
        'user_email',
        'user_status',
        'api_key',
        'user_totp_secret',
        'user_type',
        'user_enabled',
        'add_user',
        'add_date',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be hidden for serialization.
     */
    protected $hidden = [
        'password',
        'salt',
        'user_totp_secret',
        'api_key',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'add_date' => 'datetime',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain that owns the user.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the groups for the user.
     */
    public function groups(): BelongsToMany
    {
        return $this->belongsToMany(
            Group::class,
            'v_user_groups',
            'user_uuid',
            'group_uuid',
            'user_uuid',
            'group_uuid'
        );
    }

    /**
     * Get the user groups records.
     */
    public function userGroups(): HasMany
    {
        return $this->hasMany(UserGroup::class, 'user_uuid', 'user_uuid');
    }

    /**
     * Get the user settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(UserSetting::class, 'user_uuid', 'user_uuid');
    }

    /**
     * Get the user logs.
     */
    public function logs(): HasMany
    {
        return $this->hasMany(UserLog::class, 'user_uuid', 'user_uuid');
    }

    /**
     * Get the extensions assigned to this user.
     */
    public function extensions(): BelongsToMany
    {
        return $this->belongsToMany(
            FusionPbxExtension::class,
            'v_extension_users',
            'user_uuid',
            'extension_uuid',
            'user_uuid',
            'extension_uuid'
        );
    }

    /**
     * Get new UUID for the model
     */
    public function newUniqueId(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['user_uuid'];
    }

    /**
     * Determine if the user can access the Filament panel.
     */
    public function canAccessPanel(Panel $panel): bool
    {
        return $this->user_enabled === 'true';
    }

    /**
     * Check if user is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->user_enabled === 'true';
    }

    /**
     * Check if user has a specific permission.
     */
    public function hasPermission(string $permissionName): bool
    {
        return $this->groups()
            ->whereHas('permissions', function ($query) use ($permissionName) {
                $query->where('permission_name', $permissionName);
            })
            ->exists();
    }

    /**
     * Check if user belongs to a specific group.
     */
    public function inGroup(string $groupName): bool
    {
        return $this->groups()
            ->where('group_name', $groupName)
            ->exists();
    }
}
