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
    public $result=null;
    public function render()
    {        
    	$seg = request()->segment(2);    	
	   	$data = TblStaticpage::where('slug',$seg)->first();
        $this->result = $data;
        //show 404 for improper slugs
        if($data==null){ abort(404); }
        
            add_action("apm_main",function(){
                echo view('livewire.staticpages-list',['list'=>$this->result])->render();
            },20,1);

        return view('livewire.sample_content');
    	// return view('livewire.staticpages-list',['list'=>$data]);
    }

}
