<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblChat;
use App\Models\User;

class ConversationList extends Component
{
    public $selectedConvId;
	protected $listeners = ['updateSelection'];
  	public function updateSelection($selectedId)
    {
        // Parent se aane wali nayi ID ko property mein set karein
        $this->selectedConvId = $selectedId;
    }
    public function selectConversation($toId, $postId)
    {
        // Parent component (Messenger) ko event bhej rahe hain
        $this->emit('conversationSelected', $toId, $postId);
    }

    public function render()
    {
        $userid = auth()->id();

        // Step 1: User se judi saari messages nikal lein, sabse nayi pehle
        $allMessages = TblChat::where(function ($query) use ($userid) {
    $query->where('from_id', $userid)
        ->orWhere('to_id', $userid);
})
->whereNotNull('msg')
->whereNull('deleted_at')
->latest()
->get();

// TblPost table se active posts ki IDs nikal lein
$activePostIds = \DB::table('tbl_posts')
    ->whereIn('id', $allMessages->pluck('post_id')->unique()->filter())
    ->whereNull('deleted_at')
    ->pluck('id');

// Step 2: PHP mein conversations ko uniquely group karein
$conversations = $allMessages->filter(function ($item) use ($activePostIds) {
    // Sirf wo messages rahein jinki post active hai
    return $activePostIds->contains($item->post_id);
})->groupBy(function($item) use ($userid) {
    $otherUserId = ($item->from_id == $userid) ? $item->to_id : $item->from_id;
    return $item->post_id . '-' . $otherUserId;
})->map(function($group) {
    return $group->first();
});
 
        return view('livewire.conversation-list', [
            'conversations' => $conversations
        ]);
    }
}