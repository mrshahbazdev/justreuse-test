<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblPost;
use App\Models\TblPostInsight;
use App\Models\TblSavedPosts;
use App\Models\TblReview;
use App\Models\TblChat;
use App\Models\TblExchangedPost;
use App\Models\TblStaticpage;
use App\Models\TblPayment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class InsightsComponent extends Component
{
    public $postId;
    public $post;

    // Stats Properties
    public $unique_user_views = 0;
    public $total_user_views = 0;
    public $total_likes = 0;
    public $total_comments = 0;
    public $total_offer_request = 0;
    public $total_exchange_request = 0;
    public $total_city = [];
    public $is_promoted = false;
    public $reach_page;

    // Chart Properties
    public $chartPeriod = 'weekly';
    public $chartLabels = [];
    public $chartData = [];

    public function mount($postId)
    {
        $this->postId = $postId;
        $this->post = TblPost::findOrFail($postId);

        if ($this->post->user_id !== Auth::id()) {
            abort(403, 'Unauthorized action.');
        }

        $this->loadStats();
        $this->loadChartData();
    }

    public function loadStats()
    {
        $this->total_user_views = TblPostInsight::where('post_id', $this->postId)->sum('views');
        $this->unique_user_views = TblPostInsight::where('post_id', $this->postId)->distinct('user_id')->count();
        $this->total_likes = TblSavedPosts::where('post_id', $this->postId)->count();
        $this->total_comments = TblReview::where('post_id', $this->postId)->whereNull('deleted_at')->count();
        $this->total_offer_request = TblChat::where('post_id', $this->postId)->where('make_offer', 1)->whereNull('deleted_at')->count();
        $this->total_exchange_request = TblExchangedPost::where('post_id', $this->postId)->whereIn('status', ['pending', 'accepted'])->count();
        
        $this->total_city = TblPostInsight::where('post_id', $this->postId)
            ->where('city', '!=', '')
            ->select('city', DB::raw('count(distinct user_id) as user_count'))
            ->groupBy('city')
            ->orderBy('user_count', 'desc')
            ->get();

        $this->is_promoted = TblPayment::where('post_id', $this->postId)
            ->where('active', '1')
            ->whereDate('end_date', '>=', now())
            ->exists();

        $this->reach_page = TblStaticpage::where('slug', 'insights-reachmore')->first();
    }

    public function updateChart($period)
    {
        $this->chartPeriod = $period;
        $this->loadChartData();
        $this->dispatchBrowserEvent('chart-updated', ['labels' => $this->chartLabels, 'data' => $this->chartData]);
    }

    private function loadChartData()
    {
        $labels = [];
        $data = [];

        switch ($this->chartPeriod) {
            case 'monthly':
                $period = CarbonPeriod::create(now()->subMonths(5), '1 month', now());
                foreach ($period as $date) {
                    $labels[] = $date->format('M Y');
                    $data[] = TblPostInsight::where('post_id', $this->postId)
                        ->whereYear('created_at', $date->year)
                        ->whereMonth('created_at', $date->month)
                        ->sum('views');
                }
                break;
            case 'yearly':
                $period = CarbonPeriod::create(now()->subYears(5), '1 year', now());
                foreach ($period as $date) {
                    $labels[] = $date->format('Y');
                    $data[] = TblPostInsight::where('post_id', $this->postId)
                        ->whereYear('created_at', $date->year)
                        ->sum('views');
                }
                break;
            case 'weekly':
            default:
                $period = CarbonPeriod::create(now()->subDays(6), '1 day', now());
                foreach ($period as $date) {
                    $labels[] = $date->format('D, M j');
                    $data[] = TblPostInsight::where('post_id', $this->postId)
                        ->whereDate('created_at', $date)
                        ->sum('views');
                }
                break;
        }

        $this->chartLabels = $labels;
        $this->chartData = $data;
    }


    public function render()
    {
        return view('livewire.insights')
            ->layout('layouts.insights');
    }
}
