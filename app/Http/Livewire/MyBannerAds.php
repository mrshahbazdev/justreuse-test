<?php

namespace App\Http\Livewire;

use App\Models\TblBannerAdvertisement;
use App\Models\TblPostMethod;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyBannerAds extends Component
{
    use WithPagination;

    public $search = '';
    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function render()
    {
        // Check if the banner ads feature is active
        $is_active = TblPostMethod::get_active_post_methods()->pluck('name')->contains("bannerads");

        if (!$is_active) {
            // If not active, you can show a disabled view or abort
            // For now, we will pass an empty collection to the view
            $bannerads = collect();
        } else {
            $query = TblBannerAdvertisement::where('user_id', Auth::id())
                ->orderBy('created_at', 'desc');

            if ($this->search) {
                $query->where(function ($q) {
                    $q->where('page', 'like', '%' . $this->search . '%')
                      ->orWhere('status', 'like', '%' . $this->search . '%');
                });
            }
            
            $bannerads = $query->paginate(10);
        }

        return view('livewire.my-banner-ads', [
            'bannerads' => $bannerads,
            'is_feature_active' => $is_active,
        ])->layout('layouts.packagebuy');
    }
}
