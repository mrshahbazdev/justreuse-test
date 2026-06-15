<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblCity extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'country_id',
        'state_id',
        'name',
        'locality', 
        'latitude',
        'logitude',
        'active'
    ];

    // UUID begin
    public $incrementing = false;
    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
    // UUID end

    /**
     * Get the country that owns the city.
     */
    public function country()
    {
        return $this->belongsTo(TblCountry::class, 'country_id');
    }

    /**
     * Get the state that owns the city.
     */
    public function state()
    {
        return $this->belongsTo(TblState::class, 'state_id');
    }

    /**
     * Get the user profiles for the city.
     */
    public function userProfiles()
    {
        return $this->hasMany(User_profile::class, 'city_id');
    }

    /**
     * Scope active cities
     */
    public function scopeActive($query)
    {
        return $query->where('active', 1);
    }

    /**
     * Get full address attribute
     */
    public function getFullAddressAttribute()
    {
        $address = $this->name;
        if ($this->locality && $this->locality != $this->name) {
            $address = $this->locality . ', ' . $this->name;
        }
        return $address;
    }

    /**
     * Get coordinates attribute
     */
    public function getCoordinatesAttribute()
    {
        if ($this->latitude && $this->logitude) {
            return [
                'lat' => $this->latitude,
                'lng' => $this->logitude
            ];
        }
        return null;
    }
}