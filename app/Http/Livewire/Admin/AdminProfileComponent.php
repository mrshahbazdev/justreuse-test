<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User_profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\WithFileUploads;
Use Storage;

class AdminProfileComponent extends Component
{
    use WithFileUploads;

    public $first_name, $last_name, $phone, $address_line1, $address_line2, $date_of_birth, $gender, $user_id;
    public $name, $email, $profile_photo_path, $new_profile_photo_path;
    public $isOpen = 0;
    public $cnfopen = 0;
    public $state = [];
    public $gendArr = array("male"=>"Male","female"=>"Female","other"=>"Other");

    public function mount()
    {
        $id = Auth::user()->id;
        
        $user_pro = Auth::user($id)->user_profile;
        $user = User::find($id);
        // dd($user->profile_photo_path);
        $this->user_id = $id;
       $this->name = $user->name;
       $this->email = $user->email;
       $this->profile_photo_path = $user->profile_photo_path;
        $this->first_name = $user_pro->first_name;
        $this->last_name = $user_pro->last_name;
        $this->phone = $user_pro->phone;
        $this->address_line1 = $user_pro->address_line1;
        $this->address_line2 = $user_pro->address_line2;
        $this->date_of_birth = $user_pro->date_of_birth;
        $this->gender = $user_pro->gender;
    }


    public function render()
    {
        return view('livewire.admin.myprofile.show');
    }

    public function store()
    {
        $isDemoUser = User::isDemoUser();
        if($isDemoUser["result"]==true)
        {
            return redirect()->route('admin-myprofile');
            exit;
        }

        // dd($this->name,$this->email,$this->first_name,$this->last_name,$this->phone, $this->address_line1,$this->address_line2,$this->date_of_birth,$this->gender);

        $user = User::where('id',Auth::user()->id);

        $imagename = "";
        
        if($this->new_profile_photo_path!=null)
        {
        $imagename =  $this->new_profile_photo_path->store('profile-photos','public');
        $user->update([
            'profile_photo_path' => $imagename
        ]);
        }

        $user->update([
            'name' => $this->name,
            'email' => $this->email,
        ]);
        $user_pro = Auth::user(Auth::user()->id)->user_profile;
         $user_profile = User_profile::findOrFail($user_pro->id);
        
        $user_profile->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'phone' => $this->phone,
           
        ]);

        return redirect()->route('admin-myprofile');

    }


}