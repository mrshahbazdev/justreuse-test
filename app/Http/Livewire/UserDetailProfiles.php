<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User_profile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;


class UserDetailProfiles extends Component
{
   
    
    public $first_name, $last_name, $phone, $address_line1, $address_line2, $date_of_birth, $gender, $user_id;
    public $isOpen = 0;
    public $cnfopen = 0;
    public $state = [];
    public $gendArr = array("male"=>"Male","female"=>"Female","other"=>"Other");
    
    public function mount()
    {
        $id = Auth::user()->id;
        $user_pro = Auth::user($id)->user_profile;
        $this->user_id = $id;
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
        return view('livewire.profile.update-user-profile');
    }

    
    public function store()
    {      
        $user_pro = Auth::user(Auth::user()->id)->user_profile;
        $user = User_profile::findOrFail($user_pro->id);
        $user->update([
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'address_line1' => $this->address_line1,
            'address_line2' => $this->address_line2,
            'date_of_birth' => $this->date_of_birth,
            'gender' => $this->gender,
            'phone' => $this->phone,
        ]);   
        return redirect()->route('user-profile.show');            
    }
  
}
