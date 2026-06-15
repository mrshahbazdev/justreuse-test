<?php

namespace App\Http\Livewire;

use Livewire\Component;

class ChatPage extends Component
{
    public $selectedChat = null;

    protected $listeners = ['chatSelected'];

    public function chatSelected($chatData)
    {
        $this->selectedChat = $chatData;
    }

    public function render()
    {
        return view('livewire.chat-page')
               ->layout('layouts.frontnew'); // Yahan apni main layout file ka naam dein
    }
}