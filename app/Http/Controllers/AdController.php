<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\View\Components\DisplayAd;

class AdController extends Controller
{
    public function getAd($pageLocation)
    {
        $adComponent = new DisplayAd($pageLocation);
        return $adComponent->finalHtml ?? '<div></div>';
    }
}