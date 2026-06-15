<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ReportType;
use App\Models\TblPaymentsMethod;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class PaymentMethodsComponent extends Component
{

    public $method_list;

    public function render()
    {
        $this->update_payment_list();
        $this->method_list = TblPaymentsMethod::get();
		return view('livewire.admin.payment_methods.show');
    }


    public function update_payment_list()
    {
        //begin - read all folders
        $folders_list = scandir(base_path().'/extra/plugins');
        $folders=[];
        $only_folders = [];
        foreach($folders_list as $f)
        {
            if($f=="." || $f==".."){continue;}

            $types =                     [
                'name'=>$f,
                'display_name'=>ucfirst($f),
                'description'=>'Payment With '.ucfirst($f),
                'active'=>'0'
                ];

            $folders[] = $types;
            $only_folders[] = $f;
        }
        //end - read all folders
        // print_r($folders);exit();

        //begin - inserting/removing list of payments detail
        foreach($folders as $j)
        {
            
            $path = base_path().'/extra/plugins/'.$j['name'];
            if (file_exists($path)) {            
                $keys_file_path = $path."/keys.json";   
                $get_keys_data = file_get_contents($keys_file_path);               
                $isExist = TblPaymentsMethod::where('name',$j['name']);
                if($isExist->count()==0){                   
                    TblPaymentsMethod::create([
                        'name'=>$j['name'],
                        'display_name'=>$j['display_name'],
                        'description'=>$j['description'],
                        'active'=>$j['active'],
                        'keys_info'=>$get_keys_data
                    ]);
                }else{
                    // $node = TblPaymentsMethod::where('name',$j['name'])->get();
                    // $update_node = TblPaymentsMethod::find($node[0]->id);
                    // $update_node->update([
                    //     'keys_info' =>$get_keys_data,
                    // ]);
                }
            }
            else{
                $isExist = TblPaymentsMethod::where('name',$j['name'])->delete();
            }
        }
        //end - inserting/removing list of payments detail
        $tkt = TblPaymentsMethod::whereNotIn('name',$only_folders);
        $tkt->delete();

        //begin - remove from table, unnecessary folder names

        //end - remove from table, unnecessary folder names

    }



    public function enable_package()
    {

        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                return response()->json(['message' => $isDemoUser["message"]]);
            }
        //end check demo user

        $id = request()->id;
        $val = request()->active;

         $node = TblPaymentsMethod::find($id);
 
         $node->update([
            'active' => $val,
        ]);

        return response()->json(['message'=>'updated successfully']);
        
    }


}