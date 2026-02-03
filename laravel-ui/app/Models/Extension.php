<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Extension extends Model
{
    use HasFactory, HasUuids;

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
        'extension',
        'number_alias',
        'password',
        'effective_caller_id_name',
        'effective_caller_id_number',
        'user_context',
        'call_timeout',
        'call_group',
        'hold_music',
        'do_not_disturb',
        'forward_all_destination',
        'user_record',
        'accountcode',
        'description',
        'enabled',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'enabled' => 'boolean',
        'do_not_disturb' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the domain that owns the extension.
     */
    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the call records for the extension.
     */
    public function callRecords()
    {
        return $this->hasMany(CallRecord::class, 'extension_uuid', 'extension_uuid');
    }
}
