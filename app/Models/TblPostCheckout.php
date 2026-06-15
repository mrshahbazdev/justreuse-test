<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblPostCheckout extends Model
{
    use HasFactory;
    use SoftDeletes;
    
    protected $table = 'tbl_post_checkouts';
    protected $fillable = ['user_id','web_id','user_id','post_id','seller_id','shipping_address','price','shipping_fee','order_total','currency_id'];
    protected $dates = ['deleted_at'];
}
