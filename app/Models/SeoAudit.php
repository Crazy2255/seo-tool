<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SeoAudit extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'url',
        'title',
        'meta_description',
        'h1_tags',
        'h2_tags',
        'status_code',
        'page_load_speed',
        'internal_links_count',
        'external_links_count',
        'images_without_alt',
        'images_count',
        'word_count',
        'meta_keywords',
        'canonical_url',
        'robots_meta',
        'sitemap_url',
        'robots_txt_status',
        'ssl_certificate',
        'mobile_friendly',
        'schema_markup',
        'social_meta_tags',
        'broken_links',
        'audit_score',
        'recommendations',
        'audit_date'
    ];

    protected $casts = [
        'h1_tags' => 'array',
        'h2_tags' => 'array',
        'broken_links' => 'array',
        'recommendations' => 'array',
        'social_meta_tags' => 'array',
        'schema_markup' => 'array',
        'audit_date' => 'datetime',
        'ssl_certificate' => 'boolean',
        'mobile_friendly' => 'boolean'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function keywords(): HasMany
    {
        return $this->hasMany(Keyword::class);
    }

    public function backlinks(): HasMany
    {
        return $this->hasMany(Backlink::class);
    }

    public function auditReports(): HasMany
    {
        return $this->hasMany(AuditReport::class);
    }

    public function getAuditScoreAttribute($value)
    {
        return round($value, 1);
    }

    public function scopeRecent($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
