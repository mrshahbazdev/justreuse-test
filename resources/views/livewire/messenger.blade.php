<div class="flex h-screen antialiased text-gray-800">
    <div class="flex flex-row h-full w-full overflow-x-hidden">

        <div class="w-full md:w-1/3 flex-shrink-0 border-r border-gray-200 {{ $selectedConversation ? 'hidden md:flex' : 'flex' }}">
            @livewire('conversation-list', ['selectedConvId' => $selectedConversation['id'] ?? null])
        </div>

        <div class="flex-auto h-full ">
            @if ($selectedConversation)
                @livewire('chat-box', ['conversationData' => $selectedConversation], key($selectedConversation['id']))
            @else
                <div class="flex items-center justify-center h-full w-full bg-white text-gray-500">
                    <div class="text-center">
                        <h3 class="text-lg font-medium">Select a conversation</h3>
                        <p class="mt-1 text-sm">Start chatting by selecting a conversation from the list.</p>
                    </div>
                </div>
            @endif
        </div>

    </div>
</div>