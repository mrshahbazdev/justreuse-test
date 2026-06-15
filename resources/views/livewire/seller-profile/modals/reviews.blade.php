<!-- Reviews Modal -->
<div x-data="{ show: @entangle('showReviewsModal') }" x-show="show" @keydown.escape.window="show = false" class="fixed z-50 inset-0 overflow-y-auto" style="display: none;">
    <div class="flex items-center justify-center min-h-screen">
        <div x-show="show" x-transition.opacity class="fixed inset-0 transition-opacity" aria-hidden="true">
            <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
        </div>
        <div x-show="show" x-transition class="inline-block bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:max-w-2xl w-full">
            <div class="bg-white px-6 py-4">
                <h3 class="text-xl font-semibold text-gray-900 mb-4">Seller Reviews</h3>
                
                <!-- New Review Form -->
                @if(Auth::check() && $isBuyer)
                <div class="border rounded-lg p-4 mb-6" x-data="{ rating: @entangle('reviewRating'), hoverRating: 0 }">
                    <h4 class="font-semibold mb-2">Write a Review</h4>
                    <form wire:submit.prevent="submitReview">
                        <div class="flex items-center mb-2">
                             <template x-for="star in [1, 2, 3, 4, 5]" :key="star">
                                <button type="button" @click="rating = star; $wire.setRating(star)" @mouseover="hoverRating = star" @mouseleave="hoverRating = 0"
                                        class="text-2xl" :class="(hoverRating >= star || rating >= star) ? 'text-yellow-400' : 'text-gray-300'">
                                    ★
                                </button>
                            </template>
                        </div>
                        @error('reviewRating') <span class="text-red-500 text-sm mb-2">{{ $message }}</span> @enderror
                        
                        <textarea wire:model="reviewText" class="mt-1 block w-full px-3 py-2 bg-white border border-gray-300 rounded-md shadow-sm focus:outline-none focus:ring-green-500 focus:border-green-500" rows="3" placeholder="Share your experience..."></textarea>
                        @error('reviewText') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        
                        <div class="text-right mt-2">
                             <button type="submit" wire:loading.attr="disabled" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-green-600 text-base font-medium text-white hover:bg-green-700 focus:outline-none sm:w-auto sm:text-sm">
                                Submit Review
                            </button>
                        </div>
                    </form>
                </div>
                @elseif(Auth::check() && !$isBuyer)
                 <div class="bg-blue-50 text-blue-700 p-4 rounded-lg mb-6 text-center">
                    You need to purchase an item from this seller before you can leave a review.
                </div>
                @else
                <div class="bg-gray-100 p-4 rounded-lg mb-6 text-center">
                    <a href="{{ route('login') }}" class="font-semibold text-green-600 hover:underline">Log in</a> to leave a review.
                </div>
                @endif


                <!-- Existing Reviews List -->
                <div class="max-h-80 overflow-y-auto space-y-4">
                    @forelse($reviews as $review)
                    <div class="border-b pb-4">
                        <div class="flex items-center mb-1">
                            @for ($i = 1; $i <= 5; $i++)
                                <i class="fa fa-star {{ $i <= $review->ratings ? 'text-yellow-400' : 'text-gray-300' }}"></i>
                            @endfor
                            <span class="ml-2 font-bold text-gray-800">{{ $review->name }}</span>
                        </div>
                         <p class="text-sm text-gray-500 mb-2">{{ \Carbon\Carbon::parse($review->created_at)->format('d M Y') }}</p>
                        <p class="text-gray-700">{{ $review->comment }}</p>
                        @if(!$review->approved)
                            <p class="text-sm text-yellow-600 mt-2">(Your comment is waiting for approval)</p>
                        @endif
                    </div>
                    @empty
                    <p class="text-gray-500 text-center py-4">This seller has no reviews yet.</p>
                    @endforelse
                </div>
            </div>
            <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                <button @click="show = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm">
                    Close
                </button>
            </div>
        </div>
    </div>
</div>
