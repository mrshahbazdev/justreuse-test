<div wire:poll.5s class="flex flex-col w-full bg-white h-full">
    <div class="p-5 border-b">
         <h2 class="text-2xl font-bold">Chats</h2>
         <input wire:model.live.debounce.300ms="search" type="text" class="w-full mt-2 py-2.5 pl-4 pr-4 bg-gray-100 rounded-2xl focus:outline-none" placeholder="Search...">
    </div>

    <div class="flex-grow overflow-y-auto custom-scrollbar p-2">
        @forelse($chatlists as $chatlist)
            @php
                $senderId = ($chatlist->from_id == auth()->id()) ? $chatlist->to_id : $chatlist->from_id;
                $sender = \App\Models\User::find($senderId);
                $lastChat = \App\Models\TblChat::getLastChat($senderId, $chatlist->post_id);
                $unreadCount = \App\Models\TblChat::getUnreadCount(auth()->id(), $senderId, $chatlist->post_id);
            @endphp
            @if ($sender)
            <div wire:click="selectChat({{ $senderId }}, {{ $chatlist->post_id }})"
                 class="group flex items-center p-3 my-1 rounded-2xl cursor-pointer
                 {{ ($selectedChatId == $senderId . '-' . $chatlist->post_id) ? 'bg-orange-100' : 'hover:bg-gray-100' }}">
                
                <div class="relative">
                     <img src="{{ $sender->profile_photo_path ? asset('storage/' . $sender->profile_photo_path) : 'https://placehold.co/40x40/E2E8F0/4A5568?text=' . strtoupper(substr($sender->name, 0, 2)) }}" class="h-10 w-10 rounded-full object-cover">
                </div>
                <div class="ml-3 flex-grow text-sm">
                    <p class="font-semibold">{{ $sender->name }}</p>
                    <p class="text-xs text-gray-500 truncate">{{ $lastChat['msg'] ?? 'Attachment' }}</p>
                </div>
                <div class="flex flex-col items-end text-xs">
                    <span class="text-gray-500 mb-1.5">{{ \Carbon\Carbon::parse($lastChat['created_at'])->format('h:i a') }}</span>
                    @if($unreadCount > 0)
                        <span class="flex items-center justify-center bg-green-500 text-white rounded-full h-5 w-5 font-bold">{{ $unreadCount }}</span>
                    @endif
                </div>
            </div>
            @endif
        @empty
            <p class="text-center p-4 text-gray-500">No chats found.</p>
        @endforelse
    </div>
</div>