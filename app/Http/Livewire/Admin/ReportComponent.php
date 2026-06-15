<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\User;
use App\Models\TblReportThisAd;
use Livewire\WithPagination;
use DB;

class ReportComponent extends Component
{
    use WithPagination;

    public $search;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {

        $record = TblReportThisAd::join('users', 'tbl_report_this_ads.user_id', '=', 'users.id')
            ->join('tbl_posts', 'tbl_report_this_ads.post_id', '=', 'tbl_posts.id')
            ->join('report_types', 'tbl_report_this_ads.report_type_id', '=', 'report_types.id')
            ->select("tbl_report_this_ads.created_at", "tbl_report_this_ads.comment", "users.email as user_email", "users.name as user_name", "tbl_posts.id as post_id", "tbl_posts.title", "tbl_posts.slug", "report_types.name as report_name", "tbl_report_this_ads.id", "tbl_report_this_ads.view")
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->orwhere('report_types.name', 'like', '%' . $this->search . '%')
            ->orderBy('tbl_report_this_ads.created_at','desc')
            ->paginate(10);
        return view('livewire.admin.report.show', compact('record'));
    }


    public function report_comment()
    {
        $id = request()->id;

        $comment = TblReportThisAd::find($id)->comment;

        $view = TblReportThisAd::find($id);

        $view->update([
            'view' => "1",
        ]);

        return response()->json(['message' => $comment]);
    }
}
