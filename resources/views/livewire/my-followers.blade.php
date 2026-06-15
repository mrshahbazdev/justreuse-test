<div>
    {{-- Header Section --}}
    <div class="w-full float-left  border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">My Followers</h1>
        </div>
    </div>

    {{-- Tabs and Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            {{-- Tabs --}}
            <div class="border-b border-gray-200 mb-6">
                <ul class="flex flex-wrap -mb-px">
                    <li class="mr-2">
                        <button wire:click="switchTab('following')"
                           class="inline-flex items-center gap-2 py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'following' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-user-check"></i>
                            <span>Following ({{ $followings->total() }})</span>
                        </button>
                    </li>
                    <li class="mr-2">
                         <button wire:click="switchTab('followers')"
                           class="inline-block py-4 px-4 text-sm font-medium text-center rounded-t-lg border-b-2 {{ $activeTab == 'followers' ? 'text-green-600 border-green-600' : 'text-gray-500 border-transparent hover:text-gray-600 hover:border-gray-300' }}">
                            <i class="fa fa-users"></i>
                            <span>Followers ({{ $followers->total() }})</span>
                        </button>
                    </li>
                </ul>
            </div>

            {{-- Search Bar --}}
            <div class="mb-6 relative">
                 <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                    <i class="fa fa-search text-gray-400"></i>
                </div>
                <input wire:model.debounce.300ms="search" type="text" placeholder="Search by name or email..." class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
            </div>

            {{-- Main loading indicator --}}
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            {{-- Content Area --}}
            <div wire:loading.remove>
                {{-- Following List --}}
                @if($activeTab == 'following')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse($followings as $user)
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 text-center p-6 flex flex-col items-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <a href="{{ url('seller-profile/' . $user->id) }}">
                                    <img src="{{ $user->profile_photo_url }}" class="rounded-full h-24 w-24 object-cover mx-auto border-4 border-white shadow-md" alt="{{ $user->name }}" />
                                </a>
                                <div class="flex-grow mt-4">
                                    <a href="{{ url('seller-profile/' . $user->id) }}">
                                        <strong class="capitalize text-slate-800 font-semibold text-lg hover:text-green-600">{{ $user->name }}</strong>
                                    </a>
                                    <p class="text-slate-500 text-sm mt-1">{{ $user->email }}</p>
                                </div>
                                <div class="w-full mt-6">
                                    <button wire:click="unfollow('{{ $user->id }}')" wire:loading.attr="disabled"
                                        class="w-full inline-flex items-center justify-center gap-2 rounded-lg outline-none focus:outline-none ease-linear transition-all duration-150 bg-red-50 border-2 border-red-200 text-sm text-red-600 hover:bg-red-100 hover:border-red-300 px-6 py-2.5 font-semibold">
                                         <i class="fa fa-user-minus"></i>
                                         <span wire:loading.remove wire:target="unfollow('{{ $user->id }}')">Unfollow</span>
                                         <span wire:loading wire:target="unfollow('{{ $user->id }}')">...</span>
                                    </button>
                                </div>
                            </div>
                        @empty
                             <div class="text-center py-16 sm:col-span-2 lg:col-span-3 xl:col-span-4">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                  <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-xl text-gray-600 font-semibold mt-4">You are not following anyone yet.</p>
                                <p class="text-gray-500">When you follow someone, they will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                     <div class="mt-8">
                        {{ $followings->links() }}
                    </div>
                @endif

                {{-- Followers List --}}
                @if($activeTab == 'followers')
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @forelse($followers as $user)
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 text-center p-6 flex flex-col items-center hover:shadow-lg hover:-translate-y-1 transition-all duration-300">
                                <a href="{{ url('seller-profile/' . $user->id) }}">
                                    <img src="{{ $user->profile_photo_url }}" class="rounded-full h-24 w-24 object-cover mx-auto border-4 border-white shadow-md" alt="{{ $user->name }}" />
                                </a>
                                <div class="flex-grow mt-4">
                                    <a href="{{ url('seller-profile/' . $user->id) }}">
                                        <strong class="capitalize text-slate-800 font-semibold text-lg hover:text-green-600">{{ $user->name }}</strong>
                                    </a>
                                    <p class="text-slate-500 text-sm mt-1">{{ $user->email }}</p>
                                </div>
                                <div class="w-full mt-6">
                                    <a href="{{ url('seller-profile/' . $user->id) }}" class="w-full inline-flex items-center justify-center gap-2 rounded-lg outline-none focus:outline-none ease-linear transition-all duration-150 bg-green-500 border-2 border-green-500 text-sm text-white hover:bg-green-600 px-6 py-2.5 font-semibold">
                                        <i class="fa fa-eye"></i> View Profile
                                    </a>
                                </div>
                            </div>
                        @empty
                             <div class="text-center py-16 sm:col-span-2 lg:col-span-3 xl:col-span-4">
                                <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                                  <path vector-effect="non-scaling-stroke" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                </svg>
                                <p class="text-xl text-gray-600 font-semibold mt-4">You have no followers yet.</p>
                                <p class="text-gray-500">When someone follows you, they will appear here.</p>
                            </div>
                        @endforelse
                    </div>
                    <div class="mt-8">
                        {{ $followers->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
    
     {{-- Toaster for notifications --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg"
         style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;">
        <p id="toast-message"></p>
    </div>

    <script>
        document.addEventListener('livewire:load', function () {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            let toastTimeout;

            window.addEventListener('show-toast', event => {
                if (toastTimeout) {
                    clearTimeout(toastTimeout);
                }
                toastMessage.innerText = event.detail.message;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);
                toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';
                    setTimeout(() => {
                        toast.style.display = 'none';
                    }, 300);
                }, 3000);
            });
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

