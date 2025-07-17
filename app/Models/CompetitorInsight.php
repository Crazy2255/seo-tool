<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CompetitorInsight extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'domain',
        'url',
        'title',
        'description',
        'analytics_tools',
        'social_pixels',
        'chat_widgets',
        'email_marketing',
        'advertising_networks',
        'retargeting_tools',
        'seo_tools',
        'opengraph_tags',
        'schema_markup',
        'utm_parameters',
        'blog_analysis',
        'content_strategy',
        'performance_tools',
        'security_tools',
        'cms_detection',
        'strategy_summary',
        'actionable_insights',
        'total_tools_detected',
        'strategy_score',
        'backlinks_data',
        'total_backlinks',
        'keywords_data',
        'total_keywords',
        'last_scanned_at',
        'scan_duration_seconds',
        'scan_status',
        'scan_error',
    ];

    protected $casts = [
        'analytics_tools' => 'array',
        'social_pixels' => 'array',
        'chat_widgets' => 'array',
        'email_marketing' => 'array',
        'advertising_networks' => 'array',
        'retargeting_tools' => 'array',
        'seo_tools' => 'array',
        'opengraph_tags' => 'array',
        'schema_markup' => 'array',
        'utm_parameters' => 'array',
        'blog_analysis' => 'array',
        'content_strategy' => 'array',
        'performance_tools' => 'array',
        'security_tools' => 'array',
        'cms_detection' => 'array',
        'strategy_summary' => 'array',
        'actionable_insights' => 'array',
        'backlinks_data' => 'array',
        'keywords_data' => 'array',
        'last_scanned_at' => 'datetime',
        'total_tools_detected' => 'integer',
        'total_backlinks' => 'integer',
        'total_keywords' => 'integer',
        'strategy_score' => 'decimal:2',
        'scan_duration_seconds' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('last_scanned_at', '>=', now()->subDays($days));
    }

    public function scopeCompleted($query)
    {
        return $query->where('scan_status', 'completed');
    }

    public function getFormattedDomainAttribute()
    {
        return parse_url($this->url, PHP_URL_HOST) ?? $this->domain;
    }

    public function getStrategySummaryCountAttribute()
    {
        return is_array($this->strategy_summary) ? count($this->strategy_summary) : 0;
    }

    public function getTimeSinceLastScanAttribute()
    {
        return $this->last_scanned_at ? $this->last_scanned_at->diffForHumans() : 'Never';
    }
}
