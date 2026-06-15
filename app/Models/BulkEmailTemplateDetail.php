<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class BulkEmailTemplateDetail extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'template_id', 'unique_id', 'user_id', 'user_email_id', 'sent_status', 'deleted_at'
    ];

}
