<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Languages extends Model
{
    use HasFactory;
    
    protected $fillable = ['lang_text,lang_code,lang_org_text,type'];
}
