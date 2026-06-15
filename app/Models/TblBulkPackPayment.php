<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TblBulkPackPayment extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['currency_id','s_payment_id','user_id','start_date','end_date','live_days','package_amount','active','payment_type','payment_status','package_id','payment_loc_ref_id','coupon_id','coupon_code','coupon_type','coupon_val','deleted_at'];

   
    public static function get_remaining_ads($id){
        /* get data from bulkpayment */
        $get_pack_id = TblBulkPackPayment::where('id', $id)->pluck('package_id')->first();
        /* get package info bulkpayment */
        $get_bulklimit = Package::where('id', $get_pack_id)->pluck('bulk_limit')->first();
        /* get already added payment post count for bulck */     
        $ads_count = TblPayment::where('user_id', Auth::id())
                    ->where('package_id',$get_pack_id)
                    ->where('active','1')
                    ->where('is_bulk',$id)
                    ->count('post_id');
        $remaing_ads = $get_bulklimit - $ads_count;
        return $remaing_ads;
    }
    

    public static function get_assigned_ads($id){
        /* get data from bulkpayment */
        $get_pack_id = TblBulkPackPayment::where('id', $id)->pluck('package_id')->first();
        /* get already added payment post ids for bulck */     
        $ads_ids = TblPayment::where('user_id', Auth::id())
                    ->where('package_id',$get_pack_id)
                    ->where('active','1')
                    ->where('is_bulk',$id)
                    ->pluck('post_id')
                    ->toArray();
        /*get bulck payment post list*/    
        $ads_list = TblPost::whereIn('id',$ads_ids)->get()->toArray();
        return $ads_list;

    }

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
