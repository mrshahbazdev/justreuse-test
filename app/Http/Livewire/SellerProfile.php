<?php

namespace App\Http\Livewire;

use App\Models\Setting;
use App\Models\TblCategory;
use App\Models\TblFollowers;
use App\Models\TblPost;
use App\Models\TblSellerReviews;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ReportType;
use App\Models\TblReportThisUser; // Corrected model name

class SellerProfile extends Component
{
    use WithPagination;

    public User $seller;
    public $isDifferentUser;
    public $isFollowing;
    public $isBuyer = false;

    // Stats
    public $followersCount;
    public $followingCount;
    public $reviewsCount;
    public $totalAdsCount;
    public $avgRating;


    // Filtering & Sorting
    public $sortBy = 'post-desc';
    public $view = 'grid';
    public $selectedCategory = null;

    // Modals State
    public $showFollowersModal = false;
    public $showFollowingModal = false;
    public $showInviteModal = false;
    public $showReportModal = false;
    public $showReviewsModal = false;

    // Report Form
    public $reportType;
    public $reportComment;
    public $reportStatus = null; // To manage modal state ('success', 'already_reported')


    // Review Form
    public $reviewText;
    public $reviewRating;

    protected $queryString = [
        'sortBy' => ['except' => 'post-desc'],
        'view' => ['except' => 'grid'],
        'selectedCategory' => ['except' => null],
    ];

    public function mount(User $seller)
    {
        $this->seller = $seller;
        $this->isDifferentUser = Auth::id() !== $this->seller->id;

        if (Auth::check()) {
            $this->isFollowing = TblFollowers::check_is_follow(Auth::id(), $this->seller->id);
            // You need to implement the logic for $isBuyer here.
        }

        $this->updateStats();
    }

    public function updateStats()
    {
        $this->followersCount = TblFollowers::where('seller_id', $this->seller->id)->where('is_followed', 1)->count();
        // FIX: Added 'is_followed' condition to get the correct count
        $this->followingCount = TblFollowers::where('user_id', $this->seller->id)->where('is_followed', 1)->count();
        $this->reviewsCount = TblSellerReviews::revi_count($this->seller->id);
        $this->avgRating = round(TblSellerReviews::rate_avg($this->seller->id));
        $this->totalAdsCount = TblPost::where('user_id', $this->seller->id)->where('active', 1)->count();
    }

    public function toggleFollow()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $follower = TblFollowers::where('user_id', Auth::id())
            ->where('seller_id', $this->seller->id)
            ->first();

        if ($follower) {
            $follower->delete();
            $this->isFollowing = false;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Unfollowed successfully.']);
        } else {
            TblFollowers::create([
                'user_id' => Auth::id(),
                'seller_id' => $this->seller->id,
                'is_followed' => 1
            ]);
            $this->isFollowing = true;
            $this->dispatchBrowserEvent('show-toast', ['message' => 'Followed successfully.']);
        }

        $this->updateStats();
    }

    public function filterByCategory($categoryId)
    {
        $this->selectedCategory = $categoryId;
        $this->resetPage();
    }

    public function updatedSortBy()
    {
        $this->resetPage();
    }

    public function openReportModal()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $existingReport = TblReportThisUser::where('user_id', Auth::id())
                                       ->where('reported_user_id', $this->seller->id)
                                       ->exists();

        if ($existingReport) {
            $this->reportStatus = 'already_reported';
        } else {
            $this->reset('reportType', 'reportComment', 'reportStatus');
        }

        $this->showReportModal = true;
    }

    public function submitReport()
    {
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $this->validate([
            'reportType' => 'required',
            'reportComment' => 'required|string|max:500',
        ]);

        $existingReport = TblReportThisUser::where('user_id', Auth::id())
                                       ->where('reported_user_id', $this->seller->id)
                                       ->exists();

        if ($existingReport) {
            $this->reportStatus = 'already_reported';
            return;
        }

        TblReportThisUser::create([
            'user_id' => Auth::id(),
            'reported_user_id' => $this->seller->id,
            'report_type_id' => $this->reportType,
            'comment' => $this->reportComment,
        ]);

        $this->reportStatus = 'success';
    }


    public function render()
    {
        $query = TblPost::where('user_id', $this->seller->id)->where('active', 1);

        if ($this->selectedCategory) {
            $query->where('category_id', $this->selectedCategory);
        }

        switch ($this->sortBy) {
            case 'price-asc':
                $query->orderBy('price', 'asc');
                break;
            case 'price-desc':
                $query->orderBy('price', 'desc');
                break;
            case 'most-viewed':
                $query->orderBy('views', 'desc');
                break;
            default:
                $query->orderBy('created_at', 'desc');
        }

        $sellerPosts = $query->paginate(12);

        $categories = TblCategory::whereHas('posts', function ($q) {
            $q->where('user_id', $this->seller->id)->where('active', 1);
        })->withCount(['posts' => function ($q) {
            $q->where('user_id', $this->seller->id)->where('active', 1);
        }])->get();

        $followers = TblFollowers::getFollowers($this->seller->id);
        $following = TblFollowers::getFollowings($this->seller->id);
        $reviews = TblSellerReviews::getReviews($this->seller->id);
        $report_types = ReportType::all();


        return view('livewire.seller-profile', [
            'sellerPosts' => $sellerPosts,
            'categories' => $categories,
            'followers' => $followers,
            'following' => $following,
            'reviews' => $reviews,
            'report_types' => $report_types
        ]);
    }
}

