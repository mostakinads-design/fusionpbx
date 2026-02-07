<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Extension extends Model
{
    protected $table = 'v_extensions';
    protected $primaryKey = 'extension_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'extension_uuid',
        'domain_uuid',
        'extension',
        'number_alias',
        'password',
        'accountcode',
        'effective_caller_id_name',
        'effective_caller_id_number',
        'outbound_caller_id_name',
        'outbound_caller_id_number',
        'emergency_caller_id_name',
        'emergency_caller_id_number',
        'directory_first_name',
        'directory_last_name',
        'directory_visible',
        'directory_exten_visible',
        'limit_max',
        'limit_destination',
        'user_context',
        'enabled',
        'description',
        'insert_user',
        'update_user',
    ];

    protected $hidden = [
        'password',
    ];

    protected $casts = [
        'directory_visible' => 'boolean',
        'directory_exten_visible' => 'boolean',
        'enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function users()
    {
        return $this->belongsToMany(
            User::class,
            'v_extension_users',
            'extension_uuid',
            'user_uuid',
            'extension_uuid',
            'user_uuid'
        );
    }

    public function callCenterAgent()
    {
        return $this->hasOne(CallCenterAgent::class, 'agent_name', 'extension');
    }
}
