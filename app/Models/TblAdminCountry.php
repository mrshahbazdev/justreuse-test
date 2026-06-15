<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblAdminCountry extends Model
{
    use HasFactory;
    protected $fillable = ['code','name','active','currency_code','GMT_offset','time_zone'];
}
