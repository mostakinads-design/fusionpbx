<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ExtensionUser extends Model
{
    protected $table = 'v_extension_users';
    protected $primaryKey = 'extension_user_uuid';
    public $incrementing = false;
    protected $keyType = 'string';
    
    const CREATED_AT = null;
    const UPDATED_AT = null;

    protected $fillable = [
        'extension_user_uuid',
        'domain_uuid',
        'extension_uuid',
        'user_uuid',
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

    public function extension()
    {
        return $this->belongsTo(Extension::class, 'extension_uuid', 'extension_uuid');
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
