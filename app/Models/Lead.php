<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'ip_address',
        'user_agent',
        'referrer',
        'lead_magnet_id',
        'downloaded_at',
        'email_sent',
        'email_sent_at',
        'additional_data',
    ];

    protected $casts = [
        'downloaded_at' => 'datetime',
        'email_sent' => 'boolean',
        'email_sent_at' => 'datetime',
        'additional_data' => 'array',
    ];

    public function leadMagnet(): BelongsTo
    {
        return $this->belongsTo(LeadMagnet::class);
    }

    public function scopeDownloaded($query)
    {
        return $query->whereNotNull('downloaded_at');
    }

    public function scopeEmailSent($query)
    {
        return $query->where('email_sent', true);
    }

    public function markAsDownloaded(): void
    {
        $this->update(['downloaded_at' => now()]);
    }

    public function markEmailAsSent(): void
    {
        $this->update([
            'email_sent' => true,
            'email_sent_at' => now()
        ]);
    }

    public function getFormattedCreatedAtAttribute(): string
    {
        return $this->created_at->format('M j, Y g:i A');
    }
}
