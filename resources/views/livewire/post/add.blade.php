<div>
    <div class="pa-shell">
        {{-- ===== PROGRESS HEADER ===== --}}
        <div class="pa-progress-bar">
            <div class="pa-step-item {{ $currentStep >= 1 ? 'active' : '' }}">
                <div class="pa-step-circle">1</div>
                <span>Basic Info</span>
            </div>
            <div class="pa-step-line {{ $currentStep >= 2 ? 'filled' : '' }}"></div>
            <div class="pa-step-item {{ $currentStep >= 2 ? 'active' : '' }}">
                <div class="pa-step-circle">2</div>
                <span>Details & Submit</span>
            </div>
        </div>

        @if (session()->has('message'))
            <div class="pa-alert pa-alert-success">
                <i class="fa-solid fa-check-circle"></i>
                {{ session('message') }}
            </div>
        @endif

        {{-- ===================== STEP 1 ===================== --}}
        @if($currentStep == 1)
        <div id="step-1">
            {{-- Category Card --}}
            <div class="pa-card">
                <div class="pa-card-header">
                    <i class="fa-solid fa-layer-group"></i>
                    <h2>Choose Category</h2>
                </div>

                <select wire:model="selectedParentCategory" class="pa-select" required>
                    <option value="">Select a Category</option>
                    @foreach($parentCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>

                @if(!empty($childCategories) && $selectedParentCategory)
                    <div class="pa-sub-label">What are you selling in <strong>{{ $selectedParentCategoryTitle }}</strong>?</div>
                    <div class="pa-chips">
                        @foreach($childCategories as $child)
                            <button
                                class="pa-chip {{ $selectedChildCategory == $child->id ? 'active' : '' }}"
                                type="button"
                                wire:click="selectChildCategory({{ $child->id }})"
                                wire:key="category-{{ $child->id }}">
                                <img src="{{ asset('storage/' . $child->image) }}"
                                     alt="{{ $child->title }}"
                                     class="pa-chip-img">
                                {{ $child->title }}
                            </button>
                        @endforeach
                    </div>

                    @if($selectedChildCategory)
                        {{-- Loading --}}
                        <div wire:loading wire:target="selectChildCategory" class="pa-loading">
                            <div class="pa-spinner"></div>
                            <p>Loading fields...</p>
                        </div>

                        <div wire:loading.remove wire:target="selectChildCategory">
                            {{-- Product Title --}}
                            <div class="pa-card" style="margin-top:20px;">
                                <div class="pa-card-header">
                                    <i class="fa-solid fa-tag"></i>
                                    <h2>Product Title</h2>
                                </div>
                                <input class="pa-input" type="text" placeholder="e.g. iPhone 14 Pro Max 256GB" wire:model.defer="title" required />
                                @error('title') <span class="pa-error">{{ $message }}</span> @enderror
                            </div>

                            {{-- Custom Fields --}}
                            @if(!empty($customFields) && count($customFields) > 0)
                            <div class="pa-card" style="margin-top:20px;" wire:key="custom-fields-wrapper-{{ $selectedChildCategory }}">
                                <div class="pa-card-header">
                                    <i class="fa-solid fa-sliders"></i>
                                    <h2>Additional Details</h2>
                                </div>
                                <div class="pa-fields-grid">
                                    @foreach($customFields as $field)
                                        @php
                                            $fieldName = $field->id . '_' . $field->form_field_name;
                                            $fieldWireModel = 'customFieldsData.' . $fieldName;
                                            $isRequired = $field->required != '0';
                                        @endphp

                                        {{-- Text, Number, Date --}}
                                        @if(in_array($field->type, ['text', 'number', 'date']))
                                            <div class="pa-field" wire:key="field-{{ $field->id }}">
                                                <label class="pa-label">{{ $field->name }} @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <input type="{{ $field->type }}"
                                                    wire:model.defer="{{ $fieldWireModel }}"
                                                    {{ $isRequired ? 'required' : '' }}
                                                    class="pa-input" />
                                                @error($fieldWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        {{-- Textarea --}}
                                        @if($field->type == 'textarea')
                                            <div class="pa-field pa-field-full" wire:key="field-{{ $field->id }}">
                                                <label class="pa-label">{{ $field->name }} @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <textarea wire:model.defer="{{ $fieldWireModel }}"
                                                    {{ $isRequired ? 'required' : '' }}
                                                    rows="3" class="pa-textarea"></textarea>
                                                @error($fieldWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        {{-- Brand with Model --}}
                                        @if($field->form_field_name === 'brandwithmodel')
                                            @php
                                                $brandOptions = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')->orderBy('key', 'asc')->get();
                                            @endphp
                                            <div class="pa-field" wire:key="field-{{ $field->id }}">
                                                <label class="pa-label">{{ $field->name }} @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <select wire:model="{{ $fieldWireModel }}" {{ $isRequired ? 'required' : '' }} class="pa-select">
                                                    <option value="">Select Brand</option>
                                                    @foreach($brandOptions as $option)
                                                        <option value="{{ $option->id }}">{{ $option->key }}</option>
                                                    @endforeach
                                                </select>
                                                @error($fieldWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                            @php
                                                $modelFieldName = $field->id . '_brandswithmodels';
                                                $modelWireModel = 'customFieldsData.' . $modelFieldName;
                                            @endphp
                                            <div class="pa-field" wire:key="field-models-{{ $field->id }}">
                                                <label class="pa-label">Models @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <select wire:model.defer="{{ $modelWireModel }}" {{ $isRequired ? 'required' : '' }} class="pa-select">
                                                    @if(isset($dynamicModels[$field->id]) && !empty($dynamicModels[$field->id]))
                                                        <option value="">Select Model</option>
                                                        @foreach($dynamicModels[$field->id] as $model)
                                                            <option value="{{ strtolower(str_replace(' ', '-', $model)) }}">{{ $model }}</option>
                                                        @endforeach
                                                    @else
                                                        <option value="">Select brand first</option>
                                                    @endif
                                                </select>
                                                @error($modelWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        {{-- Select, Autocomplete, Radio Group --}}
                                        @if(in_array($field->type, ['select', 'autocomplete', 'radio-group']) && $field->form_field_name !== 'brandwithmodel')
                                            @php
                                                $options = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')->get();
                                            @endphp
                                            <div class="pa-field" wire:key="field-{{ $field->id }}">
                                                <label class="pa-label">{{ $field->name }} @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <select wire:model.defer="{{ $fieldWireModel }}" {{ $isRequired ? 'required' : '' }} class="pa-select">
                                                    <option value="">Select {{ $field->name }}</option>
                                                    @foreach($options as $option)
                                                        <option value="{{ $option->value }}">{{ $option->key }}</option>
                                                    @endforeach
                                                </select>
                                                @error($fieldWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                        @endif

                                        {{-- Checkbox Group --}}
                                        @if($field->type == 'checkbox-group')
                                            @php
                                                $options = \App\Models\TblFieldsOption::where('form_field_name', $field->form_field_name)
                                                    ->where('active', '1')->get();
                                            @endphp
                                            <div class="pa-field pa-field-full" wire:key="field-{{ $field->id }}">
                                                <label class="pa-label">{{ $field->name }} @if($isRequired)<span class="pa-req">*</span>@endif</label>
                                                <div class="pa-option-chips">
                                                    @foreach($options as $option)
                                                        <button type="button"
                                                            wire:click="$set('{{ $fieldWireModel }}', '{{ $option->value }}')"
                                                            class="pa-option-chip {{ isset($customFieldsData[$fieldName]) && $customFieldsData[$fieldName] == $option->value ? 'active' : '' }}">
                                                            {{ $option->key }}
                                                        </button>
                                                    @endforeach
                                                </div>
                                                @error($fieldWireModel) <span class="pa-error">{{ $message }}</span> @enderror
                                            </div>
                                        @endif
                                    @endforeach
                                </div>
                            </div>
                            @endif

                            {{-- Product Condition --}}
                            @if(!empty($productConditionHtml))
                            <div class="pa-card" style="margin-top:20px;" wire:key="product-condition-{{ $selectedChildCategory }}">
                                {!! $productConditionHtml !!}
                            </div>
                            @endif

                            {{-- Continue Button --}}
                            <div class="pa-actions">
                                <button class="pa-btn pa-btn-primary pa-btn-lg" type="button" wire:click="nextStep" wire:loading.attr="disabled">
                                    <span wire:loading.remove wire:target="nextStep">Continue <i class="fa-solid fa-arrow-right" style="margin-left:8px;"></i></span>
                                    <span wire:loading wire:target="nextStep"><i class="fa-solid fa-spinner fa-spin"></i> Loading...</span>
                                </button>
                            </div>
                        </div>
                    @else
                        <div class="pa-empty-state">
                            <i class="fa-solid fa-hand-pointer"></i>
                            <p>Select a sub-category to continue</p>
                        </div>
                    @endif
                @else
                    <div class="pa-empty-state">
                        <i class="fa-solid fa-list"></i>
                        <p>Select a main category first</p>
                    </div>
                @endif
            </div>
        </div>
        @endif

        {{-- ===================== STEP 2 ===================== --}}
        @if($currentStep == 2)
        <div id="step-2">

            {{-- Price & Location Card --}}
            <div class="pa-card">
                <div class="pa-card-header">
                    <i class="fa-solid fa-coins"></i>
                    <h2>Price & Location</h2>
                </div>

                <div class="pa-row">
                    <div class="pa-field">
                        <label class="pa-label">Price <span class="pa-req">*</span></label>
                        <div class="pa-price-group">
                            <select wire:model="currency_id" class="pa-currency-select">
                                @foreach($currencies as $currency)
                                    <option value="{{ $currency->id }}">{!! $currency->short_code . " (" . $currency->currency_hex . ")" !!}</option>
                                @endforeach
                            </select>
                            <input type="number" class="pa-price-input" wire:model="price" min="0" placeholder="0.00" required>
                        </div>
                        @error('price') <span class="pa-error">{{ $message }}</span> @enderror
                    </div>

                    <div class="pa-field">
                        <label class="pa-label">Location <span class="pa-req">*</span></label>
                        <input type="text" id="searchTextField" class="pa-input" wire:model="location" placeholder="Start typing your address..." required>
                        @error('location') <span class="pa-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                <div class="pa-row">
                    <div class="pa-field">
                        <label class="pa-label">City <span class="pa-req">*</span></label>
                        <input type="text" class="pa-input pa-input-readonly" wire:model="text_city_sst" placeholder="Auto-filled from location" readonly>
                        @error('text_city_sst') <span class="pa-error">{{ $message }}</span> @enderror
                    </div>
                    <div class="pa-field">
                        <label class="pa-label">Country <span class="pa-req">*</span></label>
                        <input type="text" class="pa-input pa-input-readonly" wire:model="text_country_sst" placeholder="Auto-filled from location" readonly>
                        @error('text_country_sst') <span class="pa-error">{{ $message }}</span> @enderror
                    </div>
                </div>

                {{-- Hidden Location Fields --}}
                <input type="hidden" wire:model="city_name">
                <input type="hidden" wire:model="main_city_name">
                <input type="hidden" wire:model="city_lat">
                <input type="hidden" wire:model="city_lag">
                <input type="hidden" wire:model="country_long">
                <input type="hidden" wire:model="country_short">
                <input type="hidden" wire:model="state_long">
                <input type="hidden" wire:model="state_short">
            </div>

            {{-- Options Card --}}
            <div class="pa-card" style="margin-top:20px;">
                <div class="pa-card-header">
                    <i class="fa-solid fa-gear"></i>
                    <h2>Options</h2>
                </div>
                <div class="pa-toggles">
                    <label class="pa-toggle-row">
                        <span class="pa-toggle-label">
                            <i class="fa-solid fa-repeat"></i> Exchange to Buy
                        </span>
                        <div class="pa-toggle-wrap">
                            <input type="checkbox" class="sr-only" wire:model="exchangeToBuy" value="1" id="Products_exchangeToBuy">
                            <div class="pa-toggle-track"></div>
                            <div class="pa-toggle-thumb"></div>
                        </div>
                    </label>
                    <label class="pa-toggle-row">
                        <span class="pa-toggle-label">
                            <i class="fa-solid fa-bolt"></i> Instant Buy
                        </span>
                        <div class="pa-toggle-wrap">
                            <input type="checkbox" class="sr-only" wire:model="InstantBuy" value="1" id="Products_InstantBuy">
                            <div class="pa-toggle-track"></div>
                            <div class="pa-toggle-thumb"></div>
                        </div>
                    </label>
                    <label class="pa-toggle-row">
                        <span class="pa-toggle-label">
                            <i class="fa-solid fa-lock"></i> Fixed Price
                        </span>
                        <div class="pa-toggle-wrap">
                            <input type="checkbox" class="sr-only" wire:model="FixedPrice" value="1" id="Products_FixedPrice">
                            <div class="pa-toggle-track"></div>
                            <div class="pa-toggle-thumb"></div>
                        </div>
                    </label>
                </div>

                @if($showShippingFee)
                <div class="pa-field" style="margin-top:16px;">
                    <label class="pa-label">Shipping Cost</label>
                    <input type="number" placeholder="0.00" class="pa-input" wire:model="shipping_fee" min="0" step="0.01">
                </div>
                @endif
            </div>

            {{-- Description Card --}}
            <div class="pa-card" style="margin-top:20px;">
                <div class="pa-card-header">
                    <i class="fa-solid fa-align-left"></i>
                    <h2>Description</h2>
                </div>
                <textarea class="pa-textarea" wire:model="description" placeholder="Describe your product in detail — condition, features, what's included..." required></textarea>
                @error('description') <span class="pa-error">{{ $message }}</span> @enderror
            </div>

            {{-- Video URL Card --}}
            <div class="pa-card" style="margin-top:20px;">
                <div class="pa-card-header">
                    <i class="fa-brands fa-youtube"></i>
                    <h2>Product Video <span style="font-weight:400;font-size:13px;color:#9ca3af;">(Optional)</span></h2>
                </div>
                <input type="text" placeholder="https://www.youtube.com/embed/..." class="pa-input" wire:model="video_url">
            </div>

            {{-- Images & Package Row --}}
            <div class="pa-two-col" style="margin-top:20px;">
                {{-- Image Upload --}}
                <div class="pa-card">
                    <div class="pa-card-header">
                        <i class="fa-solid fa-images"></i>
                        <h2>Product Images</h2>
                    </div>

                    <div class="pa-upload-zone">
                        <input type="file" wire:model="images" multiple accept="image/*" class="pa-upload-input" id="image-upload">
                        <label for="image-upload" class="pa-upload-label">
                            <i class="fa-solid fa-cloud-arrow-up"></i>
                            <strong>Click to upload</strong>
                            <span>or drag and drop (Max 5MB)</span>
                        </label>
                    </div>

                    <div wire:loading wire:target="images" class="pa-upload-progress">
                        <i class="fa-solid fa-spinner fa-spin"></i> Uploading images...
                    </div>

                    @if ($uploadedImages && count($uploadedImages) > 0)
                        <div class="pa-image-grid" style="margin-top:16px;">
                            @foreach($uploadedImages as $index => $image)
                                <div data-index="{{ $index }}"
                                    class="pa-image-item"
                                    wire:key="uploaded-image-{{ $index }}-{{ $image->getFilename() }}">
                                    <img src="{{ asset('storage/livewire-tmp/' . $image->getFilename()) }}" alt="Product image">
                                    @if ($index === 0)
                                        <span class="pa-cover-badge">Cover</span>
                                    @endif
                                    <span class="pa-image-num">#{{ $index + 1 }}</span>
                                    <button type="button"
                                        wire:click.prevent="removeImage({{ $index }})"
                                        wire:confirm="Are you sure you want to remove this image?"
                                        wire:loading.attr="disabled"
                                        class="pa-image-remove">&times;</button>
                                    <div class="drag-handle pa-drag-handle">
                                        <i class="fa-solid fa-grip-vertical"></i>
                                    </div>
                                    <div wire:loading wire:target="removeImage({{ $index }})" class="pa-image-overlay">Removing...</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="pa-no-images">
                            <i class="fa-regular fa-image"></i>
                            <p>No images uploaded yet</p>
                        </div>
                    @endif

                    @error('images') <span class="pa-error">{{ $message }}</span> @enderror
                </div>

                {{-- Package Selection --}}
                <div class="pa-card">
                    <div class="pa-card-header">
                        <i class="fa-solid fa-cube"></i>
                        <h2>Package</h2>
                    </div>
                    <p class="pa-muted" style="margin-bottom:16px;">Choose your advertisement package</p>

                    @if(!empty($packagesList) && !empty($payment_methods))
                        <div class="pa-packages">
                            @foreach($packagesList as $pk_index => $package)
                                <label for="package_{{ $pk_index }}" class="pa-package {{ $package_type == $package->id ? 'active' : '' }}">
                                    <input type="radio" name="package_type" id="package_{{ $pk_index }}"
                                        class="sr-only" wire:model="package_type" value="{{ $package->id }}"
                                        wire:change="$set('showPaymentMethods', {{ $package->short_name == 'free' ? 'false' : 'true' }})">
                                    <div class="pa-package-radio">
                                        <div class="pa-package-dot"></div>
                                    </div>
                                    <div class="pa-package-info">
                                        <strong>{{ $package->name }}</strong>
                                        <span>
                                            <?php $pack_currency = App\Models\Setting::get_admin_default_currency(); ?>
                                            {!! $pack_currency['currency_hex'] !!} {{ number_format($package->price, 2) }}
                                        </span>
                                    </div>
                                    @if($package->short_name == 'free')
                                        <span class="pa-badge pa-badge-free tooltip-container">Free
                                            <span class="tooltip-text">Duration: {{ $package->duration }} days, Limit: {{ $package->single_pack_limit }} Ad</span>
                                        </span>
                                    @else
                                        <span class="pa-badge pa-badge-pro tooltip-container">Upgrade
                                            <span class="tooltip-text">Duration: {{ $package->duration }} days, Limit: 1 Ad</span>
                                        </span>
                                    @endif
                                </label>
                            @endforeach
                        </div>
                    @endif
                    @error('package_type') <span class="pa-error">{{ $message }}</span> @enderror

                    {{-- Payment Methods --}}
                    @if($showPaymentMethods)
                    <div style="margin-top:20px;" id="payment-section">
                        <label class="pa-label">Payment Method <span class="pa-req">*</span></label>
                        <div class="pa-payment-methods">
                            @foreach($payment_methods as $index => $p)
                                <label class="pa-payment-option {{ $payment_type == $p['name'] ? 'active' : '' }}">
                                    <input type="radio" class="sr-only" name="payment_type" wire:model="payment_type" value="{{ $p['name'] }}" required>
                                    <div class="pa-package-radio">
                                        <div class="pa-package-dot"></div>
                                    </div>
                                    <span>{{ $p['display_name'] }}</span>
                                </label>
                            @endforeach
                        </div>
                        @error('payment_type') <span class="pa-error">{{ $message }}</span> @enderror
                    </div>
                    @endif
                </div>
            </div>

            {{-- Action Buttons --}}
            <div class="pa-actions pa-actions-split">
                <button class="pa-btn pa-btn-secondary" type="button" wire:click="previousStep" wire:loading.attr="disabled">
                    <i class="fa-solid fa-arrow-left" style="margin-right:6px;"></i> Back
                </button>
                <button class="pa-btn pa-btn-primary pa-btn-lg" type="button" wire:click="submit" wire:loading.attr="disabled">
                    <span wire:loading.remove wire:target="submit"><i class="fa-solid fa-paper-plane" style="margin-right:8px;"></i> Submit Post</span>
                    <span wire:loading wire:target="submit"><i class="fa-solid fa-spinner fa-spin"></i> Submitting...</span>
                </button>
            </div>
        </div>
        @endif
    </div>

    {{-- Google Maps Autocomplete Script --}}
    <script>
        function initMap() {
            var input = document.getElementById('searchTextField');
            if (!input) { setTimeout(initMap, 500); return; }
            var autocomplete = new google.maps.places.Autocomplete(input);
            autocomplete.addListener('place_changed', function() {
                var place = autocomplete.getPlace();
                if (!place.address_components) return;
                const components = place.address_components;
                const country = components.find(item => item.types.includes('country'));
                const state = components.find(item => item.types.includes('administrative_area_level_1'));
                const mainCity = components.find(item => item.types.includes('locality'));
                const localArea = components.find(item => item.types.includes('sublocality')) ||
                                  components.find(item => item.types.includes('administrative_area_level_2'));
                @this.set('city_name', localArea ? localArea.long_name : (mainCity ? mainCity.long_name : ''));
                @this.set('main_city_name', mainCity ? mainCity.long_name : (state ? state.long_name : ''));
                @this.set('text_city_sst', mainCity ? mainCity.long_name : (state ? state.long_name : ''));
                @this.set('text_country_sst', country ? country.long_name : '');
                if (place.geometry && place.geometry.location) {
                    @this.set('city_lat', place.geometry.location.lat());
                    @this.set('city_lag', place.geometry.location.lng());
                }
                if (country) { @this.set('country_long', country.long_name); @this.set('country_short', country.short_name); }
                if (state) { @this.set('state_long', state.long_name); @this.set('state_short', state.short_name); }
            });
        }
        document.addEventListener('livewire:load', function() {
            if (!document.querySelector('script[src*="maps.googleapis.com"]')) {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=AIzaSyDH2sK6kRKk4kd4Z0wfA0rsY8RQwUPJYq8&libraries=places&callback=initMap';
                script.async = true; script.defer = true;
                document.head.appendChild(script);
            } else { initMap(); }
        });
        document.addEventListener('livewire:update', function() { setTimeout(initMap, 100); });
        Livewire.on('categorySelected', (data) => { console.log('Category selected:', data.categoryId); });
    </script>

<style>
    /* ===== VARIABLES ===== */
    :root {
        --pa-brand: #f3981a;
        --pa-brand-dark: #e68a07;
        --pa-green: #16a34a;
        --pa-green-dark: #15803d;
        --pa-ink: #0f172a;
        --pa-muted: #6b7280;
        --pa-line: #e5e7eb;
        --pa-bg: #f9fafb;
        --pa-white: #ffffff;
        --pa-radius: 12px;
        --pa-radius-lg: 16px;
    }

    /* ===== LAYOUT FIX — push footer to bottom ===== */
    .pa-shell *, .pa-shell *::before, .pa-shell *::after { box-sizing: border-box; }

    body {
        overflow-x: hidden;
        min-height: 100vh !important;
        display: flex !important;
        flex-direction: column !important;
        margin: 0 !important;
        background: var(--pa-bg) !important;
    }
    .page-wrapper {
        flex-grow: 1 !important;
        display: flex !important;
        flex-direction: column !important;
        min-height: 100vh !important;
    }
    .page-wrapper > main,
    .page-wrapper > div:not(footer):not(:first-child) {
        flex-grow: 1 !important;
    }
    footer {
        flex-shrink: 0 !important;
        width: 100% !important;
        background: var(--pa-bg) !important;
        border-top-color: #e5e7eb !important;
    }

    /* ===== SHELL ===== */
    .pa-shell {
        max-width: 880px;
        margin: 0 auto;
        padding: 24px 16px 60px;
        font-family: 'Inter', system-ui, -apple-system, sans-serif;
        color: var(--pa-ink);
        flex-grow: 1;
    }

    /* ===== PROGRESS BAR ===== */
    .pa-progress-bar {
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0;
        margin-bottom: 32px;
        padding: 20px;
        background: var(--pa-white);
        border-radius: var(--pa-radius-lg);
        box-shadow: 0 1px 4px rgba(0,0,0,0.05);
    }
    .pa-step-item {
        display: flex;
        align-items: center;
        gap: 10px;
        color: #9ca3af;
        font-weight: 600;
        font-size: 14px;
        transition: color 0.3s;
    }
    .pa-step-item.active { color: var(--pa-brand); }
    .pa-step-circle {
        width: 36px; height: 36px;
        border-radius: 50%;
        display: flex; align-items: center; justify-content: center;
        font-weight: 800; font-size: 14px;
        background: #f3f4f6; color: #9ca3af;
        transition: all 0.3s;
    }
    .pa-step-item.active .pa-step-circle {
        background: var(--pa-brand); color: #fff;
        box-shadow: 0 4px 12px rgba(243,152,26,0.3);
    }
    .pa-step-line {
        width: 80px; height: 3px;
        background: #e5e7eb;
        margin: 0 16px;
        border-radius: 2px;
        transition: background 0.3s;
    }
    .pa-step-line.filled { background: var(--pa-brand); }

    @media (max-width: 480px) {
        .pa-step-item span { display: none; }
        .pa-step-line { width: 40px; }
        .pa-progress-bar { padding: 14px; }
    }

    /* ===== CARDS ===== */
    .pa-card {
        background: var(--pa-white);
        border: 1px solid var(--pa-line);
        border-radius: var(--pa-radius-lg);
        padding: 24px;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .pa-card-header {
        display: flex;
        align-items: center;
        gap: 10px;
        margin-bottom: 20px;
        padding-bottom: 14px;
        border-bottom: 1px solid #f3f4f6;
    }
    .pa-card-header i {
        font-size: 18px;
        color: var(--pa-brand);
        background: #fef3e2;
        width: 36px; height: 36px;
        display: flex; align-items: center; justify-content: center;
        border-radius: 10px;
    }
    .pa-card-header h2 {
        font-size: 18px;
        font-weight: 700;
        margin: 0;
        color: var(--pa-ink);
    }

    @media (max-width: 600px) {
        .pa-card { padding: 18px 14px; }
    }

    /* ===== INPUTS ===== */
    .pa-input, .pa-select, .pa-textarea {
        width: 100%;
        padding: 12px 14px;
        border: 1.5px solid var(--pa-line);
        border-radius: 10px;
        font-size: 15px;
        color: var(--pa-ink);
        background: var(--pa-white);
        outline: none;
        transition: border 0.2s, box-shadow 0.2s;
        font-family: inherit;
    }
    .pa-input:focus, .pa-select:focus, .pa-textarea:focus {
        border-color: var(--pa-brand);
        box-shadow: 0 0 0 3px rgba(243,152,26,0.12);
    }
    .pa-textarea { min-height: 120px; resize: vertical; }
    .pa-input-readonly {
        background: #f9fafb;
        border-style: dashed;
        cursor: not-allowed;
        color: var(--pa-muted);
    }
    .pa-label {
        display: block;
        font-weight: 600;
        font-size: 14px;
        color: #374151;
        margin-bottom: 6px;
    }
    .pa-req { color: #dc2626; }
    .pa-error {
        display: block;
        color: #dc2626;
        font-size: 13px;
        margin-top: 4px;
    }
    .pa-muted { color: var(--pa-muted); font-size: 14px; }
    .pa-sub-label {
        font-size: 15px;
        color: #374151;
        margin: 20px 0 12px;
        font-weight: 500;
    }

    /* ===== FIELDS GRID ===== */
    .pa-fields-grid {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
    }
    .pa-field-full { grid-column: 1 / -1; }
    @media (max-width: 600px) {
        .pa-fields-grid { grid-template-columns: 1fr; }
    }

    /* ===== ROW ===== */
    .pa-row {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 16px;
        margin-bottom: 16px;
    }
    @media (max-width: 600px) {
        .pa-row { grid-template-columns: 1fr; }
    }

    /* ===== TWO COL ===== */
    .pa-two-col {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 20px;
        align-items: start;
    }
    @media (max-width: 768px) {
        .pa-two-col { grid-template-columns: 1fr; }
    }

    /* ===== CATEGORY CHIPS ===== */
    .pa-chips {
        display: flex;
        gap: 10px;
        flex-wrap: wrap;
    }
    .pa-chip {
        display: flex;
        align-items: center;
        gap: 8px;
        padding: 8px 16px;
        border: 1.5px solid var(--pa-line);
        border-radius: 10px;
        background: var(--pa-white);
        cursor: pointer;
        font-weight: 600;
        font-size: 14px;
        transition: all 0.2s;
        color: var(--pa-ink);
        font-family: inherit;
    }
    .pa-chip:hover { border-color: #d1d5db; background: #f9fafb; }
    .pa-chip.active {
        border-color: var(--pa-brand);
        background: #fef8ee;
        box-shadow: 0 0 0 3px rgba(243,152,26,0.12);
    }
    .pa-chip-img {
        width: 26px; height: 26px;
        border-radius: 50%;
        object-fit: cover;
    }

    /* ===== OPTION CHIPS ===== */
    .pa-option-chips { display: flex; gap: 8px; flex-wrap: wrap; }
    .pa-option-chip {
        padding: 8px 16px;
        border: 1.5px solid var(--pa-line);
        border-radius: 20px;
        background: var(--pa-white);
        cursor: pointer;
        font-weight: 500;
        font-size: 13px;
        transition: all 0.2s;
        color: #374151;
        font-family: inherit;
    }
    .pa-option-chip:hover { border-color: #d1d5db; }
    .pa-option-chip.active {
        background: var(--pa-brand);
        color: #fff;
        border-color: var(--pa-brand);
    }

    /* ===== PRICE GROUP ===== */
    .pa-price-group {
        display: flex;
        border: 1.5px solid var(--pa-line);
        border-radius: 10px;
        overflow: hidden;
        transition: border 0.2s, box-shadow 0.2s;
    }
    .pa-price-group:focus-within {
        border-color: var(--pa-brand);
        box-shadow: 0 0 0 3px rgba(243,152,26,0.12);
    }
    .pa-currency-select {
        width: auto;
        min-width: 100px;
        padding: 12px;
        border: none;
        border-right: 1px solid var(--pa-line);
        background: #f9fafb;
        font-weight: 600;
        font-size: 14px;
        outline: none;
        border-radius: 0;
    }
    .pa-price-input {
        flex: 1;
        padding: 12px 14px;
        border: none;
        outline: none;
        font-size: 15px;
        font-family: inherit;
    }

    /* ===== TOGGLES ===== */
    .pa-toggles {
        display: flex;
        flex-wrap: wrap;
        gap: 16px;
    }
    .pa-toggle-row {
        display: flex;
        align-items: center;
        gap: 12px;
        cursor: pointer;
        padding: 10px 16px;
        border: 1.5px solid var(--pa-line);
        border-radius: 10px;
        transition: all 0.2s;
        background: var(--pa-white);
    }
    .pa-toggle-row:hover { border-color: #d1d5db; }
    .pa-toggle-label {
        font-weight: 600;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 8px;
        white-space: nowrap;
    }
    .pa-toggle-label i { color: var(--pa-brand); font-size: 14px; }
    .pa-toggle-wrap {
        position: relative;
        width: 44px;
        height: 24px;
        flex-shrink: 0;
    }
    .pa-toggle-track {
        width: 44px; height: 24px;
        background: #d1d5db;
        border-radius: 12px;
        transition: background 0.2s;
    }
    .pa-toggle-thumb {
        position: absolute;
        top: 2px; left: 2px;
        width: 20px; height: 20px;
        background: #fff;
        border-radius: 50%;
        box-shadow: 0 1px 3px rgba(0,0,0,0.15);
        transition: transform 0.2s;
    }
    input[type="checkbox"].sr-only:checked ~ .pa-toggle-track { background: var(--pa-brand); }
    input[type="checkbox"].sr-only:checked ~ .pa-toggle-thumb { transform: translateX(20px); }

    @media (max-width: 600px) {
        .pa-toggles { flex-direction: column; }
    }

    /* ===== UPLOAD ===== */
    .pa-upload-zone {
        position: relative;
        border: 2px dashed #d1d5db;
        border-radius: var(--pa-radius);
        padding: 32px;
        text-align: center;
        transition: border-color 0.2s, background 0.2s;
        cursor: pointer;
    }
    .pa-upload-zone:hover { border-color: var(--pa-brand); background: #fffdf7; }
    .pa-upload-input {
        position: absolute;
        inset: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
    }
    .pa-upload-label {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 6px;
        pointer-events: none;
    }
    .pa-upload-label i { font-size: 32px; color: var(--pa-brand); }
    .pa-upload-label strong { font-size: 15px; color: var(--pa-ink); }
    .pa-upload-label span { font-size: 13px; color: var(--pa-muted); }
    .pa-upload-progress {
        margin-top: 12px;
        padding: 12px;
        background: #eff6ff;
        border: 1px solid #bfdbfe;
        border-radius: 10px;
        color: #2563eb;
        font-weight: 600;
        font-size: 14px;
        text-align: center;
    }

    /* ===== IMAGE GRID ===== */
    .pa-image-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 12px;
    }
    .pa-image-item {
        position: relative;
        border-radius: 10px;
        overflow: hidden;
        aspect-ratio: 1;
        background: #f3f4f6;
        border: 1px solid var(--pa-line);
    }
    .pa-image-item img { width: 100%; height: 100%; object-fit: cover; }
    .pa-cover-badge {
        position: absolute;
        bottom: 6px; right: 6px;
        background: var(--pa-green);
        color: #fff;
        font-size: 11px;
        font-weight: 700;
        padding: 2px 8px;
        border-radius: 6px;
    }
    .pa-image-num {
        position: absolute;
        bottom: 6px; left: 6px;
        background: rgba(0,0,0,0.5);
        color: #fff;
        font-size: 11px;
        padding: 2px 6px;
        border-radius: 4px;
    }
    .pa-image-remove {
        position: absolute;
        top: 6px; right: 6px;
        width: 26px; height: 26px;
        background: #dc2626;
        color: #fff;
        border: none;
        border-radius: 50%;
        cursor: pointer;
        font-size: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        opacity: 0;
        transition: opacity 0.2s;
    }
    .pa-image-item:hover .pa-image-remove { opacity: 1; }
    .pa-drag-handle {
        position: absolute;
        top: 6px; left: 6px;
        background: rgba(0,0,0,0.5);
        color: #fff;
        width: 26px; height: 26px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: move;
        opacity: 0;
        transition: opacity 0.2s;
        font-size: 12px;
    }
    .pa-image-item:hover .pa-drag-handle { opacity: 1; }
    .pa-image-overlay {
        position: absolute;
        inset: 0;
        background: rgba(0,0,0,0.5);
        color: #fff;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
    }
    .pa-no-images {
        text-align: center;
        padding: 24px 0;
        color: #9ca3af;
    }
    .pa-no-images i { font-size: 32px; margin-bottom: 8px; display: block; }
    .pa-no-images p { font-size: 14px; margin: 0; }

    @media (max-width: 480px) {
        .pa-image-grid { grid-template-columns: repeat(2, 1fr); }
    }

    /* ===== PACKAGES ===== */
    .pa-packages { display: flex; flex-direction: column; gap: 10px; }
    .pa-package {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 14px 16px;
        border: 1.5px solid var(--pa-line);
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.2s;
        background: var(--pa-white);
    }
    .pa-package:hover { border-color: #d1d5db; }
    .pa-package.active {
        border-color: var(--pa-brand);
        background: #fef8ee;
        box-shadow: 0 0 0 3px rgba(243,152,26,0.1);
    }
    .pa-package-radio {
        width: 20px; height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: border-color 0.2s;
    }
    .pa-package.active .pa-package-radio { border-color: var(--pa-brand); }
    .pa-package-dot {
        width: 10px; height: 10px;
        border-radius: 50%;
        background: var(--pa-brand);
        transform: scale(0);
        transition: transform 0.2s;
    }
    .pa-package.active .pa-package-dot { transform: scale(1); }
    .pa-package-info {
        flex: 1;
        display: flex;
        flex-direction: column;
        gap: 2px;
    }
    .pa-package-info strong { font-size: 15px; color: var(--pa-ink); }
    .pa-package-info span { font-size: 13px; color: var(--pa-muted); }
    .pa-badge {
        font-size: 11px;
        font-weight: 700;
        padding: 4px 10px;
        border-radius: 20px;
        white-space: nowrap;
    }
    .pa-badge-free { background: #fef3c7; color: #92400e; }
    .pa-badge-pro { background: var(--pa-brand); color: #fff; }

    /* ===== PAYMENT METHODS ===== */
    .pa-payment-methods { display: flex; flex-direction: column; gap: 8px; }
    .pa-payment-option {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 12px 14px;
        border: 1.5px solid var(--pa-line);
        border-radius: 10px;
        cursor: pointer;
        transition: all 0.2s;
        font-weight: 500;
        font-size: 14px;
    }
    .pa-payment-option:hover { border-color: #d1d5db; }
    .pa-payment-option.active {
        border-color: var(--pa-brand);
        background: #fef8ee;
    }
    .pa-payment-option.active .pa-package-radio { border-color: var(--pa-brand); }
    .pa-payment-option.active .pa-package-dot { transform: scale(1); }

    /* ===== BUTTONS ===== */
    .pa-actions {
        margin-top: 24px;
        display: flex;
        justify-content: flex-end;
    }
    .pa-actions-split { justify-content: space-between; }
    .pa-btn {
        padding: 12px 24px;
        border: none;
        border-radius: 12px;
        font-weight: 700;
        font-size: 15px;
        cursor: pointer;
        transition: all 0.2s;
        font-family: inherit;
        display: inline-flex;
        align-items: center;
    }
    .pa-btn:disabled { opacity: 0.6; cursor: not-allowed; }
    .pa-btn-primary {
        background: var(--pa-brand);
        color: #fff;
        box-shadow: 0 4px 12px rgba(243,152,26,0.25);
    }
    .pa-btn-primary:hover { background: var(--pa-brand-dark); transform: translateY(-1px); }
    .pa-btn-lg { padding: 14px 32px; font-size: 16px; }
    .pa-btn-secondary {
        background: #f3f4f6;
        color: var(--pa-ink);
    }
    .pa-btn-secondary:hover { background: #e5e7eb; }

    /* ===== EMPTY STATE ===== */
    .pa-empty-state {
        text-align: center;
        padding: 32px 16px;
        color: #9ca3af;
    }
    .pa-empty-state i {
        font-size: 36px;
        margin-bottom: 12px;
        display: block;
        color: #d1d5db;
    }
    .pa-empty-state p { font-size: 15px; margin: 0; }

    /* ===== LOADING ===== */
    .pa-loading { text-align: center; padding: 24px; color: var(--pa-muted); }
    .pa-spinner {
        display: inline-block;
        width: 24px; height: 24px;
        border: 3px solid #e5e7eb;
        border-top-color: var(--pa-brand);
        border-radius: 50%;
        animation: pa-spin 0.6s linear infinite;
        margin-bottom: 8px;
    }
    @keyframes pa-spin { to { transform: rotate(360deg); } }

    /* ===== ALERT ===== */
    .pa-alert {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 14px 18px;
        border-radius: var(--pa-radius);
        margin-bottom: 20px;
        font-weight: 500;
        font-size: 14px;
    }
    .pa-alert-success {
        background: #f0fdf4;
        border: 1px solid #bbf7d0;
        color: #166534;
    }

    /* ===== TOOLTIP ===== */
    .tooltip-container { position: relative; }
    .tooltip-text {
        visibility: hidden;
        background: #1f2937;
        color: #fff;
        text-align: center;
        border-radius: 8px;
        padding: 6px 12px;
        position: absolute;
        z-index: 10;
        bottom: 125%;
        left: 50%;
        transform: translateX(-50%);
        opacity: 0;
        transition: opacity 0.2s;
        width: max-content;
        max-width: 200px;
        font-size: 12px;
        font-weight: 400;
    }
    .tooltip-container:hover .tooltip-text { visibility: visible; opacity: 1; }

    /* ===== SR ONLY ===== */
    .sr-only {
        position: absolute !important;
        width: 1px !important;
        height: 1px !important;
        padding: 0 !important;
        margin: -1px !important;
        overflow: hidden !important;
        clip: rect(0,0,0,0) !important;
        white-space: nowrap !important;
        border: 0 !important;
    }

    /* ===== SORTABLE ===== */
    .sortable-ghost { opacity: 0.4; }
    .sortable-chosen { border: 2px dashed #3b82f6; }
</style>

</div>
