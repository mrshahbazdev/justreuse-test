<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserAdvertisement extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'user_id', 'ad_zone_id', 'ad_template_id', 'content', 'start_date', 'end_date',
        'total_amount', 'payment_status', 'payment_id', 'status', 'payment_intent_id', 'paid_at', 'payment_type'
    ];

    protected $casts = [
        'content' => 'array',
        'start_date' => 'date',
        'end_date' => 'date',
        'paid_at' => 'datetime',
        'total_amount' => 'decimal:2',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }

    /**
     * Get the user that owns the advertisement.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the ad zone for the advertisement.
     */
    public function adZone(): BelongsTo
    {
        return $this->belongsTo(AdZone::class, 'ad_zone_id');
    }
    
    /**
     * Get the ad template for the advertisement.
     */
    public function adTemplate(): BelongsTo
    {
        return $this->belongsTo(AdTemplate::class, 'ad_template_id');
    }

    /**
     * Scope for active advertisements
     */
    public function scopeActive($query)
    {
        return $query->where('status', 'active')
                    ->where('payment_status', 'completed');
    }

    /**
     * Scope for pending payment advertisements
     */
    public function scopePendingPayment($query)
    {
        return $query->where('payment_status', 'pending')
                    ->orWhere('payment_status', 'pending_payment');
    }

    /**
     * Check if advertisement is active
     */
    public function isActive(): bool
    {
        return $this->status === 'active' && $this->payment_status === 'completed';
    }

    /**
     * Check if payment is completed
     */
    public function isPaid(): bool
    {
        return $this->payment_status === 'completed';
    }
}