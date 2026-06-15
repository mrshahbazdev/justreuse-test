<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblSellerReviews extends Model
{
    use HasFactory;
    protected $fillable = ['user_id','seller_id','comment','ratings','approved'];


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
      //UUID end
	 
	  public static function rate_avg($seller_id)
    {
        $avg_rating = "0.0";
        // Star rating calculation begin
        $tot2 = TblSellerReviews::where('seller_id', $seller_id)->where('approved', '1')->sum('ratings');

        if ($tot2 > 0) {
            $count = TblSellerReviews::where('seller_id', $seller_id)->where('approved', '1')->count();
            if ($count > 0) {
                $avg_rating = number_format($tot2 / $count, 1, '.', ''); // Correct average calculation
            }
        }

        // Star rating calculation end
        return $avg_rating;
    }


    public static function revi_count($seller_id){

        $count = TblSellerReviews::where('seller_id',$seller_id)->where('approved', '1')->count();
        return $count;
    }

    /**
     * Get all reviews for a given seller.
     *
     * @param string $sellerId
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getReviews($sellerId)
    {
        return self::join('users', 'users.id', '=', 'tbl_seller_reviews.user_id')
            ->where('tbl_seller_reviews.seller_id', $sellerId)
            ->select('tbl_seller_reviews.*', 'users.name')
            ->orderBy('tbl_seller_reviews.created_at', 'desc')
            ->get();
    }
}
