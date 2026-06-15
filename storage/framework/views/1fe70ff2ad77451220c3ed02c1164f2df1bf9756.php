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
                        <?php $__currentLoopData = $allCategories; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $category): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <option value="<?php echo e($category->slug); ?>"><?php echo e($category->title); ?></option>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <aside class="sidebar">
            <h2 class="sidebar-title"><i class="fas fa-filter"></i> Filters</h2>
            
            <div class="filter-section active">
                <?php
                    $groupedFilters = $this->allFilters->groupBy('group');
                ?>

                <?php $__currentLoopData = $groupedFilters; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $groupName => $filtersInGroup): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <div class="main-filter-group" x-data="{ open: false }" wire:key="main-group-<?php echo e($groupName); ?>">
                        <h2 @click="open = !open" class="main-group-heading">
                            <span><i class="<?php echo e($this->getGroupIcon($groupName)); ?>"></i> <?php echo e($groupName); ?></span>
                            <i class="fas fa-chevron-down toggle-icon" :class="{ 'rotate-180': open }"></i>
                        </h2>
                        <div x-show="open" x-collapse.duration.300ms>
                            <?php $__currentLoopData = $filtersInGroup; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $filter): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <?php
                                    $filterIndex = $this->allFilters->search(function ($item) use ($filter) {
                                        return $item->id == $filter->id;
                                    });
                                ?>
                                <div class="filter-group" wire:key="filter-group-<?php echo e($filter->id); ?>">
                                    <h3 wire:click="openFilterModal(<?php echo e($filterIndex); ?>)" class="filter-heading">
                                        <span><?php echo e($filter->name); ?></span>
                                        <i class="fas fa-chevron-right toggle-icon-inner"></i>
                                    </h3>
                                    <div>
                                        <?php if($filter->id === 'main_categories' && $categorySlug): ?>
                                            <div class="tags-container">
                                                <span class="tag-item">
                                                    <?php echo e($allCategories->firstWhere('slug', $categorySlug)->title ?? ''); ?>

                                                    <button wire:click="$set('categorySlug', '')" title="Remove"><i class="fas fa-times"></i></button>
                                                </span>
                                            </div>
                                        <?php elseif($filter->id === 'sub_categories' && $selectedSubCategory): ?>
                                            <div class="tags-container">
                                                <span class="tag-item">
                                                    <?php echo e($subCategories->firstWhere('slug', $selectedSubCategory)->title ?? $selectedSubCategory); ?>

                                                    <button wire:click="removeSubCategory()" title="Remove"><i class="fas fa-times"></i></button>
                                                </span>
                                            </div>
                                        <?php elseif(!in_array($filter->type, ['price', 'distance', 'radio', 'main_category_radio']) && !empty($customFilters[$filter->id]) && is_array($customFilters[$filter->id]) && count($customFilters[$filter->id]) > 0): ?>
                                            <div class="tags-container">
                                                <?php $__currentLoopData = $customFilters[$filter->id]; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $selectedOption): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                                    <span class="tag-item">
                                                        <?php echo e($selectedOption); ?>

                                                        <button wire:click="removeCustomFilter('<?php echo e($filter->id); ?>', '<?php echo e($selectedOption); ?>')" title="Remove">
                                                            <i class="fas fa-times"></i>
                                                        </button>
                                                    </span>
                                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </div>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </div>
        </aside>

        <main class="main-content">
            <div class="top-bar">
                <div class="results-count">Showing <?php echo e($filtered_data->count()); ?> of <?php echo e($total_posts); ?> results</div>
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
        
            <div wire:loading wire:target="searchQuery, categorySlug, selectedSubCategory, minPrice, maxPrice, sortBy, distance, customFilters" style="width: 100%; text-align: center; padding: 40px;">
                <div class="loading-spinner" style="display: inline-block;"></div>
            </div>
        
            <div wire:loading.remove wire:target="searchQuery, categorySlug, selectedSubCategory, minPrice, maxPrice, sortBy, distance, customFilters">
                <div class="card-grid">
                    <?php $__empty_1 = true; $__currentLoopData = $filtered_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php echo App\Models\Setting::htmlAdBlock($product->id); ?>

                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <p style="grid-column: 1 / -1; text-align: center; padding: 40px;">No products found matching your criteria.</p>
                    <?php endif; ?>
                </div>
                
                <?php if($filtered_data->count() < $total_posts): ?>
                    <div class="load-more-btn-container">
                        <button class="load-more-btn" wire:click="loadMore"><i class="fas fa-arrow-down"></i> Load More</button>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>

    <?php if($showFilterModal && $this->currentFilter): ?>
    <div class="modal-overlay" x-data @click.self="$wire.closeFilterModal()">
        <div class="modal-content">
            <div class="modal-header">
                <?php if(count($modalHistory) > 1): ?>
                    <button wire:click="navigateBack()" class="modal-back-btn"><i class="fas fa-arrow-left"></i> Back</button>
                <?php else: ?>
                    <span></span>
                <?php endif; ?>
                
                <h2 class="modal-title">
                    <?php echo e($this->currentFilter->name); ?>


                    <?php if($this->currentFilter->type === 'distance'): ?>
                        (<?php echo e($distance); ?> km)
                    <?php endif; ?>
                </h2>

                <button class="modal-close" wire:click="closeFilterModal()" title="Close">&times;</button>
            </div>
            <div class="modal-body">
                <?php if($this->currentFilter->type === 'price'): ?>
                    <div class="price-filter" style="padding: 20px 10px;">
                        <div class="price-range-slider">
                            <input type="range" min="0" max="500000" step="1000" wire:model.live="minPrice" class="slider-min">
                            <input type="range" min="0" max="500000" step="1000" wire:model.live="maxPrice" class="slider-max">
                        </div>
                        <div class="price-values"><span> <?php echo e(number_format($minPrice)); ?></span><span> <?php echo e(number_format($maxPrice)); ?></span></div>
                    </div>
                <?php elseif($this->currentFilter->type === 'distance'): ?>
                     <div class="distance-filter" style="padding: 20px 10px;">
                        <div class="distance-range-slider">
                            <input type="range" min="500" max="5000" step="10" wire:model.live="distance" class="distance-slider" style="width: 100%;">
                        </div>
                        <div class="distance-values"><span>500 km</span><span><?php echo e($maxDistance); ?> km</span></div>
                    </div>
                <?php else: ?>
                    <div class="modal-search-wrapper">
                        <i class="fas fa-search"></i>
                        <input type="text" 
                               wire:model.live.debounce.300ms="modalSearchTerm" 
                               placeholder="Search options..." 
                               class="modal-search-input">
                    </div>
                    <ul class="modal-options-list">
                        <?php if($this->currentFilter->type === 'main_category_radio'): ?>
                            <?php $__currentLoopData = $this->currentFilter->options->filter(function($option) {
                                return empty($this->modalSearchTerm) || stripos($option->title, $this->modalSearchTerm) !== false;
                            }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li wire:key="modal-option-main-cat-<?php echo e($option->id); ?>">
                                    <label class="modal-option">
                                        <input type="radio" wire:model.live="categorySlug" name="categorySlugModal" value="<?php echo e($option->slug); ?>">
                                        <span class="option-label"><?php echo e($option->title); ?></span>
                                        <span class="custom-radio"></span>
                                    </label>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php elseif($this->currentFilter->type === 'radio'): ?>
                             <?php $__currentLoopData = $this->currentFilter->options->filter(function($option) {
                                return empty($this->modalSearchTerm) || stripos($option->title, $this->modalSearchTerm) !== false;
                            }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li wire:key="modal-option-sub-cat-<?php echo e($option->id); ?>">
                                    <label class="modal-option">
                                        <input type="radio" wire:model.live="selectedSubCategory" name="selectedSubCategoryModal" value="<?php echo e($option->slug); ?>">
                                        <span class="option-label"><?php echo e($option->title); ?></span>
                                        <span class="custom-radio"></span>
                                    </label>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php else: ?>
                            <?php $__currentLoopData = $this->currentFilter->options->filter(function($option) {
                                return empty($this->modalSearchTerm) || stripos($option->key, $this->modalSearchTerm) !== false;
                            }); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $option): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                <li wire:key="modal-option-<?php echo e($option->id); ?>">
                                    <label class="modal-option">
                                        <input type="checkbox" wire:model.live="customFilters.<?php echo e($this->currentFilter->id); ?>" value="<?php echo e($option->key); ?>">
                                        <span class="option-label"><?php echo e($option->key); ?></span>
                                        <span class="custom-checkbox"></span>
                                    </label>
                                </li>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                        <?php endif; ?>
                    </ul>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button class="done-btn" wire:click="closeFilterModal()">Done</button>
                <?php if(isset($this->allFilters[end($modalHistory) + 1])): ?>
                    <button class="next-step-footer-btn" wire:click="navigateToNextStep()">Next &gt;</button>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <style>
        .main-filter-group {
            border-bottom: 1px solid #e9ecef;
        }
        .main-group-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 5px;
            cursor: pointer;
            font-size: 1.1rem;
            font-weight: 700;
            color: #343a40;
        }
        .main-group-heading .toggle-icon {
            transition: transform 0.3s ease;
        }
        .main-group-heading .toggle-icon.rotate-180 {
            transform: rotate(180deg);
        }
        .filter-group {
            border-bottom: 1px solid #f1f3f5;
            margin-left: 15px;
        }
        .filter-group:last-of-type {
            border-bottom: none;
        }
        .filter-heading {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 12px 5px;
            cursor: pointer;
            font-size: 1rem;
            font-weight: 500;
            color: #495057;
        }
        .filter-heading:hover {
            color: #3498db;
        }
        .filter-heading span {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .filter-heading .toggle-icon-inner {
            font-size: 12px;
            color: #adb5bd;
        }
        .tags-container {
            padding: 0 5px 15px 5px;
        }
        
        .loading-spinner { border: 4px solid #f3f3f3; border-top: 4px solid #3498db; border-radius: 50%; width: 40px; height: 40px; animation: spin 1s linear infinite; }
        @keyframes  spin { 0% { transform: rotate(0deg); } 100% { transform: rotate(360deg); } }
        .main-content { position: relative; }
        .tag-item { background-color: #e9ecef; color: #495057; padding: 4px 10px; border-radius: 9999px; font-size: 14px; display: inline-flex; align-items: center; }
        .tag-item button { background: none; border: none; color: #adb5bd; margin-left: 8px; cursor: pointer; padding: 0; line-height: 1; }
        .tag-item button:hover { color: #495057; }
        .modal-overlay { position: fixed; top: 0; left: 0; right: 0; bottom: 0; background-color: rgba(0, 0, 0, 0.5); z-index: 1000; display: flex; justify-content: center; align-items: center; padding: 20px; }
        
        .modal-content {
            background-color: #fff;
            width: 100%;
            max-width: 480px;
            border-radius: 12px;
            box-shadow: 0 10px 25px rgba(0,0,0,0.1);
            animation: fadeIn 0.3s ease-out;
            
            display: flex;
            flex-direction: column;
            
            height: 75vh;
            max-height: 700px;
        }

        @keyframes  fadeIn { from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); } }
        
        .modal-header { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; border-bottom: 1px solid #e9ecef; flex-shrink: 0; }
        .modal-title { font-size: 18px; color: #212529; font-weight: 600; text-align: center; flex-grow: 1; }
        .modal-back-btn { background: none; border: none; font-size: 16px; cursor: pointer; color: #3498db; font-weight: 600; display: flex; align-items: center; gap: 5px; }
        .modal-close { background: #f1f3f5; border: none; font-size: 16px; cursor: pointer; color: #495057; border-radius: 50%; width: 32px; height: 32px; line-height: 32px; text-align: center; }
        
        .modal-body { 
            flex-grow: 1;
            overflow-y: auto;
            padding: 8px 0;
        }

        .modal-search-wrapper { position: relative; padding: 16px 24px; border-bottom: 1px solid #e9ecef; flex-shrink: 0; }
        .modal-search-wrapper i { position: absolute; left: 38px; top: 50%; transform: translateY(-50%); color: #adb5bd; }
        .modal-search-input { width: 100%; padding: 12px 12px 12px 35px; border: 1px solid #ced4da; border-radius: 8px; font-size: 16px; }
        .modal-search-input:focus { border-color: #3498db; outline: none; box-shadow: 0 0 0 2px rgba(52, 152, 219, 0.2); }
        
        .modal-options-list { list-style: none; padding: 0; margin: 0; }
        .modal-option { display: flex; align-items: center; justify-content: space-between; padding: 16px 24px; cursor: pointer; border-bottom: 1px solid #f1f3f5; transition: background-color 0.2s ease; }
        .modal-option:hover { background-color: #f8f9fa; }
        .modal-option input[type="radio"], .modal-option input[type="checkbox"] { display: none; }
        .option-label { font-size: 16px; color: #495057; }
        .custom-radio, .custom-checkbox { width: 20px; height: 20px; border: 2px solid #adb5bd; border-radius: 50%; display: inline-block; position: relative; flex-shrink: 0; }
        .custom-checkbox { border-radius: 4px; }
        .modal-option input:checked + .option-label + .custom-radio,
        .modal-option input:checked + .option-label + .custom-checkbox { border-color: #3498db; background-color: #3498db; }
        .modal-option input:checked + .option-label + .custom-radio:after { content: ''; position: absolute; top: 5px; left: 5px; width: 8px; height: 8px; background: white; border-radius: 50%; }
        .modal-option input:checked + .option-label + .custom-checkbox:after { content: '✓'; position: absolute; top: -2px; left: 4px; font-size: 16px; color: white; }
        
        .modal-footer { padding: 16px 24px; border-top: 1px solid #e9ecef; display: flex; justify-content: space-between; align-items:center; background-color: #f8f9fa; flex-shrink: 0; }
        .done-btn, .next-step-footer-btn { background-color: #3498db; color: white; border: none; padding: 12px 25px; border-radius: 8px; cursor: pointer; font-size: 16px; font-weight: 600; }
        
        .price-values, .distance-values { display: flex; justify-content: space-between; margin-top: 5px; font-weight: 600; color: #495057; }
    </style>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/search-detail.blade.php ENDPATH**/ ?>