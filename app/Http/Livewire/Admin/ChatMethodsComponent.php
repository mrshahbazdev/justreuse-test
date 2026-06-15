<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\ReportType;
use App\Models\TblAdminChatMethods;
use App\Models\TblPaymentsMethod;
use App\Models\User;
use Illuminate\Support\Facades\URL;

class ChatMethodsComponent extends Component
{

    public $method_list;

    public function render()
    {
        $this->update_chat_list();
        $this->method_list = TblAdminChatMethods::get();
		return view('livewire.admin.chat_methods.show');
    }


    public function update_chat_list()
    {
        //begin - read all folders
        $folders_list = scandir(base_path().'/extra/chatplugins');
        $folders=[];
        $only_folders = [];
        foreach($folders_list as $f)
        {
            if($f=="." || $f==".."){continue;}

            $types =                     [
                'name'=>$f,
                'display_name'=>ucfirst($f),
                'description'=>'Chat - '.ucfirst($f),
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
            
            $path = base_path().'/extra/chatplugins/'.$j['name'];
            if (file_exists($path)) {             
                $isExist = TblAdminChatMethods::where('name',$j['name']);
                if($isExist->count()==0){                   
                    TblAdminChatMethods::create([
                        'name'=>$j['name'],
                        'display_name'=>$j['display_name'],
                        'description'=>$j['description'],
                        'active'=>$j['active']
                    ]);
                }
            }
            else{
                $isExist = TblAdminChatMethods::where('name',$j['name'])->delete();
            }
        }
        //end - inserting/removing list of payments detail
        $tkt = TblAdminChatMethods::whereNotIn('name',$only_folders);
        $tkt->delete();

        //begin - remove from table, unnecessary folder names

        //end - remove from table, unnecessary folder names

    }



    public function enable_chat_method()
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

        $node_pre = TblAdminChatMethods::where('id','!=',$id)->update(['active'=>0]);
        $node = TblAdminChatMethods::where('id',$id)->update(['active'=>$val]);

        return response()->json(['message'=>'updated successfully']);        
        
    }


}