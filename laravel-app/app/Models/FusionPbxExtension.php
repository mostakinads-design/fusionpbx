<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * FusionPBX Extension Model
 * 
 * Represents a SIP extension in FusionPBX
 * 
 * @property string $extension_uuid
 * @property string|null $domain_uuid
 * @property string $extension
 * @property string|null $password
 * @property string|null $accountcode
 * @property string|null $effective_caller_id_name
 * @property string|null $effective_caller_id_number
 * @property string|null $outbound_caller_id_name
 * @property string|null $outbound_caller_id_number
 * @property string|null $emergency_caller_id_name
 * @property string|null $emergency_caller_id_number
 * @property string|null $directory_first_name
 * @property string|null $directory_last_name
 * @property string|null $directory_visible
 * @property string|null $do_not_disturb
 * @property string|null $forward_all_destination
 * @property string|null $forward_all_enabled
 * @property string|null $forward_busy_destination
 * @property string|null $forward_busy_enabled
 * @property string|null $forward_no_answer_destination
 * @property string|null $forward_no_answer_enabled
 * @property string|null $enabled
 * @property string|null $description
 */
class FusionPbxExtension extends FpbxBaseModel
{
    use HasUuids, SoftDeletes;

    /**
     * The table associated with the model.
     */
    protected $table = 'v_extensions';

    /**
     * The primary key for the model.
     */
    protected $primaryKey = 'extension_uuid';

    /**
     * The "type" of the primary key ID.
     */
    protected $keyType = 'string';

    /**
     * Indicates if the IDs are auto-incrementing.
     */
    public $incrementing = false;

    /**
     * The attributes that are mass assignable.
     */
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
        'max_registrations',
        'limit_max',
        'limit_destination',
        'missed_call_app',
        'missed_call_data',
        'user_context',
        'toll_allow',
        'call_timeout',
        'call_group',
        'call_screen_enabled',
        'user_record',
        'hold_music',
        'auth_acl',
        'sip_force_contact',
        'nibble_account',
        'sip_force_expires',
        'mwi_account',
        'sip_bypass_media',
        'unique_id',
        'dial_string',
        'dial_user',
        'dial_domain',
        'do_not_disturb',
        'forward_all_destination',
        'forward_all_enabled',
        'forward_busy_destination',
        'forward_busy_enabled',
        'forward_no_answer_destination',
        'forward_no_answer_enabled',
        'forward_user_not_registered_destination',
        'forward_user_not_registered_enabled',
        'follow_me_uuid',
        'follow_me_enabled',
        'follow_me_destinations',
        'extension_language',
        'extension_dialect',
        'extension_voice',
        'extension_type',
        'enabled',
        'description',
        'absolute_codec_string',
        'force_ping',
        'insert_user',
        'update_user',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'call_timeout' => 'integer',
        'sip_force_expires' => 'integer',
        'unique_id' => 'integer',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    /**
     * The attributes that should be hidden.
     */
    protected $hidden = [
        'password',
    ];

    /**
     * Get the domain.
     */
    public function domain(): BelongsTo
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the users assigned to this extension.
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(
            FusionPbxUser::class,
            'v_extension_users',
            'extension_uuid',
            'user_uuid',
            'extension_uuid',
            'user_uuid'
        );
    }

    /**
     * Get the extension settings.
     */
    public function settings(): HasMany
    {
        return $this->hasMany(ExtensionSetting::class, 'extension_uuid', 'extension_uuid');
    }

    /**
     * Get the CDR records for this extension.
     */
    public function cdrRecords(): HasMany
    {
        return $this->hasMany(XmlCdr::class, 'extension_uuid', 'extension_uuid');
    }

    /**
     * Get new UUID for the model
     */
    public function newUniqueId(): string
    {
        return static::generateUuid();
    }

    /**
     * Get the columns that should receive a unique identifier.
     */
    public function uniqueIds(): array
    {
        return ['extension_uuid'];
    }

    /**
     * Check if extension is enabled.
     */
    public function isEnabled(): bool
    {
        return $this->enabled === 'true';
    }

    /**
     * Check if DND is enabled.
     */
    public function isDndEnabled(): bool
    {
        return $this->do_not_disturb === 'true';
    }

    /**
     * Check if call forward all is enabled.
     */
    public function isForwardAllEnabled(): bool
    {
        return $this->forward_all_enabled === 'true';
    }

    /**
     * Check if call forward busy is enabled.
     */
    public function isForwardBusyEnabled(): bool
    {
        return $this->forward_busy_enabled === 'true';
    }

    /**
     * Check if call forward no answer is enabled.
     */
    public function isForwardNoAnswerEnabled(): bool
    {
        return $this->forward_no_answer_enabled === 'true';
    }

    /**
     * Get the full name from directory fields.
     */
    public function getFullNameAttribute(): string
    {
        return trim("{$this->directory_first_name} {$this->directory_last_name}");
    }

    /**
     * Scope to get only enabled extensions.
     */
    public function scopeEnabled($query)
    {
        return $query->where('enabled', 'true');
    }

    /**
     * Scope to get extensions with DND enabled.
     */
    public function scopeWithDnd($query)
    {
        return $query->where('do_not_disturb', 'true');
    }
}
