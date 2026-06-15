<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\TblStaticpage;
use App\Models\User;
use Livewire\WithPagination;
use Illuminate\Support\Str;

class Staticpages extends Component
{

    use WithPagination;
    public $page_id, $title, $content, $slug, $editdata, $staticpages;
    public $insertMode = false;
    public $updateMode=false;
    public $cnfopen = 0;
   

    public function render()
    {
        return view('livewire.admin.staticpage.compo', [
            'list' => TblStaticpage::paginate(10),
        ]);
        
    }

    // for back button redirect page
    public function back()
    {   
        return redirect()->route('admin/staticpage');
    }

    public function create()
    {           
        $this->insertMode=true;
        $this->updateMode = false;
    }
    public function store($formdata){
        // dd($formdata['meta_title'], $formdata['meta_key'], $formdata['meta_description']);
        $slug = Str::slug($formdata['slug'],"-");
        $allpages = TblStaticpage::where('slug',$slug)->first();
        if(empty($allpages)){
            TblStaticpage::create([
            'title' => $formdata['title'],
            'slug' => $slug,
            'content' => $formdata['content'],
            'meta_title' => $formdata['meta_title'],
            'meta_key' => $formdata['meta_key'],
            'meta_description' => $formdata['meta_description'],
            ]);
            $this->insertMode = false;
            $this->updateMode=false;
            session()->flash('message', 'New page added successfully.');
            Session()->flash('class', 'success'); 
           return redirect()->route('admin/staticpage');
        }else{
            session()->flash('message', 'Page name already exist!.');
            Session()->flash('class', 'error');  
            return redirect()->route('admin/staticpage');       
        }
        
    }

    public function edit($id)
    {
        $this->editdata = TblStaticpage::find($id);     
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
            return redirect()->route('admin/staticpage');
            exit;
        }

        $staticpages = TblStaticpage::find($formdata['page_id']);  
        $staticpages->update([
            'title' => $formdata['title'],
            'content' => $formdata['content'],
            'meta_title' => $formdata['meta_title'],
            'meta_key' => $formdata['meta_key'],
            'meta_description' => $formdata['meta_description'],
        ]);
        $this->insertMode = false;
        $this->updateMode=false;
        session()->flash('message', 'Updated successfully.');
        Session()->flash('class', 'success'); 
        return redirect()->route('admin/staticpage');
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
        TblStaticpage::find($id)->delete();
        session()->flash('message', 'Deleted successfully.');
        Session()->flash('class', 'success'); 
    }

}