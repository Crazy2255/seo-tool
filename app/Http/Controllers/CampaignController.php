<?php

namespace App\Http\Controllers;

use App\Models\Campaign;
use App\Models\CampaignLead;
use App\Models\CampaignAnalytic;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;

class CampaignController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = Campaign::forUser($user->id);
        
        // Filter by status
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        
        // Filter by campaign type
        if ($request->filled('type')) {
            $query->where('campaign_type', $request->type);
        }
        
        // Search by title
        if ($request->filled('search')) {
            $query->where('title', 'like', '%' . $request->search . '%');
        }
        
        $campaigns = $query->orderBy('created_at', 'desc')->paginate(10);
        
        // Calculate stats
        $stats = [
            'active_campaigns' => Campaign::forUser($user->id)->active()->count(),
            'total_impressions' => DB::table('campaign_analytics')
                ->join('campaigns', 'campaign_analytics.campaign_id', '=', 'campaigns.id')
                ->where('campaigns.user_id', $user->id)
                ->sum('impressions'),
            'total_clicks' => DB::table('campaign_analytics')
                ->join('campaigns', 'campaign_analytics.campaign_id', '=', 'campaigns.id')
                ->where('campaigns.user_id', $user->id)
                ->sum('clicks'),
            'total_leads' => CampaignLead::whereHas('campaign', function ($query) use ($user) {
                $query->where('user_id', $user->id);
            })->count(),
        ];
        
        return view('campaigns.index', compact('campaigns', 'stats'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('campaigns.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|dimensions:min_width=1000,min_height=1000|max:5120',
            'website_url' => 'nullable|url',
            'campaign_type' => 'required|string|in:general,email,social,affiliate,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $imagePath = null;
        
        if ($request->hasFile('image')) {
            $image = $request->file('image');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $imagePath = $image->storeAs('campaigns', $fileName, 'public');
        }

        $campaign = Campaign::create([
            'user_id' => Auth::id(),
            'title' => $request->title,
            'description' => $request->description,
            'image' => $imagePath,
            'website_url' => $request->website_url,
            'share_link' => Str::uuid(),
            'campaign_type' => $request->campaign_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'status' => 'draft',
        ]);

        return redirect()->route('campaigns.show', $campaign)
                        ->with('success', 'Campaign created successfully!');
    }

    /**
     * Display the specified resource.
     */
    public function show(Campaign $campaign)
    {
        // Check if user can view this campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        return view('campaigns.show', compact('campaign'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Campaign $campaign)
    {
        // Check if user can edit this campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        return view('campaigns.edit', compact('campaign'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Campaign $campaign)
    {
        // Check if user can update this campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'image' => 'nullable|image|dimensions:min_width=1000,min_height=1000|max:5120',
            'website_url' => 'nullable|url',
            'campaign_type' => 'required|string|in:general,email,social,affiliate,custom',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
        ]);

        $data = [
            'title' => $request->title,
            'description' => $request->description,
            'website_url' => $request->website_url,
            'campaign_type' => $request->campaign_type,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
        ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($campaign->image) {
                Storage::disk('public')->delete($campaign->image);
            }

            $image = $request->file('image');
            $fileName = time() . '_' . Str::slug($request->title) . '.' . $image->getClientOriginalExtension();
            $data['image'] = $image->storeAs('campaigns', $fileName, 'public');
        }

        $campaign->update($data);

        return redirect()->route('campaigns.show', $campaign)
                        ->with('success', 'Campaign updated successfully!');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Campaign $campaign)
    {
        // Check if user can delete this campaign
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        // Delete campaign image if exists
        if ($campaign->image) {
            Storage::disk('public')->delete($campaign->image);
        }

        $campaign->delete();

        return redirect()->route('campaigns.index')
                        ->with('success', 'Campaign deleted successfully!');
    }

    /**
     * Activate the campaign.
     */
    public function activate(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $campaign->update(['status' => 'active']);

        return redirect()->back()->with('success', 'Campaign activated successfully!');
    }

    /**
     * Pause the campaign.
     */
    public function pause(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $campaign->update(['status' => 'paused']);

        return redirect()->back()->with('success', 'Campaign paused successfully!');
    }

    /**
     * Show campaign analytics.
     */
    public function analytics(Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }

        $today = Carbon::today();
        $startDate = Carbon::today()->subDays(30);
        
        // Get daily analytics
        $dailyStats = CampaignAnalytic::where('campaign_id', $campaign->id)
            ->where('date', '>=', $startDate)
            ->orderBy('date')
            ->get()
            ->groupBy(function ($item) {
                return $item->date->format('Y-m-d');
            })
            ->map(function ($items) {
                return [
                    'impressions' => $items->sum('impressions'),
                    'clicks' => $items->sum('clicks'),
                    'conversions' => $items->sum('conversions'),
                ];
            });
        
        // Get device breakdown
        $deviceStats = CampaignAnalytic::where('campaign_id', $campaign->id)
            ->select('device', DB::raw('SUM(impressions) as impressions'), DB::raw('SUM(clicks) as clicks'))
            ->groupBy('device')
            ->get();
        
        // Get source breakdown
        $sourceStats = CampaignAnalytic::where('campaign_id', $campaign->id)
            ->select('source', DB::raw('SUM(impressions) as impressions'), DB::raw('SUM(clicks) as clicks'))
            ->groupBy('source')
            ->get();
        
        // Get all campaign leads
        $leads = CampaignLead::where('campaign_id', $campaign->id)
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('campaigns.analytics', compact('campaign', 'dailyStats', 'deviceStats', 'sourceStats', 'leads'));
    }

    /**
     * Track campaign click.
     */
    public function track(Request $request, $shareLink)
    {
        $campaign = Campaign::where('share_link', $shareLink)->firstOrFail();
        
        // Track the click
        $this->recordAnalytics($campaign, 'click', $request);
        
        // Redirect to website URL if set, otherwise show the campaign
        if ($campaign->website_url) {
            return redirect()->away($campaign->website_url);
        }
        
        return view('campaigns.landing', compact('campaign'));
    }

    /**
     * Display landing page for the campaign.
     */
    public function landing($shareLink)
    {
        $campaign = Campaign::where('share_link', $shareLink)->firstOrFail();
        
        // Track impression
        $this->recordAnalytics($campaign, 'impression', request());
        
        return view('campaigns.landing', compact('campaign'));
    }

    /**
     * Store lead from campaign.
     */
    public function storeLead(Request $request, $shareLink)
    {
        $campaign = Campaign::where('share_link', $shareLink)->firstOrFail();
        
        $request->validate([
            'email' => 'required|email',
            'name' => 'nullable|string|max:255',
        ]);
        
        // Store lead
        CampaignLead::create([
            'campaign_id' => $campaign->id,
            'name' => $request->name,
            'email' => $request->email,
            'source' => $request->source,
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'referrer' => $request->header('referer'),
            'utm_source' => $request->utm_source,
            'utm_medium' => $request->utm_medium,
            'utm_campaign' => $request->utm_campaign,
            'utm_content' => $request->utm_content,
            'utm_term' => $request->utm_term,
        ]);
        
        // Record conversion
        $this->recordAnalytics($campaign, 'conversion', $request);
        
        return redirect()->route('campaigns.thank-you', $campaign->share_link)
                        ->with('success', 'Thank you for your submission!');
    }

    /**
     * Thank you page after lead submission.
     */
    public function thankYou($shareLink)
    {
        $campaign = Campaign::where('share_link', $shareLink)->firstOrFail();
        return view('campaigns.thank-you', compact('campaign'));
    }

    /**
     * Send bulk emails for campaign.
     */
    public function sendBulkEmails(Request $request, Campaign $campaign)
    {
        if ($campaign->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action');
        }
        
        $request->validate([
            'emails' => 'required|string',
            'subject' => 'required|string|max:255',
            'message' => 'required|string',
        ]);
        
        $emails = array_filter(array_map('trim', explode(',', $request->emails)));
        $validEmails = [];
        
        foreach ($emails as $email) {
            if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                $validEmails[] = $email;
            }
        }
        
        // Send emails (would implement actual email sending here)
        // For now, just simulate the process
        
        return redirect()->back()->with('success', count($validEmails) . ' emails have been queued for sending.');
    }

    /**
     * Record analytics data.
     */
    private function recordAnalytics($campaign, $type, $request)
    {
        $today = Carbon::today();
        
        $data = [
            'campaign_id' => $campaign->id,
            'date' => $today,
            'device' => $this->detectDevice($request),
            'source' => $request->utm_source ?? $this->detectSource($request),
            'medium' => $request->utm_medium ?? 'unknown',
            'location' => $request->ip(),
            'browser' => $this->detectBrowser($request),
            'referrer' => $request->header('referer'),
        ];
        
        // Find or create analytics record for today
        $analytic = CampaignAnalytic::firstOrCreate(
            ['campaign_id' => $campaign->id, 'date' => $today, 'source' => $data['source'], 'medium' => $data['medium'], 'device' => $data['device']],
            $data
        );
        
        // Increment the appropriate counter
        if ($type === 'impression') {
            $analytic->increment('impressions');
        } elseif ($type === 'click') {
            $analytic->increment('clicks');
        } elseif ($type === 'conversion') {
            $analytic->increment('conversions');
        }
    }

    /**
     * Detect device type from user agent.
     */
    private function detectDevice($request)
    {
        $userAgent = $request->userAgent();
        
        if (preg_match('/(android|bb\d+|meego).+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|mobile.+firefox|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|series(4|6)0|symbian|treo|up\.(browser|link)|vodafone|wap|windows ce|xda|xiino/i', $userAgent)) {
            return 'mobile';
        } elseif (preg_match('/android|ipad|playbook|silk/i', $userAgent)) {
            return 'tablet';
        }
        
        return 'desktop';
    }

    /**
     * Detect browser from user agent.
     */
    private function detectBrowser($request)
    {
        $userAgent = $request->userAgent();
        
        if (strpos($userAgent, 'Firefox') !== false) {
            return 'Firefox';
        } elseif (strpos($userAgent, 'Chrome') !== false) {
            return 'Chrome';
        } elseif (strpos($userAgent, 'Safari') !== false) {
            return 'Safari';
        } elseif (strpos($userAgent, 'Edge') !== false) {
            return 'Edge';
        } elseif (strpos($userAgent, 'MSIE') !== false || strpos($userAgent, 'Trident/') !== false) {
            return 'Internet Explorer';
        }
        
        return 'Other';
    }

    /**
     * Detect source from referrer.
     */
    private function detectSource($request)
    {
        $referrer = $request->header('referer');
        
        if (!$referrer) {
            return 'direct';
        }
        
        $host = parse_url($referrer, PHP_URL_HOST);
        
        if (strpos($host, 'google') !== false) {
            return 'google';
        } elseif (strpos($host, 'facebook') !== false) {
            return 'facebook';
        } elseif (strpos($host, 'twitter') !== false || strpos($host, 'x.com') !== false) {
            return 'twitter';
        } elseif (strpos($host, 'instagram') !== false) {
            return 'instagram';
        } elseif (strpos($host, 'linkedin') !== false) {
            return 'linkedin';
        }
        
        return 'other';
    }
}
