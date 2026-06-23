<div x-data="{ mobileOpen: false, closeMobile() { this.mobileOpen = false; } }">

    {{-- Search Section --}}
    <div class="af-search-section">
        <div class="af-search-content">
            <h1 class="af-search-title">Find What You Need</h1>
            <p class="af-search-subtitle">Browse through thousands of pre-loved goods in multiple categories.</p>
            <div class="af-search-form">
                <div class="af-search-group">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="What are you looking for?" wire:model.live.debounce.500ms="searchQuery" class="af-search-input">
                </div>
                <div class="af-search-group af-search-cat">
                    <i class="fas fa-list"></i>
                    <select wire:model.live="categorySlug" class="af-cat-select">
                        <option value="">All Categories</option>
                        @foreach ($allCategories as $category)
                            <option value="{{ $category->slug }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="af-search-group af-search-loc" wire:ignore>
                    <i class="fas fa-map-marker-alt"></i>
                    <input type="text" id="af-location-input" placeholder="Location..." class="af-loc-input" value="{{ $locationText }}">
                    @if($locationText)
                    <button type="button" class="af-loc-clear" onclick="window._afClearLoc()"><i class="fas fa-times"></i></button>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @php
        $hasActiveFilters = $categorySlug || $selectedSubCategory || ($minPrice > 0 || $maxPrice < 500000) || $locationText || collect($customFilters)->flatten()->filter()->isNotEmpty();
        $allFiltersList = $this->allFilters->values();
    @endphp

    {{-- Mobile Filter Button --}}
    <button class="af-mobile-toggle" @click="mobileOpen = true">
        <i class="fas fa-sliders-h"></i> Filters
        @if($hasActiveFilters)<span class="af-mobile-badge">!</span>@endif
    </button>

    <div class="af-container">
        {{-- LEFT SIDEBAR (Carsales-style) --}}
        <aside class="af-sidebar" :class="{ 'af-sidebar-open': mobileOpen }">
            {{-- Mobile close --}}
            <button class="af-sidebar-close" @click="closeMobile()"><i class="fas fa-times"></i></button>

            <div class="af-sidebar-head">
                <h2 class="af-sidebar-title">Filters</h2>
                @if($hasActiveFilters)
                    <button class="af-clear-all" wire:click="clearAllFilters">Clear all</button>
                @endif
            </div>

            {{-- Filter Items List --}}
            <div class="af-filter-list">
                @foreach($allFiltersList as $idx => $filter)
                @php
                    $activeCount = 0;
                    $activeValue = null;
                    if ($filter->id === 'main_categories' && $categorySlug) {
                        $activeCount = 1;
                        $activeValue = $allCategories->firstWhere('slug', $categorySlug)->title ?? $categorySlug;
                    } elseif ($filter->id === 'sub_categories' && $selectedSubCategory) {
                        $activeCount = 1;
                        $activeValue = $subCategories->firstWhere('slug', $selectedSubCategory)->title ?? $selectedSubCategory;
                    } elseif ($filter->id === 'price_range' && ($minPrice > 0 || $maxPrice < 500000)) {
                        $activeCount = 1;
                        $activeValue = number_format($minPrice) . ' – ' . number_format($maxPrice);
                    } elseif ($filter->id === 'distance' && $distance != 500) {
                        $activeCount = 1;
                        $activeValue = $distance . ' km';
                    } elseif (!in_array($filter->type, ['price', 'distance', 'radio', 'main_category_radio']) && !empty($customFilters[$filter->id]) && is_array($customFilters[$filter->id])) {
                        $vals = array_filter($customFilters[$filter->id]);
                        $activeCount = count($vals);
                        $activeValue = implode(', ', array_slice($vals, 0, 2)) . (count($vals) > 2 ? '…' : '');
                    }
                @endphp
                <div class="af-filter-item" wire:key="af-item-{{ $filter->id }}">
                    <div class="af-filter-row" onclick="window._afOpen('{{ $filter->id }}')">
                        <span class="af-filter-name">{{ $filter->name }}</span>
                        <span class="af-filter-chevron"><i class="fas fa-chevron-right"></i></span>
                    </div>
                    @if($activeValue)
                        <div class="af-filter-active-value">
                            <span class="af-active-text">{{ $activeValue }}</span>
                            @if($filter->id === 'main_categories')
                                <button class="af-remove-val" wire:click="$set('categorySlug', '')" title="Remove"><i class="fas fa-times-circle"></i></button>
                            @elseif($filter->id === 'sub_categories')
                                <button class="af-remove-val" wire:click="removeSubCategory()" title="Remove"><i class="fas fa-times-circle"></i></button>
                            @elseif($filter->id === 'price_range')
                                <button class="af-remove-val" wire:click="clearPriceFilter" title="Remove"><i class="fas fa-times-circle"></i></button>
                            @elseif($filter->id === 'distance')
                                <button class="af-remove-val" wire:click="$set('distance', 500)" title="Remove"><i class="fas fa-times-circle"></i></button>
                            @else
                                @foreach(array_filter($customFilters[$filter->id] ?? []) as $cv)
                                    <button class="af-remove-val" wire:click="removeCustomFilter('{{ $filter->id }}', '{{ $cv }}')" title="Remove {{ $cv }}"><i class="fas fa-times-circle"></i></button>
                                @endforeach
                            @endif
                        </div>
                    @endif
                </div>
                @endforeach
            </div>
        </aside>

        {{-- Mobile overlay --}}
        <div class="af-sidebar-overlay" x-show="mobileOpen" x-transition.opacity @click="closeMobile()" style="display:none"></div>

        {{-- MAIN CONTENT --}}
        <main class="af-main">
            <div class="af-topbar">
                <div class="af-results-count">
                    {{ $total_posts }} results
                </div>
                <div class="af-sort">
                    <label>Sort by</label>
                    <select wire:model.live="sortBy">
                        <option value="post-desc">Recently Posted</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="most-viewed">Popular</option>
                    </select>
                </div>
            </div>

            {{-- Active Filters Chips --}}
            @if($hasActiveFilters)
            <div class="af-active-bar">
                @if($categorySlug)
                    <span class="af-chip">
                        {{ $allCategories->firstWhere('slug', $categorySlug)->title ?? $categorySlug }}
                        <button wire:click="$set('categorySlug', '')"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($selectedSubCategory)
                    <span class="af-chip">
                        {{ $subCategories->firstWhere('slug', $selectedSubCategory)->title ?? $selectedSubCategory }}
                        <button wire:click="removeSubCategory()"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($minPrice > 0 || $maxPrice < 500000)
                    <span class="af-chip af-chip-price">
                        {{ number_format($minPrice) }} – {{ number_format($maxPrice) }}
                        <button wire:click="clearPriceFilter"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($locationText)
                    <span class="af-chip">
                        <i class="fas fa-map-marker-alt" style="margin-right:4px"></i>{{ $locationText }}
                        <button wire:click="clearLocation"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @foreach($customFilters as $fieldId => $values)
                    @if(is_array($values))
                        @foreach($values as $val)
                            @if($val)
                            <span class="af-chip">
                                {{ $val }}
                                <button wire:click="removeCustomFilter('{{ $fieldId }}', '{{ $val }}')"><i class="fas fa-times"></i></button>
                            </span>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
            @endif

            <div wire:loading wire:target="searchQuery, categorySlug, selectedSubCategory, minPrice, maxPrice, sortBy, distance, selectedCities, customFilters" style="display:none;" class="af-loading">
                <div class="af-spinner"></div>
                <span>Updating results...</span>
            </div>

            <div class="af-grid">
                @forelse($filtered_data as $product)
                    @php $adHtml = App\Models\Setting::htmlAdBlock($product->id); @endphp
                    @if(!empty(trim($adHtml)))
                        {!! $adHtml !!}
                    @endif
                @empty
                    <div class="af-empty">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search query.</p>
                        <button class="af-empty-btn" wire:click="clearAllFilters"><i class="fas fa-times"></i> Clear All Filters</button>
                    </div>
                @endforelse
            </div>

            @if($filtered_data->count() < $total_posts)
                <div class="af-loadmore-wrap">
                    <button class="af-loadmore-btn" wire:click="loadMore"><i class="fas fa-arrow-down"></i> Load More</button>
                </div>
            @endif
        </main>
    </div>

    {{-- POPUP MODALS — outside flex container for proper stacking --}}
    @php
        $customFieldIds = collect($allFiltersList)->filter(fn($f) => !in_array($f->type ?? '', ['price','distance','main_category_radio','radio']))->pluck('id')->values()->toArray();
    @endphp
    @foreach($allFiltersList as $idx => $filter)
    <div class="af-modal-overlay"
         data-af-modal="{{ $filter->id }}"
         onclick="window._afClose()"
         style="display:none"
         wire:ignore.self
         wire:key="af-modal-{{ $filter->id }}">
        <div class="af-modal" onclick="event.stopPropagation()">
            <div class="af-modal-header">
                <h3 class="af-modal-title">
                    @if($filter->type === 'main_category_radio' && $selectedSubCategory && collect($customFieldsForView)->isNotEmpty())
                        {{ \App\Models\TblCategory::where('slug', $selectedSubCategory)->value('title') ?? 'Filters' }}
                    @elseif($filter->type === 'main_category_radio' && $categorySlug && $selectedCategory)
                        {{ $selectedCategory->title }}
                    @else
                        {{ $filter->name }}
                    @endif
                </h3>
                <button class="af-modal-close" onclick="window._afClose()"><i class="fas fa-times"></i></button>
            </div>
            <div class="af-modal-body">
                @if($filter->type === 'price')
                    <div class="af-price-section">
                        <div class="af-price-row">
                            <div class="af-price-field">
                                <label>Min Price</label>
                                <input type="number" wire:model.live.debounce.500ms="minPrice" min="0" max="500000" placeholder="0">
                            </div>
                            <span class="af-price-dash">–</span>
                            <div class="af-price-field">
                                <label>Max Price</label>
                                <input type="number" wire:model.live.debounce.500ms="maxPrice" min="0" max="500000" placeholder="500,000">
                            </div>
                        </div>
                        <div class="af-price-slider">
                            <input type="range" min="0" max="500000" step="1000" wire:model.live.debounce.300ms="minPrice">
                            <input type="range" min="0" max="500000" step="1000" wire:model.live.debounce.300ms="maxPrice">
                        </div>
                        <div class="af-price-labels">
                            <span>{{ number_format($minPrice) }}</span>
                            <span>{{ number_format($maxPrice) }}</span>
                        </div>
                    </div>

                @elseif($filter->type === 'distance')
                    <div class="af-distance-section">
                        <input type="range" min="500" max="{{ $maxDistance }}" step="10" wire:model.live.debounce.300ms="distance" class="af-distance-slider">
                        <div class="af-distance-labels">
                            <span>500 km</span>
                            <span class="af-distance-current">{{ $distance }} km</span>
                            <span>{{ $maxDistance }} km</span>
                        </div>
                    </div>

                @elseif($filter->type === 'main_category_radio')
                    @php
                        $hasCustomFields = $selectedSubCategory && collect($customFieldsForView)->isNotEmpty();
                        $showSubLevel = $categorySlug && $subCategories->isNotEmpty() && !$hasCustomFields;
                        $showFieldsLevel = $hasCustomFields;
                    @endphp
                    <div class="af-drilldown" data-af-drilldown="{{ $filter->id }}">
                        {{-- Level 0: Parent Categories --}}
                        <div class="af-drill-level" data-drill-level="0" @if($showSubLevel || $showFieldsLevel) style="display:none" @endif>
                            <div class="af-modal-search">
                                <span class="af-modal-search-label">Search categories</span>
                                <div class="af-modal-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input type="text" oninput="window._afFilter(this)" placeholder="Search..." class="af-modal-search-input">
                                </div>
                            </div>
                            <ul class="af-options-list">
                                @foreach($filter->options->sortBy('title') as $option)
                                <li data-search-text="{{ strtolower($option->title) }}" wire:key="af-mcat-{{ $option->id }}">
                                    <div class="af-option-label af-drill-item" onclick="window._afDrillCategory('{{ $option->slug }}', '{{ addslashes($option->title) }}')">
                                        @if($option->picture)
                                        <img src="{{ asset('storage/' . $option->picture) }}" class="af-drill-icon" alt="">
                                        @else
                                        <span class="af-drill-icon-placeholder"><i class="fas fa-folder"></i></span>
                                        @endif
                                        <span class="af-option-text">{{ $option->title }}</span>
                                        <span class="af-option-count"><i class="fas fa-chevron-right"></i></span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                        </div>
                        {{-- Level 1: Sub Categories --}}
                        <div class="af-drill-level" data-drill-level="1" @if(!$showSubLevel) style="display:none" @endif>
                            <div class="af-drill-back" onclick="window._afDrillBack()">
                                <i class="fas fa-arrow-left"></i>
                                <span class="af-drill-back-title">{{ $selectedCategory->title ?? 'Back' }}</span>
                            </div>
                            <div class="af-modal-search">
                                <span class="af-modal-search-label">Search sub-categories</span>
                                <div class="af-modal-search-wrap">
                                    <i class="fas fa-search"></i>
                                    <input type="text" oninput="window._afFilter(this)" placeholder="Search..." class="af-modal-search-input">
                                </div>
                            </div>
                            <ul class="af-options-list af-drill-subcats">
                                @if($subCategories->isNotEmpty())
                                    @foreach($subCategories->sortBy('title') as $sub)
                                    <li data-search-text="{{ strtolower($sub->title) }}" wire:key="af-dsub-{{ $sub->id }}">
                                        <div class="af-option-label af-drill-item" onclick="window._afSelectSubCat('{{ $sub->slug }}')">
                                            <span class="af-option-text">{{ $sub->title }}</span>
                                            <span class="af-option-count"><i class="fas fa-chevron-right"></i></span>
                                        </div>
                                    </li>
                                    @endforeach
                                @endif
                            </ul>
                        </div>
                        {{-- Level 2: Custom Fields (Make, Model, etc.) after sub-category selection --}}
                        <div class="af-drill-level" data-drill-level="2" @if(!$showFieldsLevel) style="display:none" @endif>
                            <div class="af-drill-back" onclick="window._afDrillBackToSubs()">
                                <i class="fas fa-arrow-left"></i>
                                <span class="af-drill-back-title">{{ \App\Models\TblCategory::where('slug', $selectedSubCategory)->value('title') ?? 'Back' }}</span>
                            </div>
                            <ul class="af-options-list">
                                @foreach(collect($customFieldsForView)->filter(fn($f) => in_array($f->type, ['select','autocomplete','checkbox-group','radio-group'])) as $cf)
                                <li data-search-text="{{ strtolower($cf->name) }}" wire:key="af-cflvl-{{ $cf->id }}">
                                    <div class="af-option-label af-drill-item" onclick="window._afClose(); setTimeout(function(){ window._afOpen('{{ $cf->id }}'); }, 100);">
                                        <span class="af-option-text">{{ $cf->name }}</span>
                                        @if(!empty($customFilters[$cf->id]) && count(array_filter($customFilters[$cf->id])) > 0)
                                        <span class="af-drill-badge">{{ count(array_filter($customFilters[$cf->id])) }}</span>
                                        @endif
                                        <span class="af-option-count"><i class="fas fa-chevron-right"></i></span>
                                    </div>
                                </li>
                                @endforeach
                            </ul>
                            <div style="padding: 12px 16px;">
                                <button class="af-modal-search-btn" onclick="window._afClose()" style="width:100%">Done</button>
                            </div>
                        </div>
                    </div>

                @elseif($filter->type === 'radio')
                    @if($subCatDrillParent)
                    <div class="af-drill-back" onclick="@this.call('drillBackSubCat')">
                        <i class="fas fa-arrow-left"></i>
                        <span class="af-drill-back-title">Back</span>
                    </div>
                    @endif
                    <div class="af-modal-search">
                        <span class="af-modal-search-label">Search {{ strtolower($filter->name) }}</span>
                        <div class="af-modal-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" oninput="window._afFilter(this)" placeholder="Search..." class="af-modal-search-input">
                        </div>
                    </div>
                    <ul class="af-options-list">
                        @foreach($filter->options->sortBy('title') as $option)
                        <li data-search-text="{{ strtolower($option->title) }}" wire:key="af-sub-{{ $option->id }}">
                            <div class="af-option-label af-drill-item" onclick="window._afSelectSubCat('{{ $option->slug }}')">
                                <span class="af-option-text">{{ $option->title }}</span>
                                <span class="af-option-count"><i class="fas fa-chevron-right"></i></span>
                            </div>
                        </li>
                        @endforeach
                    </ul>

                @else
                    <div class="af-modal-search">
                        <span class="af-modal-search-label">Search {{ strtolower($filter->name) }}</span>
                        <div class="af-modal-search-wrap">
                            <i class="fas fa-search"></i>
                            <input type="text" oninput="window._afFilter(this)" placeholder="Search..." class="af-modal-search-input">
                        </div>
                    </div>
                    <ul class="af-options-list">
                        @foreach($filter->options->sortBy('key') as $option)
                        <li data-search-text="{{ strtolower($option->key) }}" wire:key="af-cust-{{ $option->id }}">
                            <label class="af-option-label">
                                <input type="checkbox"
                                       class="af-checkbox-input"
                                       wire:model.live="customFilters.{{ $filter->id }}"
                                       value="{{ $option->key }}">
                                <span class="af-checkbox-mark"></span>
                                <span class="af-option-text">{{ $option->key }}</span>
                                <span class="af-option-count"><i class="fas fa-chevron-right"></i></span>
                            </label>
                        </li>
                        @endforeach
                    </ul>
                @endif
            </div>
            <div class="af-modal-footer">
                @if($filter->type === 'main_category_radio' && $categorySlug)
                    <button class="af-modal-clear" wire:click="$set('categorySlug', '')">Clear</button>
                @elseif($filter->type === 'radio' && $selectedSubCategory)
                    <button class="af-modal-clear" wire:click="removeSubCategory()">Clear</button>
                @elseif($filter->type === 'price' && ($minPrice > 0 || $maxPrice < 500000))
                    <button class="af-modal-clear" wire:click="clearPriceFilter">Clear</button>
                @elseif($filter->type === 'distance' && $distance != 500)
                    <button class="af-modal-clear" wire:click="$set('distance', 500)">Clear</button>
                @elseif(!in_array($filter->type, ['price', 'distance', 'main_category_radio', 'radio']) && !empty($customFilters[$filter->id]))
                    <button class="af-modal-clear" wire:click="$set('customFilters.{{ $filter->id }}', [])">Clear</button>
                @else
                    <span></span>
                @endif
                @php
                    $currentFieldIdx = array_search($filter->id, $customFieldIds);
                    $nextFieldId = ($currentFieldIdx !== false && isset($customFieldIds[$currentFieldIdx + 1])) ? $customFieldIds[$currentFieldIdx + 1] : null;
                @endphp
                @if($nextFieldId && !in_array($filter->type, ['price', 'distance', 'main_category_radio', 'radio']))
                    <button class="af-modal-next-btn" onclick="window._afClose(); setTimeout(function(){ window._afOpen('{{ $nextFieldId }}'); }, 100);">Next <i class="fas fa-arrow-right"></i></button>
                @else
                    <button class="af-modal-search-btn" onclick="window._afClose()">Done</button>
                @endif
            </div>
        </div>
    </div>
    @endforeach

    <script>
    (function() {
        window._afOpen = function(id) {
            id = String(id);
            document.body.style.overflow = 'hidden';
            document.querySelectorAll('[data-af-modal]').forEach(function(el) {
                if (el.getAttribute('data-af-modal') === id) {
                    el.style.display = 'flex';
                    var searchInput = el.querySelector('.af-modal-search-input');
                    if (searchInput) { searchInput.value = ''; window._afFilter(searchInput); }
                } else {
                    el.style.display = 'none';
                }
            });
        };
        window._afClose = function() {
            document.body.style.overflow = '';
            document.querySelectorAll('[data-af-modal]').forEach(function(el) {
                el.style.display = 'none';
            });
        };
        window._afFilter = function(input) {
            var query = input.value.toLowerCase();
            var list = input.closest('.af-modal-body').querySelector('.af-options-list');
            if (!list) return;
            list.querySelectorAll('li[data-search-text]').forEach(function(li) {
                li.style.display = li.getAttribute('data-search-text').includes(query) ? '' : 'none';
            });
        };
        // Category drill-down functions
        window._afDrillCategory = function(slug, title) {
            @this.set('categorySlug', slug);
        };
        window._afDrillBack = function() {
            @this.set('categorySlug', '');
        };
        window._afDrillBackToSubs = function() {
            @this.call('drillBackFromFields');
        };
        window._afSelectSubCat = function(slug) {
            @this.call('drillIntoSubCat', slug);
        };

        window._afClearLoc = function() {
            var input = document.getElementById('af-location-input');
            if (input) input.value = '';
            var clearBtn = document.querySelector('.af-loc-clear');
            if (clearBtn) clearBtn.style.display = 'none';
            @this.clearLocation();
        };
        function _afInitPlaces() {
            if (typeof google === 'undefined' || !google.maps || !google.maps.places) return;
            var input = document.getElementById('af-location-input');
            if (!input || input._afInit) return;
            input._afInit = true;
            var ac = new google.maps.places.Autocomplete(input, { types: ['(cities)'] });
            ac.addListener('place_changed', function() {
                var place = ac.getPlace();
                if (!place || !place.geometry) return;
                var lat = place.geometry.location.lat();
                var lng = place.geometry.location.lng();
                var text = place.formatted_address || place.name || input.value;
                @this.setLocation(lat, lng, text);
                var clearBtn = document.querySelector('.af-loc-clear');
                if (clearBtn) clearBtn.style.display = 'flex';
            });
            input.addEventListener('keydown', function(e) { if (e.key === 'Enter') e.preventDefault(); });
        }
        if (typeof google !== 'undefined' && google.maps && google.maps.places) {
            _afInitPlaces();
        } else {
            var _origInit = window.initMap;
            window.initMap = function() { if (typeof _origInit === 'function') _origInit(); _afInitPlaces(); };
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') window._afClose();
        });
        // Close modal on browser event from Livewire
        window.addEventListener('af-close-modal', function() {
            window._afClose();
        });
    })();
    </script>

    <style>
    /* ========================================
       Advanced Filter — Carsales-style
       All classes prefixed with af-
       ======================================== */

    /* Search Section */
    .af-search-section {
        background: #fff;
        padding: 2.5rem 1.5rem;
        margin: 20px auto;
        max-width: 1200px;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        border: 1px solid #e5e7eb;
        position: relative;
        overflow: hidden;
    }
    .af-search-section::before {
        content: '';
        position: absolute;
        inset: 0;
        background: linear-gradient(135deg, rgba(248,153,27,0.04), rgba(57,118,58,0.04));
        z-index: 0;
    }
    .af-search-content { position: relative; z-index: 1; }
    .af-search-title {
        text-align: center;
        font-size: 2rem;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0 0 0.3rem;
    }
    .af-search-subtitle {
        text-align: center;
        font-size: 1rem;
        color: #6b7280;
        margin: 0 0 1.5rem;
    }
    .af-search-form {
        display: flex;
        gap: 10px;
        background: #f9fafb;
        padding: 8px;
        border-radius: 10px;
        border: 2px solid var(--primary);
    }
    .af-search-form:focus-within {
        box-shadow: 0 0 0 3px rgba(248,153,27,0.15);
    }
    .af-search-group {
        display: flex;
        align-items: center;
        flex: 1;
        background: #fff;
        padding: 10px 15px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
    }
    .af-search-group i {
        color: #9ca3af;
        margin-right: 10px;
        font-size: 14px;
    }
    .af-search-input {
        width: 100%;
        border: none;
        outline: none;
        font-size: 15px;
        background: transparent;
        color: #1a1a2e;
    }
    .af-search-input::placeholder { color: #9ca3af; }
    .af-search-cat { border-left: 1px solid #e5e7eb; }
    .af-search-loc { border-left: 1px solid #e5e7eb; position: relative; }
    .af-cat-select {
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        color: #1a1a2e;
        cursor: pointer;
        flex: 1;
        padding-left: 6px;
    }
    .af-loc-input {
        border: none;
        outline: none;
        background: transparent;
        font-size: 15px;
        color: #1a1a2e;
        flex: 1;
        padding-left: 6px;
        min-width: 0;
    }
    .af-loc-input::placeholder { color: #9ca3af; }
    .af-loc-clear {
        display: flex;
        align-items: center;
        justify-content: center;
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0 4px;
        font-size: 14px;
    }
    .af-loc-clear:hover { color: #ef4444; }

    /* Drill-down navigation */
    .af-drilldown { width: 100%; }
    .af-drill-level { animation: af-slide-in 0.2s ease; }
    @keyframes af-slide-in { from { opacity: 0; transform: translateX(10px); } to { opacity: 1; transform: translateX(0); } }
    .af-drill-back {
        display: flex;
        align-items: center;
        gap: 10px;
        padding: 10px 16px;
        cursor: pointer;
        color: var(--primary, #f8991b);
        font-weight: 600;
        font-size: 14px;
        border-bottom: 1px solid #f0f0f0;
        transition: background 0.15s;
    }
    .af-drill-back:hover { background: #fef9f3; }
    .af-drill-back i { font-size: 13px; }
    .af-drill-item {
        cursor: pointer;
        display: flex;
        align-items: center;
        padding: 10px 16px;
        gap: 12px;
        transition: background 0.15s;
    }
    .af-drill-item:hover { background: #f9fafb; }
    .af-drill-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        object-fit: cover;
    }
    .af-drill-icon-placeholder {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        background: #f3f4f6;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #9ca3af;
        font-size: 14px;
    }
    .af-drill-badge {
        background: var(--primary, #f8991b);
        color: #fff;
        font-size: 11px;
        font-weight: 600;
        min-width: 20px;
        height: 20px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0 6px;
        margin-left: auto;
        margin-right: 8px;
    }

    /* Container */
    .af-container {
        display: flex;
        gap: 24px;
        padding: 0 24px 24px;
        max-width: 1200px;
        margin: 0 auto;
    }

    /* ===== SIDEBAR ===== */
    .af-sidebar {
        width: 280px;
        flex-shrink: 0;
        position: sticky;
        top: 80px;
        height: fit-content;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
        scrollbar-width: thin;
        scrollbar-color: #e5e7eb transparent;
    }
    .af-sidebar::-webkit-scrollbar { width: 3px; }
    .af-sidebar::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 3px; }
    .af-sidebar-close {
        display: none;
        position: absolute;
        top: 16px;
        right: 16px;
        background: none;
        border: none;
        font-size: 20px;
        color: #6b7280;
        cursor: pointer;
        z-index: 10;
    }
    .af-sidebar-head {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0 0 16px;
    }
    .af-sidebar-title {
        font-size: 22px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }
    .af-clear-all {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.2s;
    }
    .af-clear-all:hover { opacity: 0.7; }

    /* Filter Items */
    .af-filter-list {
        border-top: 1px solid #e5e7eb;
    }
    .af-filter-item {
        border-bottom: 1px solid #f0f0f0;
    }
    .af-filter-row {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 14px 4px;
        cursor: pointer;
        transition: background 0.15s ease;
        border-radius: 4px;
    }
    .af-filter-row:hover { background: #f9fafb; }
    .af-filter-name {
        font-size: 15px;
        font-weight: 500;
        color: #374151;
    }
    .af-filter-chevron {
        color: #d1d5db;
        font-size: 12px;
        transition: color 0.2s;
    }
    .af-filter-row:hover .af-filter-chevron { color: #9ca3af; }

    /* Active value under filter name */
    .af-filter-active-value {
        display: flex;
        align-items: center;
        gap: 6px;
        padding: 0 4px 12px 16px;
        flex-wrap: wrap;
    }
    .af-active-text {
        font-size: 14px;
        font-weight: 500;
        color: var(--primary);
    }
    .af-remove-val {
        background: none;
        border: none;
        color: var(--primary);
        cursor: pointer;
        font-size: 14px;
        padding: 0;
        opacity: 0.7;
        transition: opacity 0.2s;
    }
    .af-remove-val:hover { opacity: 1; }

    /* ===== POPUP MODAL ===== */
    .af-modal-overlay {
        position: fixed;
        inset: 0;
        z-index: 9999;
        background: rgba(0,0,0,0.4);
        backdrop-filter: blur(3px);
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 20px;
    }
    .af-modal {
        background: #fff;
        border-radius: 14px;
        width: 100%;
        max-width: 520px;
        max-height: 80vh;
        display: flex;
        flex-direction: column;
        box-shadow: 0 20px 60px rgba(0,0,0,0.18);
        overflow: hidden;
    }
    .af-modal-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 18px 24px;
        border-bottom: 1px solid #f0f0f0;
    }
    .af-modal-title {
        font-size: 18px;
        font-weight: 700;
        color: #1a1a2e;
        margin: 0;
    }
    .af-modal-close {
        background: none;
        border: none;
        font-size: 18px;
        color: #9ca3af;
        cursor: pointer;
        width: 36px;
        height: 36px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        transition: all 0.2s;
    }
    .af-modal-close:hover { background: #fee2e2; color: #dc2626; }

    .af-modal-body {
        flex: 1;
        overflow-y: auto;
        padding: 20px 24px;
        scrollbar-width: thin;
        scrollbar-color: #e5e7eb transparent;
    }
    .af-modal-body::-webkit-scrollbar { width: 4px; }
    .af-modal-body::-webkit-scrollbar-thumb { background: #e5e7eb; border-radius: 4px; }

    /* Modal Search */
    .af-modal-search { margin-bottom: 16px; }
    .af-modal-search-label {
        display: block;
        font-size: 13px;
        color: #6b7280;
        margin-bottom: 6px;
    }
    .af-modal-search-wrap {
        position: relative;
    }
    .af-modal-search-wrap i {
        position: absolute;
        left: 12px;
        top: 50%;
        transform: translateY(-50%);
        color: #d1d5db;
        font-size: 13px;
    }
    .af-modal-search-input {
        width: 100%;
        padding: 10px 12px 10px 36px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 14px;
        outline: none;
        background: #fff;
        transition: border-color 0.2s;
    }
    .af-modal-search-input:focus { border-color: var(--primary); }

    /* Options List */
    .af-options-list {
        list-style: none;
        padding: 0;
        margin: 0;
    }
    .af-options-list li {
        border-bottom: 1px solid #f5f5f5;
    }
    .af-options-list li:last-child { border-bottom: none; }
    .af-option-label {
        display: flex;
        align-items: center;
        gap: 12px;
        padding: 12px 4px;
        cursor: pointer;
        transition: background 0.15s;
        border-radius: 4px;
    }
    .af-option-label:hover { background: #f9fafb; }

    /* Custom Checkbox (Carsales-style) */
    .af-checkbox-input { display: none; }
    .af-checkbox-mark {
        width: 20px;
        height: 20px;
        border: 2px solid #d1d5db;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        transition: all 0.2s ease;
        color: transparent;
        font-size: 12px;
    }
    .af-checkbox-mark::after {
        content: '\f00c';
        font-family: 'Font Awesome 6 Free';
        font-weight: 900;
    }
    .af-checkbox-input:checked + .af-checkbox-mark {
        background: var(--primary);
        border-color: var(--primary);
        color: #fff;
    }
    .af-option-text {
        flex: 1;
        font-size: 15px;
        color: #374151;
        font-weight: 400;
    }
    .af-option-count {
        font-size: 11px;
        color: #d1d5db;
        display: flex;
        align-items: center;
        gap: 4px;
    }

    /* Modal Footer */
    .af-modal-footer {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 16px 24px;
        border-top: 1px solid #f0f0f0;
        background: #fafbfc;
    }
    .af-modal-clear {
        background: none;
        border: none;
        color: var(--primary);
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        padding: 0;
        transition: opacity 0.2s;
    }
    .af-modal-clear:hover { opacity: 0.7; }
    .af-modal-search-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 28px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
    }
    .af-modal-search-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(248,153,27,0.3);
    }
    .af-modal-next-btn {
        background: var(--secondary, #39763a);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        transition: all 0.2s;
        display: flex;
        align-items: center;
        gap: 6px;
    }
    .af-modal-next-btn:hover {
        background: #2d5f2e;
        transform: translateY(-1px);
        box-shadow: 0 3px 10px rgba(57,118,58,0.3);
    }

    /* Modal overlay uses display:none/flex toggled by JS */

    /* ===== PRICE Section inside modal ===== */
    .af-price-section { padding: 4px 0; }
    .af-price-row {
        display: flex;
        align-items: flex-end;
        gap: 12px;
        margin-bottom: 16px;
    }
    .af-price-field { flex: 1; }
    .af-price-field label {
        display: block;
        font-size: 12px;
        font-weight: 600;
        color: #9ca3af;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-bottom: 6px;
    }
    .af-price-field input {
        width: 100%;
        padding: 10px 12px;
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        font-size: 15px;
        outline: none;
        text-align: center;
        transition: border-color 0.2s;
    }
    .af-price-field input:focus { border-color: var(--primary); }
    .af-price-dash {
        font-size: 20px;
        color: #d1d5db;
        padding-bottom: 10px;
    }
    .af-price-slider {
        position: relative;
        height: 6px;
        background: #e5e7eb;
        border-radius: 3px;
        margin: 0 0 8px;
    }
    .af-price-slider input[type="range"] {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        position: absolute;
        top: 0;
        background: transparent;
        pointer-events: none;
        z-index: 2;
    }
    .af-price-slider input[type="range"]::-webkit-slider-thumb {
        -webkit-appearance: none;
        height: 20px;
        width: 20px;
        background: var(--primary);
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        pointer-events: all;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .af-price-slider input[type="range"]::-moz-range-thumb {
        height: 20px;
        width: 20px;
        background: var(--primary);
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        pointer-events: all;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .af-price-labels {
        display: flex;
        justify-content: space-between;
        font-size: 13px;
        font-weight: 600;
        color: var(--primary);
    }

    /* ===== DISTANCE Section inside modal ===== */
    .af-distance-section { padding: 4px 0; }
    .af-distance-slider {
        -webkit-appearance: none;
        width: 100%;
        height: 6px;
        background: linear-gradient(to right, var(--primary), var(--secondary));
        border-radius: 3px;
        outline: none;
        margin: 10px 0 10px;
    }
    .af-distance-slider::-webkit-slider-thumb {
        -webkit-appearance: none;
        width: 22px;
        height: 22px;
        background: var(--secondary);
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .af-distance-slider::-moz-range-thumb {
        width: 22px;
        height: 22px;
        background: var(--secondary);
        border: 3px solid #fff;
        border-radius: 50%;
        cursor: pointer;
        box-shadow: 0 2px 6px rgba(0,0,0,0.15);
    }
    .af-distance-labels {
        display: flex;
        justify-content: space-between;
        font-size: 12px;
        color: #6b7280;
    }
    .af-distance-current {
        font-weight: 700;
        color: var(--secondary);
        font-size: 14px;
    }

    /* ===== MAIN CONTENT ===== */
    .af-main { flex: 1; min-width: 0; }
    .af-topbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 16px;
        padding: 14px 20px;
        background: #fff;
        border-radius: 10px;
        border: 1px solid #e5e7eb;
        box-shadow: 0 1px 4px rgba(0,0,0,0.04);
    }
    .af-results-count {
        font-size: 14px;
        color: #6b7280;
        font-weight: 500;
    }
    .af-sort {
        display: flex;
        align-items: center;
        gap: 8px;
    }
    .af-sort label {
        font-size: 14px;
        color: #374151;
        font-weight: 500;
    }
    .af-sort select {
        padding: 7px 12px;
        border-radius: 8px;
        border: 1px solid #e5e7eb;
        font-size: 13px;
        background: #fff;
        cursor: pointer;
        outline: none;
        transition: border-color 0.2s;
    }
    .af-sort select:focus { border-color: var(--primary); }

    /* Active Filters Chips */
    .af-active-bar {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        margin-bottom: 16px;
    }
    .af-chip {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        background: #f3f4f6;
        color: #374151;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 13px;
        font-weight: 500;
        border: 1px solid #e5e7eb;
        transition: all 0.2s;
    }
    .af-chip:hover { background: #e5e7eb; }
    .af-chip button {
        background: none;
        border: none;
        color: #9ca3af;
        cursor: pointer;
        padding: 0;
        font-size: 11px;
        transition: color 0.2s;
    }
    .af-chip button:hover { color: #dc2626; }
    .af-chip-price { background: #f0fdf4; border-color: #bbf7d0; color: #166534; }

    /* Card Grid */
    .af-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
        gap: 20px;
    }
    .af-grid > div:empty { display: none; }

    /* Loading */
    .af-loading {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        padding: 60px 20px;
        gap: 12px;
    }
    .af-spinner {
        border: 4px solid #f3f3f3;
        border-top: 4px solid var(--primary);
        border-radius: 50%;
        width: 36px;
        height: 36px;
        animation: af-spin 0.8s linear infinite;
    }
    @keyframes af-spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
    .af-loading span { font-size: 14px; color: #6b7280; }

    /* Empty State */
    .af-empty {
        grid-column: 1 / -1;
        text-align: center;
        padding: 60px 20px;
        color: #6b7280;
    }
    .af-empty i { font-size: 48px; color: #e5e7eb; margin-bottom: 16px; display: block; }
    .af-empty h3 { font-size: 20px; font-weight: 600; color: #374151; margin: 0 0 8px; }
    .af-empty p { font-size: 14px; margin: 0 0 20px; }
    .af-empty-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 10px 24px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
    }
    .af-empty-btn:hover { opacity: 0.9; }

    /* Load More */
    .af-loadmore-wrap {
        display: flex;
        justify-content: center;
        margin-top: 24px;
    }
    .af-loadmore-btn {
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 12px 28px;
        border-radius: 8px;
        font-size: 14px;
        font-weight: 600;
        cursor: pointer;
        display: inline-flex;
        align-items: center;
        gap: 8px;
        transition: all 0.2s;
    }
    .af-loadmore-btn:hover {
        background: var(--primary-dark);
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(248,153,27,0.3);
    }

    /* Mobile Filter Toggle */
    .af-mobile-toggle {
        display: none;
        position: fixed;
        bottom: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 998;
        background: var(--primary);
        color: #fff;
        border: none;
        padding: 14px 28px;
        border-radius: 30px;
        font-size: 15px;
        font-weight: 600;
        cursor: pointer;
        box-shadow: 0 6px 20px rgba(248,153,27,0.4);
        align-items: center;
        gap: 8px;
    }
    .af-mobile-badge {
        background: #dc2626;
        color: #fff;
        font-size: 10px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        margin-left: 4px;
    }

    /* Mobile sidebar overlay */
    .af-sidebar-overlay {
        position: fixed;
        inset: 0;
        z-index: 1000;
        background: rgba(0,0,0,0.4);
    }

    /* ===== RESPONSIVE ===== */
    @media (max-width: 992px) {
        .af-mobile-toggle { display: flex; }
        .af-sidebar {
            position: fixed;
            top: 0;
            left: -320px;
            width: 300px !important;
            height: 100vh;
            z-index: 1001;
            background: #fff;
            padding: 24px 20px;
            border-radius: 0;
            box-shadow: none;
            transition: left 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            overflow-y: auto;
            max-height: 100vh;
        }
        .af-sidebar.af-sidebar-open {
            left: 0;
            box-shadow: 10px 0 30px rgba(0,0,0,0.15);
        }
        .af-sidebar-close { display: block; }
        .af-container { padding: 0 16px 16px; }
        .af-search-section { margin: 12px 16px; padding: 1.5rem 1rem; }
    }
    @media (max-width: 768px) {
        .af-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        .af-topbar { flex-direction: column; gap: 10px; align-items: flex-start; }
        .af-search-form { flex-direction: column; }
        .af-search-cat { border-left: none; border-top: 1px solid #e5e7eb; }
        .af-search-loc { border-left: none; border-top: 1px solid #e5e7eb; }
        .af-modal-overlay { align-items: flex-end; padding: 0; }
        .af-modal {
            max-width: 100%;
            max-height: 90vh;
            border-radius: 14px 14px 0 0;
        }
    }
    @media (max-width: 480px) {
        .af-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
    }
    </style>
</div>
