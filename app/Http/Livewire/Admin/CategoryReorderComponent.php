<?php
namespace App\Http\Livewire\Admin;
use Livewire\Component;
use App\Models\TblCategory;
use App\Models\TblCustomField;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\URL;
use Session;
use Livewire\WithPagination;

class CategoryReorderComponent extends Component
{
    use WithPagination;
    public $ancestors;
    
    
    public function render()
    {
        
        $seg3 = request()->segment(3);
        if($seg3 !=null) {
            //$id = $seg2;
            $uuid  = $seg3;
            $id =  TblCategory::where('uuid',$uuid)->get();
            $id = $id[0]->id;
            $this->ancestors = TblCategory::ancestorsAndSelf($id);
            $list = TblCategory::orderBy('list_order','asc')->descendantsOf($id)->toTree();            
        }
        else{
            $list = TblCategory::withDepth()->having('depth', '=', 0)->orderBy('list_order','asc')->get();
        }

		return view('livewire.admin.category_reorder.show',compact('list'));

    }



    public function update_category_order()
    {

        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('result', '0');
                return response()->json(['result'=>"failed"]);
            }
        //end check demo user

        if($_SERVER["REQUEST_METHOD"]=="POST"){

            $formdata = request()->all();
            
            if(!empty($formdata)){
            $data_arr = ($formdata["data_array"]==null)?"":$formdata["data_array"];
                foreach($data_arr as $j)
                {
                    $rowid = $j["row_id"];
                    $list_order = $j["list_order"];
                    $node = TblCategory::where('id',$rowid);
                    $node->update(['list_order'=>$list_order]);
                }
                Session::flash('result','1');
                Session::flash('message','Reorder done successfully');
    
                return response()->json(['result'=>"success"]);
            }
            else{
                Session::flash('result','0');
                Session::flash('message','Nothing to update');
    
                return response()->json(['result'=>"failed"]);
            }

        }

    }

	
	
}
