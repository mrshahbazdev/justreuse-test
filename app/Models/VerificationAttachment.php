<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VerificationAttachment extends Model
{
    use HasFactory;

    protected $table = 'verification_attachments';

    protected $fillable=['id','verify_id','attachments'];

}
