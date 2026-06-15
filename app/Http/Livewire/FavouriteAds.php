<?php

namespace App\Http\Livewire;

use App\Models\TblPost;
use App\Models\TblSavedPosts;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class FavouriteAds extends Component
{
    use WithPagination;

    public $search = "";
    public $selectedAds = [];
    public $selectAll = false;

    // Modal State Properties
    public $showDeleteConfirmation = false;
    public $showBulkDeleteConfirmation = false;
    public $adToDelete = null;

    protected $paginationTheme = 'tailwind';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function updatedSelectAll($value)
    {
        if ($value) {
            $this->selectedAds = $this->getPaginatedAdsQuery()->pluck('fav_id')->map(fn ($id) => (string) $id);
        } else {
            $this->selectedAds = [];
        }
    }

    // Opens the single delete confirmation modal
    public function confirmDelete($favId)
    {
        $this->adToDelete = $favId;
        $this->showDeleteConfirmation = true;
    }

    // Performs the actual deletion after confirmation
    public function deleteConfirmedAd()
    {
        if ($this->adToDelete) {
            TblSavedPosts::find($this->adToDelete)->delete();
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Ad removed from favourites.']);
        }
        // Close modal and reset property
        $this->showDeleteConfirmation = false;
        $this->adToDelete = null;
    }

    // Opens the bulk delete confirmation modal
    public function confirmBulkDelete()
    {
        if (!empty($this->selectedAds)) {
            $this->showBulkDeleteConfirmation = true;
        }
    }

    // Performs the bulk deletion after confirmation
    public function deleteSelected()
    {
        if (!empty($this->selectedAds)) {
            TblSavedPosts::whereIn('id', $this->selectedAds)->delete();
            $this->selectedAds = [];
            $this->selectAll = false;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Selected ads have been removed.']);
        }
        // Close modal
        $this->showBulkDeleteConfirmation = false;
    }

    protected function getPaginatedAdsQuery()
    {
        $userId = Auth::id();

        $savedPosts = TblSavedPosts::where('user_id', $userId)->get();
        $activePostIds = [];
        foreach ($savedPosts as $row) {
            $get_post_ids = TblPost::check_payment_pack_expired($row->post_id);
            if (count($get_post_ids) > 0) {
                $activePostIds[] = $get_post_ids[0];
            }
        }

        return TblPost::select("tbl_posts.*", "tbl_cities.name as city_name", "tbl_saved_posts.id as fav_id")
            ->whereIn('tbl_posts.id', $activePostIds)
            ->where('tbl_posts.active', 1)
            ->where('tbl_posts.sold_status', 0)
            ->whereNull('tbl_posts.deleted_at')
            ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
            ->join("tbl_cities", "tbl_cities.id", "=", "tbl_posts.city")
            ->join("tbl_saved_posts", function ($join) use ($userId) {
                $join->on("tbl_saved_posts.post_id", "=", "tbl_posts.id")
                    ->where("tbl_saved_posts.user_id", "=", $userId);
            })
            ->join("users", function ($join) {
                $join->on("users.id", "=", "tbl_posts.user_id")
                    ->where("users.is_blocked", "=", "0");
            });
    }

    public function render()
    {
        $data = $this->getPaginatedAdsQuery()->paginate(12);
        return view('livewire.favourite-ads', ['data' => $data]);
    }
}

