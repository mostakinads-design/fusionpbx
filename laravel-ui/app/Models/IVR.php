<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class IVR extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'ivr_menus';
    protected $primaryKey = 'ivr_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'ivr_name',
        'ivr_extension',
        'ivr_description',
        'ivr_greeting_long',
        'ivr_greeting_short',
        'ivr_invalid_sound',
        'ivr_exit_sound',
        'ivr_timeout',
        'ivr_inter_digit_timeout',
        'ivr_max_failures',
        'ivr_max_timeouts',
        'ivr_digit_len',
        'is_active',
        'direct_dial',
        'ringback',
        'caller_id_name_prefix',
        'caller_id_number_prefix',
    ];

    protected $casts = [
        'ivr_timeout' => 'integer',
        'ivr_inter_digit_timeout' => 'integer',
        'ivr_max_failures' => 'integer',
        'ivr_max_timeouts' => 'integer',
        'ivr_digit_len' => 'integer',
        'is_active' => 'boolean',
        'direct_dial' => 'boolean',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    public function options()
    {
        return $this->hasMany(IVROption::class, 'ivr_uuid', 'ivr_uuid')->orderBy('option_digits');
    }
}
