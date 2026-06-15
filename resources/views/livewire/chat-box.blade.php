<div x-data="{ 
    showModal: false, 
    modalImageUrl: '', 
    activeTab: 'message',
    showOptionsMenu: false,
    showConfirmModal: false,
    confirmText: '',
    confirmAction: '',
    showLocationModal: false,
    mapInitialized: false
}" 
x-init="
    // Image modal ke liye body scroll lock karein
    $watch('showModal', value => {
        if (value) {
            document.body.classList.add('overflow-hidden');
        } else {
            document.body.classList.remove('overflow-hidden');
        }
    });

    // Location modal ke liye map ko initialize karein
    $watch('showLocationModal', value => {
        if (value && !mapInitialized) {
            setTimeout(() => { 
                if (typeof initMap !== 'undefined') {
                    initMap();
                }
                mapInitialized = true;
            }, 150); // Thora sa delay taake modal sahi se render ho jaye
        }
    });
"
class="flex flex-col h-full bg-white">
    {{-- ## HEADER ## --}}
    <div class="flex items-center justify-between p-4 border-b bg-white flex-shrink-0 shadow-sm">
        @if ($recipient && $post)
        <div class="flex items-center">
          	<button wire:click="$emit('backToList')" class="md:hidden mr-2 p-2 -ml-2 rounded-full hover:bg-gray-100">
            <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                <line x1="19" y1="12" x2="5" y2="12"></line>
                <polyline points="12 19 5 12 12 5"></polyline>
            </svg>
        </button>
            <img src="{{ $recipient->profile_photo_path ? asset('storage/' . $recipient->profile_photo_path) : 'https://placehold.co/40x40/E2E8F0/4A5568?text=' . strtoupper(substr($recipient->name, 0, 2)) }}" 
                 class="h-10 w-10 rounded-full object-cover shadow-sm">
            <div class="ml-3">
                <p class="font-semibold text-gray-800">{{ $recipient->name }}</p>
                <p class="text-sm text-gray-500">RE: {{ $post->title }}</p>
            </div>
        </div>
        @endif
        <div class="relative">
            <button @click="showOptionsMenu = !showOptionsMenu" class="p-2 rounded-full hover:bg-gray-100 transition-colors" title="More options">
                <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20"><path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z"></path></svg>
            </button>
            
             <div x-show="showOptionsMenu" @click.outside="showOptionsMenu = false" ...>
                <div class="py-2"> 
                    @if($isBlocked)
                        <button @click="confirmText = 'Are you sure you want to unblock this user?'; confirmAction = 'unblockUser'; showConfirmModal = true; showOptionsMenu = false;" class="w-full text-left ...">
                            Unblock User
                        </button>
                    @else
                        <button @click="confirmText = 'Are you sure you want to block this user?'; confirmAction = 'blockUser'; showConfirmModal = true; showOptionsMenu = false;" class="w-full text-left ...">
                            Block User
                        </button>
                    @endif
                    
                    <div class="border-t my-1"></div>
                    
                    <button @click="confirmText = 'Are you sure you want to delete this chat?'; confirmAction = 'deleteChat'; showConfirmModal = true; showOptionsMenu = false;" class="w-full text-left text-red-600 ...">
                        Delete Chat
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Flash message dikhane ke liye --}}
    @if (session()->has('message'))
        <div class="p-3 bg-green-100 text-green-800 text-sm font-medium">
            {{ session('message') }}
        </div>
    @endif

    {{-- ## MESSAGES AREA ## --}}
    <div id="chat-box-container" wire:poll.visible.5s="loadMessages" class="flex-grow p-4 overflow-y-auto bg-gradient-to-b from-white to-[#fffbfa]">
    @if($chatMessages)
        @forelse($chatMessages as $message)
            @php $isOutgoing = $message->from_id == auth()->id(); @endphp
            
            {{-- MESSAGE TIME SEPARATOR --}}
            @if($loop->first || \Carbon\Carbon::parse($message->created_at)->diffInHours(\Carbon\Carbon::parse($chatMessages[$loop->index-1]->created_at)) > 1)
            <div class="flex justify-center my-4">
                <span class="bg-gray-100 text-gray-500 text-xs px-3 py-1 rounded-full">
                    {{ \Carbon\Carbon::parse($message->created_at)->format('M j, Y') }}
                </span>
            </div>
            @endif
            
            {{-- INCOMING MESSAGE (LEFT SIDE) --}}
            @if(!$isOutgoing)
            <div class="flex items-start mb-4 group">
                <img src="{{ $recipient->profile_photo_path ? asset('storage/' . $recipient->profile_photo_path) : 'https://placehold.co/32x32/E2E8F0/4A5568?text=' . strtoupper(substr($recipient->name, 0, 2)) }}" 
                     class="h-8 w-8 rounded-full object-cover flex-shrink-0 mt-1">
                <div class="ml-3 max-w-xs lg:max-w-md">
                    <div class="bg-white text-gray-800 p-3 rounded-2xl shadow-sm border border-gray-100 transition-all duration-200 hover:shadow-md" 
                         style="border-bottom-left-radius: 8px;">
                        @if($message->msg && !$message->make_offer && !$message->location)
                            <p class="text-sm leading-relaxed">{{ $message->msg }}</p>
                        
                        @elseif($message->attachment)
                            <div class="relative">
                                <img @click="modalImageUrl = '{{ $message->attachment ? asset('storage/' . $message->attachment) : '' }}'; showModal = true" 
                                     src="{{ $message->attachment ? asset('storage/' . $message->attachment) : '' }}" 
                                     class="rounded-lg max-w-full h-auto cursor-pointer transition-transform duration-200 hover:scale-[1.02]" 
                                     alt="attachment">
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                    Click to enlarge
                                </div>
                            </div>
                        @elseif($message->location && $googleApiKey)
                            <a href="https://maps.google.com/?q={{ $message->latitude }},{{ $message->longitude }}" target="_blank" class="block">
                                <div class="relative">
                                    <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $message->latitude }},{{ $message->longitude }}&zoom=15&size=300x200&markers=color:red%7C{{ $message->latitude }},{{ $message->longitude }}&key={{ $googleApiKey }}" 
                                         class="rounded-lg max-w-full h-auto transition-transform duration-200 hover:scale-[1.02]" 
                                         alt="Location Map">
                                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                        View on Maps
                                    </div>
                                </div>
                            </a>
                        @elseif($message->make_offer)
                            <div class="p-2 text-center">
                                <div class="flex items-center justify-center mb-1">
                                    <svg class="w-4 h-4 text-green-500 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h3 class="font-bold text-xs text-green-600">Offer Received</h3>
                                </div>
                                <p class="text-2xl font-bold text-center py-1 text-gray-800">€{{ number_format($message->msg) }}</p>
                                
                                {{-- NAYA FEATURE: Offer Accept/Deny Buttons --}}
                                @if($message->make_offer && $message->accept_offer == 0 && $message->denied_offer == 0)
                                <div class="mt-2 pt-2 border-t flex space-x-2">
                                    <button wire:click="acceptOffer('{{ $message->id }}')" class="text-xs font-semibold bg-green-100 text-green-700 px-3 py-1 rounded-full hover:bg-green-200">Accept</button>
                                    <button wire:click="denyOffer('{{ $message->id }}')" class="text-xs font-semibold bg-red-100 text-red-700 px-3 py-1 rounded-full hover:bg-red-200">Deny</button>
                                </div>
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 mt-1 ml-1">{{ \Carbon\Carbon::parse($message->created_at)->format('h:i a') }}</div>
                </div>
            </div>
            @endif

            {{-- OUTGOING MESSAGE (RIGHT SIDE) --}}
            @if($isOutgoing)
            <div class="flex items-start justify-end mb-4 group">
                <div class="max-w-xs lg:max-w-md">
                    <div class="bg-gradient-to-r from-orange-500 to-orange-600 text-white p-3 rounded-2xl shadow-sm transition-all duration-200 hover:shadow-md" 
                         style="border-bottom-right-radius: 8px;">
                        @if($message->msg && !$message->make_offer && !$message->location)
                            <p class="text-sm leading-relaxed">{{ $message->msg }}</p>
                        @elseif($message->attachment)
                            <div class="relative">
                                <img @click="modalImageUrl = '{{ $message->attachment ? asset('storage/' . $message->attachment) : '' }}'; showModal = true" 
                                     src="{{ $message->attachment ? asset('storage/' . $message->attachment) : '' }}" 
                                     class="rounded-lg max-w-full h-auto cursor-pointer transition-transform duration-200 hover:scale-[1.02]" 
                                     alt="attachment">
                                <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded opacity-0 group-hover:opacity-100 transition-opacity">
                                    Click to enlarge
                                </div>
                            </div>
                        @elseif($message->location && $googleApiKey)
                            <a href="https://maps.google.com/?q={{ $message->latitude }},{{ $message->longitude }}" target="_blank" class="block">
                                <div class="relative">
                                    <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $message->latitude }},{{ $message->longitude }}&zoom=15&size=300x200&markers=color:red%7C{{ $message->latitude }},{{ $message->longitude }}&key={{ $googleApiKey }}" 
                                         class="rounded-lg max-w-full h-auto transition-transform duration-200 hover:scale-[1.02]" 
                                         alt="Location Map">
                                    <div class="absolute bottom-2 right-2 bg-black bg-opacity-50 text-white text-xs px-2 py-1 rounded">
                                        View on Maps
                                    </div>
                                </div>
                            </a>
                        @elseif($message->make_offer)
                            <div class="p-2 text-center">
                                <div class="flex items-center justify-center mb-1">
                                    <svg class="w-4 h-4 text-white mr-1" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    <h3 class="font-bold text-xs">Your Offer</h3>
                                </div>
                                <p class="text-2xl font-bold text-center py-1">€{{ number_format($message->msg) }}</p>
                                 
                                @if($message->make_offer && $message->accept_offer == 0 && $message->denied_offer == 0)
                                 <!--<div class="mt-2 pt-2 border-t border-white/20">
                                    <button @click="activeTab = 'offer'; $wire.set('offerAmount', {{ $message->msg }})" class="text-xs font-semibold bg-white/20 text-white px-3 py-1 rounded-full hover:bg-white/30">Edit Offer</button>
                                </div>!-->
                                @endif
                            </div>
                        @endif
                    </div>
                    <div class="text-xs text-gray-400 mt-1 mr-1 text-right flex items-center justify-end">
                        <span>{{ \Carbon\Carbon::parse($message->created_at)->format('h:i a') }}</span>
                        
                        {{-- NAYA FEATURE: Message Seen Status (Ticks) --}}
                        <span class="ml-1">
                           @if($message->read_status == 1)
                              {{-- Double Tick for Read --}}
                              <svg class="w-4 h-4 text-blue-500" fill="currentColor" viewBox="0 0 24 24">
                                  <path d="M18.71,7.21a1,1,0,0,0-1.42,0L9.84,14.67,6.71,11.53A1,1,0,1,0,5.29,13l3.84,3.84a1,1,0,0,0,1.42,0l8.16-8.16A1,1,0,0,0,18.71,7.21Z"/>
                                  <path d="M22.71,7.21a1,1,0,0,0-1.42,0L13.84,14.67,12.71,13.53a1,1,0,0,0-1.42,1.42l2.84,2.84a1,1,0,0,0,1.42,0l8.16-8.16A1,1,0,0,0,22.71,7.21Z"/>
                              </svg>
                          @else
                              {{-- Single Tick for Sent --}}
                              <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                                  <path d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z"/>
                              </svg>
                          @endif
                        </span>
                    </div>
                </div>
                <img src="{{ auth()->user()->profile_photo_path ? asset('storage/' . auth()->user()->profile_photo_path) : 'https://placehold.co/32x32/E2E8F0/4A5568?text=' . strtoupper(substr(auth()->user()->name, 0, 2)) }}" 
                     class="h-8 w-8 rounded-full object-cover flex-shrink-0 mt-1 ml-3">
            </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center h-full text-center py-12">
                <div class="bg-gray-100 p-4 rounded-full mb-4">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-medium text-gray-700 mb-1">No messages yet</h3>
                <p class="text-sm text-gray-500 max-w-xs">Start the conversation by sending a message or making an offer!</p>
            </div>
        @endforelse
    @endif
