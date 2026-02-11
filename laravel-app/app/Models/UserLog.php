<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * User Log Model
 * 
 * Stores user activity logs
 * 
 * @property string $user_log_uuid
 * @property string|null $domain_uuid
 * @property string|null $hostname
 * @property \DateTime|null $timestamp
 * @property string|null $user_uuid
 * @property string|null $username
 * @property string|null $type
 * @property string|null $result
 * @property string|null $remote_address
 * @property string|null $user_agent
 * @property string|null $session_id
 */
class UserLog extends FpbxBaseModel
{
    use HasUuids;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_user_logs';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'user_log_uuid';

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
        'user_log_uuid',
        'domain_uuid',
        'hostname',
        'timestamp',
        'user_uuid',
        'username',
        'type',
        'result',
        'remote_address',
        'user_agent',
        'session_id',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'timestamp' => 'datetime',
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
        return ['user_log_uuid'];
    }

    /**
     * Scope to get successful logins.
     */
    public function scopeSuccessfulLogins($query)
    {
        return $query->where('type', 'login')->where('result', 'success');
    }

    /**
     * Scope to get failed logins.
     */
    public function scopeFailedLogins($query)
    {
        return $query->where('type', 'login')->where('result', 'failure');
    }

    /**
     * Scope to get recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('timestamp', '>=', now()->subDays($days));
    }
}
