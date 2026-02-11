<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\DB;
use App\Models\Scopes\DomainScope;

/**
 * FpbxBaseModel
 * 
 * Base model for all FusionPBX models with multi-tenant domain support
 */
abstract class FpbxBaseModel extends Model
{
    use HasFactory;

    /**
     * The database connection that should be used by the model.
     *
     * @var string|null
     */
    protected $connection = null;

    /**
     * Indicates if the model should use domain scoping
     *
     * @var bool
     */
    protected $usesDomainScoping = true;

    /**
     * The domain UUID column name
     *
     * @var string
     */
    protected $domainColumn = 'domain_uuid';

    /**
     * Boot the model
     */
    protected static function boot()
    {
        parent::boot();

        static::addGlobalScope(new DomainScope);

        // Auto-set domain_uuid on creation if multi-tenant is enabled
        static::creating(function ($model) {
            if ($model->usesDomainScoping && 
                config('freeswitch.multi_tenant') && 
                !isset($model->{$model->domainColumn})) {
                
                $model->{$model->domainColumn} = session('domain_uuid') 
                    ?? auth()->user()?->domain_uuid 
                    ?? static::getDefaultDomainUuid();
            }
        });
    }

    /**
     * Get the default domain UUID
     */
    protected static function getDefaultDomainUuid(): ?string
    {
        $domain = DB::table('v_domains')
            ->where('domain_name', config('freeswitch.default_domain'))
            ->first();

        return $domain->domain_uuid ?? null;
    }

    /**
     * Scope a query to a specific domain
     */
    public function scopeForDomain($query, string $domainUuid)
    {
        if ($this->usesDomainScoping) {
            return $query->where($this->domainColumn, $domainUuid);
        }

        return $query;
    }

    /**
     * Scope a query without domain restriction
     */
    public function scopeWithoutDomainScope($query)
    {
        return $query->withoutGlobalScope(DomainScope::class);
    }

    /**
     * Get the table prefix for FusionPBX tables
     */
    public function getTable()
    {
        if (!isset($this->table)) {
            // Auto-generate table name with v_ prefix for FusionPBX convention
            $this->table = 'v_' . str_replace('\\', '', snake_case(str_replace('App\\Models\\', '', static::class)));
        }

        return $this->table;
    }

    /**
     * Generate a UUID v4
     */
    public static function generateUuid(): string
    {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0xffff)
        );
    }

    /**
     * Set the UUID before creating if not set
     */
    protected static function bootFpbxBaseModel()
    {
        static::creating(function ($model) {
            $primaryKey = $model->getKeyName();
            
            // If primary key ends with _uuid and is not set, generate one
            if (str_ends_with($primaryKey, '_uuid') && !$model->$primaryKey) {
                $model->$primaryKey = static::generateUuid();
            }
        });
    }
}
