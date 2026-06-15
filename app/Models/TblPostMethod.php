<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblPostMethod extends Model
{
    use HasFactory;
    protected $table = 'tbl_post_methods';
    protected $fillable = ['name', 'display_name', 'description', 'active'];

    public static function get_active_post_methods()
    {
        $data = TblPostMethod::where('active', 1)->get();
        return $data;
    }
}
