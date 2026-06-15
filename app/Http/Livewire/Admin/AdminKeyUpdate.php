<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\TblPostValue;
use App\Models\TblPaymentsMethod;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use Livewire\WithPagination;
use Livewire\WithFileUploads;
use Illuminate\Support\Str; //for slug
use Session;

class AdminKeyUpdate extends Component {

    public $post_updateMode = false;
    public $user_updateMode = false;
    public $method_list;

    public function mount()
    {

         //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                Session::flash('message', $isDemoUser["message"]);
                return redirect('admin/payment-methods');
            } 
        //end check demo user

    }

    public function render() {

    if ($_SERVER["REQUEST_METHOD"] == "POST") {  

            $is_error = 0;          
            $formdata = request()->all();
            
            foreach($formdata as $key => $val){               
                if(($key == "_token") || ($key == "payment_id")){                
                }else{
                    $trim_val = trim($val);
                    if($trim_val != ""){
                        $data['keys'][]=array(
                            $key=>$val
                        );
                    }else{
                        $is_error = 1;
                        break;
                    }
                    
                }                
            }

            if($is_error == 1){
                Session::flash('result', '0');
                Session::flash('message', 'All the fields are required!');
                redirect('admin/payment-methods/keysupdate?id='.$formdata["payment_id"]);
            }else{
                $node = TblPaymentsMethod::find($formdata['payment_id']);
                $node->update([
                    'keys_value' =>json_encode($data['keys']),
                ]);
                Session::flash('result', '1');
                Session::flash('message', 'Updated successfully!');
                redirect('admin/payment-methods');
            }   
            
    }
    $this->method_list = TblPaymentsMethod::get();
    return view('livewire.admin.payment_methods.show');
  }

}