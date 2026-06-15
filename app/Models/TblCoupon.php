<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblCoupon extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['coupon_code','coupon_title','value','start_date','end_date','description','type','limit_type','limit_value','tax']; 
    protected $dates = ['deleted_at'];
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
