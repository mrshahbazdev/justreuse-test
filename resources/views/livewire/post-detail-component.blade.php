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
            .tab-btn { position: relative; padding-bottom: 0.75rem; font-weight: 600; color: #6b7280; transition: color 0.3s ease; border: none; background: none; cursor: pointer; font-size: 15px; }
            .tab-btn::after { content: ''; position: absolute; bottom: -1px; left: 0; right: 0; height: 3px; background-color: #16a34a; transform: scaleX(0); transition: transform 0.3s ease; border-radius: 2px; }
            .tab-btn.active { color: #16a34a; }
            .tab-btn.active::after { transform: scaleX(1); }
            .tab-content { display: none; }
            .tab-content.active { display: block; animation: fadeIn 0.4s ease; }
            @keyframes fadeIn { from { opacity: 0; transform: translateY(8px); } to { opacity: 1; transform: translateY(0); } }

            /* Breadcrumb */
            .pd-breadcrumb { font-size: 13px; color: #6b7280; margin-bottom: 1.5rem; }
            .pd-breadcrumb a { color: #16a34a; text-decoration: none; font-weight: 500; }
            .pd-breadcrumb a:hover { text-decoration: underline; }
            .pd-breadcrumb span { margin: 0 6px; color: #d1d5db; }

            /* Image gallery improvements */
            .pd-gallery-main { border-radius: 16px; overflow: hidden; background: #f9fafb; }
            .pd-thumb { border-radius: 10px; transition: all 0.2s ease; }
            .pd-thumb:hover { transform: scale(1.05); }

            /* Price badge */
            .pd-price-badge { background: linear-gradient(135deg, #ecfdf5, #d1fae5); border: 1px solid #a7f3d0; border-radius: 12px; padding: 16px 20px; }

            /* Seller card */
            .pd-seller-card { transition: all 0.2s ease; }
            .pd-seller-card:hover { transform: translateY(-2px); box-shadow: 0 8px 25px rgba(0,0,0,0.08); }

            /* Feature list */
            .pd-feature-row { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; padding: 12px 0; border-bottom: 1px solid #f3f4f6; }
            .pd-feature-row:last-child { border-bottom: none; }
            .pd-feature-key { font-size: 14px; color: #6b7280; font-weight: 500; }
            .pd-feature-val { font-size: 14px; color: #111827; font-weight: 600; text-align: right; }

            /* Related Ads */
            .pd-related-section { margin-top: 3rem; padding-top: 2.5rem; border-top: 1px solid #e5e7eb; }
            .pd-related-header { display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.5rem; }
            .pd-related-title { font-size: 1.5rem; font-weight: 700; color: #111827; }
            .pd-related-count { font-size: 13px; color: #9ca3af; font-weight: 500; }
            .pd-load-more-btn { display: block; margin: 2rem auto 0; padding: 12px 32px; background: #f3f4f6; color: #374151; font-size: 14px; font-weight: 600; border: 1px solid #e5e7eb; border-radius: 30px; cursor: pointer; transition: all 0.2s ease; }
            .pd-load-more-btn:hover { background: #e5e7eb; transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.06); }
            .pd-load-more-btn i { margin-right: 6px; }

            /* Action buttons */
            .pd-action-btn { display: flex; align-items: center; justify-content: center; gap: 8px; width: 100%; padding: 14px 24px; font-size: 16px; font-weight: 600; border-radius: 12px; cursor: pointer; transition: all 0.2s ease; border: none; }
            .pd-action-btn:hover { transform: translateY(-1px); box-shadow: 0 4px 12px rgba(0,0,0,0.1); }
            .pd-action-primary { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
            .pd-action-secondary { background: #f3f4f6; color: #374151; border: 1px solid #e5e7eb; }

            /* Tags */
            .pd-tag { display: inline-flex; align-items: center; gap: 4px; padding: 4px 10px; background: #f3f4f6; border-radius: 6px; font-size: 12px; font-weight: 500; color: #6b7280; }

            /* Meta info */
            .pd-meta-row { display: flex; align-items: center; gap: 16px; flex-wrap: wrap; margin-bottom: 1rem; }
            .pd-meta-item { display: flex; align-items: center; gap: 6px; font-size: 13px; color: #6b7280; }
            .pd-meta-item i { font-size: 12px; color: #9ca3af; }
        </style>
    @endpush

    <main class="container mx-auto px-4 sm:px-6 lg:px-8 py-6 lg:py-10">
        {{-- Breadcrumb --}}
        <div class="pd-breadcrumb">
            <a href="/">Home</a><span>/</span>
            <a href="/{{ strtolower($category_name) }}">{{ $category_name }}</a><span>/</span>
            <span class="text-gray-500">{{ Str::limit($post->title, 40) }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 lg:gap-10">
            
            {{-- Left Column --}}
            <div class="lg:col-span-7 flex flex-col gap-6">
                {{-- Image Gallery --}}
                <div class="bg-white rounded-2xl shadow-sm border p-4 space-y-3">
                    <div @click="openImageZoom()" class="pd-gallery-main relative aspect-[4/3] cursor-pointer group">
                        <template x-for="(image, index) in images" :key="index">
                            <img x-show="currentImageIndex === index" :src="image" class="w-full h-full object-contain transition-opacity duration-300">
                        </template>
                        <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-10 transition-all flex items-center justify-center">
                            <i class="fa fa-search-plus text-white text-3xl opacity-0 group-hover:opacity-100 transition-opacity drop-shadow-lg"></i>
                        </div>
                         @if(empty($jsImages))
                             <p class="text-gray-400 font-medium flex items-center justify-center h-full">No images available</p>
                         @endif
                        @if($post->video_url)
                            <button @click.stop="$wire.set('showVideoModal', true)" class="absolute top-3 right-3 z-10 bg-red-600 text-white px-4 py-2 rounded-full text-xs font-bold flex items-center gap-2 hover:bg-red-700 shadow-lg">
                                <i class="fa fa-play-circle"></i> Watch Video
                            </button>
                        @endif
                    </div>
                    <div class="grid grid-cols-5 gap-2 mt-2">
                        <template x-for="(image, index) in images" :key="index">
                            <div @click="selectImage(index)" 
                                 :class="{'border-green-500 ring-2 ring-green-200 shadow-sm': currentImageIndex === index, 'border-gray-200': currentImageIndex !== index}"
                                 class="pd-thumb border-2 cursor-pointer aspect-square overflow-hidden">
                                <img :src="image" class="w-full h-full object-cover">
                            </div>
                        </template>
                    </div>
                </div>
                
                {{-- Details/Description Tabs --}}
                <div class="bg-white rounded-2xl shadow-sm border p-6">
                    <div class="border-b border-gray-100">
                        <nav class="flex space-x-8 -mb-px">
                      		<button @click="activeTab = 'details'" :class="{ 'active': activeTab === 'details' }" class="tab-btn">Item Details</button>
                            <button @click="activeTab = 'description'" :class="{ 'active': activeTab === 'description' }" class="tab-btn">Description</button>
                        </nav>
                    </div>
                    <div class="py-6">
                        <div x-show="activeTab === 'details'" class="tab-content active" x-data="{ showAll: false }">
                            @if(!empty($allFeatures))
                                <div class="space-y-0">
                                @foreach($allFeatures as $index => $feature)
                                    <div x-show="showAll || {{ $index }} < 6" x-transition class="pd-feature-row">
                                        <span class="pd-feature-key">{{ $feature['k'] }}</span>
                                        <span class="pd-feature-val">{{ $feature['v'] }}</span>
                                    </div>
                                @endforeach
                                </div>
                                @if(count($allFeatures) > 6)
                                <button @click="showAll = !showAll" class="mt-4 text-green-600 font-semibold text-sm hover:text-green-700 transition">
                                    <span x-show="!showAll"><i class="fa fa-chevron-down mr-1"></i> Show All ({{ count($allFeatures) }})</span>
                                    <span x-show="showAll"><i class="fa fa-chevron-up mr-1"></i> Show Less</span>
                                </button>
                                @endif
                            @else
                                <p class="text-gray-400 text-sm">No additional details available for this item.</p>
                            @endif
                        </div>
                        <div x-show="activeTab === 'description'" class="tab-content prose max-w-none text-gray-600 leading-relaxed">
                            {!! nl2br(e($post->description)) !!}
                        </div>
                    </div>
                </div>
            </div>

            {{-- Right Column --}}
            <div class="lg:col-span-5">
                <div class="sticky top-28 flex flex-col gap-6">
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-3 leading-tight">{{ $post->title }}</h1>
                        
                        {{-- Meta Info --}}
                        <div class="pd-meta-row">
                            <span class="pd-meta-item"><i class="fa fa-tag"></i> {{ $category_name }}</span>
                            <span class="pd-meta-item"><i class="fa fa-eye"></i> {{ number_format($post->views_count) }} views</span>
                            <span class="pd-meta-item"><i class="fa fa-clock-o"></i> {{ $post->created_at->diffForHumans() }}</span>
                        </div>

                        <div class="pd-price-badge mb-6">
                            <p class="text-3xl lg:text-4xl font-bold text-green-700">{!! \App\Models\TblPost::get_post_currency($post->currency_id)[0] ?? '$' !!}{{ number_format($post->price) }}</p>
                            @if($post->fixed_price)
                                <span class="pd-tag mt-2"><i class="fa fa-lock"></i> Fixed Price</span>
                            @else
                                <span class="pd-tag mt-2"><i class="fa fa-exchange"></i> Negotiable</span>
                            @endif
                        </div>

                        <div class="flex flex-col gap-3 mb-6">
                            @if(Auth::id() != $post->user_id)
                                <button @auth @click.prevent="Livewire.emit('startChat', '{{ $post->user_id }}', '{{ $post->id }}')" @else onclick="window.location.href='{{ route('login') }}'" @endauth class="pd-action-btn pd-action-primary">
                                    <i class="fa fa-comments"></i> Chat with Seller
                                </button>
                                @if(!$post->fixed_price)
                                    <button wire:click="$set('showOfferModal', true)" class="pd-action-btn pd-action-secondary">Make an Offer</button>
                                @endif
                            @endif
                        </div>

                        <div class="flex justify-center space-x-6 text-sm text-gray-500 font-medium border-t pt-4">
                            <button wire:click="toggleFavorite" class="transition flex items-center gap-2 hover:text-red-500">
                                <i class="fa {{ $is_favorited ? 'fa-heart text-red-500' : 'fa-heart-o' }}"></i>
                                <span>{{ $is_favorited ? 'Saved' : 'Save' }}</span>
                            </button>
                             <div x-data="{ open: false }" class="relative">
                                <button @click="open = !open" class="hover:text-green-600 transition flex items-center gap-2"><i class="fa fa-share-alt"></i> Share</button>
                                <div x-show="open" @click.away="open = false" class="origin-top-right absolute right-0 mt-2 w-44 rounded-lg shadow-lg bg-white ring-1 ring-black ring-opacity-5 z-10 p-2" style="display:none;">
                                    <a href="https://wa.me/?text={{ urlencode(url()->current()) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"><i class="fa fa-whatsapp text-green-500"></i> WhatsApp</a>
                                    <a href="https://www.facebook.com/sharer/sharer.php?u={{ urlencode(url()->current()) }}" target="_blank" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md"><i class="fa fa-facebook text-blue-600"></i> Facebook</a>
                                    <button onclick="navigator.clipboard.writeText('{{ url()->current() }}')" class="flex items-center gap-2 px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded-md w-full"><i class="fa fa-link"></i> Copy Link</button>
                                </div>
                            </div>
                             @if(Auth::check() && Auth::id() != $post->user_id)
                                <button wire:click="$set('showReportModal', true)" class="transition flex items-center gap-2 hover:text-yellow-600"><i class="fa fa-flag"></i> Report</button>
                            @endif
                        </div>
                    </div>
                     {{-- Seller Info Card --}}
                    <a href="{{ url('seller-profile/' . $info_user->id) }}" class="pd-seller-card bg-white rounded-2xl shadow-sm border p-6 block">
                         <h3 class="text-lg font-bold mb-4 text-gray-800">Seller Information</h3>
                         <div class="flex items-center gap-4">
                             <img src="{{ $info_user->profile_photo_url }}" alt="{{ $info_user->name }}" class="w-14 h-14 rounded-full object-cover ring-2 ring-green-100">
                             <div class="flex-1">
                                 <p class="font-bold text-gray-800">{{ $info_user->name }}</p>
                                 <p class="text-xs text-gray-500 mt-1">Member since {{ $info_user->created_at->isoFormat('MMM YYYY') }}</p>
                             </div>
                             <span class="px-3 py-1.5 bg-green-50 text-green-700 text-xs font-semibold rounded-full border border-green-200">View <i class="fa fa-chevron-right text-[10px] ml-1"></i></span>
                         </div>
                    </a>
                    {{-- Location Card --}}
                    @if($info_location && $info_location->latitude && $info_location->logitude)
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h3 class="text-lg font-bold mb-3 text-gray-800">Location</h3>
                        <p class="text-gray-600 text-sm mb-4"><i class="fa fa-map-marker-alt mr-2 text-green-500"></i>{{ $info_location->name }}</p>
                        <div class="rounded-xl overflow-hidden h-44 border">
                            <div id="map" class="w-full h-full"></div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        @php
            $relatedWithUser = collect($related_products)->filter(fn($ad) => isset($ad['user']))->values();
        @endphp
        @if($relatedWithUser->count() > 0)
        <div class="pd-related-section" x-data="{ visibleCount: 8 }">
            <div class="pd-related-header">
                <h2 class="pd-related-title">Related Ads</h2>
                <span class="pd-related-count" x-text="Math.min(visibleCount, {{ $relatedWithUser->count() }}) + ' of {{ $relatedWithUser->count() }}'"></span>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-2 lg:grid-cols-4 gap-4 lg:gap-6">
                @foreach($relatedWithUser as $index => $ad)
                    <div x-show="{{ $index }} < visibleCount" x-transition>
                        {!! \App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    </div>
                @endforeach
            </div>
            @if($relatedWithUser->count() > 8)
                <button x-show="visibleCount < {{ $relatedWithUser->count() }}" @click="visibleCount += 8" class="pd-load-more-btn">
                    <i class="fa fa-arrow-down"></i> Load More
                </button>
            @endif
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

