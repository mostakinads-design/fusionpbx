<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Domain extends Model
{
    protected $table = 'v_domains';
    protected $primaryKey = 'domain_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'domain_uuid',
        'domain_name',
        'domain_enabled',
        'domain_description',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'domain_enabled' => 'boolean',
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

    public function users()
    {
        return $this->hasMany(User::class, 'domain_uuid', 'domain_uuid');
    }

    public function extensions()
    {
        return $this->hasMany(Extension::class, 'domain_uuid', 'domain_uuid');
    }

    public function callCenterQueues()
    {
        return $this->hasMany(CallCenterQueue::class, 'domain_uuid', 'domain_uuid');
    }

    public function campaigns()
    {
        return $this->hasMany(Campaign::class, 'domain_uuid', 'domain_uuid');
    }
}
