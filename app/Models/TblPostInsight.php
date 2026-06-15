<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPostInsight extends Model
{
    use HasFactory;
	
	protected $fillable = ['user_id', 'post_id', 'ip_address','city', 'latitude', 'logitude', 'visited_date', 'views'];
 
    public static function views_count($post_id)
    {
        $count = TblPostInsight::where('post_id',$post_id)->sum('views');
        return $count;
    }
	
	
}
