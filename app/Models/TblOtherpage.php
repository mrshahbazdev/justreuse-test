<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblOtherpage extends Model
{
    use HasFactory;
    protected $fillable =['title','slug','content', 'meta_title', 'meta_key', 'meta_description']; 
    

    public static function get_meta($slug = NULL)
    {
        $get_meta = TblOtherpage::where('slug', $slug)->first();

        return $get_meta;
    }
    
}
