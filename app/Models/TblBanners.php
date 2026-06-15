<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\TblBannerAdvertisement;
use Illuminate\Support\Facades\URL;
use App\Models\Setting;

class TblBanners extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'images', 'banner_url','content'
    ];
}
