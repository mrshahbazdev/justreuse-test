<?php

namespace App\Http\Livewire;

use App\Models\TblFollowers;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyFollowers extends Component
{
    use WithPagination;

    public $activeTab = 'following'; // 'following' or 'followers'
    public $search = '';

    protected $queryString = [
        'search' => ['except' => ''],
        'page' => ['except' => 1],
    ];

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage(); // Reset pagination when switching tabs
    }

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function unfollow($sellerId)
    {
        if (!Auth::check()) {
            return;
        }

        TblFollowers::where('user_id', Auth::id())
            ->where('seller_id', $sellerId)
            ->delete();

        $this->dispatchBrowserEvent('show-toast', ['message' => 'Unfollowed successfully!']);
    }

    public function render()
    {
        $userId = Auth::id();

        // Get IDs of users the current user is following
        $followingIds = TblFollowers::where('user_id', $userId)
            ->where('is_followed', 1)
            ->pluck('seller_id');

        // Get IDs of users who are following the current user
        $followerIds = TblFollowers::where('seller_id', $userId)
            ->where('is_followed', 1)
            ->pluck('user_id');

        $followingsQuery = User::whereIn('id', $followingIds)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        $followersQuery = User::whereIn('id', $followerIds)
            ->where(function ($query) {
                $query->where('name', 'like', '%' . $this->search . '%')
                      ->orWhere('email', 'like', '%' . $this->search . '%');
            });

        return view('livewire.my-followers', [
            'followings' => $followingsQuery->paginate(10, ['*'], 'followingsPage'),
            'followers' => $followersQuery->paginate(10, ['*'], 'followersPage'),
        ]);
    }
}
