<?php

namespace App\Http\Livewire;

use App\Models\TblExchangedPost;
use App\Models\TblPost;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class MyExchanges extends Component
{
    use WithPagination;

    public $activeTab = 'incoming';
    protected $paginationTheme = 'tailwind';
    protected $queryString = ['activeTab' => ['except' => 'incoming']];

    public function mount($tab = 'incoming')
    {
        $allowedTabs = ['incoming', 'outgoing', 'successful', 'failed'];
        $this->activeTab = in_array($tab, $allowedTabs) ? $tab : 'incoming';
    }

    public function switchTab($tab)
    {
        $this->activeTab = $tab;
        $this->resetPage();
    }

    public function updateStatus($exchangeId, $status)
    {
        $exchange = TblExchangedPost::find($exchangeId);
        if (!$exchange) return;

        // Authorization check
        if ($exchange->post_owner_id !== Auth::id() && $exchange->user_id !== Auth::id()) {
            return;
        }

        $exchange->status = $status;
        $exchange->save();

        if ($status === 'success') {
            TblPost::where('id', $exchange->post_id)->update(['sold_status' => 1]);
            TblPost::where('id', $exchange->exchanged_post_id)->update(['sold_status' => 1]);
        }

        $this->dispatchBrowserEvent('show-toast', ['message' => "Exchange status updated to '{$status}'."]);
    }

    public function toggleBlock($exchangeId)
    {
        $exchange = TblExchangedPost::find($exchangeId);
        if (!$exchange || $exchange->post_owner_id !== Auth::id()) return;

        $newBlockStatus = !$exchange->block_exchange;
        $exchange->block_exchange = $newBlockStatus;
        $exchange->save();
        
        TblPost::find($exchange->exchanged_post_id)->update(['block_exchange' => $newBlockStatus]);
        
        $message = $newBlockStatus ? 'Exchange has been blocked.' : 'Exchange has been unblocked.';
        $this->dispatchBrowserEvent('show-toast', ['message' => $message]);
    }

    public function render()
    {
        $userId = Auth::id();
        $query = TblExchangedPost::with([
            'post:id,slug,title,locality,user_id', 
            'exchangedPost:id,slug,title,locality,user_id', 
            'owner:id,name', 
            'requester:id,name'
        ]);

        switch ($this->activeTab) {
            case 'outgoing':
                $query->where('user_id', $userId)->whereIn('status', ['pending', 'accepted']);
                break;
            case 'successful':
                $query->where('status', 'success')->where(fn($q) => $q->where('user_id', $userId)->orWhere('post_owner_id', $userId));
                break;
            case 'failed':
                $query->whereIn('status', ['cancelled', 'declined', 'failed'])->where(fn($q) => $q->where('user_id', $userId)->orWhere('post_owner_id', $userId));
                break;
            case 'incoming':
            default:
                $query->where('post_owner_id', $userId)->whereIn('status', ['pending', 'accepted']);
                break;
        }

        $exchanges = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('livewire.my-exchanges', [
            'exchanges' => $exchanges
        ])->layout('layouts.packagebuy');
    }
}
