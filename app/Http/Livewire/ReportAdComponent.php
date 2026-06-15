<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User_profile;
use App\Models\User;
use App\Models\TblReportThisAd;
use Illuminate\Support\Facades\Auth;


class ReportAdComponent extends Component
{


    public function render()
    {
        return view('livewire.report-ad');
    }




    public function report_ad()
    {

        $re_type = request()->retype;
        $user_id = auth()->user()->id;
        $comment = request()->comment;
        $post_id = request()->post_id;

        if(Auth::user()==null)
        {
             $message = "Your report cannot be taken..";
        }
        else{

        TblReportThisAd::create([
            'user_id' => $user_id,
            'post_id' => $post_id,
            'report_type_id' => $re_type,
            'comment' => $comment,
        ]);
    
        $message = "Your report has been taken, we will take action soon as possible..";
        return response()->json(['message'=>$message]);
        } 
    }

}