<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use Livewire\WithPagination;
use Spatie\Permission\Models\Role;
use App\Actions\Fortify\PasswordValidationRules;
use App\Models\User_profile;
use Illuminate\Support\Facades\Hash;
use DB;

class Users extends Component
{

    use WithPagination;
    use PasswordValidationRules;


    public $get_role;
    public $user_id, $name, $email, $phone, $password, $password_confirmation, $role_list = [];
    public $roles;
    public $userRole = [];
    public $cnfopen = 0;
    public $insertMode = false;
    public $updateMode = false;
    public $search;

    public function render()
    {


        $search = !empty($this->search) ? $this->search : "";
        // $users = User::where('email', 'Like', '%' . $search . '%')->orWhere('phone', 'Like', '%' . $search . '%')->whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);

        if (!empty($this->get_role) && $this->get_role == "admin") {
           
            $users = User::join('model_has_roles', 'model_has_roles.model_id', '=', 'users.id')
                ->where('model_has_roles.role_id', '=', '780bd74f-aa54-4116-ac11-057b3996a575')
                ->whereNull('users.deleted_at')
                ->orderBy('users.created_at', 'desc')
                ->paginate(10);
                // dd($users);
        } else {
            $users = User::where('email', 'Like', '%' . $search . '%')->orWhere('phone', 'Like', '%' . $search . '%')->whereNull('deleted_at')->orderBy('created_at', 'desc')->paginate(10);
        }

        return view('livewire.admin.user.show', compact('users'));
    }

    private function resetInputFields()
    {
        $this->name = '';
        $this->user_id = '0';
        $this->email = '';
        $this->phone = '';
        $this->password = '';
        $this->password_confirmation = '';
        $this->role_list = '';
    }

    public function closeModal()
    {
        $this->resetValidation();
        $this->insertMode = false;
        $this->updateMode = false;
    }

    public function create()
    {
        $this->roles = Role::all();
        $this->resetInputFields();
        $this->insertMode = true;
    }

    public function store()

    {

        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return redirect()->route('admin/user');
            exit;
        }
        if ($this->user_id == "0") {
            $this->validate([
                'role_list' => 'required',
                'name' => 'required',
                'email' => 'required|email',
                'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10', 'unique:users'],
                'password' => 'required|confirmed'
            ]);
            $chk_user = User::where('email', $this->email)->count();

            if ($chk_user > 0) {
                session()->flash('message', 'This User Already exist.');
            } else {
                $user = User::create([
                    'name' => $this->name,
                    'email' => $this->email,
                    'phone' => $this->phone,
                    'password' => Hash::make($this->password)
                ]);
                $role = $this->role_list;
                $user->assignRole([$role]);
                //set user id to user profile table
                User_profile::create([
                    'user_id' => $user->id,
                    'phone' => $this->phone
                ]);
                session()->flash('message', 'User Created Successfully.');
            }
        } else {
           
        //    dd($this->role_list);
            $this->validate([
                'role_list' => 'required',
                'name' => 'required',
                'email' => 'required|email',
                'phone' => ['required', 'regex:/^([0-9\s\-\+\(\)]*)$/', 'min:10'],
            ]);
            $user = User::find($this->user_id);
            $user_profile = User_profile::where('user_id', $this->user_id)->first();
            //   dd($this->role_list);
            $user->update([
                'name' => $this->name,
                'email' => $this->email,
                'phone' => $this->phone
            ]);

            $user_profile->update([
                'phone' => $this->phone
            ]);

            DB::table('model_has_roles')->where('model_id', $this->user_id)->delete();
            $role = $this->role_list;
           
            $role_id = "";
            // foreach($role as $get_role){
                // dd($get_role);
                if( $role == "SuperAdmin"){
                    $role_id = "39d21443-27be-4ed1-bcf6-40d2f78beca5";
                }elseif($role == "Admin"){
                    $role_id = "780bd74f-aa54-4116-ac11-057b3996a575";
                }else{
                    $role_id = "b27d896c-f396-4670-91d5-1df724afe84c";
                }
            // }
            

            $user->assignRole([$role]);
            $model_role =  DB::table('model_has_roles')
            ->where('model_id',  $this->user_id) // Adjust this according to your model namespace
            ->update(['role_id' =>$role_id]);
            session()->flash('message', 'User Updated Successfully.');
        }
        $this->resetInputFields();
        $this->closeModal();
        $this->insertMode = false;
        $this->updateMode = false;
    }

    public function edit($id)
    {

        $user = User::find($id);
        $this->roles = Role::all();
        //   dd($this->roles);
        $this->userRole = $user->roles->pluck('name', 'name')->all();
        //   dd($userRole);
        $this->user_id = $id;
        $this->name = $user->name;
        $this->email = $user->email;
        $this->phone = $user->phone;
        $selected_ids = array();
        foreach ($this->userRole as $key => $value) {
            array_push($selected_ids, $key);
        }
        $this->role_list = $selected_ids;
        $this->updateMode = true;
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


        $isDemoUser = User::isDemoUser();
        if ($isDemoUser["result"] == true) {
            $this->insertMode = false;
            $this->updateMode = false;
            session()->flash('message', $isDemoUser["message"]);
            Session()->flash('class', 'error');
            return redirect()->route('admin/user');
            exit;
        }
        $this->cnfopen = 0;
        User::find($id)->delete();
        session()->flash('message', 'User Deleted Successfully.');
    }
}
