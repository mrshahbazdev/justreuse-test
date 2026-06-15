<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblPostView extends Model
{
    use HasFactory;

    protected $fillable = ['ip_address', 'post_id', 'last_viewed_on','views'];


//view counts
    public static function views_count($post_id)
    {
        $count = TblPostView::where('post_id',$post_id)->sum('views');
        return $count;
    }



    //for uuid working
public $incrementing = false;

protected $keyType = 'string';

public static function boot()
{
    parent::boot();

    static::creating(function ($model) {
        $model->{$model->getKeyName()} = (string) Str::uuid();
    });
} 


}
