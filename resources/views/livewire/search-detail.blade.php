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

    {{-- Active Filters Chips Bar --}}
    @php
        $hasActiveFilters = $categorySlug || $selectedSubCategory || ($minPrice > 0 || $maxPrice < 500000) || collect($customFilters)->flatten()->filter()->isNotEmpty();
    @endphp
    @if($hasActiveFilters)
    <div class="active-filters-bar">
        <div class="active-filters-inner">
            <span class="active-filters-label"><i class="fas fa-filter"></i> Active Filters:</span>
            <div class="active-chips-list">
                @if($categorySlug)
                    <span class="active-chip">
                        <i class="fas fa-folder"></i>
                        {{ $allCategories->firstWhere('slug', $categorySlug)->title ?? $categorySlug }}
                        <button wire:click="$set('categorySlug', '')" title="Remove"><i class="fas fa-times"></i></button>
                    </span>
                @endif
                @if($selectedSubCategory)
                    <span class="active-chip">
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
                            <span class="active-chip">
                                {{ $val }}
                                <button wire:click="removeCustomFilter('{{ $fieldId }}', '{{ $val }}')" title="Remove"><i class="fas fa-times"></i></button>
                            </span>
                            @endif
                        @endforeach
                    @endif
                @endforeach
            </div>
            <button class="clear-all-btn" wire:click="clearAllFilters"><i class="fas fa-trash-alt"></i> Clear All</button>
        </div>
    </div>
    @endif

    {{-- Mobile Filter Toggle --}}
    <button class="mobile-filter-toggle" onclick="document.getElementById('filterSidebar').classList.toggle('sidebar-open')">
        <i class="fas fa-sliders-h"></i> Filters
        @if($hasActiveFilters)
            <span class="mobile-filter-badge">!</span>
        @endif
    </button>

    <div class="container">
        <aside class="sidebar" id="filterSidebar">
            <div class="sidebar-header">
                <h2 class="sidebar-title"><i class="fas fa-filter"></i> Filters</h2>
                <button class="sidebar-close-mobile" onclick="document.getElementById('filterSidebar').classList.remove('sidebar-open')">
                    <i class="fas fa-times"></i>
                </button>
            </div>

            @php
                $groupedFilters = $this->allFilters->groupBy('group');
            @endphp

            @foreach($groupedFilters as $groupName => $filtersInGroup)
                <div class="filter-group-card" x-data="{ groupOpen: true }" wire:key="main-group-{{ $groupName }}">
                    <div class="filter-group-header" @click="groupOpen = !groupOpen">
                        <div class="filter-group-header-left">
                            <i class="{{ $this->getGroupIcon($groupName) }} filter-group-icon"></i>
                            <span>{{ $groupName }}</span>
                        </div>
                        <i class="fas fa-chevron-down filter-toggle-icon" :class="{ 'rotated': groupOpen }"></i>
                    </div>
                    <div class="filter-group-body" x-show="groupOpen" x-collapse.duration.200ms>
                        @foreach($filtersInGroup as $filter)
                            @php
                                $filterIndex = $this->allFilters->search(function ($item) use ($filter) {
                                    return $item->id == $filter->id;
                                });
                                $activeCount = 0;
                                if ($filter->id === 'main_categories' && $categorySlug) $activeCount = 1;
                                elseif ($filter->id === 'sub_categories' && $selectedSubCategory) $activeCount = 1;
                                elseif ($filter->id === 'price_range' && ($minPrice > 0 || $maxPrice < 500000)) $activeCount = 1;
                                elseif ($filter->id === 'distance' && $distance != 500) $activeCount = 1;
                                elseif (!in_array($filter->type, ['price', 'distance', 'radio', 'main_category_radio']) && !empty($customFilters[$filter->id]) && is_array($customFilters[$filter->id]))
                                    $activeCount = count(array_filter($customFilters[$filter->id]));
                            @endphp
                            <div class="filter-item-block" 
                                 x-data="{ itemOpen: false, searchTerm: '' }" 
                                 wire:key="filter-item-{{ $filter->id }}">
                                <div class="filter-item-header" @click="itemOpen = !itemOpen">
                                    <span class="filter-item-name">{{ $filter->name }}</span>
                                    <div class="filter-item-right">
                                        @if($activeCount > 0)
                                            <span class="filter-count-badge">{{ $activeCount }}</span>
                                        @endif
                                        <i class="fas fa-chevron-right filter-item-chevron" :class="{ 'rotated-down': itemOpen }"></i>
                                    </div>
                                </div>
                                <div class="filter-item-body" x-show="itemOpen" x-collapse.duration.200ms>
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
                                        @if($filter->options->count() > 6)
                                        <div class="filter-search-inline">
                                            <i class="fas fa-search"></i>
                                            <input type="text" x-model="searchTerm" placeholder="Search categories..." class="filter-search-input">
                                        </div>
                                        @endif
                                        <ul class="filter-options-list">
                                            @foreach($filter->options as $option)
                                                <li x-show="!searchTerm || '{{ strtolower($option->title) }}'.includes(searchTerm.toLowerCase())" wire:key="cat-opt-{{ $option->id }}">
                                                    <label class="filter-option-label">
                                                        <input type="radio" wire:model.live="categorySlug" name="sidebarCategorySlug" value="{{ $option->slug }}" class="filter-radio">
                                                        <span class="custom-radio-mark"></span>
                                                        <span class="option-text">{{ $option->title }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>

                                    @elseif($filter->type === 'radio')
                                        @if($filter->options->count() > 6)
                                        <div class="filter-search-inline">
                                            <i class="fas fa-search"></i>
                                            <input type="text" x-model="searchTerm" placeholder="Search..." class="filter-search-input">
                                        </div>
                                        @endif
                                        <ul class="filter-options-list">
                                            @foreach($filter->options as $option)
                                                <li x-show="!searchTerm || '{{ strtolower($option->title) }}'.includes(searchTerm.toLowerCase())" wire:key="subcat-opt-{{ $option->id }}">
                                                    <label class="filter-option-label">
                                                        <input type="radio" wire:model.live="selectedSubCategory" name="sidebarSubCategory" value="{{ $option->slug }}" class="filter-radio">
                                                        <span class="custom-radio-mark"></span>
                                                        <span class="option-text">{{ $option->title }}</span>
                                                    </label>
                                                </li>
                                            @endforeach
                                        </ul>

                                    @else
                                        @if($filter->options->count() > 6)
                                        <div class="filter-search-inline">
                                            <i class="fas fa-search"></i>
                                            <input type="text" x-model="searchTerm" placeholder="Search options..." class="filter-search-input">
                                        </div>
                                        @endif
                                        <ul class="filter-options-list">
                                            @foreach($filter->options as $option)
                                                <li x-show="!searchTerm || '{{ strtolower($option->key) }}'.includes(searchTerm.toLowerCase())" wire:key="custom-opt-{{ $option->id }}">
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
            @endforeach
        </aside>

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
                    {!! App\Models\Setting::htmlAdBlock($product->id) !!}
                @empty
                    <p style="grid-column: 1 / -1; text-align: center; padding: 40px; color: #6c757d;">No products found matching your criteria.</p>
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
        /* ===== Active Filters Bar ===== */
        .active-filters-bar {
            max-width: 1200px;
            margin: 0 auto 10px;
            padding: 0 25px;
        }
        .active-filters-inner {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--card-bg);
            padding: 12px 18px;
            border-radius: 10px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.04);
            border: 1px solid var(--light-gray);
            flex-wrap: wrap;
        }
        .active-filters-label {
            font-size: 13px;
            font-weight: 600;
            color: var(--gray);
            white-space: nowrap;
        }
        .active-filters-label i { margin-right: 4px; }
        .active-chips-list {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            flex: 1;
        }
        .active-chip {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: linear-gradient(135deg, #f0f4ff, #e8ecf8);
            color: #3b5998;
            padding: 5px 12px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #d0d8f0;
            transition: all 0.2s ease;
        }
        .active-chip:hover { background: linear-gradient(135deg, #e0e8ff, #d8dcf0); }
        .active-chip i:first-child { font-size: 11px; opacity: 0.7; }
        .active-chip button {
            background: none;
            border: none;
            color: #3b5998;
            cursor: pointer;
            padding: 0;
            line-height: 1;
            opacity: 0.6;
            transition: opacity 0.2s;
        }
        .active-chip button:hover { opacity: 1; }
        .active-chip.chip-price { background: linear-gradient(135deg, #f0fff0, #e0f5e0); color: #2a7a2a; border-color: #c0e0c0; }
        .clear-all-btn {
            background: #fee2e2;
            color: #dc2626;
            border: 1px solid #fca5a5;
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            white-space: nowrap;
            transition: all 0.2s ease;
        }
        .clear-all-btn:hover { background: #fecaca; }
        .clear-all-btn i { margin-right: 4px; font-size: 11px; }

        /* ===== Mobile Filter Toggle ===== */
        .mobile-filter-toggle {
            display: none;
            position: fixed;
            bottom: 20px;
            left: 50%;
            transform: translateX(-50%);
            z-index: 999;
            background: var(--primary);
            color: #fff;
            border: none;
            padding: 14px 28px;
            border-radius: 30px;
            font-size: 15px;
            font-weight: 600;
            cursor: pointer;
            box-shadow: 0 6px 20px rgba(248,153,27,0.4);
            transition: all 0.3s ease;
            gap: 8px;
            align-items: center;
        }
        .mobile-filter-toggle:hover { transform: translateX(-50%) translateY(-2px); box-shadow: 0 8px 25px rgba(248,153,27,0.5); }
        .mobile-filter-badge {
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

        /* ===== Sidebar ===== */
        .sidebar-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .sidebar-close-mobile { display: none; background: none; border: none; font-size: 20px; color: var(--gray); cursor: pointer; }

        /* ===== Filter Group Card ===== */
        .filter-group-card {
            background: #fff;
            border: 1px solid #f0f0f0;
            border-radius: 10px;
            margin-bottom: 12px;
            overflow: hidden;
            transition: box-shadow 0.2s ease;
        }
        .filter-group-card:hover { box-shadow: 0 2px 10px rgba(0,0,0,0.05); }

        .filter-group-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 14px 16px;
            cursor: pointer;
            user-select: none;
            transition: background 0.15s ease;
        }
        .filter-group-header:hover { background: #fafbfc; }
        .filter-group-header-left {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 15px;
            font-weight: 600;
            color: #1a1a2e;
        }
        .filter-group-icon {
            color: var(--primary);
            font-size: 14px;
            width: 28px;
            height: 28px;
            background: linear-gradient(135deg, #fff5e6, #ffe8cc);
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .filter-toggle-icon {
            font-size: 12px;
            color: #adb5bd;
            transition: transform 0.3s ease;
        }
        .filter-toggle-icon.rotated { transform: rotate(180deg); }

        .filter-group-body { padding: 0 12px 12px; }

        /* ===== Individual Filter Item ===== */
        .filter-item-block {
            border-bottom: 1px solid #f5f5f5;
        }
        .filter-item-block:last-child { border-bottom: none; }

        .filter-item-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px 6px;
            cursor: pointer;
            transition: all 0.15s ease;
            border-radius: 6px;
        }
        .filter-item-header:hover { background: #f8f9fa; }
        .filter-item-name {
            font-size: 14px;
            font-weight: 500;
            color: #495057;
        }
        .filter-item-right {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-count-badge {
            background: var(--primary);
            color: #fff;
            font-size: 11px;
            font-weight: 700;
            min-width: 20px;
            height: 20px;
            border-radius: 10px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }
        .filter-item-chevron {
            font-size: 11px;
            color: #ced4da;
            transition: transform 0.3s ease;
        }
        .filter-item-chevron.rotated-down { transform: rotate(90deg); }

        .filter-item-body { padding: 4px 6px 10px; }

        /* ===== Inline Search ===== */
        .filter-search-inline {
            position: relative;
            margin-bottom: 8px;
        }
        .filter-search-inline i {
            position: absolute;
            left: 10px;
            top: 50%;
            transform: translateY(-50%);
            color: #ced4da;
            font-size: 12px;
        }
        .filter-search-input {
            width: 100%;
            padding: 8px 10px 8px 30px;
            border: 1px solid #e9ecef;
            border-radius: 8px;
            font-size: 13px;
            outline: none;
            transition: border-color 0.2s ease;
            background: #fafbfc;
        }
        .filter-search-input:focus { border-color: var(--primary); background: #fff; }

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

        /* ===== Responsive — Mobile Drawer ===== */
        @media (max-width: 992px) {
            .mobile-filter-toggle { display: flex; }
            .sidebar-close-mobile { display: block; }

            .sidebar {
                position: fixed !important;
                top: 0 !important;
                left: -320px;
                width: 300px !important;
                height: 100vh !important;
                z-index: 1001;
                border-radius: 0 !important;
                transition: left 0.35s cubic-bezier(0.4, 0, 0.2, 1);
                overflow-y: auto;
                box-shadow: none;
            }
            .sidebar.sidebar-open {
                left: 0;
                box-shadow: 10px 0 30px rgba(0,0,0,0.15);
            }

            .active-filters-bar { padding: 0 15px; }
        }
    </style>
</div>
