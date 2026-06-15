<div wire:poll.2s="loadMessages" class="flex flex-col flex-auto flex-shrink-0 rounded-2xl bg-white h-full shadow-lg">

    <div class="flex items-center justify-between p-4 border-b">
        @if ($recipient && $post)
        <div class="flex items-center">
             <img src="{{ $recipient->profile_photo_path ? asset('storage/app/public/' . $recipient->profile_photo_path) : 'https://placehold.co/40x40' }}" class="h-10 w-10 rounded-full object-cover">
            <div class="ml-3">
                <p class="font-semibold">{{ $recipient->name }}</p>
                <p class="text-sm text-gray-500 truncate">RE: {{ $post->title }}</p>
            </div>
        </div>
        @endif
    </div>

    <div id="chat-container-{{ $chat['id'] }}" class="flex-grow p-4 overflow-y-auto custom-scrollbar">
        @foreach($messages as $message)
            @php $isOutgoing = $message->from_id == auth()->id(); @endphp
            
            <div class="flex mb-4 {{ $isOutgoing ? 'justify-end' : 'justify-start' }}">
                <div class="{{ $isOutgoing ? 'bg-orange-500 text-white' : 'bg-gray-100' }} py-2 px-4 shadow rounded-2xl max-w-xs md:max-w-md">
                    @if($message->msg && !$message->make_offer && !$message->location)
                        <p>{{ $message->msg }}</p>
                    @elseif($message->attachment)
                        <img src="{{ asset('storage/' . $message->attachment) }}" class="rounded-lg max-w-full h-auto">
                    @elseif($message->location)
                        <a href="https://www.google.com/maps?q={{ $message->latitude }},{{ $message->longitude }}" target="_blank" class="flex items-center underline">
                            <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                            View Location
                        </a>
                    @elseif($message->make_offer)
                        <div class="p-2">
                            <h3 class="font-bold text-md">{{ $isOutgoing ? 'Your Offer' : 'Offer Received' }}</h3>
                            <p class="text-3xl font-bold text-center py-2">€{{ number_format($message->msg) }}</p>
                        </div>
                    @endif
                    <div class="text-xs {{ $isOutgoing ? 'text-gray-200' : 'text-gray-500' }} mt-1 text-right">
                        {{ \Carbon\Carbon::parse($message->created_at)->format('h:i a') }}
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <div class="p-4 border-t">
        <form wire:submit.prevent="sendMessage" class="flex items-center h-16 rounded-2xl bg-gray-100 px-4">
            <label for="image-upload-{{$chat['id']}}" class="cursor-pointer p-2 rounded-full hover:bg-gray-200" title="Upload Image"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"></rect><circle cx="8.5" cy="8.5" r="1.5"></circle><polyline points="21 15 16 10 5 21"></polyline></svg></label>
            <input id="image-upload-{{$chat['id']}}" type="file" wire:model="image" class="hidden">
            
            <button type="button" onclick="shareLocation()" class="p-2 rounded-full hover:bg-gray-200" title="Share Location"><svg xmlns="http://www.w3.org/2000/svg" width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg></button>
            
            <div wire:loading wire:target="image" class="ml-2 text-sm text-gray-500">Uploading...</div>

            <div class="flex-grow ml-2">
                <input wire:model.defer="newMessage" type="text" class="w-full bg-transparent focus:outline-none" placeholder="Type a message..."/>
            </div>
            <button type="submit" class="flex items-center justify-center bg-orange-500 hover:bg-orange-600 text-white px-4 py-2.5 rounded-xl ml-3">Send</button>
        </form>

        <form wire:submit.prevent="sendOffer" class="flex items-center mt-2">
            <input wire:model.defer="offerAmount" type="number" class="w-full bg-gray-100 rounded-lg p-2 focus:outline-none" placeholder="Make an offer..."/>
            <button type="submit" class="bg-green-500 text-white px-4 py-2 rounded-lg ml-2">Send Offer</button>
        </form>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            // Unique ID ke saath container ko target karein
            const container = document.getElementById('chat-container-{{ $chat['id'] }}');
            if(container) {
                container.scrollTop = container.scrollHeight;
            }

            window.addEventListener('scroll-to-bottom', event => {
                if(container) {
                    container.scrollTop = container.scrollHeight;
                }
            });
        });

        function shareLocation() {
            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(position => {
                    @this.call('sendLocation', position.coords.latitude, position.coords.longitude);
                }, error => { alert('Could not get your location.'); });
            } else { alert("Geolocation is not supported by this browser."); }
        }
    </script>
</div>