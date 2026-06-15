<?php

namespace App\Http\Livewire;

use Livewire\Component;
use App\Models\TblChat;
use App\Models\User;
use App\Models\TblPost;
use App\Models\TblBlockeduser;
use Livewire\WithFileUploads;


class ChatBox extends Component
{
    use WithFileUploads;

    public $conversationData;
    public $chatMessages;
    public $recipient;
    public $post;
    public $isBlocked = false;
    public $newMessage = '';
    public $offerAmount = '';
    public $image;
	public $suggestedOffers = [];
  	public $currencySymbol = '$';
    public function mount($conversationData)
    {
        $this->conversationData = $conversationData;
        
        // 1. Post aur Recipient ko find karein
        $this->recipient = User::find($conversationData['to_id']);
        $this->post = TblPost::find($conversationData['post_id']);

        // 2. Yahan naya check lagayen
        if (!$this->recipient || !$this->post) {
            // Agar recipient ya post exist nahi karta, to user ko wapas messages page par bhej dein
            // 'messages' ko us route name se badal dein jo aapki inbox list ka hai.
            return redirect()->route('messages'); 
        }
        
        // Agar sab theek hai, to aage proceed karein
        $currencyData = TblPost::get_post_currency($this->post->currency_id);
        if (!empty($currencyData[0])) {
            $this->currencySymbol = html_entity_decode($currencyData[0]);
        }
        $this->loadMessages();
        $this->calculateSuggestedOffers();
        #$this->checkBlockStatus();
    }
  	public function calculateSuggestedOffers()
    {
        $price = $this->post->price;
        if ($price <= 0) return;

        $offers = [];
        // Pehla offer post ki asli price hogi
        $offers[] = floor($price);

        // Baaki 3 offers calculate karein (e.g., 5%, 10%, 15% kam)
        for ($i = 1; $i <= 3; $i++) {
            $percentageToReduce = $i * 5; // 5%, 10%, 15%
            $reduction = ($price * $percentageToReduce) / 100;
            $newOffer = $price - $reduction;
            
            // Offers ko aapsi takkar se bachane ke liye check
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

    // NAYA FUNCTION: Quick offer send karne ke liye
    public function sendQuickOffer($amount)
    {
        $this->offerAmount = $amount;
        $this->sendOffer();
    }
	 public function checkBlockStatus()
    {
        if (!$this->recipient || !$this->post) return;

        $isBlockedByMe = TblBlockeduser::where('post_id', $this->post->id)
            ->where('blocked_by', auth()->id())
            ->where('blocked_id', $this->recipient->id)
            ->where('block_status', 1)
            ->exists();

        $isBlockedByOther = TblBlockeduser::where('post_id', $this->post->id)
            ->where('blocked_by', $this->recipient->id)
            ->where('blocked_id', auth()->id())
            ->where('block_status', 1)
            ->exists();

        $this->isBlocked = $isBlockedByMe || $isBlockedByOther;
    }
    public function loadMessages()
    {
        if (!$this->recipient || !$this->post) return;

        // NAYA LOGIC: Incoming unread messages ko 'read' mark karein
        TblChat::where('post_id', $this->post->id)
            ->where('to_id', auth()->id())
            ->where('read_status', 0) // Pehle yahan 'read_at' tha
            ->update(['read_status' => 1]); // Pehle yahan 'read_at' => now() tha

        // Baaki ka function waisa he rahega
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
  	public function acceptOffer($messageId)
    {
        $message = TblChat::find($messageId);
        if ($message && $message->to_id == auth()->id()) {
            $message->update(['accept_offer' => 1, 'denied_offer' => 0]);
            $this->sendQuickMessage("I have accepted your offer.");
        }
    }

    // NAYA FUNCTION: Offer ko deny karne ke liye
    public function denyOffer($messageId)
    {
        $message = TblChat::find($messageId);
        if ($message && $message->to_id == auth()->id()) {
            $message->update(['denied_offer' => 1, 'accept_offer' => 0]);
            $this->sendQuickMessage("Sorry, I can't accept this offer.");
        }
    }
	public function blockUser()
    {
        TblBlockeduser::updateOrCreate(
            ['post_id' => $this->post->id, 'blocked_by' => auth()->id(), 'blocked_id' => $this->recipient->id],
            ['block_status' => 1]
        );
        session()->flash('message', 'User has been blocked.');
        
    }

    public function unblockUser()
    {
        TblBlockeduser::where('post_id', $this->post->id)
            ->where('blocked_by', auth()->id())
            ->where('blocked_id', $this->recipient->id)
            ->update(['block_status' => 0]);
        session()->flash('message', 'User has been unblocked.');
    }
    // NAYA FUNCTION: Chat ko delete karne ke liye
    public function deleteChat()
    {
        TblChat::where('post_id', $this->post->id)
            ->where(function ($query) {
                $query->where(function ($q) {
                    $q->where('from_id', auth()->id())->where('to_id', $this->recipient->id);
                })->orWhere(function ($q) {
                    $q->where('from_id', $this->recipient->id)->where('to_id', auth()->id());
                });
            })
            ->delete();

        // Chat delete karne ke baad, user ko inbox par wapas bhej dein
        return redirect()->route('messages');
    }
    public function sendMessage()
    {
        if (empty($this->newMessage)) return;

        // Message ko database mein save karein
        $newMessage = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'receiver' => $this->recipient->id,
            'msg' => $this->newMessage,
        ]);

        // chatMessages collection mein naya message add karein
        $this->chatMessages->push($newMessage);

        // Input field ko khaali karein
        $this->newMessage = '';

        // JavaScript ko scroll down karne ka signal dein
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }
    
    // YEH FUNCTION IMAGE UPLOAD KE LIYE BOHOT ZAROORI HAI
    public function updatedImage()
    {
        $this->validate(['image' => 'image|max:2048']);
        $imageName = $this->image->store('chatimage', 'public');

        $newMessage = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'receiver' => $this->recipient->id,
            'attachment' => $imageName,
        ]);
        
        $this->chatMessages->push($newMessage);
        $this->image = null; // Temporary file ko clear karein
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }
    public function sendOffer()
    {
        $this->validate(['offerAmount' => 'required|numeric|min:1']);

        $newMessage = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'receiver' => $this->recipient->id,
            'msg' => $this->offerAmount,
            'make_offer' => 1
        ]);
        
        $this->chatMessages->push($newMessage);
        $this->offerAmount = '';
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }

    public function sendLocation($latitude, $longitude)
    {
        $newMessage = TblChat::create([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'receiver' => $this->recipient->id,
            'location' => "Location Shared",
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);
        
        $this->chatMessages->push($newMessage);
        $this->dispatchBrowserEvent('scroll-to-bottom');
    }
    
    private function saveMessage($data)
    {
        TblChat::create(array_merge([
            'from_id' => auth()->id(),
            'to_id' => $this->recipient->id,
            'post_id' => $this->post->id,
            'receiver' => $this->recipient->id,
        ], $data));
        $this->loadMessages();
    }

    public function render()
    {
      	$this->checkBlockStatus();
        $googleApiKey = config('services.google.maps_api_key');
      
        return view('livewire.chat-box', ['googleApiKey' => $googleApiKey]);
    }
}