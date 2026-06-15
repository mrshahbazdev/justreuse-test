<div>
    @if($isOpen && $recipient && $post)
    <div x-data="{ 
        isMinimized: false, 
        activeTab: 'message',
        showImageModal: false,
        currentImage: '',
        showLocationModal: false,
        mapInitialized: false,
 
        initMap() {
            if (typeof google === 'undefined' || !document.getElementById('shared_location_map')) {
                console.error('Google Maps not ready or map container not found.');
                return;
            }
            
            const initialLocation = { lat: 31.7165, lng: 73.7133 };
            document.getElementById('user_latitude').value = initialLocation.lat;
            document.getElementById('user_longitude').value = initialLocation.lng;

            let map = new google.maps.Map(document.getElementById('shared_location_map'), {
                center: initialLocation,
                zoom: 13
            });
            
            let marker = new google.maps.Marker({
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
    }" 
    x-init="
        $watch('showLocationModal', value => {
            if (value && !mapInitialized) {
                setTimeout(() => { 
                    this.initMap(); // Yahan ab local function call hoga
                    this.mapInitialized = true;
                }, 150);
            }
        });
    "
    class="fixed inset-x-0 bottom-0 md:right-4 md:left-auto md:bottom-0 md:inset-x-auto w-full md:max-w-sm bg-white rounded-t-xl md:rounded-t-xl shadow-2xl border border-gray-200 z-50">
        
        {{-- Image Preview Modal --}}
        <div x-show="showImageModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-90 flex items-center justify-center z-60 p-4">
            <div class="relative max-w-4xl max-h-full w-full">
                {{-- Close Button --}}
                <button @click="showImageModal = false; currentImage = ''" class="absolute -top-12 right-0 text-white hover:text-gray-300 transition-colors">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
                
                {{-- Image --}}
                <img :src="currentImage" class="w-full h-auto max-h-[80vh] object-contain rounded-lg" alt="Preview">
            </div>
        </div>

        {{-- Location Modal --}}
        <div x-show="showLocationModal" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="transition ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center p-4 z-[999]" style="display: none;">
            <div @click.outside="showLocationModal = false" class="bg-white rounded-lg shadow-xl w-full max-w-lg">
                <div class="p-6">
                    <h3 class="text-xl font-semibold mb-4">Share Location</h3>
                    <div wire:ignore>
                        <div class="relative">
                            <input id="pac-input" class="w-full p-2 border rounded mb-2" type="text" placeholder="Enter a location">
                            {{-- Map container with proper height and border --}}
                            <div id="shared_location_map" style="height: 300px; width: 100%; border: 1px solid #ccc;" class="rounded-lg"></div>
                            <input type="hidden" id="user_latitude">
                            <input type="hidden" id="user_longitude">
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 p-4 flex justify-end space-x-3">
                    <button type="button" @click="showLocationModal = false" class="px-4 py-2 bg-gray-200 rounded-lg hover:bg-gray-300 transition-colors">Cancel</button>
                    <button type="button" @click="let lat = document.getElementById('user_latitude').value; let lng = document.getElementById('user_longitude').value; if(lat && lng){ $wire.sendLocation(lat, lng); showLocationModal = false; } else { alert('Please select a location.'); }" class="px-4 py-2 bg-green-500 text-white rounded-lg hover:bg-green-600 transition-colors">Share Location</button>
                </div>
            </div>
        </div>

        {{-- Header --}}
        <div @click="isMinimized = !isMinimized" class="flex items-center justify-between p-3 bg-gradient-to-r from-orange-500 to-orange-600 rounded-t-xl cursor-pointer">
            <div class="flex items-center">
                <div class="relative flex-shrink-0">
                    <img src="{{ $recipient->profile_photo_path ? asset('storage/' . $recipient->profile_photo_path) : 'https://placehold.co/40x40' }}" 
                         class="h-8 w-8 md:h-9 md:w-9 rounded-full object-cover border-2 border-white/50">
                    <span class="absolute bottom-0 right-0 block h-2 w-2 md:h-2.5 md:w-2.5 rounded-full bg-green-400 border-2 border-orange-500"></span>
                </div>
                <div class="ml-3">
                    <p class="font-semibold text-sm">{{ $recipient->name }}</p>
                    <p class="text-xs text-orange-100">Online</p>
                </div>
            </div>
            <div class="flex items-center space-x-2">
                <button @click.stop="isMinimized = !isMinimized" class="p-1 rounded-full hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>
                <button wire:click="closeChat" @click.stop="" class="p-1 rounded-full hover:bg-white/20 transition-colors">
                    <svg class="w-4 h-4 md:w-5 md:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        {{-- Body & Input --}}
        <div x-show="!isMinimized" x-transition class="flex flex-col" style="height: 70vh; max-height: 28rem;">
            
            {{-- Messages Area --}}
            <div id="chat-box-container" wire:poll.5s="loadMessages" class="flex-1 min-h-0 overflow-y-auto bg-gray-50">
                <div class="p-3 md:p-4 space-y-3 md:space-y-4 min-h-full flex flex-col justify-end">
                    @if($chatMessages && count($chatMessages) > 0)
                        @foreach($chatMessages as $message)
                            @php $isOutgoing = $message->from_id == auth()->id(); @endphp
                            
                            @if($isOutgoing)
                            {{-- Outgoing Message --}}
                            <div class="flex justify-end">
                                <div class="{{ $message->make_offer ? 'bg-green-100 border border-green-200 text-green-800' : ($message->location ? 'bg-blue-100 border border-blue-200' : 'bg-orange-500 text-white') }} text-xs md:text-sm p-2 md:p-3 rounded-2xl rounded-br-lg max-w-[85%] md:max-w-[80%]">
                                    @if($message->location && $googleApiKey)
                                        <div class="text-center">
                                            <h3 class="font-bold text-xs mb-2">📍 Shared Location</h3>
                                            <a href="https://www.google.com/maps?q={{ $message->latitude }},{{ $message->longitude }}" target="_blank" class="block hover:opacity-90 transition-opacity">
                                                <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $message->latitude }},{{ $message->longitude }}&zoom=15&size=300x200&markers=color:red%7C{{ $message->latitude }},{{ $message->longitude }}&key={{ $googleApiKey }}" class="rounded-lg border border-gray-300">
                                            </a>
                                            <p class="text-xs text-gray-600 mt-1">Tap to open in Maps</p>
                                        </div>
                                    @elseif($message->make_offer)
                                        <div class="text-center">
                                            <h3 class="font-bold text-xs">Your Offer</h3>
                                            <p class="text-lg md:text-xl font-bold py-1">{{ $currencySymbol }}{{ number_format($message->msg) }}</p>
                                            @if($message->accept_offer == 1) 
                                                <span class="text-xs font-semibold text-white bg-green-500 px-2 py-0.5 rounded-full">Accepted</span>
                                            @elseif($message->denied_offer == 1) 
                                                <span class="text-xs font-semibold text-white bg-red-500 px-2 py-0.5 rounded-full">Rejected</span>
                                            @else
                                                <span class="text-xs text-green-600 font-semibold">Offer sent</span>
                                            @endif
                                        </div>
                                    @elseif($message->attachment) 
                                        <img 
                                            src="{{ asset('storage/' . $message->attachment) }}" 
                                            class="rounded-lg max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity"
                                            @click="showImageModal = true; currentImage = '{{ asset('storage/' . $message->attachment) }}'"
                                            alt="Sent image"
                                        >
                                    @else 
                                        {{ $message->msg }} 
                                    @endif
                                </div>
                            </div>
                            @else
                            {{-- Incoming Message --}}
                            <div class="flex justify-start">
                                <div class="{{ $message->location ? 'bg-blue-100 border border-blue-200' : 'bg-white border border-gray-200 text-gray-800' }} text-xs md:text-sm p-2 md:p-3 rounded-2xl rounded-bl-lg max-w-[85%] md:max-w-[80%]">
                                    @if($message->location && $googleApiKey)
                                        <div class="text-center">
                                            <h3 class="font-bold text-xs mb-2">📍 Shared Location</h3>
                                            <a href="https://www.google.com/maps?q={{ $message->latitude }},{{ $message->longitude }}" target="_blank" class="block hover:opacity-90 transition-opacity">
                                                <img src="https://maps.googleapis.com/maps/api/staticmap?center={{ $message->latitude }},{{ $message->longitude }}&zoom=15&size=300x200&markers=color:red%7C{{ $message->latitude }},{{ $message->longitude }}&key={{ $googleApiKey }}" class="rounded-lg border border-gray-300">
                                            </a>
                                            <p class="text-xs text-gray-600 mt-1">Tap to open in Maps</p>
                                        </div>
                                    @elseif($message->make_offer)
                                        <div class="text-center">
                                            <h3 class="font-bold text-xs">Offer Received</h3>
                                            <p class="text-lg md:text-xl font-bold py-1">{{ $currencySymbol }}{{ number_format($message->msg) }}</p>
                                            
                                            {{-- Accept/Reject Buttons --}}
                                            @if($message->accept_offer == 0 && $message->denied_offer == 0)
                                            <div class="flex space-x-2 justify-center mt-2 border-t border-gray-300 pt-2">
                                                <button wire:click="acceptOffer('{{ $message->id }}')" class="bg-green-500 text-white px-3 py-1 rounded-full text-xs hover:bg-green-600 transition-colors">
                                                    Accept
                                                </button>
                                                <button wire:click="rejectOffer('{{ $message->id }}')" class="bg-red-500 text-white px-3 py-1 rounded-full text-xs hover:bg-red-600 transition-colors">
                                                    Reject
                                                </button>
                                            </div>
                                            @elseif($message->accept_offer == 1)
                                                <p class="text-xs text-green-600 font-semibold mt-1">You accepted this offer.</p>
                                            @elseif($message->denied_offer == 1)
                                                <p class="text-xs text-red-600 font-semibold mt-1">You rejected this offer.</p>
                                            @endif
                                        </div>
                                    @elseif($message->attachment) 
                                        <img 
                                            src="{{ asset('storage/' . $message->attachment) }}" 
                                            class="rounded-lg max-w-full h-auto cursor-pointer hover:opacity-90 transition-opacity"
                                            @click="showImageModal = true; currentImage = '{{ asset('storage/' . $message->attachment) }}'"
                                            alt="Received image"
                                        >
                                    @else 
                                        {{ $message->msg }} 
                                    @endif
                                </div>
                            </div>
                            @endif
                        @endforeach
                    @else
                        {{-- Empty state --}}
                        <div class="flex items-center justify-center h-full">
                            <p class="text-center text-xs md:text-sm text-gray-400">No messages yet.</p>
                        </div>
                    @endif
                    
                    {{-- "Is Typing..." indicator --}}
                    <div x-show="false" class="flex justify-start">
                        <div class="bg-gray-200 text-gray-500 text-xs md:text-sm p-2 md:p-3 rounded-2xl rounded-bl-lg">Typing...</div>
                    </div>
                </div>
            </div>

            {{-- Input Area --}}
            <div class="p-3 border-t bg-white flex-shrink-0">
                <div class="flex border-b mb-3">
                    <button 
                        @click="activeTab = 'message'" 
                        :class="{ 
                            'text-orange-600 border-orange-600': activeTab === 'message', 
                            'text-gray-500 border-transparent': activeTab !== 'message' 
                        }"
                        class="flex-1 py-2 text-center border-b-2 font-medium text-sm transition-colors"
                    >
                        Message
                    </button>
                    <button 
                        @click="activeTab = 'offer'" 
                        :class="{ 
                            'text-green-600 border-green-600': activeTab === 'offer', 
                            'text-gray-500 border-transparent': activeTab !== 'offer' 
                        }"
                        class="flex-1 py-2 text-center border-b-2 font-medium text-sm transition-colors"
                    >
                        Make Offer
                    </button>
                </div>

                {{-- Message Tab --}}
                <div x-show="activeTab === 'message'" x-transition>
                    {{-- Quick Messages --}}
                    <div class="flex items-center gap-1 md:gap-2 mb-2 overflow-x-auto pb-2">
                        <button wire:click="sendQuickMessage('Hello')" class="text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-full flex-shrink-0 transition-colors border border-gray-200 whitespace-nowrap">
                            Hello
                        </button>
                        <button wire:click="sendQuickMessage('Is it available?')" class="text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-full flex-shrink-0 transition-colors border border-gray-200 whitespace-nowrap">
                            Available?
                        </button>
                        <button wire:click="sendQuickMessage('What is the best price?')" class="text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-full flex-shrink-0 transition-colors border border-gray-200 whitespace-nowrap">
                            Best price?
                        </button>
                        <button wire:click="sendQuickMessage('Thanks')" class="text-xs text-gray-700 bg-gray-100 hover:bg-gray-200 py-1.5 px-3 rounded-full flex-shrink-0 transition-colors border border-gray-200 whitespace-nowrap">
                            Thanks
                        </button>
                    </div>

                    <form wire:submit.prevent="sendMessage" class="flex items-center gap-2">
                        {{-- Image send button --}}
                        <label for="mini-chat-image-upload" class="p-2 text-gray-500 hover:text-orange-500 rounded-full cursor-pointer transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </label>
                        <input id="mini-chat-image-upload" type="file" wire:model="image" class="hidden">

                        {{-- Location button --}}
                        <button type="button" @click="showLocationModal = true" class="p-2 text-gray-500 hover:text-blue-500 rounded-full cursor-pointer transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                            </svg>
                        </button>

                        {{-- Uploading indicator --}}
                        <div x-show="$wire.isUploadingImage" class="text-sm text-gray-500 mx-1 flex-shrink-0 whitespace-nowrap">
                            Uploading...
                        </div>

                        <input wire:model.defer="newMessage" type="text" placeholder="Type a message..." 
                               class="flex-1 min-w-0 bg-gray-100 border-transparent rounded-full py-2 px-4 focus:outline-none focus:ring-2 focus:ring-orange-300 text-sm">
                        <button type="submit" class="p-2 flex items-center justify-center bg-orange-500 hover:bg-orange-600 rounded-full transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path>
                            </svg>
                        </button>
                    </form>
                </div>

                {{-- Offer Tab --}}
                <div x-show="activeTab === 'offer'" x-transition>
                    {{-- Suggested Offers --}}
                    @if(!empty($suggestedOffers))
                    <div class="mb-3">
                        <p class="text-xs text-gray-500 mb-2 font-medium">Quick Offers:</p>
                        <div class="flex items-center gap-2 overflow-x-auto pb-2">
                            @foreach($suggestedOffers as $offer)
                            <button 
                                wire:click="sendQuickOffer({{ $offer }})" 
                                class="text-sm text-green-700 bg-green-100 hover:bg-green-200 py-2 px-4 rounded-full flex-shrink-0 transition-colors border border-green-200 whitespace-nowrap"
                            >
                                {{ $currencySymbol }}{{ number_format($offer) }}
                            </button>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Custom Offer Form --}}
                    <form wire:submit.prevent="sendOffer" class="flex items-center gap-2">
                        <div class="flex-1 min-w-0 relative">
                            <span class="absolute left-3 top-1/4 -translate-y-1/2 text-gray-500 font-medium text-sm">{{ $currencySymbol }}</span>
                            <input 
                                wire:model.defer="offerAmount" 
                                type="number" 
                                placeholder="Enter amount..." 
                                class="w-full bg-gray-100 border-transparent rounded-full py-2 pl-8 pr-4 focus:outline-none focus:ring-2 focus:ring-green-300 text-sm"
                            >
                        </div>
                        <button 
                            type="submit" 
                            class="bg-green-500 text-white px-4 py-2 rounded-full font-semibold hover:bg-green-600 text-sm flex-shrink-0 transition-colors whitespace-nowrap"
                        >
                            Send Offer
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
    @endif
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
                return;
            }

            const mapEl = document.getElementById('shared_location_map');
            const latEl = document.getElementById('user_latitude');
            const lngEl = document.getElementById('user_longitude');
            if (!mapEl || !latEl || !lngEl) return;
            
            const initialLocation = { lat: 31.7165, lng: 73.7133 };
            
            latEl.value = initialLocation.lat;
            lngEl.value = initialLocation.lng;

            map = new google.maps.Map(mapEl, {
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
</div>