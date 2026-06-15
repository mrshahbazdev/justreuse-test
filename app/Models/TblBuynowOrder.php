<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblBuynowOrder extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_buynow_orders';
    protected $fillable = ['currency_id', 'refund_id', 'orderId', 'user_id', 'post_id', 'seller_id', 'shipping_address', 'shipping_add_name', 'shipping_add_country', 'shipping_add_state', 'shipping_add_city', 'shipping_add_address1', 'shipping_add_address2', 'shipping_add_zipcode', 'shipping_add_phone_number', 'price', 'shipping_fee', 'total', 'order_status', 'payment_status', 'payment_id'];
    protected $dates = ['deleted_at'];
}
