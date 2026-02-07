<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User extends Model
{
    protected $table = 'v_users';
    protected $primaryKey = 'user_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'user_uuid',
        'domain_uuid',
        'username',
        'password',
        'salt',
        'api_key',
        'user_enabled',
        'contact_uuid',
        'user_email',
        'insert_user',
        'update_user',
    ];

    protected $hidden = [
        'password',
        'salt',
        'api_key',
    ];

    protected $casts = [
        'user_enabled' => 'boolean',
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

    public function extensions()
    {
        return $this->belongsToMany(
            Extension::class,
            'v_extension_users',
            'user_uuid',
            'extension_uuid',
            'user_uuid',
            'extension_uuid'
        );
    }

    public function settings()
    {
        return $this->hasMany(UserSetting::class, 'user_uuid', 'user_uuid');
    }
}
