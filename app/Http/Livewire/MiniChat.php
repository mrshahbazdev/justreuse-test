<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblChat;
use App\Models\User;
use App\Models\TblPost;
use Livewire\WithFileUploads; // Image upload ke liye
use App\Models\TblBlockeduser;

class MiniChat extends Component
{
    use WithFileUploads; // Image upload ke liye
	protected $listeners = ['startChat'];
    public $recipient;
    public $post;
    public $chatMessages;
    public $newMessage = '';
    public $isOpen = false;
    public $image;
	public $isUploadingImage = false;
  	public $isBlocked = false;
  	public $offerAmount = '';
    public $currencySymbol = '$';
    public $suggestedOffers = [];
    public function mount()
    {
        if (session()->has('mini_chat_recipient_id') && session()->has('mini_chat_post_id')) {
            $this->isOpen = true;
            $this->recipient = User::find(session('mini_chat_recipient_id'));
            $this->post = TblPost::find(session('mini_chat_post_id'));
            
            if ($this->recipient && $this->post) {
                $currencyData = TblPost::get_post_currency($this->post->currency_id);
                if (!empty($currencyData[0])) {
                    $this->currencySymbol = html_entity_decode($currencyData[0]);
                }
                $this->loadMessages();
                $this->calculateSuggestedOffers(); // NAYA FUNCTION CALL
            } else {
                $this->closeChat();
            }
        }
    }
  	 public function acceptOffer($messageId)
    {
        $message = TblChat::find($messageId);
        // Sure karein ke sirf receiver hi offer accept kar sakta hai
        if ($message && $message->to_id == auth()->id()) {
            $message->update(['accept_offer' => 1, 'denied_offer' => 0]);
            $this->sendQuickMessage("I have accepted your offer.");
        }
    }

    public function rejectOffer($messageId)
    {
        $message = TblChat::find($messageId);
        // Sure karein ke sirf receiver hi offer reject kar sakta hai
        if ($message && $message->to_id == auth()->id()) {
            $message->update(['denied_offer' => 1, 'accept_offer' => 0]);
            $this->sendQuickMessage("Sorry, I can't accept this offer.");
        }
    }
  	public function calculateSuggestedOffers()
    {
        $price = $this->post->price;
        if ($price <= 0) return;

        $offers = [floor($price)];
        for ($i = 1; $i <= 3; $i++) {
            $newOffer = $price - (($price * ($i * 5)) / 100);
            if ($newOffer > 0 && !in_array(floor($newOffer), $offers)) {
                $offers[] = floor($newOffer);
            }
        }
        $this->suggestedOffers = $offers;
    }

    public function sendQuickMessage($message)
    {
        $this->newMessage = $message;
        $this->sendMessage();
    }
    public function sendQuickOffer($amount)
    {
        $this->offerAmount = $amount;
        $this->sendOffer();
    }
	public function updatedImage()
    {
        $this->isUploadingImage = true; // Uploading shuru hone par true karein

        $this->validate(['image' => 'image|max:2048']);
        $imageName = $this->image->store('chatimage', 'public');

        $message = TblChat::create([
            'from_id' => auth()->id(), 
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id, 
            'attachment' => $imageName,
        ]);
        
        $this->chatMessages->push($message);
        $this->image = null;

        $this->isUploadingImage = false; // Uploading khatam hone par wapas false karein
    }
  	public function startChat($userId, $postId)
    {
        // User ID aur Post ID ko session mein save karein
        session([
            'mini_chat_recipient_id' => $userId,
            'mini_chat_post_id' => $postId
        ]);
        
        // mount() function ko dobara call karein taake chat foran load ho jaye
        $this->mount();
    }
    public function loadMessages()
    {
        if (!$this->isOpen) return;

        // Unread messages ko read mark karein
        TblChat::where('post_id', $this->post->id)
            ->where('to_id', auth()->id())
            ->where('from_id', $this->recipient->id)
            ->where('read_status', 0)
            ->update(['read_status' => 1]);

        $this->chatMessages = TblChat::where('post_id', $this->post->id)
            ->where(function ($q) {
                $q->where(function ($q2) {
                    $q2->where('from_id', auth()->id())->where('to_id', $this->recipient->id);
                })->orWhere(function ($q3) {
                    $q3->where('from_id', $this->recipient->id)->where('to_id', auth()->id());
                });
            })
            ->orderBy('created_at', 'asc')
            ->get();
       $this->dispatchBrowserEvent('scroll-to-bottom'); 
    }

    public function sendMessage()
    {
        if (empty($this->newMessage)) return;
        $message = TblChat::create(['from_id' => auth()->id(), 'to_id' => $this->recipient->id, 'post_id' => $this->post->id, 'msg' => $this->newMessage, 'read_status' => 0]);
        $this->chatMessages->push($message);
        $this->newMessage = '';
      	$this->dispatchBrowserEvent('scroll-to-bottom');
    }
	public function sendOffer()
    {
        $this->validate(['offerAmount' => 'required|numeric|min:1']);

        $message = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'msg' => $this->offerAmount,
            'make_offer' => 1,
            'read_status' => 0
        ]);
        
        $this->chatMessages->push($message);
        $this->offerAmount = '';
    }
    public function closeChat()
    {
        // Session se chat data remove kar dein
        session()->forget(['mini_chat_recipient_id', 'mini_chat_post_id']);
        $this->isOpen = false;
    }
	public function sendLocation($latitude, $longitude)
    {
        $message = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'location' => "Location Shared",
            'latitude' => $latitude,
            'longitude' => $longitude,
            'read_status' => 0
        ]);
        
        $this->chatMessages->push($message);
    }
    public function render()
    {
        #$this->checkBlockStatus();
        $googleApiKey = config('services.google.maps_api_key');

        return view('livewire.mini-chat', [
            'googleApiKey' => $googleApiKey
        ]);
    }
}