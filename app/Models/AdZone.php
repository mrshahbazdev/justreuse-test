<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class AdZone extends Model
{
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'name', 'page_location', 'price_per_day', 'specifications', 'auto_approve', 'is_active',
    ];

    protected $casts = [
        'specifications' => 'array',
        'auto_approve' => 'boolean',
        'is_active' => 'boolean',
    ];

    protected static function boot()
    {
        parent::boot();
        static::creating(fn ($model) => $model->id = (string) Str::uuid());
    }

    public function templates()
    {
        return $this->hasMany(AdTemplate::class);
    }
}
