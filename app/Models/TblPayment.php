<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TblPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['currency_id','s_payment_id','user_id','post_id','start_date','end_date','live_days','package_amount','active','payment_type','payment_status','package_id','payment_loc_ref_id','coupon_id','coupon_code','coupon_type','coupon_val','deleted_at','is_bulk'];



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
