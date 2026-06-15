<?php

namespace App\Http\Livewire\Admin;

use App\Models\TblBlockedPost;
use Livewire\Component;
use Illuminate\Support\Facades\DB;
use App\Models\TblContactUs;
use Livewire\WithPagination;

class BlockedPostComponent extends Component
{

    use WithPagination;
    public $search;
    public $cnfopen = 0;

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
       
        $data = TblBlockedPost::whereNull('tbl_blocked_posts.deleted_at')
                ->join('tbl_posts', 'tbl_blocked_posts.post_id', '=', 'tbl_posts.id')
                ->select(['tbl_blocked_posts.*','tbl_posts.title as post_title'])
                ->where(function ($q) {
                    $q->where('tbl_posts.title', 'like', '%' . $this->search . '%');
                })->orderBy('tbl_blocked_posts.created_at', 'desc')->paginate(50);

        return view('livewire.admin.blocked-post.show', compact('data'));
    }
}