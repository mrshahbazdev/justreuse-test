<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblContactUs;
use Livewire\WithPagination;
use App\Models\User;



class AdminContactUsComponent extends Component
{

    use WithPagination;
    public $search;
    public $cnfopen = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $data = TblContactUs::where('name', 'like', '%' . $this->search . '%')
            ->orwhere('email', 'like', '%' . $this->search . '%')
            ->orderBy('created_at','desc')
            ->paginate(10);

        return view('livewire.admin.contact-us.show', compact('data'));
    }


    public function view_description()
    {


        $id = request()->id;
        $comment = TblContactUs::find($id)->description;
        $view = TblContactUs::find($id);

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==false)
        {
            $view->update([ 'view' => "1" ]);
        }
        

        return response()->json(['success'=>true,'message' => $comment]);
    }

    public function delete_contact_us()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return response()->json(['success'=>false,'message' => $isDemoUser["message"]]);
            exit;
        }

        $ids = request()->ids;
        TblContactUs::whereIn('id', explode(",", $ids))->delete();
        return response()->json(['success'=>true,'message' => "Removed Select Items successfully.."]);
    }


    public function deleteReq($id)
    {
        $this->cnfopen = $id;
    }

    public function deleteCan()
    {
        $this->cnfopen = 0;
    }

    public function delete($id)
    {


        $this->cnfopen = 0;


        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
        }
        else{
            TblContactUs::find($id)->delete();
            session()->flash('message', 'Item Deleted Successfully.');
        }

    }
}
