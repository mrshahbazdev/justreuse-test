<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class User_profile extends Model
{
    use HasFactory;

    protected $fillable = ['stripe_private_key','stripe_public_key','allow_call','mobile_verified','first_name', 'last_name','show_mobile', 'address_line1', 'address_line2', 'date_of_birth', 'gender', 'user_id','phone','description', 'otp','city_id','country_code'];

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

     public function User()
    {
        return $this->belongsTo(User::class);
    }
    
}
