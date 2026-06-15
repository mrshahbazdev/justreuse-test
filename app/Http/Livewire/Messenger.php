<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblChat; // TblChat model ko import kiya gaya

class Messenger extends Component
{
    public $selectedConversation = null;
	protected $listeners = ['conversationSelected', 'backToList'];
    // Yeh 2 nayi properties URL ke liye hain
    public $to = '';
    public $p = '';

    // Yeh Livewire ko batayega ke in properties ko URL ke saath sync karna hai
    protected $queryString = [
        'to' => ['except' => ''],
        'p' => ['except' => ''],
    ];

    /**
     * Computed property to fetch filtered conversations.
     * Use whereHas('post') to ensure only chats related to existing posts are shown.
     */
    public function getConversationsProperty()
    {
        $userId = auth()->id();

        if (!$userId) {
            return collect();
        }

        // TblChat se messages fetch karein jo user se related hain AUR jinki post exist karti hai.
        // whereNull('deleted_at') yeh ensure karta hai ki soft-deleted chats show na hon.
        $conversations = TblChat::query()
            ->where('from_id', $userId)
            ->orWhere('to_id', $userId)
            ->whereNull('deleted_at') // <--- YEH FILTER DELETED CHATS KO HATA DETA HAI
            ->whereHas('post') 
            ->latest()
            ->get();
            
        return $conversations;
    }
    
    public function backToList()
    {
        // Selection ko clear kar dein
        $this->selectedConversation = null;
        $this->to = '';
        $this->p = '';
    }
    
    // Jab page pehli baar load hoga to yeh function chalega
    public function mount()
    {
        // Agar URL mein pehle se 'to' aur 'p' hai, to us chat ko select kar lo
        if ($this->to && $this->p) {
            $this->conversationSelected($this->to, $this->p);
        }
    }

    public function conversationSelected($toId, $postId)
    {
      	
        // 1. User/Post existence check (pehle se maujood)
        if (! User::find($toId) || ! TblPost::find($postId)) {
            $this->backToList(); 
            session()->flash('error', 'Conversation not available. The post or user may have been deleted.');
            return;
        }

        // Query builder for existence
        $chatQueryBuilder = TblChat::where('post_id', $postId)
            ->whereNull('deleted_at') // Sirf non-deleted messages check karein
            ->where(function ($q) use ($toId) {
                // Check karein current user aur $toId ke beech koi bhi message hai ya nahi
                $q->where(function ($q2) use ($toId) {
                    $q2->where('from_id', auth()->id())->where('to_id', $toId);
                })->orWhere(function ($q3) use ($toId) {
                    $q3->where('from_id', $toId)->where('to_id', auth()->id());
                });
            });

        $chatExists = $chatQueryBuilder->exists();
        
        // 2. *** CLEANUP BLOCK ***
       /* if (!$chatExists) {
            // Agar chat history nahi mili (yaani chat empty hai), to usse soft-delete karein.
            
            // Conversation ki saari messages ko soft-delete karein
            TblChat::where('post_id', $postId)
                ->where(function ($query) use ($toId) {
                    $query->where(function ($q) use ($toId) {
                        $q->where('from_id', auth()->id())->where('to_id', $toId);
                    })->orWhere(function ($q) use ($toId) {
                        $q->where('from_id', $toId)->where('to_id', auth()->id());
                    });
                })
                ->delete(); // Soft delete all matching messages

            $this->backToList(); 
            session()->flash('error', 'Chat history not found. Conversation has been removed from your list.');
            
            // Livewire ko force karein ki woh list ko dobara fetch kare
            $this->emit('$refresh'); 
            
            return;
        } */
        // *** END CLEANUP ***

        $this->to = $toId;
        $this->p = $postId;

        $this->selectedConversation = [
            'to_id' => $toId,
            'post_id' => $postId,
            'id' => $toId . '-' . $postId
        ];

        // Hum ab 'ConversationList' component ko direct data bhej rahe hain.
        $this->emitTo('conversation-list', 'updateSelection', $this->selectedConversation['id']);
    }

    public function render()
    {
        return view('livewire.messenger')
            ->layout('layouts.messenger');
    }
}
