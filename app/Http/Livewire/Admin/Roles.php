<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use DB;
use Livewire\WithPagination;


class Roles extends Component
{

    use WithPagination;

    public $role_id, $name, $options=[];
    public $permission, $role, $rolePermissions=[];
    public $isOpen = 0;
    public $cnfopen = 0;
    public $insertMode = false;
    public $updateMode=false;


    protected $rules = [
        'name' => 'required',
        // 'options' => 'required',
    ];

    // public function mount() 
    // {
    //     $this->options = ["123d8d07-0a10-4b1f-bc86-2545d6899473","e298e06e-710c-4e1b-8e8f-e0537ad2b048","e60a2452-ad31-4e0f-b805-6494a4411891"];
    //     // $this->permission = Permission::all();
    // }

    public function render()
    {
        $this->roles = Role::all();
        return view('livewire.admin.role.index', [
            'roles' => Role::paginate(10),
        ]);
        
    }

    private function resetInputFields(){
        $this->name = '';
        $this->role_id = '0';
        $this->options=[];

    }

    public function openModal()
    {
        $this->isOpen = true;
    }

    public function closeModal()
    {
        $this->isOpen = false;
        $this->resetValidation();
        $this->insertMode=false;
        $this->updateMode=false;
    }



    public function create()
    {
        $this->permission = Permission::all();
        $this->resetInputFields();
        $this->insertMode=true;
        //$this->openModal();
    }

    public function store()
    {
        $this->validate();    
//    dd($this->options);
        if($this->role_id=="0")
        {
        
           
        $per = Permission::whereIn('id',$this->options)->get();
        //  dd($per);
        $temp_arr=[];
        foreach($per as $row)
        {
            array_push($temp_arr,$row->name);
        }

        $role = Role::create(['name' => $this->name]);
        $role->syncPermissions($temp_arr);
        
        session()->flash('message', 'Role Created Successfully.');  
        
    } else{
         
        $role = Role::find($this->role_id);
        $role->name = $this->name;
        //  dd($this->options);
        $role->save();

        $per = Permission::whereIn('id',$this->options)->get();
        //  dd($per);
        $temp_arr=[];
        foreach($per as $row)
        {
            array_push($temp_arr,$row->name);
        }


        $role->syncPermissions($temp_arr);
        session()->flash('message', 'Role Updated Successfully.');  

    }
        $this->resetInputFields();
        $this->closeModal();
        $this->insertMode=false;
        $this->updateMode=false;
    }

    public function edit($id)
    {
        
        $this->resetInputFields();
            $role = Role::find($id);
            $this->role_id = $id;
            $this->permission = Permission::select(array('name', 'id'))->orderBy('name','asc')->get();
            // dd($this->permission);
            $this->rolePermissions = DB::table("role_has_permissions")->where("role_has_permissions.role_id",$id)
                ->pluck('role_has_permissions.permission_id','role_has_permissions.permission_id')
                ->all();

                $this->name = $role->name;


                $selected_ids = array();
                foreach($this->rolePermissions as $key=>$value)
                {
                    array_push($selected_ids,$key);
                }
        
                $this->options = $selected_ids;
                // dd($this->options);
            //$this->openModal();
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
        // Post::find($id)->delete();
        DB::table("roles")->where('id',$id)->delete();
        session()->flash('message', 'Post Deleted Successfully.');
    }


}