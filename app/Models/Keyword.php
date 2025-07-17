<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Keyword extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_audit_id',
        'user_id',
        'keyword',
        'current_position',
        'previous_position',
        'search_volume',
        'difficulty',
        'cpc',
        'url',
        'country',
        'city',
        'language',
        'tracked_date'
    ];

    protected $casts = [
        'tracked_date' => 'datetime',
        'current_position' => 'integer',
        'previous_position' => 'integer',
        'search_volume' => 'integer',
        'cpc' => 'decimal:2'
    ];

    public function seoAudit(): BelongsTo
    {
        return $this->belongsTo(SeoAudit::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function getPositionChangeAttribute()
    {
        if ($this->previous_position && $this->current_position) {
            return $this->previous_position - $this->current_position;
        }
        return 0;
    }

    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeByKeyword($query, $keyword)
    {
        return $query->where('keyword', 'like', "%{$keyword}%");
    }
}
