<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblShippingAddress extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_shipping_addresses';
    protected $fillable = ['user_id','name','country','address_1','address_2','city','state','zipcode','phone_number','default_address'];
    protected $dates = ['deleted_at'];
}
