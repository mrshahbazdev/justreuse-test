<?php

namespace App\Http\Livewire\Admin;

use Livewire\Component;
use App\Models\UserAdvertisement;
use Livewire\WithPagination;

class UserAdvertisements extends Component
{
    use WithPagination;

    public $filterStatus = 'pending_approval';

    public function approveAd($id)
    {
        $ad = UserAdvertisement::find($id);
        if ($ad && $ad->payment_status === 'paid') {
            $ad->status = 'approved';
            $ad->save();
            session()->flash('message', 'Advertisement approved and is now live.');
        } else {
            session()->flash('error', 'Cannot approve. Payment has not been completed.');
        }
    }

    public function rejectAd($id)
    {
        $ad = UserAdvertisement::find($id);
        if ($ad) {
            $ad->status = 'rejected';
            $ad->save();
            // You can add logic here to notify the user and handle refunds
            session()->flash('message', 'Advertisement has been rejected.');
        }
    }

    public function render()
    {
        $query = UserAdvertisement::with(['user:id,name', 'adZone:id,name'])
                    ->orderBy('created_at', 'desc');

        if ($this->filterStatus) {
            $query->where('status', $this->filterStatus);
        }

        return view('livewire.admin.user-advertisements', [
            'advertisements' => $query->paginate(10),
        ])->layout('layouts.admin');
    }
}
