<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblPostedAdPackageInfo extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','post_id','ad_type','start_date','end_date','active','publish_count'];
    
    //UUID begin
    public $incrementing = false;

    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->{$model->getKeyName()} = (string) Str::uuid();
        });
    }
    //UUID end

}
