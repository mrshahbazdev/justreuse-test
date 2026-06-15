<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblCurrency extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_currencies';
    protected $fillable = ['currency_hex', 'currency_name', 'short_code', 'active', 'default_currency_id'];
    protected $dates = ['deleted_at'];
}
