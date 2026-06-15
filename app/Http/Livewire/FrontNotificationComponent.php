<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblPost;
use App\Models\User;
use App\Models\TblNotifications;
use App\Models\TblFollowers;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Livewire\WithPagination; //for pagination

class FrontNotificationComponent extends Component {
	
	use WithPagination;
	
	
	    public function updatingSearch()
    {
        $this->resetPage();
    }


    public function render() {
		

            /* Update notification read status start */
            if(!empty(Auth::user()->id)){
                TblNotifications::where('to_id', auth()->user()->id)->update(array('read_status' => 1));
            }
            
            /* Update notification read status end */

		     $notifications = TblNotifications::where('to_id', auth()->user()->id)->orderBy('id','desc')->paginate(12);

            //  $notifications = TblNotifications::select("tbl_notifications.id as id", "tbl_notifications.slug as slug", "tbl_notifications.msg as msg", "tbl_notifications.created_at as created_at", "tbl_notifications.to_id as to_id", "users.profile_photo_path as profile_photo_path")
            // ->where('tbl_notifications.to_id', auth()->user()->id)
            // ->join('users', 'users.id', '=', 'tbl_notifications.to_id')
            // ->orderBy('id','desc')
            // ->paginate(12);
             
		
        return view('livewire.front-notifications', ['notifications' => $notifications]);
    }

	
	
	
}
