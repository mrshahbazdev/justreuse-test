<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TblBannerAdvertisement extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_banner_advertisements';
    protected $fillable = ['refund_id','approved_lately', 'approved_start_date', 'approved_end_date', 'status', 'payment_loc_ref_id', 'page', 'category_id', 'user_id', 'web_banner', 'app_banner', 'web_link', 'app_link', 'start_date', 'end_date', 'payment_id', 'payment_type', 'live_days', 'total_amount', 'active', 'payment_status','currency_id'];
    protected $dates = ['deleted_at'];

    public static function get_banner_ads_price($page, $cat_id = NULL)
    {
        $settings = Setting::where('key', 'banner_advertisement')->pluck('value');
        $values = json_decode($settings[0], true);
        if (!empty($page) && ($page == "home")) {
            $amount = !empty($values['home_page']) ? $values['home_page'] : $values['default_amount'];
            return $amount;
        } else if ((!empty($page)) && ($page != "home") && ($cat_id != "")) {
            $check_parent = TblCategory::where('id', $cat_id)->get()->first();
            $amount = ($check_parent->paid_banner_price=="0.00")?$values['default_amount']:$check_parent->paid_banner_price;
            return $amount;
            /*
            $check_parent = TblCategory::where('id', $cat_id)->pluck('parent_id')->first();
            if (empty($check_parent)) {
                $amount = !empty($values['level_1']) ? $values['level_1'] : $values['default_amount'];
                return $amount;
            } else {
                $check_child = TblCategory::where('id', $check_parent)->pluck('parent_id')->first();
                if (empty($check_child)) {
                    $amount = !empty($values['level_2']) ? $values['level_2'] : $values['default_amount'];
                    return $amount;
                } else {
                    $amount = !empty($values['level_3']) ? $values['level_3'] : $values['default_amount'];
                    return $amount;
                }
            }*/
        }
    }

    public static function check_is_expired($id)
    {
        $expired = 0;
        $current_date = date('Y-m-d');
        $check = TblBannerAdvertisement::where('id', $id)->first();
        if ($check->status == "approved") {
            if (!empty($check->approved_end_date)) {
                if ($current_date > $check->approved_end_date) {
                    $expired = 1;
                }else{
                    $expired = 3;
                }
            } else if ($current_date > $check->end_date) {
                $expired = 1;
            }else{
                $expired = 3;
            }
        } else if ($check->status == "pending") {
            $expired = 0;
        } else if ($check->status == "refunded") {
            $expired = 2;
        }

        return $expired;
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
}
