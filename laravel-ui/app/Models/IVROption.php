<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IVROption extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ivr_options';
    protected $primaryKey = 'option_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ivr_uuid',
        'option_digits',
        'option_action',
        'option_param',
        'option_description',
        'option_order',
    ];

    protected $casts = [
        'option_order' => 'integer',
    ];

    public function ivr()
    {
        return $this->belongsTo(IVR::class, 'ivr_uuid', 'ivr_uuid');
    }
}
