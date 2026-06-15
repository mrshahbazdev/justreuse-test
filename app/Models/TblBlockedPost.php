<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TblBlockedPost extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = ['post_id', 'blocked_by', 'blocked_date', 'unblocked_by', 'unblocked_date', 'active', 'deleted_at'];

}
