<?php

namespace App\Http\Livewire;

use App\Models\TblCategory;
use App\Models\TblPost;
use App\Models\User;
use App\Models\TblFieldsDetail;
use App\Models\TblFieldsOption;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Illuminate\Support\Collection;

class SearchDetail extends Component
{
    public $searchQuery = '', $categorySlug, $selectedSubCategory = null;
    public $minPrice = 0, $maxPrice = 500000;
    public $sortBy = 'post-desc';
    public $distance = 500, $latitude, $longitude, $maxDistance = 5000;

    public $customFilters = [];
    public $customFieldsForView = [];
    public $selectedCities = [];
    public $locationText = '';
    public $subCatDrillParent = null; // tracks sub-cat drill-down parent slug

    public $showFilterModal = false;
    public $modalHistory = [];
    public $modalSearchTerm = '';
    
    public Collection $allCategories;
    public $selectedCategory;
    public Collection $subCategories;
    public $perPage = 12;

    protected $queryString = [
        'searchQuery' => ['except' => '', 'as' => 's'],
        'categorySlug' => ['except' => '', 'as' => 'c'],
        'selectedSubCategory' => ['except' => '', 'as' => 'subcat'],
        'minPrice' => ['except' => 0],
        'maxPrice' => ['except' => 500000],
        'sortBy' => ['except' => 'post-desc'],
        'distance' => ['except' => 5000, 'as' => 'd'],
        'latitude' => ['except' => '', 'as' => 'lat'],
        'longitude' => ['except' => '', 'as' => 'lng'],
        'customFilters' => ['except' => []],
        'selectedCities' => ['except' => [], 'as' => 'cities'],
        'locationText' => ['except' => '', 'as' => 'loc'],
    ];
    
    private function getFilterGroup($filterName, $categoryName = null)
    {
        $categoryName = strtolower($categoryName ?? '');
        $filterName = strtolower($filterName);

        if (in_array($filterName, ['categories', 'sub categories'])) {
            return 'Category';
        }

        if (in_array($filterName, ['price range', 'distance'])) {
            return 'General';
        }

        return match ($categoryName) {
            'automobiles' => match ($filterName) {
                'make', 'body type', 'transmission', 'fuel type', 'cylinders', 'drive type', 'seats', 'doors' => 'Vehicle Specifics',
                'features', 'colors', 'safety rating' => 'Features & Options',
                'vehicle identification', 'engine', 'engine power', 'towing capacity', 'gross vehicle mass' => 'Technical Details',
                default => 'Other Details',
            },
            'phones & tablets' => match ($filterName) {
                'brand', 'storage', 'ram', 'screen size', 'color' => 'Device Specs',
                'condition', 'warranty' => 'Purchase Details',
                default => 'Other Features',
            },
            'fashion' => match ($filterName) {
                'size', 'color', 'brand', 'gender' => 'Item Details',
                'condition' => 'Purchase Details',
                default => 'Other',
            },
            default => 'General Details',
        };
    }

    public function getGroupIcon($groupName)
    {
        return match (strtolower($groupName)) {
            'category' => 'fas fa-list',
            'vehicle specifics' => 'fas fa-car',
            'features & options' => 'fas fa-star',
            'technical details' => 'fas fa-cogs',
            'device specs' => 'fas fa-mobile-alt',
            'item details' => 'fas fa-tshirt',
            'purchase details' => 'fas fa-receipt',
            'general' => 'fas fa-sliders-h',
            default => 'fas fa-info-circle',
        };
    }

    public function getCurrentFilterProperty()
    {
        if (empty($this->modalHistory)) {
            return null;
        }
        $index = end($this->modalHistory);
        return $this->allFilters[$index] ?? null;
    }

    public function openFilterModal($filterIndex)
    {
        $this->modalHistory = [$filterIndex];
        $this->modalSearchTerm = '';
        $this->showFilterModal = true;
    }

    public function closeFilterModal()
    {
        $this->showFilterModal = false;
        $this->modalHistory = [];
        $this->modalSearchTerm = '';
    }

    public function navigateBack()
    {
        array_pop($this->modalHistory);
        $this->modalSearchTerm = '';
        if (empty($this->modalHistory)) {
            $this->showFilterModal = false;
        }
    }

