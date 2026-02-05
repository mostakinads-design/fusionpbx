<?php

namespace App\Http\Controllers;

use App\Models\CampaignContact;
use App\Models\Campaign;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CampaignContactController extends Controller
{
    public function index(Request $request)
    {
        $query = CampaignContact::with('campaign');

        if ($request->has('campaign_uuid')) {
            $query->where('campaign_uuid', $request->campaign_uuid);
        }

        if ($request->has('status')) {
            $query->where('contact_status', $request->status);
        }

        if ($request->has('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('contact_name', 'like', "%{$search}%")
                  ->orWhere('contact_phone', 'like', "%{$search}%")
                  ->orWhere('contact_email', 'like', "%{$search}%");
            });
        }

        $contacts = $query->paginate(15);
        $campaigns = Campaign::all();

        return view('campaign-contacts.index', compact('contacts', 'campaigns'));
    }

    public function import(Request $request)
    {
        $campaigns = Campaign::all();
        $campaignUuid = $request->campaign_uuid;

        return view('campaign-contacts.import', compact('campaigns', 'campaignUuid'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'campaign_uuid' => 'required|exists:v_campaigns,campaign_uuid',
            'csv_file' => 'required|file|mimes:csv,txt|max:10240',
        ]);

        $campaign = Campaign::findOrFail($validated['campaign_uuid']);
        $file = $request->file('csv_file');
        
        if (($handle = fopen($file->getRealPath(), 'r')) !== false) {
            $header = fgetcsv($handle);
            $imported = 0;
            $errors = [];

            DB::beginTransaction();
            
            try {
                while (($row = fgetcsv($handle)) !== false) {
                    if (count($row) < 2) {
                        continue;
                    }

                    CampaignContact::create([
                        'campaign_uuid' => $campaign->campaign_uuid,
                        'domain_uuid' => $campaign->domain_uuid,
                        'contact_name' => $row[0] ?? '',
                        'contact_phone' => $row[1] ?? '',
                        'contact_email' => $row[2] ?? null,
                        'contact_status' => 'pending',
                        'call_attempts' => 0,
                        'insert_user' => optional(auth()->user())->username ?? 'system',
                    ]);

                    $imported++;
                }

                $campaign->increment('total_contacts', $imported);

                DB::commit();
                fclose($handle);

                return redirect()->route('campaign-contacts.index', ['campaign_uuid' => $campaign->campaign_uuid])
                    ->withSuccess("Successfully imported {$imported} contacts.");

            } catch (\Exception $e) {
                DB::rollBack();
                fclose($handle);
                
                return redirect()->back()
                    ->withErrors('Error importing contacts: ' . $e->getMessage());
            }
        }

        return redirect()->back()
            ->withErrors('Unable to read CSV file.');
    }

    public function destroy(CampaignContact $campaignContact)
    {
        $campaignUuid = $campaignContact->campaign_uuid;
        $campaignContact->delete();

        $campaign = Campaign::find($campaignUuid);
        if ($campaign) {
            $campaign->decrement('total_contacts');
        }

        return redirect()->back()
            ->withSuccess('Campaign contact deleted successfully.');
    }
}
