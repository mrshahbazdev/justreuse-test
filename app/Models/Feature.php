<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feature extends Model
{
    use HasFactory;
    // protected $fillable = ['brand_id','cat_id','make','model','generation','year_from','year_to','series','other_features'];
    protected $fillable = ['brand_id','cat_id','make', 'model', 'label_name','dog_breed_group','other_features'];
}
