<?php

namespace App\Http\Controllers;

use App\Models\SMSCampaign;
use App\Models\Domain;
use App\Models\CampaignContact;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class SMSCampaignController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = SMSCampaign::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $campaigns = $query->orderBy('created_at', 'desc')->paginate(15);
        $domains = Domain::all();
        
        return view('sms-campaigns.index', compact('campaigns', 'domains'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('sms-campaigns.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'campaign_name' => 'required|string|max:255',
            'campaign_description' => 'nullable|string',
            'sms_template' => 'required|string',
            'sender_id' => 'required|string|max:11',
            'sending_rate' => 'required|integer|min:1|max:100',
            'schedule_type' => 'required|in:immediate,scheduled,recurring',
            'scheduled_at' => 'nullable|date',
            'ai_enabled' => 'boolean',
            'ai_model' => 'nullable|string',
            'ai_personality' => 'nullable|string',
            'ai_reply_handling' => 'boolean',
        ]);

        $validated['sms_campaign_uuid'] = (string) Str::uuid();
        $validated['campaign_status'] = 'draft';
        $validated['ai_enabled'] = $request->has('ai_enabled');
        $validated['ai_reply_handling'] = $request->has('ai_reply_handling');

        if ($validated['ai_enabled']) {
            $validated['ai_config'] = [
                'model' => $validated['ai_model'] ?? 'gpt-3.5-turbo',
                'temperature' => 0.7,
                'max_tokens' => 150,
            ];
        }

        SMSCampaign::create($validated);

        return redirect()->route('sms-campaigns.index')
            ->with('success', 'SMS Campaign created successfully.');
    }

    public function show(SMSCampaign $smsCampaign)
    {
        $smsCampaign->load('domain', 'messages', 'contacts');
        
        $stats = [
            'success_rate' => $smsCampaign->success_rate,
            'pending' => $smsCampaign->total_contacts - $smsCampaign->sent_count,
            'ai_processed' => $smsCampaign->messages()->where('ai_processed', true)->count(),
        ];
        
        return view('sms-campaigns.show', compact('smsCampaign', 'stats'));
    }

    public function start(SMSCampaign $smsCampaign)
    {
        $smsCampaign->update(['campaign_status' => 'active']);
        
        return redirect()->route('sms-campaigns.show', $smsCampaign)
            ->with('success', 'SMS Campaign started successfully.');
    }

    public function pause(SMSCampaign $smsCampaign)
    {
        $smsCampaign->update(['campaign_status' => 'paused']);
        
        return redirect()->route('sms-campaigns.show', $smsCampaign)
            ->with('success', 'SMS Campaign paused successfully.');
    }
}
