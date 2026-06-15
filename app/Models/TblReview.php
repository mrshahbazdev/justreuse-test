<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblReview extends Model
{
    use HasFactory;
    
    protected $fillable = ['post_id','user_id','ratings','comment','approved','spam','view'];
    
    protected $table = 'tbl_reviews'; // Add this if your table name is different

    // ADD THIS RELATIONSHIP
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    
    // Optionally add post relationship if needed
    public function post()
    {
        return $this->belongsTo(TblPost::class, 'post_id', 'id');
    }

    public static function rate_avg($post_id)
    {
        $avg_rating = "0.0";
       //star rating calculation begin
       $tot2 = TblReview::where('post_id',$post_id)->sum('ratings');
       if($tot2>0)
       {
           $ratings2 = TblReview::select(\DB::raw('`ratings` * sum(`ratings`) as tot'))
           ->where('post_id',$post_id)
           ->groupBy(\DB::raw('ratings'))
           ->get();
           $tot1 = 0;
           foreach($ratings2 as $s)
           {
               $tot1 += $s['tot'];
           }
           //$this->avg_rating = number_format((float)$tot1/$tot2, 1, '.', '');
           $avg_rating = ceil($tot1/$tot2);
       }
       
       //star rating calculation end

    return $avg_rating;
    }


    public static function review_count($post_id)
    {
        $reviews = TblReview::where('post_id',$post_id)->get();
        return $reviews->count();
    }

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

}