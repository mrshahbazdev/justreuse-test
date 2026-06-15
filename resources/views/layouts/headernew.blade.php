@php
// --- All your existing PHP logic is here ---
if (!empty(request()->loc)) { 
    $lc = request()->loc; 
} else if (Session::has("GetAddress")) { 
    $lc = Session::get("GetAddress"); 
} else { 
    $lc = Session::get("GetCountry"); 
}

if (!empty(request()->loc)) { 
    $ci = !empty(request()->city) ? request()->city : Session::get("GetCity"); 
} else if (Session::has("GetCity")) { 
    $ci = Session::get("GetCity"); 
} else { 
    $ci = ""; 
}

if (!empty(request()->loc)) { 
    $locality = !empty(request()->locality) ? request()->locality : Session::get("GetLocality"); 
} else if (Session::has("GetLocality")) { 
    $locality = Session::get("GetLocality"); 
} else { 
    $locality = ""; 
}

if (!empty(request()->loc)) { 
    $lat = !empty(request()->lat) ? request()->lat : Session::get("Getlat"); 
} else if (Session::has("Getlat")) { 
    $lat = Session::get("Getlat"); 
} else { 
    $lat = ""; 
}

if (!empty(request()->loc)) { 
    $lng = !empty(request()->lng) ? request()->lng : Session::get("Getlng"); 
} else if (Session::has("Getlng")) { 
    $lng = Session::get("Getlng"); 
} else { 
    $lng = ""; 
}

if (!empty(request()->loc)) { 
    $st = !empty(request()->state) ? request()->state : Session::get("GetState"); 
} else if (Session::has("GetState")) { 
    $st = Session::get("GetState"); 
} else { 
    $st = ""; 
}

if (!empty(request()->loc)) { 
    $cut = !empty(request()->country) ? request()->country : Session::get("GetCountry"); 
} else if (Session::has("GetCountry")) { 
    $cut = Session::get("GetCountry"); 
} else { 
    $cut = ""; 
}

if (Session::has("GetSearched")) { 
    $cconty = Session::get("GetSearched"); 
} else if (!empty(request()->country)) { 
    $cconty = str_replace(' ', '_', strtolower(request()->country)); 
} else { 
    $cconty = str_replace(' ', '_', strtolower(Session::get("GetCountry"))); 
}

$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
$post_methods = App\Models\TblPostMethod::get_active_post_methods();
$currentURL = Request::getRequestUri();
$chat_seg = request()->segment(1);
$chat_url = App\Models\Setting::check_active_chat_system($currentURL, $chat_seg);
@endphp

@if(auth()->user())
    @php
        /* Notifications count and list */
        $notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();

        // chat count
        $userid = auth()->user()->id;

        $chatlists = [];
        $total_unread_count = 0;
        $dropdownConversations = [];
        $chat_url = route('messages'); // Default chat URL

        if ($userid) {
            // Step 1: Model se hamesha unique conversations ki list lein
            $chatlists = App\Models\TblChat::getConversations($userid);

            // Step 2: Is saaf list ko dropdown ke liye process karein
            $unreadConversations = [];
            $recentConversations = [];

            foreach ($chatlists as $conversation) {
                $senderId = ($conversation->from_id == $userid) ? $conversation->to_id : $conversation->from_id;

                // Unread count ko yahan calculate karein
                $unread_count = App\Models\TblChat::getUnreadCount($userid, $senderId, $conversation->post_id);
                $total_unread_count += $unread_count;

                $senderUser = App\Models\User::find($senderId);
                if (!$senderUser) continue;

                $conversationData = [
                    'sender' => $senderUser,
                    'last_message' => $conversation,
                    'url' => route('messages', ['to' => $senderId, 'p' => $conversation->post_id])
                ];

                if ($unread_count > 0) {
                    $unreadConversations[] = $conversationData;
                }
                $recentConversations[] = $conversationData;
            }

            // Agar unread messages hain to woh show hon, warna recent 6 show hon
            $dropdownConversations = !empty($unreadConversations) ? $unreadConversations : array_slice($recentConversations, 0, 6);
        }

        $userImg = auth()->user()->profile_photo_path;
        if (!empty($userImg)) {
            $userImgUrl = URL::asset('storage/' . $userImg);
        } else {
            $userImgUrl = URL::asset('storage/profile-avatar.jpg');
        }
    @endphp
