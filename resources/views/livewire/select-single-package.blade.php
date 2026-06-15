<div>
    {{-- Header Section --}}
    <div class="w-full float-left  border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Promote Your Ad</h1>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Form Fields --}}
                <div class="lg:col-span-2 space-y-8">
                    
                    {{-- Ad Info Card --}}
                    <div class=" rounded-lg shadow-sm border border-gray-200 p-6 flex items-center gap-6">
                        <img src="{{ \App\Models\TblChat::getPostImgForList($post->id) }}" class="h-24 w-24 rounded-lg object-cover border flex-shrink-0">
                        <div>
                            <h2 class="text-xl font-bold text-gray-800">{{ $post->title }}</h2>
                            <p class="text-lg font-bold text-green-600">{!! $currencySymbol !!}{{ number_format($post->price, 2) }}</p>
                        </div>
                    </div>

                    {{-- Packages Selection --}}
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Choose a Package</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-4">
                            @foreach($packages as $pack)
                                <label wire:key="pack-{{ $pack->id }}" class="relative block p-6 border rounded-lg cursor-pointer transition-all {{ $selectedPackageId == $pack->id ? 'bg-green-50 border-green-500 ring-2 ring-green-500' : 'border-gray-200 hover:border-gray-400' }}">
                                    <input type="radio" wire:model="selectedPackageId" value="{{ $pack->id }}" class="hidden">
                                    <p class="text-lg font-semibold text-gray-800">{{ $pack->name }}</p>
                                    <p class="text-sm text-gray-500">{{ $pack->duration }} Days</p>
                                    <p class="text-2xl font-bold text-green-600 mt-2">{!! $currencySymbol !!}{{ number_format($pack->price, 2) }}</p>
                                    @if($selectedPackageId == $pack->id)
                                        <div class="absolute top-2 right-2 h-6 w-6 bg-green-500 rounded-full flex items-center justify-center text-white">
                                            <i class="fa fa-check"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                </div>

                {{-- Payment Summary --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-28 rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4">Order Summary</h2>
                        
                        <div class="space-y-4">
                            <h3 class="text-md font-semibold text-gray-700">Payment Method</h3>
                            <div class="space-y-3">
                                @foreach($paymentMethods as $method)
                                    <label class="flex items-center p-3 border rounded-lg cursor-pointer {{ $selectedPaymentMethod == $method['name'] ? 'bg-green-50 border-green-500' : 'border-gray-200' }}">
                                        <input type="radio" wire:model="selectedPaymentMethod" name="payment_type" value="{{ $method['name'] }}" class="h-4 w-4 text-green-600 border-gray-300 focus:ring-green-500">
                                        <span class="ml-3 font-medium text-gray-700">{{ $method['display_name'] }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="mt-6">
                            <label for="coupon_code" class="text-sm font-medium text-gray-700">Have a coupon?</label>
                            <div class="flex gap-2 mt-1">
                                <input type="text" wire:model.lazy="couponCode" id="coupon_code" class="block w-full border-gray-300 rounded-md shadow-sm" placeholder="Enter code">
                                <button type="button" wire:click="applyCoupon" class="px-4 py-2 bg-gray-200 text-sm font-semibold rounded-md hover:bg-gray-300">Apply</button>
                            </div>
                        </div>

                        <div class="mt-6 pt-6 border-t space-y-2">
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Subtotal</span>
                                <span class="font-semibold">{!! $currencySymbol !!}{{ number_format($packageAmount, 2) }}</span>
                            </div>
                            @if($discountAmount > 0)
                            <div class="flex justify-between items-center text-red-600">
                                <span>Discount</span>
                                <span class="font-semibold">- {!! $currencySymbol !!}{{ number_format($discountAmount, 2) }}</span>
                            </div>
                            @endif
                             @if($taxAmount > 0)
                            <div class="flex justify-between items-center text-gray-600">
                                <span>Tax</span>
                                <span class="font-semibold">{!! $currencySymbol !!}{{ number_format($taxAmount, 2) }}</span>
                            </div>
                            @endif
                            <div class="flex justify-between items-center text-2xl font-bold text-gray-800 mt-2 pt-4 border-t">
                                <span>Total</span>
                                <span>{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span>
                            </div>

                            <button wire:click="proceedToPayment" wire:loading.attr="disabled"
                                class="w-full mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700 transition-all disabled:opacity-50">
                                <span wire:loading.remove>Proceed to Pay</span>
                                <span wire:loading>Processing...</span>
                            </button>
                        </div>
                    </div>
                </div>
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
