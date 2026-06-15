<div>
    {{-- Header Section --}}
    <div class="w-full float-left  border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">My Exchanges</h1>
        </div>
    </div>

    {{-- Tabs and Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button wire:click="switchTab('incoming')" class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'incoming' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-inbox"></i> Incoming Requests
                        </button>
                    </li>
                    <li class="mr-2">
                         <button wire:click="switchTab('outgoing')" class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'outgoing' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-paper-plane"></i> Outgoing Requests
                        </button>
                    </li>
                     <li class="mr-2">
                         <button wire:click="switchTab('successful')" class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'successful' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-check-circle"></i> Successful
                        </button>
                    </li>
                     <li class="mr-2">
                         <button wire:click="switchTab('failed')" class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'failed' ? 'text-red-600 border-red-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-times-circle"></i> Failed / Cancelled
                        </button>
                    </li>
                </ul>
            </div>
            
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            <div wire:loading.remove>
                @if($exchanges->count() > 0)
                    <div class="space-y-6">
                        @foreach($exchanges as $exchange)
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex justify-between items-center">
                                    <p class="text-xs text-gray-500">Initiated on: <span class="font-medium">{{ $exchange->created_at->format('d M Y') }}</span></p>
                                    <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                        {{ $exchange->status == 'pending' ? 'bg-yellow-100 text-yellow-800' : '' }}
                                        {{ $exchange->status == 'accepted' ? 'bg-blue-100 text-blue-800' : '' }}
                                        {{ $exchange->status == 'success' ? 'bg-green-100 text-green-800' : '' }}
                                        {{ in_array($exchange->status, ['cancelled', 'declined', 'failed']) ? 'bg-red-100 text-red-800' : '' }}
                                    ">{{ $exchange->status }}</span>
                                </div>
                                
                                <div class="p-6 grid grid-cols-1 md:grid-cols-11 gap-4 items-center">
                                    {{-- Your Item --}}
                                    <div class="md:col-span-5 flex items-center gap-4">
                                        <img src="{{ \App\Models\TblChat::getPostImgForList($exchange->post_id) }}" class="h-20 w-20 rounded-lg object-cover border flex-shrink-0">
                                        <div>
                                            <p class="text-xs text-gray-500">Your Item</p>
                                            <a href="{{ url($exchange->post->slug) }}" class="font-semibold text-gray-800 hover:text-green-600">{{ $exchange->post->title }}</a>
                                        </div>
                                    </div>
                                    
                                    <div class="text-center text-green-500 text-2xl md:col-span-1"><i class="fa fa-exchange"></i></div>
                                    
                                    {{-- Their Item --}}
                                    <div class="md:col-span-5 flex items-center gap-4">
                                        <img src="{{ \App\Models\TblChat::getPostImgForList($exchange->exchanged_post_id) }}" class="h-20 w-20 rounded-lg object-cover border flex-shrink-0">
                                        <div>
                                            <p class="text-xs text-gray-500">Their Item ({{ $exchange->requester->name }})</p>
                                            <a href="{{ url($exchange->exchangedPost->slug) }}" class="font-semibold text-gray-800 hover:text-green-600">{{ $exchange->exchangedPost->title }}</a>
                                        </div>
                                    </div>
                                </div>

                                {{-- Action Buttons --}}
                                @if($activeTab == 'incoming' && $exchange->status == 'pending')
                                <div class="p-4 bg-gray-50 border-t flex justify-end gap-3">
                                    <button wire:click="updateStatus('{{ $exchange->id }}', 'declined')" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Decline</button>
                                    <button wire:click="updateStatus('{{ $exchange->id }}', 'accepted')" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Accept</button>
                                </div>
                                @endif
                                 @if($activeTab == 'incoming' && $exchange->status == 'accepted')
                                <div class="p-4 bg-gray-50 border-t flex justify-end gap-3">
                                    <button wire:click="updateStatus('{{ $exchange->id }}', 'failed')" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Failed</button>
                                    <button wire:click="updateStatus('{{ $exchange->id }}', 'success')" class="px-4 py-2 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700">Mark as Successful</button>
                                </div>
                                @endif
                                 @if($activeTab == 'outgoing' && in_array($exchange->status, ['pending', 'accepted']))
                                 <div class="p-4 bg-gray-50 border-t flex justify-end gap-3">
                                     <button wire:click="updateStatus('{{ $exchange->id }}', 'cancelled')" class="px-4 py-2 bg-red-100 text-red-700 text-sm font-semibold rounded-lg hover:bg-red-200">Cancel Request</button>
                                 </div>
                                @endif
                                @if($activeTab == 'failed' && $exchange->post_owner_id == Auth::id())
                                <div class="p-4 bg-gray-50 border-t flex justify-end gap-3">
                                    <button wire:click="toggleBlock('{{ $exchange->id }}')" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-semibold rounded-lg hover:bg-gray-300">
                                        {{ $exchange->block_exchange ? 'Unblock Exchange' : 'Block Exchange' }}
                                    </button>
                                </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">{{ $exchanges->links() }}</div>
                @else
                    <div class="text-center py-16">
                        <p class="text-xl text-gray-600 font-semibold mt-4">No Requests Found</p>
                        <p class="text-gray-500">There are no exchanges in this category yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Toaster --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            // Toastr script
        });
    </script>
  <style>
  	
        
        
        html, body {
            height: 100%;
        }
        
        body {
            display: flex;
            flex-direction: column;
            background-color: #fffbfa;
            color: #0f172a;
            min-height: 100vh;
        }
        
        .page-wrapper {
            flex: 1;
            display: flex;
            flex-direction: column;
        }
        
        .main-content {
            flex: 1;
        }

        /* Footer */
        footer {
            border-top: 1px solid #e2e8f0;
            
            margin-top: auto;
            width: 100%;
        }
        
  </style>
</div>