@endif

<div x-data="{ open: false }">
    <header id="siteHeader" class="header sticky top-0 z-30 bg-white/90 backdrop-blur-lg border-b border-slate-200">
        <nav class="mx-auto flex max-w-7xl items-center justify-between px-4 sm:px-6 lg:px-8 h-20">
            
            {{-- Left Side: Logo --}}
            <div class="flex items-center flex-shrink-0">
                <a href="{{ URL::to('/') }}">
                    <img src="{{URL::to('storage/'.$settings['logo'])}}" class="h-10 w-auto" alt="Logo">
                </a>
            </div>

            {{-- Center: Search Bar (Hidden on Mobile) --}}
            <div class="hidden md:flex flex-1 justify-center px-4 lg:px-8">
                <div class="relative w-full max-w-lg">
                    <div x-data="searchComponent()" class="relative w-full">
                        
                        <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                            <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                        </div>
                        
                        <input 
                            style="border 1px solid"
                            type="text"
                            placeholder="Search products..."
                            x-model="searchTerm"
                            @input.debounce.300ms="fetchSuggestions"
                            @keydown.enter.prevent="performSearch()"
                            @focus="if (suggestions.length > 0) showSuggestions = true"
                            @click.outside="showSuggestions = false"
                            class="block w-full rounded-full border-0 bg-slate-100 py-2.5 pl-11 pr-4 text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 sm:text-sm sm:leading-6">
                        
                        {{-- Search Suggestions Dropdown --}}
                        <div x-show="showSuggestions && suggestions.length > 0" 
                             x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 transform -translate-y-2"
                             x-transition:enter-end="opacity-100 transform translate-y-0"
                             class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-slate-200 max-h-60 overflow-y-auto">
                            <template x-for="suggestion in suggestions" :key="suggestion.id">
                                <button 
                                    type="button"
                                    @click="selectSuggestion(suggestion)"
                                    class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 text-left border-b border-slate-100 last:border-b-0 transition-colors duration-150">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                                    <span x-text="suggestion.value" class="text-slate-700"></span>
                                </button>
                            </template>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Side: User Actions (Hidden on Mobile) --}}
            <div class="hidden md:flex flex-shrink-0 items-center justify-end gap-x-2">
                @if(auth()->user())
                    <a href="{{ route('post-add') }}" style="background:#f39c12;" class="post-ad-btn inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-full font-bold hover:bg-primary-dark transition-all">
                        <i class="fa-solid fa-circle-plus"></i> 
                        <span>Post Ad</span>
                    </a>
                    
                    {{-- Notifications --}}
                    <a href="{{ route('notifications') }}" class="w-10 h-10 inline-flex items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 hover:text-primary relative" title="Notifications">
                        <i class="fa-regular fa-bell text-xl"></i>
                        @if(isset($notifications) && count($notifications) > 0)
                            <span class="absolute top-2 right-2 flex h-2 w-2">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-red-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-2 w-2 bg-red-500"></span>
                            </span>
                        @endif
                    </a>
                    
                    {{-- Messages with dropdown --}}
                    <div x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" class="w-10 h-10 inline-flex items-center justify-center rounded-full text-slate-600 hover:bg-slate-100 hover:text-primary relative" title="Messages">
                            <i class="fa-regular fa-envelope text-xl"></i>
                            @if($total_unread_count > 0)
                            <span class="absolute top-1 right-1 flex h-3 w-3" style="position:absolute; top:4px; right:4px; display:flex; align-items:center; justify-content:center; height:18px; min-width:18px;">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-orange-400 opacity-75" style="position:absolute; inset:0; border-radius:50%; background:rgba(255,87,34,0.6); animation:ping 1.2s cubic-bezier(0,0,0.2,1) infinite;"></span>
                                <span class="relative inline-flex items-center justify-center rounded-full h-3 w-3 bg-orange-500 text-white text-[8px]" 
                                      style="position:relative; display:inline-flex; align-items:center; justify-content:center; background:#ff5722; color:#fff; font-size:10px; font-weight:600; border-radius:50%; height:18px; min-width:18px; padding:0 4px; box-shadow:0 0 3px rgba(0,0,0,0.3);">
                                    {{ $total_unread_count }}
                                </span>
                            </span>
                            @endif
                        </button>

                        <div style="display:none;" x-show="isOpen" @click.away="isOpen = false" x-cloak
                             x-transition
                             class="absolute right-0 mt-2 w-80 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-20">
                            <div class="py-2">
                                <div class="px-4 py-2 border-b">
                                    <p class="text-sm text-slate-800 font-semibold">Messages</p>
                                </div>
                                <div class="max-h-80 overflow-y-auto">
                                    @forelse($dropdownConversations as $convo)
                                        @php
                                            $senderId = $convo['sender']->id;
                                            $postId = $convo['last_message']['post_id'];
                                        @endphp

                                        <button 
                                            @click.prevent="Livewire.emit('startChat', '{{ $senderId }}', '{{ $postId }}'); isOpen = false;"
                                            class="w-full text-left flex items-center px-4 py-3 text-sm text-slate-700 hover:bg-slate-100">

                                            <img class="h-10 w-10 rounded-full object-cover mr-3" src="{{ $convo['sender']->profile_photo_path ? asset('storage/app/public/' . $convo['sender']->profile_photo_path) : 'https://placehold.co/40x40' }}" alt="Avatar">
                                            <div class="flex-grow">
                                                <p class="font-semibold truncate">{{ $convo['sender']->name }}</p>
                                                <p class="text-xs text-slate-500 truncate">{{ $convo['last_message']['msg'] ?? 'Attachment' }}</p>
                                            </div>
                                        </button>
                                    @empty
                                    <p class="px-4 py-3 text-sm text-center text-slate-500">No recent conversations.</p>
                                    @endforelse
                                </div>
                                <div class="px-4 py-2 border-t text-center">
                                    <a href="{{ $chat_url }}" class="text-sm font-medium text-orange-600 hover:underline">View All in Inbox</a>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    {{-- Language Selector --}}
                    <div x-data="{ isOpen: false }" class="relative" >
                        <button @click="isOpen = !isOpen" class="inline-flex items-center gap-2 px-3 py-2 rounded-lg hover:bg-slate-100 text-slate-700 font-semibold">
                            <i class="fa-solid fa-globe"></i>
                            <span>{{ strtoupper(config('app.locale')) }}</span>
                            <i class="fa-solid fa-chevron-down text-xs transition-transform" :class="{'rotate-180': isOpen}"></i>
                        </button>
                        <div style="display:none; overflow-y: scroll; height: 200px;" x-show="isOpen" @click.away="isOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-32 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-20">
                            <div class="py-1">
                                @foreach($select_language as $row)
                                    <?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
                                    <a href="{{ $lang_url }}" class="block px-4 py-2 text-sm text-slate-700 hover:bg-slate-100">{{$row->native}}</a>
                                @endforeach
                            </div>
                        </div>
                    </div>
                    
                    {{-- User Profile Dropdown --}}
                    <div x-data="{ isOpen: false }" class="relative">
                        <button @click="isOpen = !isOpen" class="flex items-center gap-2 rounded-full focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary">
                            <img class="h-10 w-10 rounded-full object-cover" src="{{$userImgUrl}}" alt="User Avatar">
                        </button>
                        <div style="display:none;" x-show="isOpen" @click.away="isOpen = false" x-cloak
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="opacity-0 scale-95"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-64 origin-top-right rounded-md bg-white shadow-lg ring-1 ring-black ring-opacity-5 focus:outline-none z-20">
                            <div class="py-1 max-h-96 overflow-y-auto">
                                <div class="px-4 py-3 border-b">
                                    <p class="text-sm text-slate-800 font-semibold truncate">{{ auth()->user()->name }}</p>
                                    <p class="text-xs text-slate-500 truncate">{{ auth()->user()->email }}</p>
                                </div>
                                <a href="{{ URL::to('post') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                    <i class="fa-regular fa-address-book fa-fw mr-2"></i>{{__('messages.my ads')}}
                                </a>
                                @foreach($post_methods as $post_method)
                                    @if($post_method->name == "exchange")
                                        <a href="{{ URL::to('/my-exchange/incoming') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                            <i class="fa-solid fa-arrow-right-arrow-left fa-fw mr-2"></i>{{__('messages.my exchanges')}}
                                        </a>
                                    @endif
                                    @if($post_method->name == "bannerads")
                                        <a href="{{ URL::to('/my-banner-ads') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                            <i class="fa-solid fa-rectangle-ad fa-fw mr-2"></i>{{__('messages.my banner ads')}}
                                        </a>
                                    @endif
                                    @if($post_method->name == "buynow")
                                        <a href="{{ URL::to('/my-buynow/orders') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                            <i class="fa-regular fa-calendar-check fa-fw mr-2"></i>{{__('messages.my orders & sales')}}
                                        </a>
                                    @endif
                                @endforeach
                                
                                <a href="{{ URL::to('favourite') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                    <i class="fa-regular fa-heart fa-fw mr-2"></i>{{__('messages.favourite ads')}}
                                </a>
                                <a href="{{ URL::to('/my-profile') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                    <i class="fa-regular fa-user fa-fw mr-2"></i>{{__('messages.my profile')}}
                                </a>
                                <a href="{{ URL::to('/my-followers') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                    <i class="fa-solid fa-users fa-fw mr-2"></i>{{__('messages.my followers & followings')}}
                                </a>
                                <a href="{{ URL::to('/selectPackageMultiple') }}" class="flex items-center px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary" >
                                    <i class="fa-solid fa-box-archive fa-fw mr-2"></i>{{__('messages.buy business packs')}}
                                </a>
                                <a href="{{ $chat_url }}" class="flex items-center justify-between px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary " >
                                    <span><i class="fa-regular fa-comment fa-fw mr-2"></i>{{__('messages.My Chat')}}</span> 
                                    <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ $total_unread_count }}</span>
                                </a>
                                <a href="{{ route('notifications') }}" class="flex items-center justify-between px-4 py-2 text-sm text-slate-700 hover:bg-slate-100 hover:text-primary">
                                    <span><i class="fa-regular fa-bell fa-fw mr-2"></i>Notifications</span> 
                                    <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5">{{ count($notifications) }}</span>
                                </a>
                                <div class="border-t my-1"></div>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <a href="{{ route('logout') }}" 
                                       onclick="event.preventDefault(); this.closest('form').submit();"
                                       class="flex items-center px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700">
                                        <i class="fa-solid fa-arrow-right-from-bracket fa-fw mr-2"></i>Logout
                                    </a>
                                </form>
                            </div>
                        </div>
                    </div>
                @else
                    <a href="{{ route('login') }}" 
                       class="post-ad-btn hidden sm:inline-flex items-center gap-2 bg-primary text-white px-5 py-2.5 rounded-full font-bold hover:bg-primary-dark transition-all">
                        <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                    </a>
                @endif
            </div>

            {{-- Mobile Menu Button --}}
            <div class="flex items-center md:hidden">
                <button @click="open = true" class="inline-flex h-10 w-10 items-center justify-center rounded-lg hover:bg-slate-100">
                    <i class="fa-solid fa-bars text-xl text-slate-800"></i>
                </button>
            </div>
        </nav>
    </header>

    {{-- Mobile Menu --}}
    <div x-show="open" x-cloak>
        <div x-show="open" x-transition:enter="ease-in-out duration-500" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in-out duration-500" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" @click="open = false" class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity z-40"></div>
        <div class="fixed inset-y-0 right-0 flex max-w-full pl-10 z-50">
            <div x-show="open" x-transition:enter="transform transition ease-in-out duration-500 sm:duration-700" x-transition:enter-start="translate-x-full" x-transition:enter-end="translate-x-0" x-transition:leave="transform transition ease-in-out duration-500 sm:duration-700" x-transition:leave-start="translate-x-0" x-transition:leave-end="translate-x-full" class="relative w-screen max-w-md">
                <div class="flex h-full flex-col overflow-y-scroll bg-white shadow-xl">
                    <div class="px-4 sm:px-6 py-4 border-b">
                        <div class="flex items-center justify-between">
                            <h2 class="text-lg font-semibold text-slate-900">Menu</h2>
                            <button @click="open = false" class="rounded-md text-gray-400 hover:text-gray-500">
                                <i class="fa-solid fa-xmark text-xl"></i>
                            </button>
                        </div>
                    </div>
                    <div class="relative flex-1">

                        {{-- Mobile Search Bar --}}
                        <div class="p-4 border-b">
                            <div x-data="searchComponent()" class="relative w-full">
                                <div class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4">
                                    <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                </div>
                                <input 
                                    type="text"
                                    placeholder="Search products..."
                                    x-model="searchTerm"
                                    @input.debounce.300ms="fetchSuggestions"
                                    @keydown.enter.prevent="performSearch()"
                                    @focus="if (suggestions.length > 0) showSuggestions = true"
                                    @click.outside="showSuggestions = false"
                                    class="block w-full rounded-full border-0 bg-slate-100 py-2.5 pl-11 pr-4 text-slate-900 ring-1 ring-inset ring-slate-200 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-orange-500 sm:text-sm sm:leading-6">
                                
                                {{-- Suggestions for mobile search bar --}}
                                <div x-show="showSuggestions && suggestions.length > 0" 
                                    x-cloak
                                    x-transition:enter="transition ease-out duration-200"
                                    x-transition:enter-start="opacity-0 transform -translate-y-2"
                                    x-transition:enter-end="opacity-100 transform translate-y-0"
                                    class="absolute z-50 mt-1 w-full bg-white rounded-lg shadow-lg border border-slate-200 max-h-60 overflow-y-auto">
                                    <template x-for="suggestion in suggestions" :key="suggestion.id">
                                        <button 
                                            type="button"
                                            @click="selectSuggestion(suggestion)"
                                            class="w-full flex items-center gap-3 px-4 py-3 hover:bg-slate-50 text-left border-b border-slate-100 last:border-b-0 transition-colors duration-150">
                                            <i class="fa-solid fa-magnifying-glass text-slate-400 text-sm"></i>
                                            <span x-text="suggestion.value" class="text-slate-700"></span>
                                        </button>
                                    </template>
                                </div>
                            </div>
                        </div>
                        {{-- End Mobile Search Bar --}}

                        @if(!auth()->user())
                            <div class="p-6">
                                <a href="{{ route('login') }}" class="w-full inline-flex items-center justify-center gap-2 bg-primary text-white px-5 py-2.5 rounded-full font-bold hover:bg-primary-dark transition-all">
                                    <i class="fa-solid fa-arrow-right-to-bracket"></i> Login
                                </a>
                            </div>
                        @else
                            <div class="p-6">
                                <div class="flex items-center gap-3 pb-4 border-b mb-4">
                                    <img class="h-14 w-14 rounded-full object-cover" src="{{$userImgUrl}}" alt="User Avatar">
                                    <div>
                                        <p class="text-base text-slate-800 font-bold">{{ auth()->user()->name }}</p>
                                        <a href="{{ URL::to('/my-profile') }}" class="text-sm text-slate-500 hover:text-primary">View Profile</a>
                                    </div>
                                </div>
                                
                                {{-- Mobile Notifications and Messages --}}
                                <div class="flex justify-between mb-4">
                                    <a href="{{ route('notifications') }}" class="flex-1 text-center px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-50 hover:text-primary">
                                        <i class="fa-regular fa-bell fa-fw mr-1"></i> Notifications
                                        @if(isset($notifications) && count($notifications) > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 ml-1">{{ count($notifications) }}</span>
                                        @endif
                                    </a>
                                    <a href="{{ $chat_url }}" class="flex-1 text-center px-3 py-2 rounded-md font-medium text-slate-700 hover:bg-slate-50 hover:text-primary">
                                        <i class="fa-regular fa-envelope fa-fw mr-1"></i> Messages
                                        @if($total_unread_count > 0)
                                            <span class="bg-red-500 text-white text-xs rounded-full px-1.5 py-0.5 ml-1">{{ $total_unread_count }}</span>
                                        @endif
                                    </a>
                                </div>
                                
                                <div class="space-y-1">
                                    <a href="{{ URL::to('post') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                        <i class="fa-regular fa-address-book fa-fw mr-3"></i>{{__('messages.my ads')}}
                                    </a>
                                    @foreach($post_methods as $post_method)
                                        @if($post_method->name == "exchange")
                                            <a href="{{ URL::to('/my-exchange/incoming') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                                <i class="fa-solid fa-arrow-right-arrow-left fa-fw mr-3"></i>{{__('messages.my exchanges')}}
                                            </a>
                                        @endif
                                        @if($post_method->name == "bannerads")
                                            <a href="{{ URL::to('/my-banner-ads') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                                <i class="fa-solid fa-rectangle-ad fa-fw mr-3"></i>{{__('messages.my banner ads')}}
                                            </a>
                                        @endif
                                        @if($post_method->name == "buynow")
                                            <a href="{{ URL::to('/my-buynow/orders') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                                <i class="fa-regular fa-calendar-check fa-fw mr-3"></i>{{__('messages.my orders & sales')}}
                                            </a>
                                        @endif
                                    @endforeach
                                    <a href="{{ URL::to('favourite') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                        <i class="fa-regular fa-heart fa-fw mr-3"></i>{{__('messages.favourite ads')}}
                                    </a>
                                    <a href="{{ URL::to('/my-followers') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                        <i class="fa-solid fa-users fa-fw mr-3"></i>{{__('messages.my followers & followings')}}
                                    </a>
                                    <a href="{{ URL::to('/selectPackageMultiple') }}" class="flex items-center px-3 py-2 text-base text-slate-700 hover:bg-slate-100 hover:text-primary rounded-md">
                                        <i class="fa-solid fa-box-archive fa-fw mr-3"></i>{{__('messages.buy business packs')}}
                                    </a>
                                    
                                    {{-- Language Selector for Mobile --}}
                                    <div class="px-3 py-2">
                                        <p class="text-sm font-medium text-slate-500 mb-2">Language</p>
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($select_language as $row)
                                                <?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
                                                <a href="{{ $lang_url }}" class="text-sm px-3 py-1 rounded-full border border-slate-300 text-slate-700 hover:bg-slate-100">{{$row->native}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <a class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-primary px-4 py-3 font-semibold text-white hover:bg-primary-dark" href="{{ route('post-add') }}">
                                        <i class="fa-solid fa-plus"></i> Post Ad
                                    </a>
                                    <form method="POST" action="{{ route('logout') }}" class="mt-2">
                                        @csrf
                                        <a class="w-full inline-flex items-center justify-center gap-2 rounded-md bg-slate-100 px-4 py-3 font-semibold text-slate-700 hover:bg-slate-200" href="{{ route('logout') }}" onclick="event.preventDefault(); this.closest('form').submit();">
                                            <i class="fa-solid fa-arrow-right-from-bracket"></i> Logout
                                        </a>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Hidden Inputs --}}
    <div class="hidden">
        <input id="category" type="hidden" value="{{ request()->c ?? '' }}">
        <input type="hidden" id="cityName" value="{{ $ci ?? '' }}">
        <input type="hidden" id="locality" value="{{ $locality ?? '' }}">
        <input type="hidden" id="countryName" value="{{ $cut ?? '' }}">
        <input type="hidden" id="stateName" value="{{ $st ?? '' }}">
        <input type="hidden" id="currentCountry" value="{{ $cconty ?? '' }}">
        <input type="hidden" id="currentLocation" value="{{ Session::get('GetCountry') ?? '' }}">
        <input type="hidden" id="latitude" value="{{ $lat ?? '' }}" />
        <input type="hidden" id="longitude" value="{{ $lng ?? '' }}" />
        <input type="hidden" id="is_location_allowed" value="0" />
        <input type="hidden" id="subcategory" value="">
        <input type="hidden" id="is_search" value="{{ Session::get('IsSearch') ?? '' }}">
    </div>
