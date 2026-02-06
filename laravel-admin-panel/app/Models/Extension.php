<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Extension extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_extensions';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'extension_uuid';

    /**
     * Indicates if the model's ID is auto-incrementing.
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
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'domain_uuid',
        'extension',
        'number_alias',
        'password',
        'accountcode',
        'effective_caller_id_name',
        'effective_caller_id_number',
        'outbound_caller_id_name',
        'outbound_caller_id_number',
        'directory_user_uuid',
        'directory_username',
        'directory_full_name',
        'enabled',
        'description',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'enabled' => 'boolean',
    ];

    /**
     * Scope a query to only include enabled extensions.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 'true');
    }
}
