<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Domain extends Model
{
    use HasFactory, HasUuids;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'v_domains';

    /**
     * The primary key associated with the table.
     *
     * @var string
     */
    protected $primaryKey = 'domain_uuid';

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
        'domain_name',
        'domain_enabled',
        'domain_description',
        'domain_parent_uuid',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'domain_enabled' => 'boolean',
        'insert_date' => 'datetime',
        'update_date' => 'datetime',
    ];

    /**
     * Get the users for the domain.
     */
    public function users()
    {
        return $this->hasMany(User::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the extensions for the domain.
     */
    public function extensions()
    {
        return $this->hasMany(Extension::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the call records for the domain.
     */
    public function callRecords()
    {
        return $this->hasMany(CallRecord::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Get the parent domain.
     */
    public function parent()
    {
        return $this->belongsTo(Domain::class, 'domain_parent_uuid', 'domain_uuid');
    }

    /**
     * Get the child domains.
     */
    public function children()
    {
        return $this->hasMany(Domain::class, 'domain_parent_uuid', 'domain_uuid');
    }
}
