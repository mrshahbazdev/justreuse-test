<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Languagecode extends Model
{
    use HasFactory;
    protected $table = 'country_lang_code';
    protected $fillable = ['country_code,country_name,language_code'];
}
