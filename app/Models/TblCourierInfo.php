<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblCourierInfo extends Model
{
    use HasFactory;
    protected $table = 'tbl_courier_infos';
    protected $fillable = ['order_id','shipping_date','courier_name','courier_service','tracking_id','more_info'];
}
