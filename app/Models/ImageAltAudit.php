<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ImageAltAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'page_title',
        'total_images',
        'images_without_alt',
        'images_with_empty_alt',
        'images_with_good_alt',
        'images_with_issues',
        'images_data',
        'crawl_summary',
        'pages_crawled',
        'is_multi_page',
        'analyzed_at',
    ];

    protected $casts = [
        'images_data' => 'array',
        'crawl_summary' => 'array',
        'analyzed_at' => 'datetime',
        'is_multi_page' => 'boolean',
    ];

    /**
     * Get the user that owns the audit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope for user audits.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the overall accessibility score.
     */
    public function getAccessibilityScoreAttribute(): float
    {
        if ($this->total_images === 0) {
            return 100;
        }

        $goodImages = $this->images_with_good_alt;
        return round(($goodImages / $this->total_images) * 100, 1);
    }

    /**
     * Get the issues percentage.
     */
    public function getIssuesPercentageAttribute(): float
    {
        if ($this->total_images === 0) {
            return 0;
        }

        $issuesCount = $this->images_without_alt + $this->images_with_empty_alt + $this->images_with_issues;
        return round(($issuesCount / $this->total_images) * 100, 1);
    }

    /**
     * Get formatted URL for display.
     */
    public function getFormattedUrlAttribute(): string
    {
        return parse_url($this->url, PHP_URL_HOST) ?? $this->url;
    }

    /**
     * Get status badge color based on accessibility score.
     */
    public function getStatusBadgeAttribute(): array
    {
        $score = $this->accessibility_score;
        
        if ($score >= 90) {
            return ['color' => 'green', 'text' => 'Excellent'];
        } elseif ($score >= 70) {
            return ['color' => 'yellow', 'text' => 'Good'];
        } elseif ($score >= 50) {
            return ['color' => 'orange', 'text' => 'Needs Work'];
        } else {
            return ['color' => 'red', 'text' => 'Poor'];
        }
    }
}
