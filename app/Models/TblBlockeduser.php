<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TblBlockeduser extends Model {

    use HasFactory;

    protected $fillable = ['id', 'blocked_id', 'blocked_by', 'block_status', 'post_id'];

}
