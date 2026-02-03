<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignContact;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CampaignContactController extends Controller
{
    public function index(Campaign $campaign, Request $request)
    {
        $query = $campaign->contacts();

        if ($request->has('status')) {
            $query->where('status', $request->status);
        }

        $contacts = $query->paginate(50);
        
        return view('campaigns.contacts.index', compact('campaign', 'contacts'));
    }

    public function create(Campaign $campaign)
    {
        return view('campaigns.contacts.create', compact('campaign'));
    }

    public function store(Request $request, Campaign $campaign)
    {
        $validated = $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'nullable|string|max:255',
            'phone_number' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'custom_data' => 'nullable|array',
            'priority' => 'nullable|integer|min:1|max:10',
        ]);

        $validated['contact_uuid'] = (string) Str::uuid();
        $validated['campaign_uuid'] = $campaign->campaign_uuid;
        $validated['status'] = 'new';
        $validated['attempts'] = 0;
        
        CampaignContact::create($validated);

        return redirect()->route('campaigns.contacts.index', $campaign)
            ->with('success', 'Contact added successfully.');
    }

    public function import(Request $request, Campaign $campaign)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $file = $request->file('csv_file');
        $handle = fopen($file->getRealPath(), 'r');
        
        $header = fgetcsv($handle);
        $imported = 0;
        
        while (($row = fgetcsv($handle)) !== false) {
            if (count($row) >= 2) {
                CampaignContact::create([
                    'contact_uuid' => (string) Str::uuid(),
                    'campaign_uuid' => $campaign->campaign_uuid,
                    'first_name' => $row[0] ?? '',
                    'last_name' => $row[1] ?? '',
                    'phone_number' => $row[2] ?? '',
                    'email' => $row[3] ?? null,
                    'status' => 'new',
                    'attempts' => 0,
                    'priority' => 5,
                ]);
                $imported++;
            }
        }
        
        fclose($handle);

        return redirect()->route('campaigns.contacts.index', $campaign)
            ->with('success', "Successfully imported {$imported} contacts.");
    }

    public function destroy(Campaign $campaign, CampaignContact $contact)
    {
        $contact->delete();

        return redirect()->route('campaigns.contacts.index', $campaign)
            ->with('success', 'Contact deleted successfully.');
    }
}
