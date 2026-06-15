<?php

namespace App\Http\Livewire\User;

use App\Models\UserAdvertisement;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyAdvertisements extends Component
{
    use WithPagination;

    public function render()
    {
        $advertisements = UserAdvertisement::where('user_id', Auth::id())
            ->with('adZone:id,name')
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('livewire.user.my-advertisements', [
            'advertisements' => $advertisements,
        ])->layout('layouts.packagebuy');
    }
}