</div>

<style>
@keyframes ping {
  0% { transform: scale(1); opacity: 0.8; }
  75%, 100% { transform: scale(2); opacity: 0; }
}
[x-cloak] { display: none !important; }
</style>

<script>
// Search Component Functionality
function searchComponent() {
    return {
        searchTerm: '',
        suggestions: [],
        showSuggestions: false,
        
        fetchSuggestions() {
            if (this.searchTerm.trim().length < 2) {
                this.suggestions = [];
                this.showSuggestions = false;
                return;
            }
            
            this.suggestions = [{ value: 'Searching...', loading: true }];
            this.showSuggestions = true;
            
            fetch(`{{ URL::to('/search') }}?q=${encodeURIComponent(this.searchTerm)}`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error('Network response was not ok');
                    }
                    return response.json();
                })
                .then(data => {
                    this.suggestions = data.data || [];
                    this.showSuggestions = this.suggestions.length > 0;
                })
                .catch(error => {
                    console.error('Error fetching suggestions:', error);
                    this.suggestions = [];
                    this.showSuggestions = false;
                });
        },
        
        selectSuggestion(suggestion) {
            this.searchTerm = suggestion.value;
            this.showSuggestions = false;
            this.performSearch();
        },
        
        performSearch() {
            if (this.searchTerm.trim() === '') return;
            
            const latitude = document.getElementById('latitude').value;
            const longitude = document.getElementById('longitude').value;
            const searchUrl = new URL('{{ url('/') }}' + '/' + '{{ str_replace(' ', '_', strtolower(Session::get('GetCountry') ?? '')) }}');
            
            searchUrl.searchParams.set('s', this.searchTerm);
            searchUrl.searchParams.set('loc', '{{ Session::get('GetAddress') ?? '' }}');
            searchUrl.searchParams.set('country', '{{ Session::get('GetCountry') ?? '' }}');
            
            if (latitude && longitude) {
                searchUrl.searchParams.set('lat', latitude);
                searchUrl.searchParams.set('lng', longitude);
            }

            window.location.href = searchUrl.toString();
        }
    };
}