    public function navigateToNextStep()
    {
        $currentIndex = end($this->modalHistory);
        $nextIndex = $currentIndex + 1;
        if (isset($this->allFilters[$nextIndex])) {
            $this->modalHistory[] = $nextIndex;
            $this->modalSearchTerm = '';
        }
    }

    public function getAllFiltersProperty(): Collection
    {
        $allFilters = collect();

        $allFilters->push((object)[
            'id'      => 'main_categories',
            'name'    => 'Categories',
            'type'    => 'main_category_radio',
            'options' => $this->allCategories,
            'group'   => $this->getFilterGroup('Categories', $this->selectedCategory->title ?? null),
        ]);

        if ($this->subCategories->isNotEmpty()) {
            $allFilters->push((object)[
                'id'      => 'sub_categories',
                'name'    => 'Sub Categories',
                'type'    => 'radio',
                'options' => $this->subCategories,
                'group'   => $this->getFilterGroup('Sub Categories', $this->selectedCategory->title ?? null),
            ]);
        }
        
        $customFields = collect($this->customFieldsForView)
            ->where('form_field_name', '!=', 'modelswithbrand')
            ->filter(function ($field) {
                return in_array($field->type, ['select', 'autocomplete', 'checkbox-group', 'radio-group']);
            })
            ->map(function ($field) {
                $field->group = $this->getFilterGroup($field->name, $this->selectedCategory->title ?? null);
                return $field;
            });

        foreach ($customFields as $field) {
            $allFilters->push($field);

            if ($field->form_field_name === 'brandwithmodel') {
                $selectedBrands = $this->customFilters[$field->id] ?? [];
                $selectedBrands = is_array($selectedBrands) ? array_filter($selectedBrands) : [];

                if (!empty($selectedBrands)) {
                    $modelOptions = collect();
                    $brandOptions = TblFieldsOption::where('field_id', $field->id)
                        ->whereIn('key', $selectedBrands)
                        ->get();

                    foreach ($brandOptions as $brandOpt) {
                        if (!empty($brandOpt->value)) {
                            $models = [];
                            if (substr($brandOpt->value, 0, 1) === '[') {
                                $models = json_decode($brandOpt->value, true) ?? [];
                            } else {
                                $models = array_map('trim', explode(',', $brandOpt->value));
                            }
                            foreach ($models as $model) {
                                if ($model) {
                                    $modelOptions->push((object)[
                                        'id' => strtolower(str_replace(' ', '-', $model)),
                                        'key' => $model,
                                    ]);
                                }
                            }
                        }
                    }

                    if ($modelOptions->isNotEmpty()) {
                        $modelFieldId = $field->id . '_models';
                        if (!isset($this->customFilters[$modelFieldId])) {
                            $this->customFilters[$modelFieldId] = [];
                        }
                        $allFilters->push((object)[
                            'id'      => $modelFieldId,
                            'name'    => 'Model',
                            'type'    => 'checkbox-group',
                            'options' => $modelOptions->sortBy('key')->values(),
                            'group'   => $this->getFilterGroup('Model', $this->selectedCategory->title ?? null),
                            'form_field_name' => 'models_cascade',
                        ]);
                    }
                }
            }
        }

        $allFilters->push((object)[
            'id'      => 'price_range',
            'name'    => 'Price Range',
            'type'    => 'price',
            'options' => collect(),
            'group'   => $this->getFilterGroup('Price Range', $this->selectedCategory->title ?? null),
        ]);

        $allFilters->push((object)[
            'id'      => 'distance',
            'name'    => 'Distance',
            'type'    => 'distance',
            'options' => collect(),
            'group'   => $this->getFilterGroup('Distance', $this->selectedCategory->title ?? null),
        ]);

        return $allFilters;
    }

    
    public function mount()
    {
        $this->allCategories = TblCategory::withDepth()->having('depth', '=', 0)->get();
        $this->subCategories = collect();
        if ($this->categorySlug) {
            $this->loadCategoryDetails();
        }
    }

