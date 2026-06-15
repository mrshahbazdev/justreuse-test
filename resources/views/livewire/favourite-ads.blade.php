<div>
    {{-- Header Section --}}
    <div class="w-full float-left  border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Favourite Ads</h1>
        </div>
    </div>

    {{-- Controls and Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            {{-- Control Bar --}}
            <div class=" rounded-lg shadow-sm p-4 border border-gray-200 mb-6 flex flex-col md:flex-row items-center gap-4">
                {{-- Select All / Delete --}}
                <div class="flex items-center">
                    <input wire:model="selectAll" type="checkbox" class="h-5 w-5 rounded text-green-600 focus:ring-green-500 border-gray-300" />
                    <label for="master" class="ml-3 text-sm font-medium text-gray-700">Select All</label>
                    
                    @if($selectedAds)
                    <button wire:click="confirmBulkDelete" wire:loading.attr="disabled"
                        class="ml-4 inline-flex items-center gap-2 px-4 py-2 bg-red-50 border border-red-200 text-sm font-semibold text-red-600 rounded-lg hover:bg-red-100">
                        <i class="fa fa-trash-o"></i>
                        Delete ({{ count($selectedAds) }})
                    </button>
                    @endif
                </div>

                {{-- Search --}}
                <div class="relative md:ml-auto w-full md:w-auto md:min-w-[300px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-gray-400"></i>
                    </div>
                    <input type="text" wire:model.debounce.300ms="search" class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="Search in favourites...">
                </div>
            </div>


            {{-- Main loading indicator --}}
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            {{-- Content Area --}}
            <div wire:loading.remove>
                @if($data->count() > 0)
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($data as $row)
                            @php
                                $imgUrlfinal = \App\Models\TblChat::getPostImgForList($row['id']);
                                $slug = \App\Models\TblPost::get_post_slug($row["slug"]);
                                $currency_symbol = \App\Models\TblPost::get_post_currency($row['currency_id']);
                            @endphp
                            <div class="bg-white rounded-xl shadow-sm border border-slate-200 flex flex-col hover:shadow-lg hover:-translate-y-1 transition-all duration-300 relative">
                                <!-- Checkbox -->
                                <div class="absolute top-3 left-3 z-10">
                                    <input wire:model="selectedAds" value="{{ $row->fav_id }}" type="checkbox" class="h-5 w-5 rounded text-green-600 focus:ring-green-500 border-gray-300 shadow-sm">
                                </div>

                                <!-- Image Section -->
                                <a href="{{ $slug }}" target="_blank" class="block">
                                    <img src="{{ $imgUrlfinal }}" alt="{{ $row->title }}" class="w-full h-48 object-cover rounded-t-xl">
                                </a>

                                <!-- Card Content -->
                                <div class="p-4 flex flex-col flex-grow">
                                    <a href="{{ $slug }}" target="_blank" class="flex-grow">
                                        <h3 class="font-bold text-slate-900 line-clamp-2 ad-title capitalize hover:text-green-600">{{ $row->title }}</h3>
                                    </a>

                                    {{-- === CURRENCY FIX IS HERE === --}}
                                    <p class="text-green-600 font-extrabold text-lg mt-2 ad-price">{!! $currency_symbol[0] ?? '' !!}{{ number_format($row->price, 2) }}</p>

                                    <div class="mt-3 text-xs text-slate-500 flex items-center justify-between">
                                        <span class="ad-loc truncate pr-2"><i class="fa fa-map-marker mr-1"></i>{{ !empty($row->locality) ? $row->locality : $row->city_name }}</span>
                                        <span class="flex-shrink-0">{{ \Carbon\Carbon::parse($row->created_at)->format('d M Y') }}</span>
                                    </div>
                                    
                                    <button wire:click="confirmDelete('{{ $row->fav_id }}')" wire:loading.attr="disabled"
                                        class="w-full mt-4 inline-flex items-center justify-center gap-2 rounded-lg outline-none focus:outline-none ease-linear transition-all duration-150 bg-red-50 border border-red-200 text-xs text-red-600 hover:bg-red-100 hover:border-red-300 px-4 py-2 font-semibold">
                                        <i class="fa fa-trash-o"></i> Remove
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $data->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto h-16 w-16 text-gray-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                        <p class="text-xl text-gray-600 font-semibold mt-4">No Favourite Ads Found</p>
                        <p class="text-gray-500">You haven't saved any ads yet. Click the heart icon on an ad to save it.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
    
    {{-- Single Delete Confirmation Modal --}}
    <div x-data="{ show: @entangle('showDeleteConfirmation') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div x-show="show" x-transition class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Remove Ad</h3>
                            <div class="mt-2"><p class="text-sm text-gray-500">Are you sure you want to remove this ad from your favourites? This action cannot be undone.</p></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteConfirmedAd()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Remove</button>
                    <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Bulk Delete Confirmation Modal --}}
    <div x-data="{ show: @entangle('showBulkDeleteConfirmation') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
         <div class="flex items-center justify-center min-h-screen">
            <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div x-show="show" x-transition class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fa fa-exclamation-triangle text-red-600"></i>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Delete Selected Ads</h3>
                            <div class="mt-2"><p class="text-sm text-gray-500">Are you sure you want to remove the selected {{ count($selectedAds) }} ads? This action cannot be undone.</p></div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button wire:click="deleteSelected()" type="button" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 sm:ml-3 sm:w-auto sm:text-sm">Delete All</button>
                    <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:w-auto sm:text-sm">Cancel</button>
                </div>
            </div>
        </div>
    </div>
    
    {{-- Toaster --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            const toast = document.getElementById('toast-notification');
            const toastMessage = document.getElementById('toast-message');
            let toastTimeout;
            window.addEventListener('show-toast', event => {
                if (toastTimeout) clearTimeout(toastTimeout);
                toastMessage.innerText = event.detail.message;
                toast.style.display = 'block';
                setTimeout(() => {
                    toast.style.opacity = '1';
                    toast.style.transform = 'translateY(0)';
                }, 10);
                toastTimeout = setTimeout(() => {
                    toast.style.opacity = '0';
                    toast.style.transform = 'translateY(0.5rem)';
                    setTimeout(() => { toast.style.display = 'none'; }, 300);
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