// Mobile menu toggle functionality
document.addEventListener('DOMContentLoaded', function() {
    const mobileBtn = document.getElementById('mobileBtn');
    const mobileMenu = document.getElementById('mobileMenu');
    
    if (mobileBtn && mobileMenu) {
        mobileBtn.addEventListener('click', function() {
            mobileMenu.classList.toggle('hidden');
        });
    }
});

// Additional JavaScript for other functionality
document.addEventListener('alpine:init', () => {
    // Alpine.js initialization if needed
});
</script>
<style>
        :root {
            --primary: #f8991b;       /* Main Theme Color (Orange) */
            --primary-dark: #e68a07;  /* Darker shade for hover */
            --secondary: #39763a;     /* Accent Color (Green) */
            --secondary-dark: #2c5f2d;/* Darker Green for hover */
        }
        html, body {
            font-family:'Inter', system-ui, -apple-system, Segoe UI, Roboto, 'Helvetica Neue', Arial, 'Noto Sans', sans-serif;
            background-color: #fffbfa !important;
            color: #0f172a;
            overflow-x: hidden;
        }
        [x-cloak] { display: none !important; }
        .bg-primary { background-color: var(--primary) !important; }
        .text-primary { color: var(--primary) !important; }
        .hover\:bg-primary-dark:hover { background-color: var(--primary-dark) !important; }
        .header.scrolled { box-shadow:0 10px 30px -15px rgba(15,23,42,.25); }
        .reveal { opacity:0; transform:translateY(22px); transition:opacity .6s ease,transform .6s ease; }
        .reveal.active { opacity:1; transform:translateY(0); }
        
        /* Preloader Styles */
        #preloader { position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: #fff; display: flex; justify-content: center; align-items: center; flex-direction: column; z-index: 9999; transition: opacity 0.5s ease, visibility 0.5s ease; }
        #preloader.hide { opacity: 0; visibility: hidden; }
        .loader { border: 6px solid #f3f3f3; border-top: 6px solid var(--primary); border-radius: 50%; width: 70px; height: 70px; animation: spin 1s linear infinite; }
        @keyframes  spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    </style>