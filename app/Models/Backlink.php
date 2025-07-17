<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Backlink extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_audit_id',
        'user_id',
        'domain',
        'source_url',
        'target_url',
        'anchor_text',
        'link_type',
        'rel_attribute',
        'domain_authority',
        'page_authority',
        'spam_score',
        'status',
        'discovered_date',
        'found_at',
        'content_summary',
        'page_type',
        'last_checked'
    ];

    protected $casts = [
        'discovered_date' => 'datetime',
        'found_at' => 'datetime',
        'last_checked' => 'datetime',
        'domain_authority' => 'integer',
        'page_authority' => 'integer',
        'spam_score' => 'integer'
    ];

    public function seoAudit(): BelongsTo
    {
        return $this->belongsTo(SeoAudit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByDomain($query, $domain)
    {
        return $query->where('source_url', 'like', "%{$domain}%");
    }

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }
}
