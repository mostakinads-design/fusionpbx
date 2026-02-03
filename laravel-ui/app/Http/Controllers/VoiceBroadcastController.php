<?php

namespace App\Http\Controllers;

use App\Models\VoiceBroadcast;
use App\Models\Domain;
use App\Services\AIService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VoiceBroadcastController extends Controller
{
    protected $aiService;

    public function __construct(AIService $aiService)
    {
        $this->aiService = $aiService;
    }

    public function index(Request $request)
    {
        $query = VoiceBroadcast::with('domain');

        if ($request->has('domain_uuid')) {
            $query->where('domain_uuid', $request->domain_uuid);
        }

        $broadcasts = $query->orderBy('created_at', 'desc')->paginate(15);
        $domains = Domain::all();
        
        return view('voice-broadcasts.index', compact('broadcasts', 'domains'));
    }

    public function create()
    {
        $domains = Domain::where('domain_enabled', true)->get();
        return view('voice-broadcasts.create', compact('domains'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'domain_uuid' => 'required|exists:v_domains,domain_uuid',
            'broadcast_name' => 'required|string|max:255',
            'broadcast_description' => 'nullable|string',
            'text_to_speech' => 'boolean',
            'tts_text' => 'nullable|string',
            'tts_voice' => 'nullable|string',
            'audio_file' => 'nullable|file|mimes:mp3,wav|max:10240',
            'caller_id_name' => 'nullable|string',
            'caller_id_number' => 'nullable|string',
            'max_retries' => 'required|integer|min:1|max:10',
            'ai_enabled' => 'boolean',
            'ai_conversation_mode' => 'boolean',
            'ai_system_prompt' => 'nullable|string',
            'ai_max_conversation_turns' => 'nullable|integer|min:1|max:20',
        ]);

        $validated['broadcast_uuid'] = (string) Str::uuid();
        $validated['broadcast_status'] = 'draft';
        $validated['text_to_speech'] = $request->has('text_to_speech');
        $validated['ai_enabled'] = $request->has('ai_enabled');
        $validated['ai_conversation_mode'] = $request->has('ai_conversation_mode');

        // Handle audio file upload
        if ($request->hasFile('audio_file')) {
            $path = $request->file('audio_file')->store('broadcasts', 'public');
            $validated['audio_file_path'] = $path;
        }

        // Generate TTS if enabled
        if ($validated['text_to_speech'] && !empty($validated['tts_text'])) {
            $ttsResult = $this->aiService->textToSpeech($validated['tts_text'], $validated['tts_voice'] ?? 'alloy');
            if ($ttsResult['success']) {
                $filename = 'tts_' . Str::uuid() . '.mp3';
                Storage::disk('public')->put('broadcasts/' . $filename, $ttsResult['audio_content']);
                $validated['audio_file_path'] = 'broadcasts/' . $filename;
            }
        }

        if ($validated['ai_enabled']) {
            $validated['ai_config'] = [
                'model' => 'gpt-4',
                'conversation_mode' => $validated['ai_conversation_mode'],
                'max_turns' => $validated['ai_max_conversation_turns'] ?? 5,
            ];
        }

        VoiceBroadcast::create($validated);

        return redirect()->route('voice-broadcasts.index')
            ->with('success', 'Voice Broadcast created successfully.');
    }

    public function show(VoiceBroadcast $voiceBroadcast)
    {
        $voiceBroadcast->load('domain', 'messages', 'contacts');
        
        $stats = [
            'answer_rate' => $voiceBroadcast->total_contacts > 0 
                ? round(($voiceBroadcast->answered_count / $voiceBroadcast->total_contacts) * 100, 2) 
                : 0,
            'ai_conversations' => $voiceBroadcast->messages()->where('ai_conversation_occurred', true)->count(),
        ];
        
        return view('voice-broadcasts.show', compact('voiceBroadcast', 'stats'));
    }

    public function start(VoiceBroadcast $voiceBroadcast)
    {
        $voiceBroadcast->update(['broadcast_status' => 'active']);
        
        return redirect()->route('voice-broadcasts.show', $voiceBroadcast)
            ->with('success', 'Voice Broadcast started successfully.');
    }
}
