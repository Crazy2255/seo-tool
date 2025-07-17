<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerpPreview extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'page_title',
        'target_url',
        'meta_description',
        'preview_name'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // Accessors
    public function getFormattedTitleAttribute()
    {
        return strlen($this->page_title) > 60 
            ? substr($this->page_title, 0, 60) . '...' 
            : $this->page_title;
    }

    public function getFormattedDescriptionAttribute()
    {
        return strlen($this->meta_description) > 160 
            ? substr($this->meta_description, 0, 160) . '...' 
            : $this->meta_description;
    }

    public function getFormattedUrlAttribute()
    {
        $url = $this->target_url;
        // Remove protocol
        $url = preg_replace('#^https?://#', '', $url);
        // Remove www
        $url = preg_replace('#^www\.#', '', $url);
        // Limit length
        return strlen($url) > 50 ? substr($url, 0, 50) . '...' : $url;
    }

    // Validation helpers
    public function getTitleLengthAttribute()
    {
        return strlen($this->page_title);
    }

    public function getDescriptionLengthAttribute()
    {
        return strlen($this->meta_description);
    }

    public function getIsTitleOptimalAttribute()
    {
        $length = $this->title_length;
        return $length >= 30 && $length <= 60;
    }

    public function getIsDescriptionOptimalAttribute()
    {
        $length = $this->description_length;
        return $length >= 120 && $length <= 160;
    }

    // Scopes
    public function scopeByUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeRecent($query, $days = 30)
    {
        return $query->where('created_at', '>=', now()->subDays($days));
    }
}
