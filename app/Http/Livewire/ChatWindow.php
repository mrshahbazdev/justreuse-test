<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblChat;
use App\Models\User;
use App\Models\TblPost;
use Livewire\WithFileUploads;

class ChatWindow extends Component
{
    use WithFileUploads;

    public $chat;
    public $messages;
    public $recipient;
    public $post;

    public $newMessage = '';
    public $offerAmount = '';
    public $image;

    protected $listeners = ['sendLocation'];

    public function mount($chat)
    {
        $this->chat = $chat;
        $this->recipient = User::find($chat['to_id']);
        $this->post = TblPost::find($chat['post_id']);
        $this->loadMessages();
    }

    public function loadMessages()
    {
        $userId = auth()->id();
        $toId = $this->chat['to_id'];
        $postId = $this->chat['post_id'];

        $this->messages = TblChat::where('post_id', $postId)
            ->where(function ($q) use ($userId, $toId) {
                $q->where(function ($q2) use ($userId, $toId) {
                    $q2->where('from_id', $userId)->where('to_id', $toId);
                })->orWhere(function ($q3) use ($userId, $toId) {
                    $q3->where('from_id', $toId)->where('to_id', $userId);
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
        
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }

    public function sendMessage()
    {
        if (empty($this->newMessage)) return;
        $this->saveMessage(['msg' => $this->newMessage]);
        $this->newMessage = '';
    }

    public function sendOffer()
    {
        $this->validate(['offerAmount' => 'required|numeric|min:1']);
        $this->saveMessage([
            'msg' => $this->offerAmount,
            'make_offer' => 1
        ]);
        $this->offerAmount = '';
    }
    
    public function updatedImage()
    {
        $this->validate(['image' => 'image|max:2048']); // 2MB Max
        $imageName = $this->image->store('chatimage', 'public');
        $this->saveMessage(['attachment' => $imageName]);
    }

    public function sendLocation($latitude, $longitude)
    {
        $this->saveMessage([
            'location' => "Location Shared",
            'latitude' => $latitude,
            'longitude' => $longitude
        ]);
    }
    
    private function saveMessage($data)
    {
        $defaultData = [
            'from_id' => auth()->id(),
            'to_id' => $this->chat['to_id'],
            'post_id' => $this->chat['post_id'],
            'receiver' => $this->chat['to_id'],
        ];
        TblChat::create(array_merge($defaultData, $data));
        $this->loadMessages();
    }

    public function render()
    {
        return view('livewire.chat-window');
    }
}