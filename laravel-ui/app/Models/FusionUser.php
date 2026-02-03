<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class FusionUser extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_users';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'user_uuid';

    /**
     * Indicates if the IDs are auto-incrementing.
     *
     * @var bool
     */
    public $incrementing = false;

    /**
     * The data type of the auto-incrementing ID.
     *
     * @var string
     */
    protected $keyType = 'string';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'domain_uuid',
        'username',
        'password',
        'salt',
        'user_email',
        'user_status',
        'user_type',
        'user_enabled',
        'api_key',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'salt',
        'api_key',
        'user_totp_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'user_enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain that owns the user.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the user groups for the user.
     */
    public function userGroups()
    {
        return $this->hasMany(UserGroup::class, 'user_uuid', 'user_uuid');
    }

    /**
     * Get the balance for the user.
     */
    public function balance()
    {
        return $this->hasOne(\App\Models\UserBalance::class, 'user_uuid', 'user_uuid');
    }
}
