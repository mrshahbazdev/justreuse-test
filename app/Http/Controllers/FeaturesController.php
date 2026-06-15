<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FeaturesController extends Controller
{
    //

    public function index()
    {
        return view('livewire.admin.features.show');
    }

     public function store(Request $request)
    {
        dd("came");
    }

    public function export(Type $var = null)
    {
        # code...
    }
}
