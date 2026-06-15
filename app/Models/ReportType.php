<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class ReportType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','type',   
    ];


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