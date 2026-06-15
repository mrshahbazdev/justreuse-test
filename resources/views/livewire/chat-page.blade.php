<div>
    <div class="flex h-[calc(100vh-80px)] antialiased text-gray-800">
        <div class="flex flex-row h-full w-full overflow-x-hidden">

            <div class="w-full md:w-80 lg:w-96 flex-shrink-0 border-r border-gray-200 {{ $selectedChat ? 'hidden md:flex' : 'flex' }}">
                @livewire('chat-list', ['selectedChatId' => $selectedChat['id'] ?? null])
            </div>

            <div class="flex-auto h-full {{ $selectedChat ? 'flex' : 'hidden md:flex' }}">
                @if ($selectedChat)
                    @livewire('chat-window', ['chat' => $selectedChat], key($selectedChat['id']))
                @else
                    <div class="flex items-center justify-center h-full w-full bg-white text-gray-500">
                        <div class="text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 10h.01M12 10h.01M16 10h.01M9 16H5a2 2 0 01-2-2V6a2 2 0 012-2h14a2 2 0 012 2v8a2 2 0 01-2 2h-5l-5 5v-5z"></path></svg>
                            <h3 class="mt-2 text-sm font-medium">Select a chat</h3>
                            <p class="mt-1 text-sm">Start a conversation by selecting one from the list.</p>
                        </div>
                    </div>
                @endif
            </div>

        </div>
    </div>
</div>