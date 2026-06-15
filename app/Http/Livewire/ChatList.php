<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblChat;
use App\Models\User;
use App\Models\TblPost;

class ChatList extends Component
{
    public $selectedChatId;
    public $search = '';

    public function selectChat($toId, $postId)
    {
        $this->emit('chatSelected', [
            'id' => $toId . '-' . $postId,
            'to_id' => $toId,
            'post_id' => $postId
        ]);
    }

    public function render()
    {
        $userid = auth()->id();
        
        $chatlists = TblChat::where(function ($query) use ($userid) {
            $query->where('from_id', $userid)
                  ->orWhere('to_id', $userid);
        })
        ->join('tbl_posts', function ($join) {
            $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                 ->whereNull('tbl_posts.deleted_at')
                 ->where('tbl_posts.title', 'like', '%' . $this->search . '%')
                 ->where('tbl_posts.sold_status', 0);
        })
        ->whereNotNull('msg')
        ->whereNull('tbl_chats.deleted_at')
        ->groupBy('tbl_chats.post_id', 'receiver')
        ->orderByRaw('MAX(tbl_chats.created_at) DESC')
        ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

        return view('livewire.chat-list', [
            'chatlists' => $chatlists
        ]);
    }
}