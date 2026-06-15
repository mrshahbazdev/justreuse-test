<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Str;

class AdTemplate extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'ad_zone_id', 'name', 'html_content', 'is_active',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }

    /**
     * Get the ad zone that owns the template.
     */
    public function adZone(): BelongsTo
    {
        return $this->belongsTo(AdZone::class, 'ad_zone_id');
    }
}

