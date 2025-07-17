<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AuditReport extends Model
{
    use HasFactory;

    protected $fillable = [
        'seo_audit_id',
        'user_id',
        'report_type',
        'file_path',
        'file_name',
        'file_size',
        'generated_date',
        'download_count'
    ];

    protected $casts = [
        'generated_date' => 'datetime',
        'file_size' => 'integer',
        'download_count' => 'integer'
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

    public function scopeByType($query, $type)
    {
        return $query->where('report_type', $type);
    }

    public function getFileSizeFormattedAttribute()
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB'];
        
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }
        
        return round($bytes, 2) . ' ' . $units[$i];
    }
}
