<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblLanguage extends Model
{
    use HasFactory;
	use SoftDeletes;
    protected $fillable = ['abbr','locale','name','native','flag','app_name','script','direction','russian_pluralization','active','default','parent_id','lft','rgt','depth','deleted_at'];



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