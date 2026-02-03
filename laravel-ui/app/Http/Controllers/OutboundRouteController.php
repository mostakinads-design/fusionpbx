<?php

namespace App\Http\Controllers;

use App\Models\OutboundRoute;
use App\Models\Domain;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class OutboundRouteController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = OutboundRoute::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $routes = $query->orderBy('route_order')->paginate(25);
        $domains = Domain::all();
        
        return view('outbound-routes.index', compact('routes', 'domains'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('outbound-routes.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'route_name' => 'required|string|max:255',
            'route_description' => 'nullable|string',
            'route_order' => 'required|integer|min:0',
            'destination_pattern' => 'required|string',
            'gateway_name' => 'required|string',
            'route_type' => 'required|in:voice,sms,both',
            'dial_prefix' => 'nullable|string',
            'prefix_strip' => 'nullable|integer|min:0',
            'caller_id_name' => 'nullable|string',
            'caller_id_number' => 'nullable|string',
            'is_active' => 'boolean',
            'ai_routing_enabled' => 'boolean',
            'ai_cost_optimization' => 'boolean',
            'ai_quality_optimization' => 'boolean',
        ]);

        $validated['route_uuid'] = (string) Str::uuid();
        $validated['is_active'] = $request->has('is_active');
        $validated['ai_routing_enabled'] = $request->has('ai_routing_enabled');
        $validated['ai_cost_optimization'] = $request->has('ai_cost_optimization');
        $validated['ai_quality_optimization'] = $request->has('ai_quality_optimization');

        if ($validated['ai_routing_enabled']) {
            $validated['ai_routing_rules'] = [
                'optimize_for' => $validated['ai_cost_optimization'] ? 'cost' : 'quality',
                'learning_enabled' => true,
            ];
            $validated['ai_routing_weights'] = [
                'cost' => $validated['ai_cost_optimization'] ? 0.7 : 0.3,
                'quality' => $validated['ai_quality_optimization'] ? 0.7 : 0.3,
            ];
        }

        OutboundRoute::create($validated);

        return redirect()->route('outbound-routes.index')
            ->with('success', 'Outbound Route created successfully.');
    }

    public function show(OutboundRoute $outboundRoute)
    {
        $outboundRoute->load('domain');
        return view('outbound-routes.show', compact('outboundRoute'));
    }

    public function testRoute(Request $request)
    {
        $validated = $request->validate([
            'destination_number' => 'required|string',
        ]);

        $routes = OutboundRoute::where('is_active', true)
            ->orderBy('route_order')
            ->get();

        $matchedRoute = null;
        foreach ($routes as $route) {
            if ($route->matchesNumber($validated['destination_number'])) {
                $matchedRoute = $route;
                break;
            }
        }

        if ($matchedRoute && $matchedRoute->ai_routing_enabled) {
            $optimization = $this->aiService->optimizeRoute(
                $validated['destination_number'],
                $routes,
                $matchedRoute->ai_cost_optimization ? 'cost' : 'quality'
            );

            return response()->json([
                'matched_route' => $matchedRoute,
                'ai_recommendation' => $optimization,
            ]);
        }

        return response()->json([
            'matched_route' => $matchedRoute,
        ]);
    }
}
