<div>
    @push('styles')
        <style>
            .step-indicator { transition: all 0.3s ease; }
            .step-indicator.active { background-color: #16a74a; color: white; }
            .step-indicator.completed { background-color: #d1fae5; color: #065f46; }
            .preview-toggle button { transition: all 0.3s ease; flex: 1; }
            .preview-toggle button.active { background-color: white; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06); }
            .device-desktop { background: #e5e7eb; border-radius: 12px; padding: 20px; box-shadow: inset 0 2px 4px 0 rgba(0,0,0,0.05); max-width: 600px; margin: 0 auto; }
            .device-desktop-screen { background: #fff; border-radius: 6px; overflow: hidden; min-height: 300px; border: 1px solid #d1d5db; }
            .device-desktop-stand { width: 100px; height: 10px; background: #d1d5db; margin: 0 auto; margin-top: 10px; border-radius: 0 0 4px 4px; }
            .device-mobile { width: 320px; height: 640px; background: #1f2937; border-radius: 40px; padding: 12px; box-shadow: 0 20px 40px -10px rgba(0,0,0,0.3); margin: 0 auto; position: relative; }
            .device-mobile-screen { background: #fff; border-radius: 28px; width: 100%; height: 100%; overflow: hidden; position: relative; border: 2px solid #374151; }
            .device-mobile-notch { position: absolute; top: 0; left: 50%; transform: translateX(-50%); width: 100px; height: 20px; background: #1f2937; border-radius: 0 0 12px 12px; z-index: 10; }
            .StripeElement { box-sizing: border-box; height: 40px; padding: 10px 12px; border: 1px solid #d1d5db; border-radius: 8px; background-color: white; transition: box-shadow 150ms ease; }
            .StripeElement--focus { box-shadow: 0 1px 3px 0 #cfd7df; }
            .StripeElement--invalid { border-color: #fa755a; }
        </style>
    @endpush

    <div class="w-full bg-white border-b">
        <div class="container mx-auto px-4 py-8 text-center">
            <h1 class="text-3xl md:text-4xl font-bold text-gray-800">Create Your Advertisement</h1>
            <p class="text-gray-600 mt-2 max-w-2xl mx-auto">Follow the steps below to get your ad live on our platform.</p>
        </div>
    </div>

    <div class="container mx-auto px-4 py-12">
        
        <div class="flex items-center justify-center mb-12">
            <div class="flex items-center">
                <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold {{ $currentStep >= 1 ? ($currentStep > 1 ? 'completed' : 'active') : '' }}">1</div>
                <div class="h-0.5 w-16 {{ $currentStep > 1 ? 'bg-green-600' : 'bg-gray-300' }}"></div>
            </div>
            <div class="flex items-center">
                <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold {{ $currentStep >= 2 ? ($currentStep > 2 ? 'completed' : 'active') : '' }}">2</div>
                <div class="h-0.5 w-16 {{ $currentStep > 2 ? 'bg-green-600' : 'bg-gray-300' }}"></div>
            </div>
             <div class="flex items-center">
                <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold {{ $currentStep >= 3 ? ($currentStep > 3 ? 'completed' : 'active') : '' }}">3</div>
                <div class="h-0.5 w-16 {{ $currentStep > 3 ? 'bg-green-600' : 'bg-gray-300' }}"></div>
            </div>
            <div class="step-indicator flex items-center justify-center w-10 h-10 rounded-full font-bold {{ $currentStep === 4 ? 'active' : '' }}">✓</div>
        </div>

        <div x-data="{ currentStep: @entangle('currentStep') }">
            <div x-show="currentStep === 1" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" class="max-w-4xl mx-auto bg-white rounded-lg shadow-lg border p-8 space-y-6">
                <div>
                    <label class="block text-xl font-bold text-gray-800 mb-4">1. Choose Ad Placement</label>
                    <select wire:model="ad_zone_id" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                        <option value="">Select an ad zone</option>
                        @foreach($adZones as $zone)
                            <option value="{{ $zone->id }}">{{ $zone->name }} (Price: {!! $currencySymbol !!}{{ number_format($zone->price_per_day, 2) }}/day)</option>
                        @endforeach
                    </select>
                    @error('ad_zone_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>

                @if(!empty($templates))
                <div>
                    <label class="block text-xl font-bold text-gray-800 mb-4">2. Select a Template</label>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        @foreach($templates as $template)
                            <label class="border rounded-lg p-4 cursor-pointer transition-all {{ $ad_template_id == $template->id ? 'bg-green-50 border-green-500 ring-2 ring-green-500' : 'bg-white border-gray-200 hover:border-gray-400' }}">
                                <input type="radio" wire:model="ad_template_id" value="{{ $template->id }}" class="hidden">
                                <h4 class="font-semibold text-center mb-2">{{ $template->name }}</h4>
                                <div class="border rounded-md p-2 bg-gray-50 pointer-events-none scale-50 origin-top">
                                    {!! str_replace(['__IMAGE_URL__', '__HEADLINE__', '__SUBTITLE__', '__LINK__', '__CTA_TEXT__', '__LOGO_URL__', '__WIDTH__', '__HEIGHT__'], ['https://placehold.co/600x400/e2e8f0/e2e8f0?text=Preview', 'Template Preview', 'Subtitle preview', '#', 'Button', 'https://placehold.co/40x40', '100%', 'auto'], $template->html_content ?? '') !!}
                                </div>
                            </label>
                        @endforeach
                    </div>
                    @error('ad_template_id') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                </div>
                @endif

                <div class="text-right pt-4 border-t">
                    <button wire:click="goToStep(2)" type="button" class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg disabled:bg-gray-400" @if(!$ad_zone_id || !$ad_template_id) disabled @endif>
                        Next: Add Content &rarr;
                    </button>
                </div>
            </div>

            <div x-show="currentStep === 2" x-transition>
                 <form wire:submit.prevent="createAdvertisementAndProceedToPayment" class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                    <div class="lg:col-span-1 bg-white rounded-lg shadow-lg border p-8 space-y-6">
                        <h2 class="text-xl font-bold text-gray-800 mb-4">Ad Content & Duration</h2>
                        
                        @if(in_array('__HEADLINE__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Headline *</label>
                            <input type="text" wire:model.lazy="headline" placeholder="Ad Headline" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('headline') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(in_array('__SUBTITLE__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Subtitle (optional)</label>
                            <input type="text" wire:model.lazy="subtitle" placeholder="Ad Subtitle" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('subtitle') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(in_array('__LINK__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Destination URL *</label>
                            <input type="url" wire:model.lazy="link" placeholder="https://your-website.com" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('link') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(in_array('__CTA_TEXT__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Button Text *</label>
                            <input type="text" wire:model.lazy="cta_text" placeholder="Button Text (e.g., Shop Now)" class="w-full p-3 border border-gray-300 rounded-lg focus:ring-green-500 focus:border-green-500">
                            @error('cta_text') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(in_array('__IMAGE_URL__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Main Image *</label>
                            <input type="file" wire:model="image" class="mt-1 block w-full text-sm border border-gray-300 rounded-lg p-2">
                            @if ($image && !$errors->has('image'))
                                <img src="{{ asset('storage/livewire-tmp/' . $image->getFilename()) }}" class="mt-4 h-24 rounded-lg border object-cover">
                            @endif
                            @error('image') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif

                        @if(in_array('__LOGO_URL__', $templatePlaceholders))
                        <div>
                            <label class="block text-sm font-medium mb-1">Your Logo (optional)</label>
                            <input type="file" wire:model="logo" class="mt-1 block w-full text-sm border border-gray-300 rounded-lg p-2">
                            @if ($logo && !$errors->has('logo'))
                                <img src="{{ asset('storage/livewire-tmp/' . $logo->getFilename()) }}" class="mt-4 h-16 w-16 rounded-full border object-cover">
                            @endif
                            @error('logo') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                        </div>
                        @endif
                    </div>
                    
                    <div class="lg:col-span-1">
                        <div class="sticky top-28" x-data="{ view: 'desktop' }">
                             <div class="bg-white rounded-lg shadow-lg border p-6">
                                <h3 class="text-lg font-bold text-gray-800 mb-4">Live Preview</h3>
                                <div class="flex justify-center gap-2 mb-4 bg-gray-200 p-1 rounded-lg preview-toggle">
                                    <button type="button" @click="view = 'desktop'" class="px-4 py-2 rounded-md text-sm font-semibold transition-colors" :class="view === 'desktop' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-600 hover:text-gray-800'">Desktop</button>
                                    <button type="button" @click="view = 'mobile'" class="px-4 py-2 rounded-md text-sm font-semibold transition-colors" :class="view === 'mobile' ? 'bg-white text-gray-800 shadow-sm' : 'text-gray-600 hover:text-gray-800'">Mobile</button>
                                </div>
                                <div x-show="view === 'desktop'" x-transition class="transition-all duration-300">
                                    <div class="device-desktop mx-auto">
                                        <div class="device-desktop-screen"><div class="w-full" x-html="$wire.previewHtml"></div></div>
                                    </div>
                                    <div class="device-desktop-stand"></div>
                                </div>
                                <div x-show="view === 'mobile'" x-transition class="transition-all duration-300" style="display: none;">
                                    <div class="device-mobile mx-auto">
                                        <div class="device-mobile-screen"><div class="device-mobile-notch"></div><div class="w-full h-full overflow-y-auto">{!! $previewHtml !!}</div></div>
                                    </div>
                                </div>
                             </div>
                             <div class="mt-6 bg-white rounded-lg shadow-lg border p-6">
                                <h2 class="text-xl font-bold text-gray-800 mb-4 border-b pb-4">Order Summary</h2>
                                <div class="space-y-4">
                                     <div class="grid grid-cols-2 gap-6">
                                        <div><label class="block text-sm font-medium mb-1">Start Date *</label><input type="date" wire:model="start_date" class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">@error('start_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror</div>
                                        <div><label class="block text-sm font-medium mb-1">End Date *</label><input type="date" wire:model="end_date" class="mt-1 w-full p-2 border border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500">@error('end_date') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror</div>
                                    </div>
                                    <div class="pt-4 border-t space-y-2">
                                        <div class="flex justify-between text-gray-600"><span>Price per day:</span> <span class="font-semibold">{!! $currencySymbol !!}{{ number_format($pricePerDay, 2) }}</span></div>
                                        <div class="flex justify-between text-gray-600"><span>Live Days:</span> <span class="font-semibold">{{ $liveDays }}</span></div>
                                        <div class="flex justify-between text-2xl font-bold text-gray-800 mt-4 pt-4 border-t"><span>Total Amount:</span> <span class="text-green-600">{!! $currencySymbol !!}{{ number_format($totalAmount, 2) }}</span></div>
                                    </div>
                                    <div class="mt-6">
                                        <h3 class="text-md font-semibold text-gray-700 mb-3">Payment Method *</h3>
                                        <div class="space-y-3">
                                            @foreach($paymentMethods as $method)
                                                <label class="flex items-center p-3 border rounded-lg cursor-pointer transition-colors {{ $selectedPaymentMethod == $method['name'] ? 'bg-green-50 border-green-500 ring-1 ring-green-500' : 'border-gray-200 hover:border-gray-400' }}">
                                                    <input type="radio" wire:model="selectedPaymentMethod" name="payment_type" value="{{ $method['name'] }}" class="h-4 w-4 text-green-600 focus:ring-green-500">
                                                    <span class="ml-3 font-medium text-gray-700">{{ $method['display_name'] }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                        @error('selectedPaymentMethod') <span class="text-red-500 text-sm mt-1">{{ $message }}</span> @enderror
                                    </div>
                                </div>
                                <div class="flex justify-between mt-8 pt-6 border-t">
                                     <button type="button" wire:click="goToStep(1)" class="bg-gray-300 text-gray-800 font-bold py-3 px-6 rounded-lg hover:bg-gray-400 transition-colors">&larr; Back</button>
                                     <button type="submit" class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">Confirm & Proceed to Payment</button>
                                </div>
                             </div>
                        </div>
                    </div>
                </form>
            </div>

            <div x-show="currentStep === 3" x-transition>
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8" wire:ignore>
                    <h1 class="text-2xl font-bold text-gray-800 mb-2">Complete Your Payment</h1>
                    <p class="text-gray-600 mb-6">Total Amount: 
                        <span id="payment-amount" class="font-bold text-green-600 text-xl">
                           {!! $currencySymbol !!}{{ $paymentAmountDisplay }}
                        </span>
                    </p>
                    {{-- === YEH MUKAMMAL FIX HAI: @if ke block ko theek kar diya gaya hai === --}}
                    @if(!empty($stripeError))
                        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded mb-4">{{ $stripeError }}</div>
                    @endif
                    <div id="payment-element" class="mb-6"></div>
                    <button type="button" id="submit-payment" class="w-full bg-green-600 text-white font-bold py-3 px-4 rounded-lg hover:bg-green-700 transition-colors">
                        Pay {!! $currencySymbol !!}{{ $paymentAmountDisplay }} Now
                    </button>
                    <div id="payment-message" class="hidden text-red-500 text-sm mt-2"></div>
                    <div class="mt-6 text-center">
                        <button wire:click="goToStep(2)" class="text-gray-600 hover:text-gray-800 underline text-sm transition-colors">&larr; Back to ad details</button>
                    </div>
                </div>
            </div>
            
            <div x-show="currentStep === 4" x-transition>
                <div class="max-w-md mx-auto bg-white rounded-lg shadow-lg p-8 text-center">
                    <svg class="w-16 h-16 mx-auto text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    <h1 class="text-2xl font-bold text-gray-800 mt-4">Payment Successful!</h1>
                    <p class="text-gray-600 mt-2">Your advertisement has been submitted and will be reviewed shortly.</p>
                    <div class="mt-8">
                        <a href="/my-advertisements" class="bg-green-600 text-white font-bold py-3 px-6 rounded-lg hover:bg-green-700 transition-colors">
                            View My Advertisements
                        </a>
                    </div>
                </div>
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
        <script src="https://js.stripe.com/v3/"></script>
        <script>
            document.addEventListener('livewire:load', function () {
                let stripe = Stripe('{{ config("services.stripe.key") }}');
                let elements = null;
                let payButtonText = 'Pay Now';

                function decodeHtml(html) {
                    const txt = document.createElement("textarea");
                    txt.innerHTML = html;
                    return txt.value;
                }

                window.addEventListener('stripe-init', function(event) {
                    const clientSecret = event.detail.clientSecret;
                    const amountDisplay = event.detail.amountDisplay || '0.00'; // Add fallback
                    const currencySymbol = event.detail.currencySymbol || '$'; // Add fallback

                    const decodedSymbol = decodeHtml(currencySymbol);
                    payButtonText = `Pay ${decodedSymbol}${amountDisplay} Now`;

                    const totalAmountDisplay = document.getElementById('payment-amount');
                    if(totalAmountDisplay) {
                        totalAmountDisplay.innerHTML = `${decodedSymbol}${amountDisplay}`;
                    }

                    // Clear previous elements if they exist
                    if (elements) {
                        const paymentElement = document.getElementById('payment-element');
                        if (paymentElement) {
                            paymentElement.innerHTML = '';
                        }
                    }

                    elements = stripe.elements({
                        clientSecret: clientSecret,
                        appearance: { theme: 'stripe' },
                    });

                    const paymentElement = elements.create('payment');
                    paymentElement.mount('#payment-element');

                    const submitButton = document.getElementById('submit-payment');
                    const messageContainer = document.getElementById('payment-message');

                    if (submitButton) {
                        submitButton.textContent = payButtonText;

                        // Remove any existing event listeners by cloning the button
                        const newButton = submitButton.cloneNode(true);
                        submitButton.parentNode.replaceChild(newButton, submitButton);

                        newButton.addEventListener('click', async (e) => {
                            e.preventDefault();
                            setLoading(true, newButton);

                            const { error, paymentIntent } = await stripe.confirmPayment({
                                elements,
                                confirmParams: {
                                    return_url: "{{ url('/payment/success') }}", 
                                },
                                redirect: 'if_required' 
                            });

                            if (error) {
                                if (messageContainer) {
                                    messageContainer.textContent = error.message;
                                    messageContainer.classList.remove('hidden');
                                }
                                setLoading(false, newButton);
                            } else if (paymentIntent && paymentIntent.status === 'succeeded') {
                                if (messageContainer) {
                                    messageContainer.textContent = 'Payment successful! Finalizing...';
                                    messageContainer.classList.remove('hidden');
                                }
                                @this.call('handlePaymentSuccess', paymentIntent.id);
                            } else {
                                if (messageContainer) {
                                    messageContainer.textContent = 'Your payment is processing...';
                                    messageContainer.classList.remove('hidden');
                                }
                            }
                        });
                    }
                });

                function setLoading(isLoading, button) {
                    if (isLoading) {
                        button.disabled = true;
                        button.innerHTML = '<i class="fa fa-spinner fa-spin"></i> Processing...';
                    } else {
                        button.disabled = false;
                        button.textContent = payButtonText;
                    }
                }
            });
        </script>
</div>

