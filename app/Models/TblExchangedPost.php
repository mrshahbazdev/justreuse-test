<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class TblExchangedPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'tbl_exchanged_posts';
    protected $fillable = ['block_exchange','user_id', 'post_id','exchanged_post_id','status','post_owner_id'];
    protected $dates = ['deleted_at'];

    //UUID begin
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
