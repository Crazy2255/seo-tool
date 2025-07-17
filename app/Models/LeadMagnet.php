<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class LeadMagnet extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'slug',
        'file_path',
        'file_name',
        'file_type',
        'file_size',
        'is_active',
        'thank_you_page_title',
        'thank_you_page_content',
        'form_button_text',
        'form_title',
        'form_description',
        'download_count',
        'view_count',
        'settings',
        'user_id',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'settings' => 'array',
        'file_size' => 'integer',
        'download_count' => 'integer',
        'view_count' => 'integer',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function leads(): HasMany
    {
        return $this->hasMany(Lead::class);
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public static function generateSlug($title): string
    {
        $slug = Str::slug($title);
        $count = 2;
        
        while (static::where('slug', $slug)->exists()) {
            $slug = Str::slug($title) . '-' . $count;
            $count++;
        }
        
        return $slug;
    }

    public function getLandingUrlAttribute(): string
    {
        return route('lead.landing', $this->slug);
    }

    public function getFormattedFileSizeAttribute(): string
    {
        $bytes = $this->file_size;
        
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' bytes';
        }
    }

    public function getConversionRateAttribute(): float
    {
        return $this->view_count > 0 ? round(($this->leads()->count() / $this->view_count) * 100, 2) : 0;
    }

    public function incrementDownloads(): void
    {
        $this->increment('download_count');
    }

    public function incrementViews(): void
    {
        $this->increment('view_count');
    }
}
