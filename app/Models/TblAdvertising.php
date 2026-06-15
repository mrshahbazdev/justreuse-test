<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblAdvertising extends Model
{
    use HasFactory;
    protected $fillable =['position','tracking_code','active']; 
} 
