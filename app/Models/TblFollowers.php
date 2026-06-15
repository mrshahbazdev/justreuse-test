<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use App\Models\User; // User model ko import karein

class TblFollowers extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $fillable = ['user_id','seller_id','deleted_at','is_followed'];
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

    /* check if the user follow this seller */
    public static function check_is_follow($userid, $seller_id) {
        $check = TblFollowers::where('user_id', $userid)->where('seller_id',$seller_id)->where('is_followed',1)->first();
        if(!empty($check)){
            return true;
        }else{
            return false;
        }
        
    }

    /**
     * Get all followers for a given seller.
     *
     * @param string $sellerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFollowers($sellerId)
    {
        return self::join('users', 'users.id', '=', 'tbl_followers.user_id')
            ->where('tbl_followers.seller_id', $sellerId)
            ->where('tbl_followers.is_followed', 1)
            ->select('users.*')
            ->get();
    }

    /**
     * Get all users that a given user is following.
     *
     * @param string $userId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getFollowings($userId)
    {
        return self::join('users', 'users.id', '=', 'tbl_followers.seller_id')
            ->where('tbl_followers.user_id', $userId)
            ->where('tbl_followers.is_followed', 1)
            ->select('users.*')
            ->get();
    }
}
