<div>
    <div class="search-section">
        <div class="search-content">
            <h1 class="search-title">Find What You Need</h1>
            <p class="search-subtitle">Browse through thousands of pre-loved goods in multiple categories.</p>
            <div class="search-form">
                <div class="search-input-group">
                    <i class="fas fa-search"></i>
                    <input type="text" placeholder="What are you looking for?" wire:model.live.debounce.500ms="searchQuery" class="search-input">
                </div>
                <div class="search-input-group location-input-group">
                    <i class="fas fa-list"></i>
                    <select wire:model.live="categorySlug" class="category-select">
                        <option value="">All Categories</option>
                        @foreach ($allCategories as $category)
                            <option value="{{ $category->slug }}">{{ $category->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    {{-- Active Filters + Filter Panel --}}
    @php
        $hasActiveFilters = $categorySlug || $selectedSubCategory || ($minPrice > 0 || $maxPrice < 500000) || collect($customFilters)->flatten()->filter()->isNotEmpty();
        $allFiltersList = $this->allFilters->values();
    @endphp

    {{-- Active Filters Chips Bar --}}
    @if($hasActiveFilters)
    <div class="active-filters-bar">
        <div class="active-filters-inner">
            <div class="active-filters-top">
                <span class="active-filters-label"><i class="fas fa-filter"></i> Active Filters</span>
                <button class="clear-all-btn" wire:click="clearAllFilters"><i class="fas fa-trash-alt"></i> Clear All</button>
            </div>
            <div class="active-chips-list">
                @if($categorySlug)
                    <span class="active-chip chip-category">
                        <i class="fas fa-folder"></i>
                        {{ $allCategories->firstWhere('slug', $categorySlug)->title ?? $categorySlug }}
                        <button wire:click="$set('categorySlug', '')" title="Remove"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($selectedSubCategory)
                    <span class="active-chip chip-subcategory">
                        <i class="fas fa-folder-open"></i>
                        {{ $subCategories->firstWhere('slug', $selectedSubCategory)->title ?? $selectedSubCategory }}
                        <button wire:click="removeSubCategory()" title="Remove"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($minPrice > 0 || $maxPrice < 500000)
                    <span class="active-chip chip-price">
                        <i class="fas fa-dollar-sign"></i>
                        {{ number_format($minPrice) }} – {{ number_format($maxPrice) }}
                        <button wire:click="clearPriceFilter" title="Remove"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @foreach($customFilters as $fieldId => $values)
                    @if(is_array($values))
                        @foreach($values as $val)
                            @if($val)
                            <span class="active-chip chip-custom">
                                <i class="fas fa-tag"></i>
                                {{ $val }}
                                <button wire:click="removeCustomFilter('{{ $fieldId }}', '{{ $val }}')" title="Remove"><i class="fas fa-times"></i></button>
                            </span>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
        </div>
    </div>
    @endif

    {{-- Filter Panel Toggle + Slide-down Panel --}}
    <div x-data="{ panelOpen: false }" class="filter-panel-wrapper">
        <div class="filter-modal-toggle-bar">
            <button class="filter-modal-open-btn" @click="panelOpen = !panelOpen">
                <i class="fas fa-sliders-h"></i> Advanced Filters
                @if($hasActiveFilters)
                    <span class="filter-modal-badge">!</span>
                @endif
                <i class="fas fa-chevron-down" :class="{ 'fa-chevron-up': panelOpen }" style="margin-left:4px;font-size:12px;"></i>
            </button>
        </div>

        <div x-show="panelOpen" x-cloak x-collapse class="filter-panel">
            @foreach($allFiltersList as $filter)
            <div x-data="{ expanded: false }" class="filter-section" wire:key="fp-{{ $filter->id }}">
                <button class="filter-section-header" @click="expanded = !expanded">
                    <div class="filter-section-header-left">
                        <i class="{{ $this->getGroupIcon($filter->group ?? 'General') }} filter-section-icon"></i>
                        <span>{{ $filter->name }}</span>
                    </div>
                    <i class="fas fa-chevron-down filter-section-chevron" :class="{ 'rotated': expanded }"></i>
                </button>
                <div x-show="expanded" x-collapse class="filter-section-body">
                    @if($filter->type === 'price')
                        <div class="price-filter-inline">
                            <div class="price-inputs-row">
                                <div class="price-input-wrap">
                                    <label>Min</label>
                                    <input type="number" wire:model.live.debounce.500ms="minPrice" min="0" max="500000" placeholder="0">
                                </div>
                                <span class="price-separator">–</span>
                                <div class="price-input-wrap">
                                    <label>Max</label>
                                    <input type="number" wire:model.live.debounce.500ms="maxPrice" min="0" max="500000" placeholder="500,000">
                                </div>
                            </div>
                            <div class="price-range-slider-inline">
                                <input type="range" min="0" max="500000" step="1000" wire:model.live.debounce.300ms="minPrice" class="slider-min">
                                <input type="range" min="0" max="500000" step="1000" wire:model.live.debounce.300ms="maxPrice" class="slider-max">
                            </div>
                            <div class="price-range-labels">
                                <span>{{ number_format($minPrice) }}</span>
                                <span>{{ number_format($maxPrice) }}</span>
                            </div>
                        </div>
                    @elseif($filter->type === 'distance')
                        <div class="distance-filter-inline">
                            <input type="range" min="500" max="{{ $maxDistance }}" step="10" wire:model.live.debounce.300ms="distance" class="distance-slider-inline">
                            <div class="distance-labels">
                                <span>500 km</span>
                                <span class="distance-current">{{ $distance }} km</span>
                                <span>{{ $maxDistance }} km</span>
                            </div>
                        </div>
                    @elseif($filter->type === 'main_category_radio')
                        <ul class="filter-options-list">
                            @foreach($filter->options as $option)
                                <li wire:key="fp-cat-{{ $option->id }}">
                                    <label class="filter-option-label">
                                        <input type="radio" wire:model.live="categorySlug" name="panelCategorySlug" value="{{ $option->slug }}" class="filter-radio">
                                        <span class="custom-radio-mark"></span>
                                        <span class="option-text">{{ $option->title }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @elseif($filter->type === 'radio')
                        <ul class="filter-options-list">
                            @foreach($filter->options as $option)
                                <li wire:key="fp-sub-{{ $option->id }}">
                                    <label class="filter-option-label">
                                        <input type="radio" wire:model.live="selectedSubCategory" name="panelSubCategory" value="{{ $option->slug }}" class="filter-radio">
                                        <span class="custom-radio-mark"></span>
                                        <span class="option-text">{{ $option->title }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @else
                        <ul class="filter-options-list">
                            @foreach($filter->options as $option)
                                <li wire:key="fp-opt-{{ $option->id }}">
                                    <label class="filter-option-label">
                                        <input type="checkbox" wire:model.live="customFilters.{{ $filter->id }}" value="{{ $option->key }}" class="filter-checkbox">
                                        <span class="custom-check-mark"></span>
                                        <span class="option-text">{{ $option->key }}</span>
                                    </label>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            </div>
            @endforeach
        </div>
    </div>

    <div class="container">
        <main class="main-content">
            <div class="top-bar">
                <div class="results-count">Showing {{ $filtered_data->count() }} of {{ $total_posts }} results</div>
                <div class="sort-options">
                    <label>Sort By:</label>
                    <select wire:model.live="sortBy">
                        <option value="post-desc">Recently Posted</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="most-viewed">Popular</option>
                    </select>
                </div>
            </div>

            <div wire:loading wire:target="searchQuery, categorySlug, selectedSubCategory, minPrice, maxPrice, sortBy, distance, customFilters" style="display:none;" class="loading-overlay">
                <div class="loading-spinner"></div>
                <span class="loading-text">Updating results...</span>
            </div>

            <div class="card-grid">
                @forelse($filtered_data as $product)
                    @php $adHtml = App\Models\Setting::htmlAdBlock($product->id); @endphp
                    @if(!empty(trim($adHtml)))
                        {!! $adHtml !!}
                    @endif
                @empty
                    <div class="empty-state">
                        <i class="fas fa-search"></i>
                        <h3>No products found</h3>
                        <p>Try adjusting your filters or search query.</p>
                        <button class="clear-filters-empty" wire:click="clearAllFilters"><i class="fas fa-times"></i> Clear All Filters</button>
                    </div>
                @endforelse
            </div>

            @if($filtered_data->count() < $total_posts)
                <div class="load-more-btn-container">
                    <button class="load-more-btn" wire:click="loadMore"><i class="fas fa-arrow-down"></i> Load More</button>
                </div>
            @endif
        </main>
    </div>

    <style>
        /* ===== Card Grid ===== */
        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(240px, 1fr));
            gap: 20px;
        }
        .card-grid > div:empty { display: none; }
        @media (max-width: 768px) {
            .card-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
        }
        @media (max-width: 480px) {
            .card-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
        }

        /* ===== Active Filters Bar ===== */
        .active-filters-bar {
            max-width: 1200px;
            margin: 0 auto 16px;
            padding: 0 25px;
        }
        .active-filters-inner {
            background: #fff;
            padding: 16px 20px;
            border-radius: 14px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        }
        .active-filters-top {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .active-filters-label {
            font-size: 14px;
            font-weight: 700;
            color: #1a1a2e;
        }
        .active-filters-label i {
            margin-right: 6px;
            color: var(--primary);
        }
        .active-chips-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
        }
        .active-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: #f8f9fa;
            color: #374151;
            padding: 7px 14px;
            border-radius: 8px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e5e7eb;
            transition: all 0.2s ease;
        }
        .active-chip:hover { background: #f0f1f3; border-color: #d1d5db; }
        .active-chip i:first-child { font-size: 11px; color: #9ca3af; }
        .active-chip button {
            background: none;
            border: none;
            color: #9ca3af;
            cursor: pointer;
            padding: 0 0 0 2px;
            line-height: 1;
            transition: color 0.2s;
        }
        .active-chip button:hover { color: #dc2626; }
        .active-chip.chip-category {
            background: #fff7ed;
            border-color: #fed7aa;
            color: #c2410c;
        }
        .active-chip.chip-category i:first-child { color: #f97316; }
        .active-chip.chip-subcategory {
            background: #eff6ff;
            border-color: #bfdbfe;
            color: #1d4ed8;
        }
        .active-chip.chip-subcategory i:first-child { color: #3b82f6; }
        .active-chip.chip-price {
            background: #f0fdf4;
            border-color: #bbf7d0;
            color: #15803d;
        }
        .active-chip.chip-price i:first-child { color: #22c55e; }
        .active-chip.chip-custom {
            background: #faf5ff;
            border-color: #e9d5ff;
            color: #7c3aed;
        }
        .active-chip.chip-custom i:first-child { color: #a78bfa; }
        .clear-all-btn {
            background: none;
            color: #dc2626;
            border: none;
            padding: 4px 0;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: opacity 0.2s;
        }
        .clear-all-btn:hover { opacity: 0.7; }
        .clear-all-btn i { margin-right: 4px; font-size: 11px; }

        /* ===== Filter Modal Toggle Button ===== */
        .filter-modal-toggle-bar {
            max-width: 1200px;
            margin: 0 auto 16px;
            padding: 0 25px;
            display: flex;
            justify-content: flex-start;
        }
        .filter-modal-open-btn {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            border: none;
            padding: 12px 28px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 10px;
            box-shadow: 0 4px 15px rgba(248, 153, 27, 0.35);
            transition: all 0.3s ease;
        }
        .filter-modal-open-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(248, 153, 27, 0.45);
        }
        .filter-modal-badge {
            background: #dc2626;
            color: #fff;
            font-size: 10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }

        /* ===== Filter Panel ===== */
        [x-cloak] { display: none !important; }

        .filter-panel-wrapper {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 25px;
        }
        .filter-panel {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 14px;
            margin-top: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .filter-section {
            border-bottom: 1px solid #f0f0f0;
        }
        .filter-section:last-child { border-bottom: none; }
        .filter-section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 14px 20px;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            color: #1a1a2e;
            transition: background 0.15s ease;
        }
        .filter-section-header:hover { background: #fafbfc; }
        .filter-section-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .filter-section-icon {
            color: var(--primary);
            font-size: 14px;
            width: 32px;
            height: 32px;
            background: #fff7ed;
            border-radius: 8px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
        }
        .filter-section-chevron {
            font-size: 12px;
            color: #adb5bd;
            transition: transform 0.2s ease;
        }
        .filter-section-chevron.rotated { transform: rotate(180deg); }
        .filter-section-body {
            padding: 0 20px 16px;
        }

        /* ===== Options List ===== */
        .filter-options-list {
            list-style: none;
            padding: 0;
            margin: 0;
            max-height: 220px;
            overflow-y: auto;
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 transparent;
        }
        .filter-options-list::-webkit-scrollbar { width: 4px; }
        .filter-options-list::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }

        .filter-options-list li { margin: 0; padding: 0; }

        .filter-option-label {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 8px;
            cursor: pointer;
            border-radius: 6px;
            transition: background 0.15s ease;
            font-size: 14px;
            color: #495057;
        }
        .filter-option-label:hover { background: #f8f9fa; }

        /* Hidden native inputs */
        .filter-radio, .filter-checkbox { display: none; }

        /* Custom radio */
        .custom-radio-mark {
            width: 18px;
            height: 18px;
            border: 2px solid #ced4da;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
        }
        .custom-radio-mark::after {
            content: '';
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: transparent;
            transition: background 0.2s ease;
        }
        .filter-radio:checked + .custom-radio-mark {
            border-color: var(--primary);
        }
        .filter-radio:checked + .custom-radio-mark::after {
            background: var(--primary);
        }

        /* Custom checkbox */
        .custom-check-mark {
            width: 18px;
            height: 18px;
            border: 2px solid #ced4da;
            border-radius: 5px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            transition: all 0.2s ease;
            font-size: 11px;
            color: transparent;
        }
        .custom-check-mark::after {
            content: '\f00c';
            font-family: 'Font Awesome 6 Free';
            font-weight: 900;
        }
        .filter-checkbox:checked + .custom-check-mark {
            background: var(--primary);
            border-color: var(--primary);
            color: #fff;
        }

        .option-text { flex: 1; }

        /* ===== Price Filter Inline ===== */
        .price-filter-inline { padding: 4px 0; }
        .price-inputs-row {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 12px;
        }
        .price-input-wrap {
            flex: 1;
        }
        .price-input-wrap label {
            display: block;
            font-size: 11px;
            font-weight: 600;
            color: #adb5bd;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 4px;
        }
        .price-input-wrap input {
            width: 100%;
            padding: 8px 10px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 14px;
            outline: none;
            text-align: center;
            transition: border-color 0.2s ease;
        }
        .price-input-wrap input:focus { border-color: var(--primary); }
        .price-separator {
            font-size: 18px;
            color: #ced4da;
            padding-top: 18px;
        }
        .price-range-slider-inline {
            position: relative;
            height: 6px;
            background: #e9ecef;
            border-radius: 3px;
            margin: 16px 0 8px;
        }
        .price-range-slider-inline input[type="range"] {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            position: absolute;
            top: 0;
            background: transparent;
            pointer-events: none;
            z-index: 2;
        }
        .price-range-slider-inline input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 20px;
            width: 20px;
            background: var(--primary);
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            transition: transform 0.15s ease;
        }
        .price-range-slider-inline input[type="range"]::-webkit-slider-thumb:hover {
            transform: scale(1.15);
        }
        .price-range-slider-inline input[type="range"]::-moz-range-thumb {
            height: 20px;
            width: 20px;
            background: var(--primary);
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .price-range-labels {
            display: flex;
            justify-content: space-between;
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
        }

        /* ===== Distance Filter Inline ===== */
        .distance-filter-inline { padding: 4px 0; }
        .distance-slider-inline {
            -webkit-appearance: none;
            width: 100%;
            height: 6px;
            background: linear-gradient(to right, var(--primary), var(--secondary));
            border-radius: 3px;
            outline: none;
            margin: 10px 0 8px;
        }
        .distance-slider-inline::-webkit-slider-thumb {
            -webkit-appearance: none;
            width: 22px;
            height: 22px;
            background: var(--secondary);
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
            transition: transform 0.15s ease;
        }
        .distance-slider-inline::-webkit-slider-thumb:hover { transform: scale(1.15); }
        .distance-slider-inline::-moz-range-thumb {
            width: 22px;
            height: 22px;
            background: var(--secondary);
            border: 3px solid #fff;
            border-radius: 50%;
            cursor: pointer;
            box-shadow: 0 2px 6px rgba(0,0,0,0.15);
        }
        .distance-labels {
            display: flex;
            justify-content: space-between;
            font-size: 12px;
            color: #6c757d;
        }
        .distance-current {
            font-weight: 700;
            color: var(--secondary);
            font-size: 14px;
        }

        /* ===== Loading Overlay ===== */
        .loading-overlay {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 60px 20px;
            gap: 12px;
        }
        .loading-spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid var(--primary);
            border-radius: 50%;
            width: 40px;
            height: 40px;
            animation: spin 0.8s linear infinite;
        }
        @keyframes spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .loading-text {
            font-size: 14px;
            color: var(--gray);
            font-weight: 500;
        }

        /* ===== Empty State ===== */
        .empty-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            color: #6c757d;
        }
        .empty-state i {
            font-size: 48px;
            color: #dee2e6;
            margin-bottom: 16px;
        }
        .empty-state h3 {
            font-size: 20px;
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        .empty-state p { font-size: 14px; margin-bottom: 20px; }
        .clear-filters-empty {
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .clear-filters-empty:hover { opacity: 0.9; }
        .clear-filters-empty i { margin-right: 6px; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .filter-modal-toggle-bar { padding: 0 15px; }
            .filter-panel-wrapper { padding: 0 15px; }
            .filter-section-header { padding: 12px 16px; }
            .filter-section-body { padding: 0 16px 14px; }
            .active-filters-bar { padding: 0 15px; }
        }
    </style>


</div>
