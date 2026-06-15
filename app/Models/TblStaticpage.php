<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblStaticpage extends Model
{
    use HasFactory;
    protected $fillable =['title','slug','content', 'meta_title', 'meta_key', 'meta_description']; 
}
