<div>
    {{-- Header Section --}}
    <div class="w-full float-left bg-gray-50 border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Create a New Banner Ad</h1>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">
            <form wire:submit.prevent="saveBannerAd" class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Form Fields --}}
                <div class="lg:col-span-2 bg-white rounded-lg shadow-sm border border-gray-200 p-6 space-y-6">
                    
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ad Details</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Display Page</label>
                                <select wire:model="page" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="home">Home Page</option>
                                    <option value="search">Search/Category Page</option>
                                </select>
                            </div>

                            @if($page === 'search')
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Category</label>
                                <select wire:model="category_id" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="">Select a Category</option>
                                    @foreach($categorylist as $category)
                                        <option value="{{ $category->id }}">{{ str_repeat('--', $category->depth) }} {{ $category->title }}</option>
                                    @endforeach
                                </select>
                                @error('category_id') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            @endif
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Duration</h2>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Start Date</label>
                                <input type="date" wire:model="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('start_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">End Date</label>
                                <input type="date" wire:model="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('end_date') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                        </div>
                    </div>

                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Links & Banners</h2>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Web Link (URL)</label>
                                <input type="url" wire:model.lazy="web_link" placeholder="https://example.com" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('web_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                             <div>
                                <label class="block text-sm font-medium text-gray-700">App Link (URL)</label>
                                <input type="url" wire:model.lazy="app_link" placeholder="https://example.com/app" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                @error('app_link') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Web Banner (Image)</label>
                                <input type="file" wire:model="web_banner" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <div wire:loading wire:target="web_banner">Uploading...</div>
                                @error('web_banner') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @if ($web_banner && !$errors->has('web_banner'))
                                    <img src="{{ asset('storage/livewire-tmp/' . $web_banner->getFilename()) }}" class="mt-2 h-24 rounded-lg border">
                                @endif
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">App Banner (Image)</label>
                                <input type="file" wire:model="app_banner" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                                <div wire:loading wire:target="app_banner">Uploading...</div>
                                @error('app_banner') <span class="text-red-500 text-xs">{{ $message }}</span> @enderror
                                @if ($app_banner && !$errors->has('app_banner'))
                                    <img src="{{ asset('storage/livewire-tmp/' . $app_banner->getFilename()) }}" class="mt-2 h-24 rounded-lg border">
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-28 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4">Order Summary</h2>
                        
                        <div wire:loading.class="opacity-50" wire:target="calculatePrice" class="space-y-4 transition-opacity">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Price Per Day</span>
                                <span class="font-semibold">{!! $currencySymbol !!}{{ number_format($pricePerDay, 2) }}</span>
                            </div>
                             <div class="flex justify-between items-center text-gray-600">
                                <span>Live Days</span>
                                <span class="font-semibold">{{ $live_days }}</span>
                            </div>
                            <div class="flex justify-between items-center text-2xl font-bold text-gray-800 mt-2 pt-4 border-t">
                                <span>Total Amount</span>
                                <span>{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span>
                            </div>
                        </div>

                        <div class="mt-6">
                             <h3 class="text-md font-semibold text-gray-700 mb-3">Payment Method</h3>
                             <div class="space-y-3">
                                @foreach($paymentMethods as $method)
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer {{ $selectedPaymentMethod == $method['name'] ? 'bg-green-50 border-green-500' : 'border-gray-200' }}">
                                        <input type="radio" wire:model="selectedPaymentMethod" name="payment_type" value="{{ $method['name'] }}" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <span class="ml-3 font-medium text-gray-700">{{ $method['display_name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <button type="submit" wire:loading.attr="disabled"
                            class="w-full mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700 transition-all disabled:opacity-50">
                            <span wire:loading.remove wire:target="saveBannerAd">Create Ad & Proceed</span>
                            <span wire:loading wire:target="saveBannerAd">Creating Ad...</span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    
    {{-- Toaster --}}
    <div id="toast-notification" class="fixed bottom-5 right-5 bg-green-500 text-white px-4 py-2 rounded-lg shadow-lg" style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;"><p id="toast-message"></p></div>
    <script>
        document.addEventListener('livewire:load', function () {
            // Toastr script
        });
    </script>
</div>

