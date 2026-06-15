<!-- Reviews Modal -->
<div x-data="{ show: @entangle('showReviewsModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 bg-black/50 backdrop-blur-sm" @click="show = false"></div>

        <div x-show="show" x-transition:enter="ease-out duration-200" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" x-transition:leave="ease-in duration-150" x-transition:leave-start="opacity-100 scale-100" x-transition:leave-end="opacity-0 scale-95"
             class="relative bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
            <div class="p-6">
                <div class="flex items-center justify-between mb-5">
                    <h3 class="text-lg font-bold text-gray-900">Reviews</h3>
                    <button @click="show = false" class="w-8 h-8 rounded-full bg-gray-100 flex items-center justify-center text-gray-400 hover:bg-gray-200 hover:text-gray-600 transition">
                        <i class="fas fa-times text-sm"></i>
                    </button>
                </div>
                
                <!-- New Review Form -->
                @if(Auth::check() && $isBuyer)
                <div class="bg-gray-50 rounded-xl p-4 mb-5" x-data="{ rating: @entangle('reviewRating'), hoverRating: 0 }">
                    <h4 class="font-semibold text-sm text-gray-700 mb-3">Write a Review</h4>
                    <form wire:submit.prevent="submitReview">
                        <div class="flex items-center mb-3 gap-1">
                            <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button type="button" @click="rating = star; $wire.setRating(star)" @mouseover="hoverRating = star" @mouseleave="hoverRating = 0"
                                        class="text-2xl transition" :class="(hoverRating >= star || rating >= star) ? 'text-yellow-400' : 'text-gray-300'">
                                    &#9733;
                                </button>
                            </template>
                        </div>
                        @error('reviewRating') <span class="text-red-500 text-xs block mb-2">{{ $message }}</span> @enderror
                        
                        <textarea wire:model="reviewText" class="w-full px-4 py-3 border border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-orange-200 focus:border-orange-400 outline-none resize-none transition" rows="3" placeholder="Share your experience..."></textarea>
                        @error('reviewText') <span class="text-red-500 text-xs mt-1">{{ $message }}</span> @enderror
                        
                        <div class="text-right mt-3">
                            <button type="submit" wire:loading.attr="disabled" class="px-5 py-2 bg-orange-500 text-white rounded-lg text-sm font-medium hover:bg-orange-600 transition">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
                @elseif(Auth::check() && !$isBuyer)
                <div class="bg-blue-50 text-blue-700 p-4 rounded-xl mb-5 text-center text-sm">
                    <i class="fas fa-info-circle mr-1"></i> You need to purchase from this seller before leaving a review.
                </div>
                @else
                <div class="bg-gray-50 p-4 rounded-xl mb-5 text-center text-sm text-gray-600">
                    <a href="{{ route('login') }}" class="font-semibold text-orange-500 hover:underline">Log in</a> to leave a review.
                </div>
                @endif

                <!-- Existing Reviews List -->
                <div class="max-h-72 overflow-y-auto space-y-4 -mx-2 px-2">
                    @forelse($reviews as $review)
                    <div class="border-b border-gray-100 pb-4">
                        <div class="flex items-center justify-between mb-2">
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-gray-800 text-sm">{{ $review->name }}</span>
                                <div class="flex">
                                    @for ($i = 1; $i <= 5; $i++)
                                        <i class="fas fa-star text-xs {{ $i <= $review->ratings ? 'text-yellow-400' : 'text-gray-200' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</span>
                        </div>
                        <p class="text-sm text-gray-600">{{ $review->comment }}</p>
                        @if(!$review->approved)
                            <p class="text-xs text-yellow-600 mt-2 flex items-center gap-1"><i class="fas fa-clock"></i> Pending approval</p>
                        @endif
                    </div>
                    @empty
                    <div class="text-center py-8">
                        <div class="w-12 h-12 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-3">
                            <i class="fas fa-star text-gray-400"></i>
                        </div>
                        <p class="text-gray-500 text-sm">No reviews yet.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
