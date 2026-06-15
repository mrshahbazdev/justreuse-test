<div x-data="{
    showVideoModal: @entangle('showVideoModal'),
    showImageZoomModal: false,
    showReportModal: @entangle('showReportModal'),
    showOfferModal: @entangle('showOfferModal'),
    images: {{ json_encode($jsImages) }},
    currentImageIndex: 0,
    activeTab: 'details',
    selectImage(index) { this.currentImageIndex = index; },
    openImageZoom() { if(this.images.length > 0) this.showImageZoomModal = true; },
    nextImage() { this.currentImageIndex = (this.currentImageIndex + 1) % this.images.length; },
    prevImage() { this.currentImageIndex = (this.currentImageIndex - 1 + this.images.length) % this.images.length; }
}">
    @push('styles')
        <style>
            .tab-btn { position: relative; padding-bottom: 0.75rem; font-weight: 600; color: #6b7280; transition: color 0.3s ease; border: none; background: none; cursor: pointer; }
            .tab-btn::after { content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 3px; background-color: #16a34a; transform: scaleX(0); transition: transform 0.3s ease; }
            .tab-btn.active { color: #16a34a; }
            .tab-btn.active::after { transform: scaleX(1); }
            .tab-content { display: none; }
            .tab-content.active { display: block; animation: fadeIn 0.5s ease; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        </style>
    @endpush

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-8 lg:py-12">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-12">
            
            {{-- Left Column --}}
            <div class="lg:col-span-7 flex flex-col gap-8">
                {{-- Image Gallery --}}
                <div class="bg-white rounded-lg shadow-sm border p-4 space-y-3">
                    <div @click="openImageZoom()" class="relative aspect-[4/3] rounded-xl overflow-hidden bg-gray-100 cursor-pointer group">
                        <template x-for="(image, index) in images" :key="index">
                            <img x-show="currentImageIndex === index" :src="image" class="w-full h-full object-contain transition-opacity duration-300">
                        </template>
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                            <i class="fa fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity"></i>
                        </div>
                         @if(empty($jsImages))
                             <p class="text-gray-500 font-semibold flex items-center justify-center">No images to preview</p>
                         @endif
                        @if($post->video_url)
                            <button @click.stop="$wire.set('showVideoModal', true)" class="absolute top-2 right-2 z-10 bg-red-600 text-white px-3 py-1 rounded-full text-xs font-bold flex items-center gap-2 hover:bg-red-700">
                                <i class="fa fa-play-circle"></i> Watch Video
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-5 gap-3 mt-2">
                        <template x-for="(image, index) in images" :key="index">
                            <div @click="selectImage(index)" 
                                 :class="{'border-green-500 ring-2 ring-green-500': currentImageIndex === index}"
                                 class="border-2 rounded-xl cursor-pointer aspect-w-1 aspect-h-1 overflow-hidden transition-all">
                                <img :src="image" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>
                
                {{-- Details/Description Tabs --}}
                <div class="bg-white rounded-lg shadow-sm border p-6">
                    <div class="border-b border-gray-200">
                        <nav class="flex space-x-8 -mb-px">
                      		<button @click="activeTab = 'details'" :class="{ 'active': activeTab === 'details' }" class="tab-btn">Item Details</button>
                            <button @click="activeTab = 'description'" :class="{ 'active': activeTab === 'description' }" class="tab-btn">Description</button>
                        </nav>
                    </div>
                    <div class="py-6">
                        <div x-show="activeTab === 'details'" class="tab-content active" x-data="{ showAll: false }">
                            @if(!empty($allFeatures))
                                <ul class="space-y-3">
                                @foreach($allFeatures as $index => $feature)
                                    <li x-show="showAll || {{ $index }} < 5" x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="grid grid-cols-2 gap-4 border-b pb-3 last:border-b-0">
                                        <span class="text-gray-600">{{ $feature['k'] }}</span>
                                        <span class="font-semibold text-gray-800 text-right">{{ $feature['v'] }}</span>
                                    </li>
                                @endforeach
                                </ul>
                                @if(count($allFeatures) > 5)
                                <button @click="showAll = !showAll" class="text-green-600 font-semibold mt-4">
                                    <span x-show="!showAll">Show More...</span>
                                    <span x-show="showAll">Show Less</span>
                                </button>
                                @endif
                            @else
                                <p class="text-gray-500">No additional details available for this item.</p>
                            @endif
                        </div>
                        <div x-show="activeTab === 'description'" class="tab-content prose max-w-none text-gray-600">
                            {!! nl2br(e($post->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-5">
                <div class="sticky top-28 flex flex-col gap-8">
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h1 class="text-3xl font-bold text-gray-900 mb-2">{{ $post->title }}</h1>
                        <p class="text-gray-500 text-sm mb-6">Ad ID: #JR-{{ $post->ff }}</p>
                        <div class="bg-green-50 rounded-xl p-4 mb-6">
                            <p class="text-4xl font-bold text-green-700">{!! \App\Models\TblPost::get_post_currency($post->currency_id)[0] ?? '$' !!}{{ number_format($post->price) }}</p>
                        </div>
                        <div class="flex flex-col gap-3 mb-6">
                            @if(Auth::id() != $post->user_id)
                                <button @auth @click.prevent="Livewire.emit('startChat', '{{ $post->user_id }}', '{{ $post->id }}')" @else onclick="window.location.href='{{ route('login') }}'" @endauth class="w-full flex justify-center items-center gap-2 px-6 py-3 bg-green-600 text-white text-lg font-semibold rounded-lg hover:bg-green-700 transition">
                                    <i class="fa fa-comments"></i> Chat with Seller
                                </button>
                                @if(!$post->fixed_price)
                                    <button wire:click="$set('showOfferModal', true)" class="w-full px-6 py-3 bg-gray-200 text-gray-800 text-lg font-semibold rounded-lg hover:bg-gray-300 transition">Make an Offer</button>
                                @endif
                            @endif
                        </div>
                         <div class="flex justify-center space-x-6 text-gray-500 font-semibold">
                            <button wire:click="toggleFavorite" class="transition flex items-center gap-2 hover:text-red-500">
                                <i class="fa {{ $is_favorited ? 'fa-heart text-red-500' : 'fa-heart-o' }}"></i>
                                <span>{{ $is_favorited ? 'Favorited' : 'Favorite' }}</span>
                            </button>
                             <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="hover:text-green-600 transition flex items-center gap-2"><i class="fa fa-share-alt"></i>Share</button>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-40 rounded-md shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10" style="display:none;">
                                    {{-- Share links can be added here --}}
                                </div>
                            </div>
                             @if(Auth::check() && Auth::id() != $post->user_id)
                                <button wire:click="$set('showReportModal', true)" class="transition flex items-center gap-2 hover:text-yellow-600"><i class="fa fa-flag"></i>Report</button>
                            @endif
                        </div>
                    </div>
                     {{-- Seller Info Card --}}
                    <a href="{{ url('seller-profile/' . $info_user->id) }}" class="bg-white rounded-lg shadow-sm border p-6 block hover:shadow-md transition-shadow">
                         <h3 class="text-xl font-bold mb-4">Seller Information</h3>
                         <div class="flex items-center gap-4">
                             <img src="{{ $info_user->profile_photo_url }}" alt="{{ $info_user->name }}" class="w-16 h-16 rounded-full object-cover">
                             <div>
                                 <p class="font-bold text-lg text-gray-800">{{ $info_user->name }}</p>
                                 <p class="text-sm text-gray-500">Member since {{ $info_user->created_at->isoFormat('MMM YYYY') }}</p>
                                  <span class="inline-block mt-2 px-3 py-1 bg-green-100 text-green-800 text-xs font-semibold rounded-full">View Profile</span>
                             </div>
                         </div>
                    </a>
                    {{-- Location Card --}}
                    @if($info_location && $info_location->latitude && $info_location->logitude)
                    <div class="bg-white rounded-lg shadow-sm border p-6">
                        <h3 class="text-xl font-bold mb-4">Location</h3>
                        <p class="text-gray-600 mb-4"><i class="fa fa-map-marker-alt mr-2 text-gray-400"></i>{{ $info_location->name }}</p>
                        <div class="rounded-xl overflow-hidden h-48 border">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @if(count($related_products) > 0)
        <div class="mt-12 lg:mt-16 border-t pt-12 lg:pt-16">
            <h2 class="text-3xl font-bold mb-6">Related Ads</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($related_products as $ad)
                    @if(isset($ad['user']))
                        {!! \App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @endif
                @endforeach
            </div>
        </div>
        @endif
    </main>

    {{-- Image Zoom Modal --}}
    <div x-show="showImageZoomModal" @keydown.escape.window="showImageZoomModal = false" class="fixed z-[9999] inset-0 bg-black bg-opacity-75 flex items-center justify-center p-4" style="display: none;">
        <div @click.away="showImageZoomModal = false" class="relative max-w-4xl max-h-full">
            <template x-for="(image, index) in images" :key="index">
                <img x-show="currentImageIndex === index" :src="image" class="rounded-lg max-h-[90vh]">
            </template>
            <button @click="prevImage()" class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-12 bg-white/20 hover:bg-white/40 text-white rounded-full h-10 w-10 flex items-center justify-center text-xl">&lt;</button>
            <button @click="nextImage()" class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-12 bg-white/20 hover:bg-white/40 text-white rounded-full h-10 w-10 flex items-center justify-center text-xl">&gt;</button>
            <button @click="showImageZoomModal = false" class="absolute -top-4 -right-4 bg-white rounded-full h-10 w-10 flex items-center justify-center text-gray-800 text-2xl">&times;</button>
        </div>
    </div>
    
    {{-- Report Modal --}}
    <div x-show="showReportModal" @keydown.escape.window="showReportModal = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
         <div class="flex items-center justify-center min-h-screen">
            <div x-show="showReportModal" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true"><div class="absolute inset-0 bg-gray-500 opacity-75"></div></div>
            <div x-show="showReportModal" x-transition class="bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-lg w-full">
                <form wire:submit.prevent="submitReport">
                    <div class="bg-white px-6 py-4">
                        <h3 class="text-xl font-semibold mb-4">Report Ad</h3>
                        <select wire:model="reportType" class="w-full border-gray-300 rounded-md">
                            <option value="">Select a reason</option>
                            @foreach($report_types as $type)
                                <option value="{{ $type->id }}">{{ $type->name }}</option>
                            @endforeach
                        </select>
                        @error('reportType') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                        <textarea wire:model.lazy="reportComment" rows="4" class="w-full mt-4 border-gray-300 rounded-md" placeholder="Add a comment..."></textarea>
                        @error('reportComment') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                    </div>
                    <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                        <button type="submit" class="btn btn-primary">Submit Report</button>
                        <button type="button" @click="showReportModal = false" class="btn btn-secondary mt-3 sm:mt-0 sm:mr-3">Cancel</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
  @push('scripts')
        <script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&callback=initMadp" async defer></script>
        <script>
            // === YEH MUKAMMAL FIX HAI: Map ko theek se initialize karein ===
            function initMadp() {
                @if($info_location && $info_location->latitude && $info_location->logitude)
                    const lat = parseFloat('{{ $info_location->latitude }}');
                    const lng = parseFloat('{{ $info_location->logitude }}');
                    
                    if (!isNaN(lat) && !isNaN(lng)) {
                        const location = { lat: lat, lng: lng };
                        const mapElement = document.getElementById("map");
                        
                        if(mapElement) {
                            const map = new google.maps.Map(mapElement, {
                                zoom: 15,
                                center: location,
                                disableDefaultUI: true,
                            });
                            new google.maps.Marker({
                                position: location,
                                map: map,
                            });
                        }
                    }
                @endif
            }

            document.addEventListener('DOMContentLoaded', function () {
                // ... (Aapka baaqi tamam JavaScript code, jaise toastr, yahan hai)
            });
        </script>
    @endpush
</div>