</div>

    {{-- ## INPUT AREA ## --}}
    <div class="p-4 border-t bg-white flex-shrink-0 shadow-lg">
      	@if($isBlocked)
            <div class="text-center p-4 bg-gray-100 rounded-lg">
                <p class="text-sm font-medium text-gray-600">
                    @if(App\Models\TblBlockeduser::where('blocked_by', auth()->id())->where('blocked_id', $recipient->id)->exists())
                        You have blocked this user. You can't send messages.
                    @else
                        You can't reply to this conversation.
                    @endif
                </p>
            </div>
        @else
        <div class="flex border-b mb-4">
            <button @click="activeTab = 'message'" 
                    :class="{ 'text-orange-600 border-b-2 border-orange-600': activeTab === 'message', 'text-gray-500 hover:text-gray-700': activeTab !== 'message' }" 
                    class="flex-1 py-2 font-medium transition flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                Message
            </button>
            <button @click="activeTab = 'offer'" 
                    :class="{ 'text-orange-600 border-b-2 border-orange-600': activeTab === 'offer', 'text-gray-500 hover:text-gray-700': activeTab !== 'offer' }" 
                    class="flex-1 py-2 font-medium transition flex items-center justify-center">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path></svg>
                Make Offer
            </button>
        </div>

        {{-- MESSAGE TAB --}}
        <div x-show="activeTab === 'message'" x-transition>
          	<div class="flex items-center space-x-2 mb-3 overflow-x-auto pb-2 -mx-2 px-2">
                <button wire:click="sendQuickMessage('Hello')" class="text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors">Hello</button>
                <button wire:click="sendQuickMessage('Is it available?')" class="text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors">Is it available?</button>
                <button wire:click="sendQuickMessage('What is your final price?')" class="text-sm text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors">Final price?</button>
            </div>
            <form wire:submit.prevent="sendMessage" class="flex items-center h-16 rounded-full bg-gray-100 px-4 border border-transparent focus-within:border-orange-300 focus-within:bg-white transition-all duration-200">
                <div class="flex items-center">
                    <label for="image-upload-{{$conversationData['id']}}" class="cursor-pointer p-2 text-gray-500 hover:text-orange-600 transition-colors" title="Attach image">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </label>
                    <input id="image-upload-{{$conversationData['id']}}" type="file" wire:model="image" class="hidden" >
                    
                    <button style="box-shadow:none;" type="button" @click="showLocationModal = true" class="p-2 text-gray-500 hover:text-orange-600 transition-colors" title="Share Location">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </button>
                </div>
                
                <div class="ml-2 flex items-center text-sm text-orange-600 hidden" wire:loading.class.remove="hidden" wire:target="image">

                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    Uploading...
                </div>
                
                <div class="flex-grow mx-3">
                    <input wire:model.defer="newMessage" type="text" class="w-full bg-transparent focus:outline-none placeholder-gray-500" placeholder="Type a message..." x-ref="messageInput" @keydown.enter.prevent="if(!$event.shiftKey) $wire.sendMessage()">
                </div>
                
                <button type="submit" :disabled="!$wire.newMessage && !$wire.image" :class="{ 'bg-orange-500 hover:bg-orange-600': $wire.newMessage || $wire.image, 'bg-gray-400 cursor-not-allowed': !$wire.newMessage && !$wire.image }" class="p-3 flex items-center justify-center text-white rounded-full transition-colors duration-200 shadow-sm">
                    <svg class="w-6 h-6 transform rotate-45 -mr-px" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                </button>
            </form>
        </div>
		<div x-show="showLocationModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
          <div @click.outside="showLocationModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-lg">
              <div class="p-6">
                  <h3 class="text-xl font-semibold mb-4">Share Location</h3>
 
                  <div wire:ignore>
                      <div class="relative">
                          <input id="pac-input" class="w-full p-2 border rounded mb-2" type="text" placeholder="Enter a location">
                          <div id="shared_location_map" style="height: 300px; width: 100%;"></div>
                          <input type="hidden" id="user_latitude">
                          <input type="hidden" id="user_longitude">
                      </div>
                  </div>

              </div>
              <div class="bg-gray-50 p-4 flex justify-end space-x-3">
                  <button type="button" @click="showLocationModal = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300">Cancel</button>
                  <button type="button" 
                          @click="
                              let lat = document.getElementById('user_latitude').value;
                              let lng = document.getElementById('user_longitude').value;
                              if (lat && lng) {
                                  $wire.sendLocation(lat, lng);
                                  showLocationModal = false;
                              } else {
                                  alert('Please select a location on the map.');
                              }
                          "
                          class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600">
                      Share
                  </button>
              </div>
          </div>
      </div>
        {{-- OFFER TAB --}}
        <div x-show="activeTab === 'offer'" x-transition style="display: none;">
          	@if(!empty($suggestedOffers))
            <div class="flex items-center space-x-2 mb-3 overflow-x-auto pb-2">
                @foreach($suggestedOffers as $offer) 
                    <button wire:click="sendQuickOffer({{ $offer }})" class="text-sm text-orange-700 bg-orange-100 hover:bg-orange-200 py-1.5 px-4 rounded-full flex-shrink-0 transition-colors mr-2">
                        {{ $currencySymbol }}{{ number_format($offer) }}
                    </button>
                @endforeach
            </div>
            @endif
            <form wire:submit.prevent="sendOffer" class="flex items-center space-x-3">
                <div class="flex-grow relative">
                    <span class="absolute left-4 top-1/4 -translate-y-1/2 text-gray-500 font-medium">
                        
                        {{ $currencySymbol }}
                    </span>
                    <input wire:model.defer="offerAmount" type="number" class="w-full bg-gray-100 rounded-full p-4 pl-10 focus:outline-none focus:ring-2 focus:ring-green-500 focus:bg-white transition-all duration-200 border border-transparent" placeholder="0" min="0">
                </div>
                <button type="submit" class="text-white px-6 py-4 rounded-full font-semibold transition-colors duration-200 shadow-sm flex items-center bg-green-500 hover:bg-green-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Send Offer
                </button>
            </form>
            <p class="text-xs text-gray-500 mt-2 text-center">Make a reasonable offer to increase your chances of acceptance</p>
        </div>
      @endif
    </div>
	<div x-show="showConfirmModal" x-transition class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-50" style="display: none;">
        <div @click.outside="showConfirmModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-sm p-6 text-center">
            <h3 class="text-lg font-medium text-gray-900 mb-4" x-text="confirmText"></h3>
            <div class="flex justify-center space-x-4">
                <button @click="showConfirmModal = false" class="px-4 py-2 bg-gray-200 text-gray-800 rounded-lg hover:bg-gray-300">
                    Cancel
                </button>
                <button @click="$wire.call(confirmAction); showConfirmModal = false;" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700">
                    Confirm
                </button>
            </div>
        </div>
    </div>
  	
    <script>
      	
        document.addEventListener('DOMContentLoaded', () => {
            const container = document.getElementById('chat-box-container');
            if(container) { container.scrollTop = container.scrollHeight; }
            
            window.addEventListener('scroll-to-bottom', event => {
                if(container) { container.scrollTop = container.scrollHeight; }
            });
            
            if (typeof Livewire !== 'undefined') {
                Livewire.on('tabChanged', tab => {
                    if(tab === 'message') {
                        setTimeout(() => {
                            const input = document.querySelector('[x-ref="messageInput"]');
                            if(input) input.focus();
                        }, 100);
                    }
                });
            }
        });
      let map, marker;
        function initMap() {
            if (typeof google === 'undefined') {
                console.error("Google Maps script is not loaded yet.");
                return;
            }
            
            const initialLocation = { lat: 31.7165, lng: 73.7133 };
            
            document.getElementById('user_latitude').value = initialLocation.lat;
            document.getElementById('user_longitude').value = initialLocation.lng;

            map = new google.maps.Map(document.getElementById('shared_location_map'), {
                center: initialLocation,
                zoom: 13
            });
            
            marker = new google.maps.Marker({
                position: initialLocation,
                map: map,
                draggable: true
            });

            const input = document.getElementById('pac-input');
            const autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.bindTo('bounds', map);

            autocomplete.addListener('place_changed', () => {
                const place = autocomplete.getPlace();
                if (!place.geometry) return;
                
                map.setCenter(place.geometry.location);
                map.setZoom(17);
                marker.setPosition(place.geometry.location);
                
                document.getElementById('user_latitude').value = place.geometry.location.lat();
                document.getElementById('user_longitude').value = place.geometry.location.lng();
            });

            marker.addListener('dragend', (event) => {
                document.getElementById('user_latitude').value = event.latLng.lat();
                document.getElementById('user_longitude').value = event.latLng.lng();
            });
        }
    </script>
    
    {{-- ## FIXED IMAGE MODAL ## --}}
    <template x-teleport="body">
        <div x-show="showModal" 
             x-transition:enter="ease-out duration-300" 
             x-transition:enter-start="opacity-0" 
             x-transition:enter-end="opacity-100" 
             x-transition:leave="ease-in duration-200" 
             x-transition:leave-start="opacity-100" 
             x-transition:leave-end="opacity-0" 
             @keydown.escape.window="showModal = false"
             @click.self="showModal = false"
             class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center p-4 z-[9999]"
             style="display: none;">
            
            <div class="relative max-w-4xl max-h-full w-full flex items-center justify-center">
                <button @click="showModal = false" class="absolute -top-12 right-0 text-white hover:text-orange-300 rounded-full h-10 w-10 flex items-center justify-center text-3xl font-bold z-10">
                    &times;
                </button>
                
                <div class="bg-white rounded-lg overflow-hidden shadow-2xl max-w-full max-h-full flex items-center justify-center">
                    <template x-if="modalImageUrl">
                        <img :src="modalImageUrl" alt="Full size image" class="max-w-full max-h-[85vh] object-contain">
                    </template>
                    <template x-if="!modalImageUrl">
                        <div class="p-8 text-center text-gray-500">
                            <p>Image not available</p>
                        </div>
                    </template>
                </div>
            </div>
        </div>
    </template>
  <style>
  	/* Fix for chat interface scrolling */
body {
    overflow: hidden !important;
    height: 100vh;
}

main {
    height: 100vh;
    overflow: hidden;
}

.flex.h-screen.antialiased.text-gray-800 {
    height: 100vh;
    overflow: hidden;
}

.flex-row.h-full.w-full.overflow-x-hidden {
    height: 92vh;
    overflow: hidden;
}

/* Make only the chat messages scrollable */
#chat-box-container {
    height: calc(100vh - 200px); /* Adjust based on header and input height */
    overflow-y: auto;
    overflow-x: hidden;
}

/* Ensure header stays fixed */
.header.sticky {
    position: fixed;
    top: 0;
    width: 100%;
    z-index: 10;
}

/* Adjust main content to account for fixed header */
main {
    padding-top: 80px; /* Adjust based on header height */
}
  </style>
</div>