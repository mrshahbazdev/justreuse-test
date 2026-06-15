<?php

namespace App\Http\Livewire\Admin;
use Livewire\Component;
use Livewire\WithPagination;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Str;
use DB;

class Permissions extends Component
{

    use WithPagination;
   
    public $name, $permission_id;
    public $cnfopen = 0;
    public $isOpen = 0;
    public $insertMode = false;
    public $updateMode=false;

    public $search;

    
    protected $rules = [       
        'name' => 'required'    
    ];

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        $this->roles = Permission::all();
        return view('livewire.admin.permission.show', [
            'permission' => Permission::where('name','like', '%'.$this->search.'%')->paginate(30),
        ]);
        
    }

    private function resetInputFields(){
        $this->name = '';
        $this->permission_id = '0';
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->insertMode=false;
        $this->updateMode=false;
    }


    public function create()
    {   
        $this->resetInputFields();
        $this->insertMode=true;
    }


    public function store()
    {
        $this->validate();

        if($this->permission_id == "0"){
            Permission::create(
                ['name' => Str::slug($this->name), '-',
                'guard_name'=>"web"]
            );
            session()->flash('message', 'Permission Created Successfully.'); 

        } else{

            $permissions = Permission::find($this->permission_id);
            $permissions->update([
                'name' => $this->name,
                'guard_name'=>"web"
            ]);

            session()->flash('message', 'Permission Updated Successfully.'); 
        }


        $this->resetInputFields();
            $this->closeModal();
            $this->insertMode=false;
            $this->updateMode=false;
     
    }


    public function edit($id)
       {
          $permissions = Permission::find($id);
          $this->permission_id = $id;
          $this->name = $permissions->name;

            $this->updateMode=true;

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
        Permission::find($id)->delete();
        session()->flash('message', 'Permission Deleted Successfully.');
    }



}