    public function clearAllFilters()
    {
        $this->reset(['categorySlug', 'selectedSubCategory', 'minPrice', 'maxPrice', 'customFilters', 'customFieldsForView', 'distance', 'selectedCities', 'locationText', 'latitude', 'longitude', 'subCatDrillParent']);
        $this->selectedCategory = null;
        $this->subCategories = collect();
        $this->minPrice = 0;
        $this->maxPrice = 500000;
        $this->distance = 500;
    }

    public function clearPriceFilter()
    {
        $this->minPrice = 0;
        $this->maxPrice = 500000;
    }

    public function updatedCategorySlug($slug)
    {
        $this->reset('selectedSubCategory', 'customFilters', 'customFieldsForView', 'subCatDrillParent');
        
        if ($slug) {
            $this->loadCategoryDetails();
        } else {
            $this->selectedCategory = null;
            $this->customFieldsForView = [];
            $this->subCategories = collect();
        }
    }

    public function updatedSelectedSubCategory($slug)
    {
        $this->reset('customFieldsForView', 'customFilters');
        if ($slug) {
            $category = TblCategory::where('slug', $slug)->first();
            if ($category) $this->prepareCustomFieldsForView($category->id);
        } elseif ($this->categorySlug) {
            $this->loadCategoryDetails();
        }
    }

    public function removeSubCategory()
    {
        $this->selectedSubCategory = null;
        $this->subCatDrillParent = null;
        $this->reset('customFieldsForView', 'customFilters');
        if ($this->categorySlug) $this->loadCategoryDetails();
    }

    public function drillIntoSubCat($slug)
    {
        $category = TblCategory::where('slug', $slug)->first();
        if (!$category) return;

        $children = TblCategory::where('parent_id', $category->id)->get();
        if ($children->isNotEmpty()) {
            $this->subCatDrillParent = $slug;
            $this->subCategories = $children;
        } else {
            // Leaf node — select sub-category and load custom fields
            $this->selectedSubCategory = $slug;
            $this->subCatDrillParent = null;
            $this->reset('customFieldsForView', 'customFilters');
            $this->prepareCustomFieldsForView($category->id);
            // If no filterable custom fields, close modal
            $filterableFields = collect($this->customFieldsForView)->filter(fn($f) => in_array($f->type, ['select','autocomplete','checkbox-group','radio-group']));
            if ($filterableFields->isEmpty()) {
                $this->dispatchBrowserEvent('af-close-modal');
            }
        }
    }

    public function drillBackSubCat()
    {
        $this->subCatDrillParent = null;
        if ($this->categorySlug) {
            $category = TblCategory::where('slug', $this->categorySlug)->first();
            if ($category) {
                $this->subCategories = TblCategory::where('parent_id', $category->id)->get();
            }
        }
    }

    public function drillBackFromFields()
    {
        $this->selectedSubCategory = null;
        $this->reset('customFieldsForView', 'customFilters');
        if ($this->categorySlug) {
            $this->loadCategoryDetails();
        }
    }
    
    public function setLocation($lat, $lng, $text)
    {
        $this->latitude = $lat;
        $this->longitude = $lng;
        $this->locationText = $text;
    }

    public function clearLocation()
    {
        $this->latitude = null;
        $this->longitude = null;
        $this->locationText = '';
    }

    public function removeCity($cityId)
    {
        $this->selectedCities = array_values(array_filter($this->selectedCities, fn($id) => $id !== $cityId));
    }

    public function removeCustomFilter($fieldId, $optionKey)
    {
        if (isset($this->customFilters[$fieldId]) && is_array($this->customFilters[$fieldId])) {
            $this->customFilters[$fieldId] = array_values(array_filter($this->customFilters[$fieldId], function ($val) use ($optionKey) {
                return $val !== $optionKey;
            }));
        }
    }

    public function loadMore() { $this->perPage += 12; }

    private function loadCategoryDetails() {
        $category = TblCategory::where('slug', $this->categorySlug)->first();
        if ($category) {
            $this->selectedCategory = $category;
            $this->subCategories = TblCategory::where('parent_id', $category->id)->get();
            $this->prepareCustomFieldsForView($category->id);
        }
    }

