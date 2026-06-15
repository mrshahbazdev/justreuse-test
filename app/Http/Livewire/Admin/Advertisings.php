<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblAdvertising;
use App\Models\User;
use Livewire\WithPagination;

class Advertisings extends Component
{

    use WithPagination;
    public $ad_id, $position, $tracking_code, $editdata;
    private $pagination = 25;
    public $updateMode=false;
    public $cnfopen = 0;
    /**
     *  Livewire Lifecycle Hook
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }
    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/advertising');
    }

    public function render()
    {        
        return view('livewire.admin.advertising.compo', [
            'list' => TblAdvertising::paginate($this->pagination), 
        ]);
        
    }

    public function enable_advertising()
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
        $node = TblAdvertising::find($id); 
        $node->update([
            'active' => $val,
        ]); 
        return response()->json(['message'=>'updated successfully']);        
    }

    public function edit($id)
    {
        $this->editdata = TblAdvertising::find($id);     
        $this->updateMode=true; 
    }

    public function update($formdata)
    {   
        //start check demo user
            $isDemoUser = User::isDemoUser();
            if($isDemoUser["result"]==true)
            {
                $this->updateMode=false;
                session()->flash('message', $isDemoUser["message"]);
                Session()->flash('class', 'error');
                return redirect()->route('admin/advertising');
            }
        //end check demo user

        $staticpages = TblAdvertising::find($formdata['ad_id']);         
        $staticpages->update([
            'tracking_code' => $formdata['tracking_code'],     
        ]);
        $this->updateMode=false;
        session()->flash('message', 'Updated successfully.');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/advertising');
    } 
}