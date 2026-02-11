<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * DeviceLog Model
 * 
 * Device provisioning logs.
 * Tracks device provisioning requests and responses.
 *
 * @property string $device_log_uuid
 * @property string $domain_uuid
 * @property string $device_uuid
 * @property \Illuminate\Support\Carbon|null $datetime
 * @property string|null $device_address
 * @property string|null $request_scheme
 * @property string|null $http_host
 * @property string|null $server_port
 * @property string|null $server_protocol
 * @property string|null $query_string
 * @property string|null $remote_address
 * @property string|null $http_user_agent
 * @property string|null $http_status
 * @property string|null $http_status_code
 * @property string|null $http_content_body
 */
class DeviceLog extends FpbxBaseModel
{
    use HasUuids;

    protected $table = 'v_device_logs';
    protected $primaryKey = 'device_log_uuid';
    
    protected $fillable = [
        'domain_uuid',
        'device_uuid',
        'datetime',
        'device_address',
        'request_scheme',
        'http_host',
        'server_port',
        'server_protocol',
        'query_string',
        'remote_address',
        'http_user_agent',
        'http_status',
        'http_status_code',
        'http_content_body',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'datetime' => 'datetime',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the device.
     */
    public function device(): BelongsTo
    {
        return $this->belongsTo(Device::class, 'device_uuid', 'device_uuid');
    }

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Check if the request was successful.
     */
    public function isSuccessful(): bool
    {
        $code = (int) $this->http_status_code;
        return $code >= 200 && $code < 300;
    }

    /**
     * Scope for recent logs.
     */
    public function scopeRecent($query, int $days = 7)
    {
        return $query->where('datetime', '>=', now()->subDays($days));
    }

    /**
     * Scope for successful requests.
     */
    public function scopeSuccessful($query)
    {
        return $query->whereBetween('http_status_code', ['200', '299']);
    }

    /**
     * Scope for failed requests.
     */
    public function scopeFailed($query)
    {
        return $query->where(function ($q) {
            $q->where('http_status_code', '<', '200')
              ->orWhere('http_status_code', '>=', '400');
        });
    }

    /**
     * Scope by device address (MAC).
     */
    public function scopeByAddress($query, string $address)
    {
        return $query->where('device_address', $address);
    }
}
