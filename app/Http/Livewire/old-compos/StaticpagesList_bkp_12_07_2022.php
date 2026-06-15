<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblStaticpage;
use Exception;
use Illuminate\Support\Facades\Auth;
use App\Models\TblPostInsight;
use App\Models\TblPost;

class StaticpagesList extends Component
{	     
    public function render()
    {        
    	$seg = request()->segment(2);    	
	   	$data = TblStaticpage::where('slug',$seg)->first();
        
        //show 404 for improper slugs
        if($data==null){ abort(404); }

    	return view('livewire.staticpages-list',['list'=>$data]);
    }

}
