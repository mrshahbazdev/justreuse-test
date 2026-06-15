<div>
    {{-- Header Section --}}
    <div class="w-full float-left    border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">My Banner Ads</h1>
        </div>
    </div>

    {{-- Controls and Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">

            {{-- Control Bar --}}
            <div class="mb-6 flex flex-col md:flex-row items-center justify-between gap-4">
                @if($is_feature_active)
                    <a href="{{ url('/banner-advertise') }}"  class="inline-flex items-center gap-2 px-5 py-2.5 bg-green-600 text-white text-sm font-semibold rounded-lg hover:bg-green-700 transition-all">
                        <i class="fa fa-plus-circle"></i> Create New Banner Ad
                    </a>
                @endif
                <div class="relative w-full md:w-auto md:min-w-[300px]">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i class="fa fa-search text-gray-400"></i>
                    </div>
                    <input type="text" wire:model.debounce.300ms="search" class="w-full px-4 py-2.5 pl-10 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500" placeholder="Search by page or status...">
                </div>
            </div>

            {{-- Main loading indicator --}}
            <div wire:loading.flex class="w-full justify-center items-center py-16">
                <i class="fa fa-spinner fa-spin text-green-500 text-4xl"></i>
            </div>

            {{-- Content Area --}}
            <div wire:loading.remove>
                @if($bannerads->count() > 0)
                    <div class="space-y-6">
                        @foreach($bannerads as $ad)
                            @php
                                $check_expired = \App\Models\TblBannerAdvertisement::check_is_expired($ad->id);
                                $currency_symbol = \App\Models\TblDefaultCurrency::where('id', $ad->currency_id)->value('currency_hex');
                            @endphp
                            <div class="bg-white rounded-lg shadow-sm border border-gray-200 overflow-hidden">
                                <div class="p-4 bg-gray-50 border-b flex flex-col md:flex-row justify-between items-center gap-3">
                                    <div>
                                        <p class="text-sm font-semibold text-gray-800">
                                            Displaying on: <span class="font-bold text-green-600 uppercase">{{ $ad->page }} Page</span>
                                        </p>
                                        <p class="text-xs text-gray-500">
                                            From <span class="font-medium">{{ \Carbon\Carbon::parse($ad->start_date)->format('d M Y') }}</span> to <span class="font-medium">{{ \Carbon\Carbon::parse($ad->end_date)->format('d M Y') }}</span>
                                        </p>
                                    </div>
                                    <div class="flex items-center gap-4">
                                        <span class="text-lg font-bold text-green-700">{!! $currency_symbol !!}{{ number_format($ad->total_amount, 2) }}</span>
                                        <span class="text-xs font-bold uppercase px-2 py-1 rounded-full 
                                            @if($ad->status == 'pending') bg-yellow-100 text-yellow-800 @endif
                                            @if($ad->status == 'approved') bg-green-100 text-green-800 @endif
                                            @if($ad->status == 'cancelled') bg-red-100 text-red-800 @endif
                                        ">{{ $ad->status }}</span>
                                    </div>
                                </div>
                                <div class="p-4 grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                                    {{-- Web Banner --}}
                                    <div class="text-center">
                                        <h4 class="font-semibold text-gray-700 mb-2">Web Banner</h4>
                                        <a href="{{ $ad->web_link }}" target="_blank">
                                            <img src="{{ url('storage/' . $ad->web_banner) }}" class="w-full object-contain border rounded-lg h-32 bg-gray-100">
                                        </a>
                                        <a href="{{ $ad->web_link }}" target="_blank" class="text-xs text-blue-500 hover:underline mt-2 inline-block truncate w-full">{{ $ad->web_link }}</a>
                                    </div>
                                     {{-- App Banner --}}
                                    <div class="text-center">
                                        <h4 class="font-semibold text-gray-700 mb-2">App Banner</h4>
                                         <a href="{{ $ad->app_link }}" target="_blank">
                                            <img src="{{ url('storage/' . $ad->app_banner) }}" class="w-full object-contain border rounded-lg h-32 bg-gray-100">
                                        </a>
                                        <a href="{{ $ad->app_link }}" target="_blank" class="text-xs text-blue-500 hover:underline mt-2 inline-block truncate w-full">{{ $ad->app_link }}</a>
                                    </div>
                                </div>
                                @if($ad->status == 'approved' && $check_expired == 1)
                                    <div class="p-3 bg-red-50 text-center text-sm font-semibold text-red-700 border-t">
                                        This banner has expired.
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    <div class="mt-8">
                        {{ $bannerads->links() }}
                    </div>
                @else
                    <div class="text-center py-16">
                        <svg class="mx-auto h-16 w-16 text-gray-300" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M3 14h18m-9-4v8m-7 0h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                        </svg>
                        <p class="text-xl text-gray-600 font-semibold mt-4">No Banner Ads Found</p>
                        <p class="text-gray-500">You haven't created any banner advertisements yet.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
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
