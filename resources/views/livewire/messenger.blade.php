<div class="messenger-wrapper">
    <div class="messenger-container">

        {{-- Conversation List Panel --}}
        <div class="conversation-panel {{ $selectedConversation ? 'hidden md:flex' : 'flex' }}">
            @livewire('conversation-list', ['selectedConvId' => $selectedConversation['id'] ?? null])
        </div>

        {{-- Chat Panel --}}
        <div class="chat-panel">
            @if ($selectedConversation)
                @livewire('chat-box', ['conversationData' => $selectedConversation], key($selectedConversation['id']))
            @else
                <div class="flex items-center justify-center h-full w-full bg-gradient-to-br from-gray-50 to-orange-50/30">
                    <div class="text-center px-6">
                        <div class="bg-white p-6 rounded-full shadow-sm inline-block mb-5">
                            <svg class="w-16 h-16 text-orange-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-700 mb-2">Select a conversation</h3>
                        <p class="text-sm text-gray-400 max-w-sm">Choose a conversation from the list to start chatting with buyers or sellers.</p>
                    </div>
                </div>
            @endif
        </div>

    </div>

    <style>
        .messenger-wrapper {
            height: calc(100vh - 80px);
            overflow: hidden;
        }
        .messenger-container {
            display: flex;
            height: 100%;
            width: 100%;
            overflow: hidden;
            background: #fff;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.06);
            max-width: 1400px;
            margin: 0 auto;
        }
        .conversation-panel {
            width: 380px;
            min-width: 320px;
            flex-shrink: 0;
            border-right: 1px solid #f3f4f6;
            flex-direction: column;
            height: 100%;
            overflow: hidden;
        }
        .chat-panel {
            flex: 1;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;
        }
        @media (max-width: 768px) {
            .messenger-wrapper {
                height: calc(100vh - 60px);
            }
            .messenger-container {
                border-radius: 0;
            }
            .conversation-panel {
                width: 100%;
                min-width: 100%;
            }
        }
    </style>
</div>
