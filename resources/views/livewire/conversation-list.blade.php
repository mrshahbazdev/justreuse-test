<div wire:poll.10s class="flex flex-col w-full bg-white h-full">
    {{-- Header --}}
    <div class="px-5 py-4 border-b border-gray-100 bg-gradient-to-r from-white to-gray-50">
        <h2 class="text-xl font-bold text-gray-800 flex items-center">
            <svg class="w-5 h-5 mr-2 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
            </svg>
            Messages
        </h2>
    </div>

    {{-- Conversations List --}}
    <div class="flex-grow overflow-y-auto">
        @forelse($conversations as $conv)
            @php
                $senderId = ($conv->from_id == auth()->id()) ? $conv->to_id : $conv->from_id;
                $sender = \App\Models\User::find($senderId);
                $lastChat = \App\Models\TblChat::where('post_id', $conv->post_id)
                    ->where(function($q) use($senderId) {
                        $q->where('from_id', auth()->id())->where('to_id', $senderId)
                          ->orWhere('from_id', $senderId)->where('to_id', auth()->id());
                    })
                    ->latest()->first();
                $unreadCount = \App\Models\TblChat::getUnreadCount(auth()->id(), $senderId, $conv->post_id);
                $convId = $senderId . '-' . $conv->post_id;
                $postTitle = $conv->post ? $conv->post->title : 'Unknown listing';
            @endphp
            @if ($sender && $lastChat)
                @php
                    $safeSenderId = $senderId ?? '';
                    $safePostId = $conv->post_id ?? '';
                    $isActive = ($selectedConvId == $convId);
                @endphp
                <div wire:click="$emit('conversationSelected', '{{ $safeSenderId }}', '{{ $safePostId }}')"
                     class="flex items-center px-4 py-3 cursor-pointer transition-all duration-200 border-b border-gray-50 
                            {{ $isActive ? 'bg-orange-50 border-l-4 border-l-orange-500' : 'hover:bg-gray-50 border-l-4 border-l-transparent' }}">
                    {{-- Avatar --}}
                    <div class="relative flex-shrink-0">
                        <img src="{{ $sender->profile_photo_path ? asset('storage/' . $sender->profile_photo_path) : 'https://ui-avatars.com/api/?name=' . urlencode($sender->name) . '&background=f97316&color=fff&size=44' }}" 
                             class="h-12 w-12 rounded-full object-cover ring-2 {{ $isActive ? 'ring-orange-300' : 'ring-gray-100' }}">
                        @if($unreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 flex items-center justify-center bg-orange-500 text-white rounded-full h-5 w-5 text-[10px] font-bold ring-2 ring-white">
                                {{ $unreadCount > 9 ? '9+' : $unreadCount }}
                            </span>
                        @endif
                    </div>

                    {{-- Content --}}
                    <div class="ml-3 flex-grow min-w-0">
                        <div class="flex items-center justify-between">
                            <p class="font-semibold text-sm text-gray-900 truncate">{{ $sender->name }}</p>
                            <span class="text-[11px] text-gray-400 flex-shrink-0 ml-2">{{ $lastChat->created_at->diffForHumans(null, true, true) }}</span>
                        </div>
                        <p class="text-xs text-gray-500 truncate mt-0.5">{{ Str::limit($postTitle, 30) }}</p>
                        <p class="text-xs {{ $unreadCount > 0 ? 'text-gray-700 font-medium' : 'text-gray-400' }} truncate mt-0.5">
                            @if($lastChat->from_id == auth()->id())
                                <span class="text-gray-400">You: </span>
                            @endif
                            {{ $lastChat->msg ? Str::limit($lastChat->msg, 35) : ($lastChat->attachment ? '📷 Image' : ($lastChat->location ? '📍 Location' : ($lastChat->make_offer ? '💰 Offer' : ''))) }}
                        </p>
                    </div>
                </div>
            @endif
        @empty
            <div class="flex flex-col items-center justify-center h-full py-16 px-6">
                <div class="bg-orange-50 p-5 rounded-full mb-4">
                    <svg class="w-10 h-10 text-orange-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-2.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                    </svg>
                </div>
                <h3 class="text-base font-semibold text-gray-700 mb-1">No messages yet</h3>
                <p class="text-sm text-gray-400 text-center">Your conversations will appear here when you start chatting with sellers or buyers.</p>
            </div>
        @endforelse
    </div>
</div>
