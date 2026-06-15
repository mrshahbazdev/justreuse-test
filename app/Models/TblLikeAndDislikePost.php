<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;


class TblLikeAndDislikePost extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'post_id','likes','dislikes'];

    //relations 
    public function post()
    {
        return $this->belongsTo(TblPost::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
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
