<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class TblSavedPosts extends Model
{

    use HasFactory;

    protected $fillable = ['user_id', 'post_id'];

    //relations

    public function post()
    {
        return $this->belongsTo(Post::class, 'post_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function check_fav($postid)
    {
        $userid = Auth::id();
        $fav_style = false;
        if (!empty($userid)) {
            $getRows = TblSavedPosts::where('user_id', $userid)->where('post_id', $postid)->get();
            if (!empty($getRows->count() > 0)) {
                $fav_style = true;
            }
        }
        return $fav_style;
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
