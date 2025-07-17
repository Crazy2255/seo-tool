<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PageSpeedAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'strategy',
        'performance_score',
        'accessibility_score',
        'best_practices_score',
        'seo_score',
        'first_contentful_paint',
        'largest_contentful_paint',
        'total_blocking_time',
        'cumulative_layout_shift',
        'speed_index',
        'first_meaningful_paint',
        'time_to_interactive',
        'max_potential_fid',
        'page_title',
        'screenshot_url',
        'raw_data',
        'analyzed_at',
        'lighthouse_version',
    ];

    protected $casts = [
        'raw_data' => 'array',
        'analyzed_at' => 'datetime',
        'first_contentful_paint' => 'decimal:2',
        'largest_contentful_paint' => 'decimal:2',
        'total_blocking_time' => 'decimal:2',
        'cumulative_layout_shift' => 'decimal:4',
        'speed_index' => 'decimal:2',
        'first_meaningful_paint' => 'decimal:2',
        'time_to_interactive' => 'decimal:2',
        'max_potential_fid' => 'decimal:2',
    ];

    /**
     * Get the user that owns the audit.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope to get audits for a specific user.
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    /**
     * Get the performance rating based on score.
     */
    public function getPerformanceRatingAttribute(): string
    {
        if ($this->performance_score >= 90) {
            return 'good';
        } elseif ($this->performance_score >= 50) {
            return 'needs-improvement';
        } else {
            return 'poor';
        }
    }

    /**
     * Get the overall rating based on performance score.
     */
    public function getOverallRatingAttribute(): string
    {
        return $this->getPerformanceRatingAttribute();
    }

    /**
     * Get Core Web Vitals rating for FCP.
     */
    public function getFcpRatingAttribute(): string
    {
        if ($this->first_contentful_paint <= 1.8) {
            return 'good';
        } elseif ($this->first_contentful_paint <= 3.0) {
            return 'needs-improvement';
        } else {
            return 'poor';
        }
    }

    /**
     * Get Core Web Vitals rating for LCP.
     */
    public function getLcpRatingAttribute(): string
    {
        if ($this->largest_contentful_paint <= 2.5) {
            return 'good';
        } elseif ($this->largest_contentful_paint <= 4.0) {
            return 'needs-improvement';
        } else {
            return 'poor';
        }
    }

    /**
     * Get Core Web Vitals rating for TBT.
     */
    public function getTbtRatingAttribute(): string
    {
        if ($this->total_blocking_time <= 200) {
            return 'good';
        } elseif ($this->total_blocking_time <= 600) {
            return 'needs-improvement';
        } else {
            return 'poor';
        }
    }

    /**
     * Get Core Web Vitals rating for CLS.
     */
    public function getClsRatingAttribute(): string
    {
        if ($this->cumulative_layout_shift <= 0.1) {
            return 'good';
        } elseif ($this->cumulative_layout_shift <= 0.25) {
            return 'needs-improvement';
        } else {
            return 'poor';
        }
    }

    /**
     * Get formatted FCP value.
     */
    public function getFormattedFcpAttribute(): string
    {
        return number_format($this->first_contentful_paint, 1) . 's';
    }

    /**
     * Get formatted LCP value.
     */
    public function getFormattedLcpAttribute(): string
    {
        return number_format($this->largest_contentful_paint, 1) . 's';
    }

    /**
     * Get formatted TBT value.
     */
    public function getFormattedTbtAttribute(): string
    {
        return number_format($this->total_blocking_time, 0) . 'ms';
    }

    /**
     * Get formatted CLS value.
     */
    public function getFormattedClsAttribute(): string
    {
        return number_format($this->cumulative_layout_shift, 3);
    }

    /**
     * Get formatted Speed Index value.
     */
    public function getFormattedSpeedIndexAttribute(): string
    {
        return number_format($this->speed_index, 1) . 's';
    }
}
