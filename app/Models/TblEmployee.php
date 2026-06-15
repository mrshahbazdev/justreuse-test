<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblEmployee extends Model
{
    use HasFactory;
    protected $table = 'tbl_employees';
    protected $fillable = ['name','code','gender','education','provision','description','created_at','image','updated_at'];



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
