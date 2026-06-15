<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Verificationrequest extends Model
{
    use HasFactory;

    protected $table = 'verfication_request';

    protected $fillable=['id','name','document','user_id','email','is_approved','is_company','decline_reason','verifcation_id'];

    // public $incrementing = false;

    // protected $keyType = 'string';

    // public static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($model) {
    //         $model->{$model->getKeyName()} = (string) Str::uuid();
    //     });
    // }


    public function setFilenamesAttribute($value)
    {
        $this->attributes['document'] = json_encode($value);
    }
}
