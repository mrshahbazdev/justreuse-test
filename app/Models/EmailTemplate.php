<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class EmailTemplate extends Model {
    use HasFactory;
    protected $fillable = ['subject_title', 'key', 'html_content', 'active','content'];

}
