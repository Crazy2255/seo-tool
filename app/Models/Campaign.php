<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Campaign extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'image',
        'website_url',
        'share_link',
        'status',
        'start_date',
        'end_date',
        'campaign_type',
    ];

    protected $casts = [
        'start_date' => 'datetime',
        'end_date' => 'datetime',
    ];

    // Relationship with user
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Relationship with campaign analytics
    public function analytics()
    {
        return $this->hasMany(CampaignAnalytic::class);
    }

    // Relationship with campaign leads
    public function leads()
    {
        return $this->hasMany(CampaignLead::class);
    }

    // Scope for user's campaigns
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    // Scope for active campaigns
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    // Get total impressions
    public function getTotalImpressionsAttribute()
    {
        return $this->analytics->sum('impressions');
    }

    // Get total clicks
    public function getTotalClicksAttribute()
    {
        return $this->analytics->sum('clicks');
    }

    // Get total conversions
    public function getTotalConversionsAttribute()
    {
        return $this->analytics->sum('conversions');
    }

    // Get click-through rate
    public function getCtrAttribute()
    {
        $impressions = $this->total_impressions;
        if ($impressions == 0) {
            return 0;
        }
        return ($this->total_clicks / $impressions) * 100;
    }

    // Get conversion rate
    public function getConversionRateAttribute()
    {
        $clicks = $this->total_clicks;
        if ($clicks == 0) {
            return 0;
        }
        return ($this->total_conversions / $clicks) * 100;
    }

    // Get unique share link
    public function getShareUrlAttribute()
    {
        return url("/campaign/{$this->share_link}");
    }
}
