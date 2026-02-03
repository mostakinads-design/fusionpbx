<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class OutboundRoute extends Model
{
    use HasFactory, HasUuids;

    protected $table = 'outbound_routes';
    protected $primaryKey = 'route_uuid';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'domain_uuid',
        'route_name',
        'route_description',
        'route_order',
        'dial_prefix',
        'prefix_strip',
        'destination_pattern',
        'gateway_uuid',
        'gateway_name',
        'is_active',
        'is_emergency',
        'route_type',
        'caller_id_name',
        'caller_id_number',
        'limit_max',
        'account_code',
        'ai_routing_enabled',
        'ai_routing_rules',
        'ai_cost_optimization',
        'ai_quality_optimization',
        'ai_routing_weights',
    ];

    protected $casts = [
        'route_order' => 'integer',
        'prefix_strip' => 'integer',
        'is_active' => 'boolean',
        'is_emergency' => 'boolean',
        'limit_max' => 'integer',
        'ai_routing_enabled' => 'boolean',
        'ai_routing_rules' => 'array',
        'ai_cost_optimization' => 'boolean',
        'ai_quality_optimization' => 'boolean',
        'ai_routing_weights' => 'array',
    ];

    public function domain()
    {
        return $this->belongsTo(Domain::class, 'domain_uuid', 'domain_uuid');
    }

    /**
     * Check if this route matches a given number
     */
    public function matchesNumber($number)
    {
        if (empty($this->destination_pattern)) {
            return false;
        }

        // Convert dial plan pattern to regex
        $pattern = str_replace('X', '\d', $this->destination_pattern);
        $pattern = '/^' . $pattern . '$/';
        
        return preg_match($pattern, $number);
    }
}
