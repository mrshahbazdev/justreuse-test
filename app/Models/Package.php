<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Package extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $fillable = [
        'name', 'short_name', 'price','single_pack_limit', 'currency_code', 'ribbon', 'has_badge', 'promo_duration', 'duration', 'pictures_limit', 'facebook_ads_duration', 'google_ads_duration', 'twitter_ads_duration', 'linkedin_ads_duration', 'description', 'lft', 'recommended', 'active', 'bulk_ads', 'bulk_limit', 'ad_type', 'bulk_type',    
    ];
	protected $casts = [
        'is_free' => 'boolean',
    ];
    public static function get_active_packages() {
        $packages = Package::where('active', '1')->where('bulk_ads','0')->get();       
        return $packages;
    }


     //for uuid working
     public $incrementing = false;

     protected $keyType = 'string';
 
     public static function boot()
     {
         parent::boot();
 
         static::creating(function ($model) {
             $model->{$model->getKeyName()} = (string) Str::uuid();
         });
     }


}
