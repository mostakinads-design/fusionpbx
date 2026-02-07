<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class UserSetting extends Model
{
    protected $table = 'v_user_settings';
    protected $primaryKey = 'user_setting_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = 'insert_date';
    const UPDATED_AT = 'update_date';

    protected $fillable = [
        'user_setting_uuid',
        'user_uuid',
        'domain_uuid',
        'user_setting_category',
        'user_setting_subcategory',
        'user_setting_name',
        'user_setting_value',
        'user_setting_enabled',
        'user_setting_description',
        'insert_user',
        'update_user',
    ];

    protected $casts = [
        'user_setting_enabled' => 'boolean',
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

    public function user()
    {
        return $this->belongsTo(User::class, 'user_uuid', 'user_uuid');
    }

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }
}
