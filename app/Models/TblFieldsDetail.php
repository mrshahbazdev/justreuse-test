<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class TblFieldsDetail extends Model
{
    use HasFactory;
    protected $fillable = ['cat_id','name','type','required','filter','helptext','form_field_name','active'];



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
   public function options()
    {
        // 'field_id' کی جگہ اپنی فارن کی (foreign key) کا نام استعمال کریں
        return $this->hasMany(TblFieldsOption::class, 'field_id', 'id');
    }
    //UUID end

}