    public function prepareCustomFieldsForView($categoryId) {
        if (!$categoryId) {
            $this->customFieldsForView = [];
            return;
        }
        $this->customFieldsForView = TblFieldsDetail::where('cat_id', $categoryId)
            ->where('active', '1')->where('filter', '1')->with('options')->get();
        
        foreach ($this->customFieldsForView as $field) {
            if (in_array($field->type, ['checkbox-group', 'select', 'autocomplete', 'radio-group'])) {
                if (!isset($this->customFilters[$field->id]) || !is_array($this->customFilters[$field->id])) {
                    $this->customFilters[$field->id] = [];
                }
            }
        }
    }
	
    public function render() {
        $query = TblPost::query()
            ->where('active', 1)->where('sold_status', 0)->whereNull('deleted_at')
            ->whereNotIn('user_id', User::blocked_users() ?? []);

        $query->when($this->searchQuery, fn($q) => $q->where('title', 'like', '%' . $this->searchQuery . '%'));
        
        $finalCategorySlug = $this->selectedSubCategory ?: $this->categorySlug;
        
        $query->when($finalCategorySlug, function ($q) use ($finalCategorySlug) {
            $category = TblCategory::where('slug', $finalCategorySlug)->first();
            if ($category) { 
                $q->whereIn('category_id', TblCategory::descendantsAndSelf($category->id)->pluck('id')); 
            }
        });
        
        if ($this->minPrice > 0 || $this->maxPrice < 500000) { $query->whereBetween('price', [(int)$this->minPrice, (int)$this->maxPrice]); }
        
        if (!empty($this->selectedCities)) {
            $query->whereIn('city', $this->selectedCities);
        } elseif ($this->latitude && $this->longitude) {
            $cityIds = TblPost::get_surrounding_city_ids($this->latitude, $this->longitude, $this->distance);
            if (!empty($cityIds)) { $query->whereIn('city', $cityIds); } else { $query->whereRaw('1 = 0'); }
        }
        
        if (isset($this->customFilters)) {
             $query->applyCustomFilters($this->customFilters);
        }

        $filteredPostIds = $query->pluck('id')->toArray();
        $finalOrderedIds = $this->getOrderedPostIds($filteredPostIds);
        [$sort_key, $sort_ord] = $this->getSortParameters();

        $posts = collect(); $totalPosts = 0;
        if (!empty($finalOrderedIds)) {
            $postsQuery = TblPost::whereIn('id', $finalOrderedIds)->with(['city_name', 'category']);
            if ($sort_key && $sort_ord) { $postsQuery->orderBy($sort_key, $sort_ord); } 
            else {
                $ids_ordered = implode(',', array_map('intval', $finalOrderedIds));
                if (!empty($ids_ordered)) { $postsQuery->orderByRaw(DB::raw("FIELD(id, $ids_ordered)")); }
            }
            $totalPosts = $postsQuery->count();
            $posts = $postsQuery->take($this->perPage)->get();
        }
        
        return view('livewire.search-detail', ['filtered_data' => $posts, 'total_posts' => $totalPosts]);
    }
    
    private function getOrderedPostIds(array $filteredPostIds): array {
        $payment_ids = TblPost::get_unexpired_payment_post_ids();
        $free_ids = TblPost::get_unexpired_free_post_ids();
        $payment_ads = array_intersect($payment_ids, $filteredPostIds);
        $free_ads = array_intersect($free_ids, $filteredPostIds);
        $big = count($free_ads) > count($payment_ads) ? $free_ads : $payment_ads;
        $small = count($free_ads) > count($payment_ads) ? $payment_ads : $free_ads;
        for ($pid = 0; $pid <= count($big); $pid += 4) {
            $ids = array_slice($small, $pid, 4);
            if ($pid != 0) { $pid = $pid + 4; }
            foreach ($ids as $k) { array_splice($big, $pid, 0, $k); }
        }
        return $big;
    }

    private function getSortParameters() {
        switch ($this->sortBy) {
            case 'price-asc': return ['price', 'asc'];
            case 'price-desc': return ['price', 'desc'];
            case 'most-viewed': return ['views_count', 'desc'];
            case 'post-desc': default: return ['created_at', 'desc'];
        }
    }
}