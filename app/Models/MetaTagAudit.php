<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MetaTagAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'title',
        'title_length',
        'meta_description',
        'meta_description_length',
        'meta_keywords',
        'canonical_url',
        'robots',
        'viewport',
        'og_title',
        'og_description',
        'og_image',
        'og_type',
        'og_url',
        'twitter_card',
        'twitter_title',
        'twitter_description',
        'twitter_image',
        'h1_tags',
        'h2_tags',
        'img_alt_missing',
        'total_images',
        'status_code',
        'issues_found',
        'recommendations',
        'score',
        'analyzed_at'
    ];

    protected $casts = [
        'h1_tags' => 'array',
        'h2_tags' => 'array',
        'issues_found' => 'array',
        'recommendations' => 'array',
        'analyzed_at' => 'datetime'
    ];

    /**
     * Get the user that owns the audit
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get issues array
     */
    public function getIssuesAttribute(): array
    {
        return $this->issues_found ?? [];
    }

    /**
     * Get recommendations array
     */
    public function getRecommendationsAttribute(): array
    {
        return $this->recommendations ?? [];
    }

    /**
     * Get status label
     */
    public function getStatusLabel(): string
    {
        return $this->getStatusText();
    }

    /**
     * Get the audit score based on various factors
     */
    public function calculateScore(): int
    {
        $score = 100;

        // Title tag scoring
        if (empty($this->title)) {
            $score -= 15;
        } elseif ($this->title_length < 30 || $this->title_length > 60) {
            $score -= 10;
        }

        // Meta description scoring
        if (empty($this->meta_description)) {
            $score -= 15;
        } elseif ($this->meta_description_length < 120 || $this->meta_description_length > 160) {
            $score -= 10;
        }

        // Essential meta tags
        if (empty($this->viewport)) $score -= 10;
        if (empty($this->canonical_url)) $score -= 5;
        if (empty($this->robots)) $score -= 5;

        // Open Graph tags
        if (empty($this->og_title)) $score -= 8;
        if (empty($this->og_description)) $score -= 8;
        if (empty($this->og_image)) $score -= 7;

        // H1 tags
        $h1Count = is_array($this->h1_tags) ? count($this->h1_tags) : 0;
        if ($h1Count === 0) {
            $score -= 10;
        } elseif ($h1Count > 1) {
            $score -= 5;
        }

        return max(0, $score);
    }

    /**
     * Get status color based on score
     */
    public function getStatusColor(): string
    {
        $score = $this->score ?? $this->calculateScore();
        
        if ($score >= 80) return 'green';
        if ($score >= 60) return 'yellow';
        return 'red';
    }

    /**
     * Get status text based on score
     */
    public function getStatusText(): string
    {
        $score = $this->score ?? $this->calculateScore();
        
        if ($score >= 80) return 'Excellent';
        if ($score >= 60) return 'Good';
        if ($score >= 40) return 'Needs Improvement';
        return 'Poor';
    }
}
