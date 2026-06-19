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

    {{-- Filter Modal Toggle + Step-by-Step Modal --}}
    @php
        $allFiltersList = $this->allFilters->values();
        $totalSteps = $allFiltersList->count();
    @endphp

    <div x-data="{
        open: false,
        currentStep: 0,
        searchTerm: '',
        get totalSteps() {
            var ref = this.$refs.totalStepsRef;
            return ref ? (parseInt(ref.dataset.steps) || 1) : 1;
        },
        getSteps() {
            var body = this.$refs.modalBody;
            return body ? body.querySelectorAll('.fm-step-content') : [];
        },
        init() {
            var self = this;
            self.$watch('currentStep', function() { self.syncStepVisibility(); });
            self.$watch('open', function(val) { if (val) self.$nextTick(function() { self.syncStepVisibility(); }); });
            if (window.Livewire) {
                Livewire.hook('message.processed', function() {
                    self.$nextTick(function() { self.syncStepVisibility(); });
                });
            }
        },
        syncStepVisibility() {
            var steps = this.getSteps();
            var current = this.currentStep;
            for (var i = 0; i < steps.length; i++) { steps[i].style.display = (i === current) ? '' : 'none'; }
            if (current >= steps.length && steps.length > 0) { this.currentStep = steps.length - 1; }
        },
        openModal() {
            this.open = true;
            this.currentStep = 0;
            this.searchTerm = '';
            document.body.style.overflow = 'hidden';
            this.$nextTick(function() { this.syncStepVisibility(); }.bind(this));
        },
        closeModal() {
            this.open = false;
            this.searchTerm = '';
            document.body.style.overflow = '';
        },
        nextStep() {
            var steps = this.getSteps();
            if (this.currentStep < steps.length - 1) { this.currentStep++; this.searchTerm = ''; }
        },
        prevStep() {
            if (this.currentStep > 0) { this.currentStep--; this.searchTerm = ''; }
        }
    }" wire:ignore.self>
        <span x-ref="totalStepsRef" data-steps="{{ $totalSteps }}" style="display:none"></span>
        {{-- Filter Modal Toggle Button --}}
        <div class="filter-modal-toggle-bar">
            <button class="filter-modal-open-btn" @click="openModal()">
                <i class="fas fa-sliders-h"></i> Advanced Filters
                @if($hasActiveFilters)
                    <span class="filter-modal-badge">!</span>
                @endif
            </button>
        </div>

        {{-- Modal Overlay --}}
        <div x-show="open" x-cloak
             class="filter-modal-overlay"
             x-transition:enter="fm-fade-in"
             x-transition:leave="fm-fade-out"
             @keydown.escape.window="closeModal()">

        <div class="filter-modal-container" @click.outside="closeModal()">
            {{-- Modal Header --}}
            <div class="filter-modal-header">
                <div class="filter-modal-header-left">
                    <button x-show="currentStep > 0" @click="prevStep()" class="fm-nav-back">
                        <i class="fas fa-arrow-left"></i>
                    </button>
                    <h3 class="filter-modal-title"><i class="fas fa-filter"></i> Filters</h3>
                </div>
                <div class="filter-modal-header-right">
                    <span class="fm-step-indicator" x-text="'Step ' + (currentStep + 1) + ' / ' + totalSteps"></span>
                    <button @click="closeModal()" class="fm-close-btn"><i class="fas fa-times"></i></button>
                </div>
            </div>

            {{-- Progress Bar --}}
            <div class="fm-progress-bar">
                <div class="fm-progress-fill" :style="'width: ' + ((currentStep + 1) / totalSteps * 100) + '%'"></div>
            </div>

            {{-- Modal Body — one step per filter --}}
            <div class="filter-modal-body" x-ref="modalBody">
                @foreach($allFiltersList as $stepIndex => $filter)
                    <div class="fm-step-content" wire:key="fm-step-{{ $filter->id }}" style="display:none">
                        <div class="fm-step-label">
                            <i class="{{ $this->getGroupIcon($filter->group ?? 'General') }} fm-step-icon"></i>
                            <div>
                                <h4 class="fm-step-name">{{ $filter->name }}</h4>
                                <span class="fm-step-group">{{ $filter->group ?? '' }}</span>
                            </div>
                        </div>

                        <div class="fm-step-options">
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
                                <ul class="filter-options-list fm-options-list">
                                    @foreach($filter->options as $option)
                                        <li x-show="!searchTerm || '{{ addslashes(strtolower($option->title)) }}'.includes(searchTerm.toLowerCase())" wire:key="fm-cat-opt-{{ $option->id }}">
                                            <label class="filter-option-label">
                                                <input type="radio" wire:model.live="categorySlug" name="modalCategorySlug" value="{{ $option->slug }}" class="filter-radio">
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
                                <ul class="filter-options-list fm-options-list">
                                    @foreach($filter->options as $option)
                                        <li x-show="!searchTerm || '{{ addslashes(strtolower($option->title)) }}'.includes(searchTerm.toLowerCase())" wire:key="fm-subcat-opt-{{ $option->id }}">
                                            <label class="filter-option-label">
                                                <input type="radio" wire:model.live="selectedSubCategory" name="modalSubCategory" value="{{ $option->slug }}" class="filter-radio">
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
                                <ul class="filter-options-list fm-options-list">
                                    @foreach($filter->options as $option)
                                        <li x-show="!searchTerm || '{{ addslashes(strtolower($option->key)) }}'.includes(searchTerm.toLowerCase())" wire:key="fm-custom-opt-{{ $option->id }}">
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

            {{-- Modal Footer --}}
            <div class="filter-modal-footer">
                <button x-show="currentStep > 0" @click="prevStep()" class="fm-btn fm-btn-back">
                    <i class="fas fa-chevron-left"></i> Back
                </button>
                <div class="fm-footer-spacer" x-show="currentStep === 0"></div>

                <div class="fm-footer-right">
                    <button @click="closeModal()" class="fm-btn fm-btn-skip">
                        <i class="fas fa-check"></i> Apply & Close
                    </button>
                    <button x-show="currentStep < totalSteps - 1" @click="nextStep()" class="fm-btn fm-btn-next">
                        Next <i class="fas fa-chevron-right"></i>
                    </button>
                </div>
            </div>
        </div>
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

        /* ===== Filter Modal Overlay ===== */
        [x-cloak] { display: none !important; }

        .filter-modal-overlay {
            position: fixed;
            inset: 0;
            z-index: 9999;
            background: rgba(0, 0, 0, 0.5);
            backdrop-filter: blur(4px);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .fm-fade-in { animation: fmFadeIn 0.25s ease; }
        .fm-fade-out { animation: fmFadeOut 0.2s ease; }
        @keyframes fmFadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes fmFadeOut { from { opacity: 1; } to { opacity: 0; } }

        .filter-modal-container {
            background: #fff;
            border-radius: 16px;
            width: 100%;
            max-width: 560px;
            max-height: 85vh;
            display: flex;
            flex-direction: column;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.2);
            animation: fmSlideUp 0.3s ease;
            overflow: hidden;
        }
        @keyframes fmSlideUp {
            from { transform: translateY(30px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }

        /* Modal Header */
        .filter-modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 18px 24px;
            border-bottom: 1px solid #f0f0f0;
            background: #fafbfc;
        }
        .filter-modal-header-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .filter-modal-header-right {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .filter-modal-title {
            font-size: 18px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0;
        }
        .filter-modal-title i {
            color: var(--primary);
            margin-right: 6px;
        }
        .fm-nav-back {
            background: none;
            border: 1px solid #e9ecef;
            color: #495057;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .fm-nav-back:hover { background: #f0f0f0; }
        .fm-step-indicator {
            font-size: 13px;
            font-weight: 600;
            color: var(--primary);
            background: #fff5e6;
            padding: 4px 12px;
            border-radius: 20px;
        }
        .fm-close-btn {
            background: none;
            border: none;
            font-size: 18px;
            color: #adb5bd;
            cursor: pointer;
            width: 36px;
            height: 36px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.2s ease;
        }
        .fm-close-btn:hover { background: #fee2e2; color: #dc2626; }

        /* Progress Bar */
        .fm-progress-bar {
            height: 4px;
            background: #e9ecef;
            width: 100%;
        }
        .fm-progress-fill {
            height: 100%;
            background: linear-gradient(90deg, var(--primary), var(--secondary));
            border-radius: 0 4px 4px 0;
            transition: width 0.35s ease;
        }

        /* Modal Body */
        .filter-modal-body {
            flex: 1;
            overflow-y: auto;
            padding: 24px;
            scrollbar-width: thin;
            scrollbar-color: #dee2e6 transparent;
        }
        .filter-modal-body::-webkit-scrollbar { width: 4px; }
        .filter-modal-body::-webkit-scrollbar-thumb { background: #dee2e6; border-radius: 4px; }

        /* Step Content */
        .fm-step-content { min-height: 200px; }
        .fm-step-label {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 20px;
        }
        .fm-step-icon {
            color: var(--primary);
            font-size: 18px;
            width: 44px;
            height: 44px;
            background: linear-gradient(135deg, #fff5e6, #ffe8cc);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .fm-step-name {
            font-size: 17px;
            font-weight: 700;
            color: #1a1a2e;
            margin: 0 0 2px;
        }
        .fm-step-group {
            font-size: 12px;
            color: #adb5bd;
            font-weight: 500;
        }
        .fm-step-options { }
        .fm-options-list {
            max-height: 340px;
        }

        /* Modal Footer */
        .filter-modal-footer {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 16px 24px;
            border-top: 1px solid #f0f0f0;
            background: #fafbfc;
            gap: 12px;
        }
        .fm-footer-spacer { flex: 1; }
        .fm-footer-right {
            display: flex;
            align-items: center;
            gap: 10px;
            margin-left: auto;
        }
        .fm-btn {
            padding: 10px 22px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            border: none;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
        }
        .fm-btn-back {
            background: #f0f0f0;
            color: #495057;
        }
        .fm-btn-back:hover { background: #e2e2e2; }
        .fm-btn-skip {
            background: #e8f5e9;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        .fm-btn-skip:hover { background: #c8e6c9; }
        .fm-btn-next {
            background: linear-gradient(135deg, var(--primary), var(--primary-dark));
            color: #fff;
            box-shadow: 0 3px 10px rgba(248, 153, 27, 0.3);
        }
        .fm-btn-next:hover {
            transform: translateY(-1px);
            box-shadow: 0 5px 15px rgba(248, 153, 27, 0.4);
        }

        /* ===== Inline Search (reused inside modal) ===== */
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

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .filter-modal-toggle-bar { padding: 0 15px; }
            .filter-modal-container {
                max-width: 100%;
                max-height: 95vh;
                border-radius: 14px 14px 0 0;
                margin-top: auto;
            }
            .filter-modal-overlay {
                align-items: flex-end;
                padding: 0;
            }
            .active-filters-bar { padding: 0 15px; }
        }
    </style>


</div>
