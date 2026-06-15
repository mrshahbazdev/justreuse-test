<div>
    {{-- Header Section --}}
    <div class="w-full float-left border-b border-gray-200">
        <div class="m-auto container px-4">
            <h1 class="text-2xl md:text-3xl font-bold text-gray-800 py-8">Buy Business Packs</h1>
        </div>
    </div>

    {{-- Main Content Area --}}
    <div class="w-full float-left py-6">
        <div class="container mx-auto px-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                {{-- Packages Selection --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Top Ad Packs --}}
                    @if(count($topAdPacks) > 0)
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Top Ad Packs</h2>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($topAdPacks as $pack)
                                <label wire:key="top-pack-{{ $pack['id'] }}" class="relative block p-6 border rounded-lg cursor-pointer transition-all {{ in_array($pack['id'], $selectedPacks) ? 'bg-green-50 border-green-500 ring-2 ring-green-500' : 'bg-white border-gray-200 hover:border-gray-400' }}">
                                    <input type="checkbox" wire:model="selectedPacks" value="{{ $pack['id'] }}" class="hidden">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-lg font-semibold text-gray-800">{{ $pack['bulk_limit'] }} Ads</p>
                                            <p class="text-sm text-gray-500">Top Ad placement</p>
                                        </div>
                                        <p class="text-2xl font-bold text-green-600">{!! $currencySymbol !!}{{ number_format($pack['price'], 2) }}</p>
                                    </div>
                                    @if(in_array($pack['id'], $selectedPacks))
                                        <div class="absolute top-2 right-2 h-6 w-6 bg-green-500 rounded-full flex items-center justify-center text-white">
                                            <i class="fa fa-check"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif

                    {{-- Feature Ad Packs --}}
                    @if(count($featureAdPacks) > 0)
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Feature Ad Packs</h2>
                         <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($featureAdPacks as $pack)
                                 <label wire:key="feature-pack-{{ $pack['id'] }}" class="relative block p-6 border rounded-lg cursor-pointer transition-all {{ in_array($pack['id'], $selectedPacks) ? 'bg-green-50 border-green-500 ring-2 ring-green-500' : 'bg-white border-gray-200 hover:border-gray-400' }}">
                                    <input type="checkbox" wire:model="selectedPacks" value="{{ $pack['id'] }}" class="hidden">
                                    <div class="flex justify-between items-center">
                                        <div>
                                            <p class="text-lg font-semibold text-gray-800">{{ $pack['bulk_limit'] }} Ads</p>
                                            <p class="text-sm text-gray-500">Featured placement</p>
                                        </div>
                                        <p class="text-2xl font-bold text-green-600">{!! $currencySymbol !!}{{ number_format($pack['price'], 2) }}</p>
                                    </div>
                                    @if(in_array($pack['id'], $selectedPacks))
                                        <div class="absolute top-2 right-2 h-6 w-6 bg-green-500 rounded-full flex items-center justify-center text-white">
                                            <i class="fa fa-check"></i>
                                        </div>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>

                {{-- Payment Summary --}}
                <div class="lg:col-span-1">
                    <div class="sticky top-28 bg-white rounded-lg shadow-sm border border-gray-200 p-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4">Order Summary</h2>
                        
                        @if(empty($selectedPacks))
                            <div class="text-center py-8">
                                <p class="text-gray-500">Please select a package to continue.</p>
                            </div>
                        @else
                            <div wire:loading.class="opacity-50" class="transition-opacity">
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
                                <div class="mt-6 pt-6 border-t">
                                    <div class="flex justify-between items-center text-gray-600">
                                        <span>Total Packs</span>
                                        <span class="font-semibold">{{ count($selectedPacks) }}</span>
                                    </div>
                                    <div class="flex justify-between items-center text-2xl font-bold text-gray-800 mt-2">
                                        <span>Total Amount</span>
                                        <span>{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span>
                                    </div>

                                    <button wire:click="proceedToPayment" wire:loading.attr="disabled"
                                        class="w-full mt-6 inline-flex items-center justify-center gap-2 rounded-lg bg-green-600 px-6 py-3 font-semibold text-white hover:bg-green-700 transition-all">
                                        <span wire:loading.remove wire:target="proceedToPayment">Proceed to Pay</span>
                                        <span wire:loading wire:target="proceedToPayment">Processing...</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
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
