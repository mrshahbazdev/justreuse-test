<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblOtherpage;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Otherpages extends Component
{

    use WithPagination;
    public $page_id, $title, $content, $slug, $editdata, $staticpages;
    public $insertMode = false;
    public $updateMode=false;
    public $cnfopen = 0;
   

    public function render()
    {
            $list = TblOtherpage::paginate(20);
        return view('livewire.admin.otherpage.compo', [
            'list' => $list,
        ]);
        
    }


    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/otherpages');
    }

    public function create()
    {           
        $this->insertMode=true;
    }        


    public function store($formdata){

        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/otherpages');
            exit;
        }
        // dd($formdata['meta_title'], $formdata['meta_key'], $formdata['meta_description']);
        $slug = Str::slug($formdata['slug'],"-");
        $allpages = TblOtherpage::where('slug',$slug)->first();
        if(empty($allpages)){
            TblOtherpage::create([
            'title' => $formdata['title'],
            'slug' => $slug,
            // 'content' => $formdata['content'],
            'meta_title' => $formdata['meta_title'],
            'meta_key' => $formdata['meta_key'],
            'meta_description' => $formdata['meta_description'],
            ]);

            session()->flash('message', 'New page added successfully.');
            Session()->flash('class', 'success'); 
           return redirect()->route('admin/otherpages');
        }else{
            session()->flash('message', 'Page name already exist!.');
            Session()->flash('class', 'error');  
            return redirect()->route('admin/otherpages');       
        }
        
    }

    public function edit($id)
    {
        $this->editdata = TblOtherpage::find($id);     
        $this->updateMode=true; 
        $this->insertMode=false;
    }


    public function update($formdata)
    {        
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error'); 
            return redirect()->route('admin/otherpages');
            exit;
        }
        $staticpages = TblOtherpage::find($formdata['page_id']);  
        $staticpages->update([
            'title' => $formdata['title'],
            // 'content' => $formdata['content'],
            'meta_title' => $formdata['meta_title'],
            'meta_key' => $formdata['meta_key'],
            'meta_description' => $formdata['meta_description'],
        ]);
        $this->insertMode = false;
        $this->updateMode=false;
        session()->flash('message', 'Updated successfully.');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/otherpages');
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
        TblOtherpage::find($id)->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success'); 
    }


}