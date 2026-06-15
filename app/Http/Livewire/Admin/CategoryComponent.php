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

class CategoryComponent extends Component
{
    use WithPagination;
    public $ancestors;
    
    
    public function render()
    {
        
        $seg2 = request()->segment(3);
        $seg3 = request()->segment(4);
        if($seg3 == 'subcategories') {
            //$id = $seg2;
            $uuid  = $seg2;
            $id =  TblCategory::where('uuid',$uuid)->get();
            $id = $id[0]->id;
            $this->ancestors = TblCategory::ancestorsAndSelf($id);
            $list = TblCategory::orderBy('list_order','asc')->descendantsOf($id)->toTree();            
        }
        else{
            $list = TblCategory::withDepth()->having('depth', '=', 0)->orderBy('list_order','asc')->get();
        }

		return view('livewire.admin.category.show',compact('list'));

    }



    public function destroy($id,$parentid)
    {

           //start check demo user
           $isDemoUser = User::isDemoUser();
           if($isDemoUser["result"]==true)
           {
               session()->flash('message', $isDemoUser["message"]);
               Session()->flash('class', 'error');
               return redirect(URL::to('/admin/category/')); 
           }
       //end check demo user
           
        if ($id) {

            $redirect_url=URL::to('/admin/category/');
            if($parentid!="")
            {
                $uuid = TblCategory::where('id',$parentid)->get()[0]['uuid'];
                $redirect_url = $redirect_url.'/'.$uuid.'/subcategories';
            }

            $id =  TblCategory::where('uuid',$id)->get();
            $id = $id[0]->id;

            $record = TblCategory::where('id','=', $id);
            $record->delete();            

            Session::flash('result','1');
            Session::flash('message','Deleted Successfully');

            return redirect($redirect_url); 
        }
    }

	
	
}
