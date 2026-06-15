<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblPost;
use App\Models\TblPayment;
use Livewire\Request;
use Session;
use Livewire\WithPagination; //for pagination
use Illuminate\Support\Facades\Auth;

class MypackageComponentAdd extends Component
{

    public function render()
    {
        $bulkids = TblPayment::where('user_id', Auth::id())
            ->where('active', '1')
            ->whereDate('end_date', '>=', date('Y-m-d'))
            ->pluck('post_id')->toArray();
        $list = TblPost::where('user_id', Auth::id())
            ->whereNull('deleted_at')
            ->where('active', '1')
            ->whereNotIn('id', $bulkids)
            ->orderBy('title', 'asc')
            ->get();
        return view('livewire.mypackage.add', ['list' => $list]);
    }
}
