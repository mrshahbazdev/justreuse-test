<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblReportThisUser extends Model
{
    use HasFactory;

    /**
     * The table associated with the model. Laravel will assume 'tbl_report_this_users'.
     * If your table name is different, uncomment and change the line below.
     * protected $table = 'your_table_name';
     */

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id', 'reported_user_id', 'report_type_id', 'comment', 'view', 'active'
    ];

    /**
     * Indicates if the model should be timestamped.
     *
     * @var bool
     */
    public $timestamps = true;

    //for uuid working
    public $incrementing = false;

    protected $keyType = 'string';

    public static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = (string) Str::uuid();
            }
        });
    }
}
