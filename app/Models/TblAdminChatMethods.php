<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblAdminChatMethods extends Model
{
    use HasFactory;
    protected $fillable = ['name','display_name','description','order','active'];

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
