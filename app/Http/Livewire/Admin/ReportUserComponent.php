<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\TblReportThisUser;
use Livewire\WithPagination;
use DB;

class ReportUserComponent extends Component
{
    use WithPagination;

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
    // $record = TblReportThisUser::join('users','tbl_report_this_users.user_id','=','users.id')
    //         ->join('report_types','tbl_report_this_users.report_type_id','=','report_types.id')
    //         ->select("tbl_report_this_users.comment","users.name as user_name","report_types.name as report_name","tbl_report_this_users.id","tbl_report_this_users.view")
    //         ->where('users.name','like', '%'.$this->search.'%')
    //         ->orwhere('report_types.name','like', '%'.$this->search.'%')
    //         ->paginate(10);
    
            $record = TblReportThisUser::join('users as u1','tbl_report_this_users.user_id','=','u1.id')
            ->join('users as u2','tbl_report_this_users.reported_user_id','=','u2.id')
            ->join('report_types','tbl_report_this_users.report_type_id','=','report_types.id')
            ->select("tbl_report_this_users.comment","u1.name as user_name","u2.name as reported_user_name","report_types.name as report_name","tbl_report_this_users.id","tbl_report_this_users.view")
            ->where('u1.name','like', '%'.$this->search.'%')
            ->orwhere('report_types.name','like', '%'.$this->search.'%')
            ->paginate(10);

        return view('livewire.admin.report_user.show', compact('record'));
        
    }


    public function report_user_comment()
    {
        $id = request()->id;
         $comment = TblReportThisUser::find($id)->comment;
         $view = TblReportThisUser::find($id);
 

         $isDemoUser = User::isDemoUser();
         if($isDemoUser["result"]==false)
         {
            $view->update([
                'view' => "1",
            ]);
         }



        return response()->json(['message'=>$comment]);
        
    }


//delete report-user
public function delete_user_report() {

    $isDemoUser = User::isDemoUser();
    if($isDemoUser["result"]==true)
    {
        return response()->json(['success'=>false,'message' => $isDemoUser["message"]]);
        exit;
    }

    $ids = request()->ids;
    TblReportThisUser::whereIn('id', explode(",", $ids))->delete();
    return response()->json(['success'=>true,'message' => "Removed Select Reports successfully.."]);
}


}