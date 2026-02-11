<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Domain Model
 * 
 * Represents a multi-tenant domain in FusionPBX
 * 
 * @property string $domain_uuid
 * @property string|null $domain_parent_uuid
 * @property string $domain_name
 * @property bool $domain_enabled
 * @property string|null $domain_description
 */
class Domain extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_domains';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'domain_uuid';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * Disable domain scoping for Domain model itself
     */
    protected $usesDomainScoping = false;

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'domain_uuid',
        'domain_parent_uuid',
        'domain_name',
        'domain_enabled',
        'domain_description',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'domain_enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the users for the domain.
     */
    public function users(): HasMany
    {
        return $this->hasMany(FusionPbxUser::class, 'domain_uuid');
    }

    /**
     * Get the extensions for the domain.
     */
    public function extensions(): HasMany
    {
        return $this->hasMany(FusionPbxExtension::class, 'domain_uuid');
    }

    /**
     * Get the gateways for the domain.
     */
    public function gateways(): HasMany
    {
        return $this->hasMany(Gateway::class, 'domain_uuid');
    }

    /**
     * Get the dialplans for the domain.
     */
    public function dialplans(): HasMany
    {
        return $this->hasMany(Dialplan::class, 'domain_uuid');
    }

    /**
     * Get the devices for the domain.
     */
    public function devices(): HasMany
    {
        return $this->hasMany(Device::class, 'domain_uuid');
    }

    /**
     * Get the domain settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(DomainSetting::class, 'domain_uuid');
    }

    /**
     * Get the parent domain.
     */
    public function parent()
    {
        return $this->belongsTo(Domain::class, 'domain_parent_uuid', 'domain_uuid');
    }

    /**
     * Get the child domains.
     */
    public function children(): HasMany
    {
        return $this->hasMany(Domain::class, 'domain_parent_uuid', 'domain_uuid');
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
        return ['domain_uuid'];
    }
}
