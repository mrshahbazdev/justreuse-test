@php
$dir_rtl = App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
$class_dir_float_r = ($dir_rtl == "true") ? '' : 'float-right';
$class_dir_float_lr = ($dir_rtl == "true") ? 'float-right' : 'float-left';
$class_dir_text_lr = ($dir_rtl == "true") ? 'text-right' : 'text-left';
$class_dir_action_btn = ($dir_rtl == "true") ? 'space-x-reverse' : '';
@endphp

<div class="modern-ads-container" {{ $class_dir }}>
    @php
    $settings = App\Models\Setting::get_logos();
    $meta_title = auth()->user()->name . " | " . $settings['name'];
    @endphp
    @section('meta_title', $meta_title)

    @if ($message = Session::get('message'))
    <script>
        toastr.success("{{ $message }}");
    </script>
    @endif

    @if($slug = Session::get('slug'))
    <div class="success-popup-overlay" id="successPopup">
        <div class="success-popup-content">
            <div class="success-icon">
                <i class="fas fa-check"></i>
            </div>
            <h2>Congratulations!</h2>
            <p>Your ad should be live in no time!<br>To view your ads, go to My ads.</p>
            <a href="/{{ Session::get('slug') }}" class="btn btn-warning">
                <i class="fas fa-ad"></i>
                My ads
            </a>
        </div>
    </div>
    <script>
        document.getElementById('successPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                this.style.display = 'none';
            }
        });
    </script>
    @endif

    <!-- Modern Header -->
    <div class="modern-header">
        <div class="container">
            <div class="header-content">
                <div class="header-text">
                    <h1 class="page-title">{{__('p_myads.my ads')}}</h1>
                    <p class="page-subtitle">Manage and track your advertisements</p>
                </div>
                <div class="header-actions">
                    @php
                    $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                    if (!empty($post_methods)) {
                        $check_banner_ads = $post_methods->pluck('name')->toArray();
                        if (in_array("bannerads", $check_banner_ads)) {
                    @endphp
                    <a  href="{{ URL::to('/advertise/create') }}" class="btn btn-primary btn-lg">
                        <i class="fas fa-plus-circle"></i>
                        {{__('p_my_banner_ads.banner advertisement')}}
                    </a>
                    @php
                        }
                    }
                    @endphp
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <!-- Stats Overview -->
        <div class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-ad"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">{{ $list->total() }}</div>
                    <div class="stat-label">Total Ads</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-eye"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        @php
                            $totalViews = 0;
                            foreach($list as $post) {
                                $totalViews += App\Models\TblPostInsight::views_count($post['id']);
                            }
                            echo $totalViews;
                        @endphp
                    </div>
                    <div class="stat-label">Total Views</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-check-circle"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        {{ $list->where('sold_status', 1)->count() }}
                    </div>
                    <div class="stat-label">Sold Items</div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">
                    <i class="fas fa-clock"></i>
                </div>
                <div class="stat-content">
                    <div class="stat-number">
                        @php
                            $expiredCount = 0;
                            foreach($list as $post) {
                                $check_post_package = App\Models\TblPost::check_post_expired($post['id']);
                                if($check_post_package['expired'] == "Expired") {
                                    $expiredCount++;
                                }
                            }
                            echo $expiredCount;
                        @endphp
                    </div>
                    <div class="stat-label">Expired</div>
                </div>
            </div>
        </div>

        <!-- Main Content Card -->
        <div class="main-card">
            <!-- Bulk Actions -->
            <div class="bulk-actions-bar">
                <div class="bulk-left">
                    <div class="checkbox-group">
                        <label class="custom-checkbox">
                            <input type="checkbox" wire:model="selectAll" class="hidden-checkbox">
                            <span class="checkmark"></span>
                            <span class="checkbox-label">{{__('p_myads.select all')}}</span>
                        </label>
                    </div>
                    @if($selectedPosts)
                    <button wire:click="confirmMultiDelete" wire:loading.attr="disabled" class="btn btn-danger btn-sm">
                        <i class="fas fa-trash"></i>
                        Delete Selected ({{ count($selectedPosts) }})
                    </button>
                    @endif
                </div>
            </div>

            <!-- Filters Section -->
            <div class="filters-section">
                <div class="filter-group">
                    <div class="select-wrapper">
                        <select wire:model="pid" class="form-select">
                            <option value=""> {{__('p_myads.select package name')}} </option>
                            @if(!empty($packages_list))
                            @foreach ($packages_list as $packages_list_item)
                            @if($packages_list_item->short_name == "free")
                            <option value="free">{{ $packages_list_item->name }}</option>
                            @else
                            <option value="{{ $packages_list_item->id }}">{{ $packages_list_item->name }}</option>
                            @endif
                            @endforeach
                            @endif
                        </select>
                        <i class="select-icon fas fa-chevron-down"></i>
                    </div>
                </div>

                <div class="filter-group search-group">
                    <div class="search-wrapper">
                        <i class="search-icon fas fa-search"></i>
                        <input type="text" wire:model="search" class="search-input" placeholder="{{__('p_myads.search here')}}">
                    </div>
                </div>
            </div>

            <!-- Ads Grid -->
            @if(count($list) > 0)
            <div class="ads-grid">
                @foreach($list as $post)
                @php
                $img = App\Models\Setting::getImage($post['id'], 'medium');
                $currency_symbol = App\Models\TblPost::get_post_currency($post['currency_id']);
                $slug = App\Models\TblPost::get_post_slug($post["slug"]);
                $check_post_package = App\Models\TblPost::check_post_expired($post['id']);
                $settings = App\Models\Setting::get_logos();
                $default_currency = App\Models\TblPost::get_post_currency($settings['default_currency']);
                $viewcount = App\Models\TblPostInsight::views_count($post['id']);
                $pack_from_date = \Carbon\Carbon::parse($check_post_package['from_date'])->isoFormat('DD MMM YYYY');
                $pack_to_date = \Carbon\Carbon::parse($check_post_package['to_date'])->isoFormat('DD MMM YYYY');
                @endphp

                <div class="ad-card">
                    <div class="ad-checkbox">
                        <label class="custom-checkbox">
                            <input type="checkbox" class="hidden-checkbox del_check" wire:model="selectedPosts" value="{{ $post->id }}">
                            <span class="checkmark"></span>
                        </label>
                    </div>

                    <div class="ad-image-section">
                        <a href="{{ $slug }}" class="ad-image-link">
                            <img src="{{ $img }}" alt="{{ $post['title'] }}" class="ad-image">
                            <div class="ad-overlay">
                                <span class="view-btn">View Details</span>
                            </div>
                        </a>
                    </div>

                    <div class="ad-content">
                        <!-- Status Badges -->
                        <div class="ad-badges">
                            @if($check_post_package['ads_type'] != "free")
                                @if($check_post_package['expired'] == "Expired")
                                <span class="badge badge-expired">{{__('p_myads.expired')}}</span>
                                @else
                                    @if($check_post_package['is_bulk'] != 0)
                                    <span class="badge badge-premium">{{__('p_myads.bulk package')}} - {{$check_post_package['ads_type']}}</span>
                                    @else
                                    <span class="badge badge-premium">{{$check_post_package['ads_type']}}</span>
                                    @endif
                                @endif
                            @else
                                @if($check_post_package['expired'] == "Expired")
                                <span class="badge badge-expired">{{__('p_myads.expired')}}</span>
                                @endif
                            @endif
                            
                            @if($post['sold_status'] == 1)
                            <span class="badge badge-sold">Sold</span>
                            @endif
                        </div>

                        <h3 class="ad-title">
                            <a href="{{$slug}}">{{$post["title"]}}</a>
                        </h3>

                        <div class="ad-price">
                            {!! !empty($currency_symbol) ? $currency_symbol[0] : '' !!}{{ number_format($post["price"]) }}
                        </div>

                        <div class="ad-meta">
                            <div class="meta-item">
                                <i class="fas fa-calendar"></i>
                                <span>{{ $pack_from_date }} - {{ $pack_to_date }}</span>
                            </div>
                            @if(!empty($check_post_package['bulk_type']))
                            <div class="meta-item">
                                <i class="fas fa-clock"></i>
                                <span>{{ $check_post_package['bulk_type'] }}</span>
                            </div>
                            @endif
                            @if($check_post_package['ads_type'] != "free" && $check_post_package['expired'] != "Expired")
                            <div class="meta-item">
                                <i class="fas fa-tag"></i>
                                <span>Package: {!! !empty($default_currency) ? $default_currency[0] : '' !!}{{ $check_post_package['package_price'] }}</span>
                            </div>
                            @endif
                        </div>

                        <div class="ad-stats">
                            <div class="stat">
                                <i class="fas fa-eye"></i>
                                <span>{{ $viewcount }} {{__('p_myads.views')}}</span>
                            </div>
                        </div>

                        <div class="ad-actions">
                            <div class="action-buttons">
                                <a href="{{$slug}}" target="_blank" class="btn btn-icon" title="View">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="{{URL::to('/post-edit?id='.$post->id)}}" class="btn btn-icon" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <button wire:click.prevent="confirmDelete('{{ $post->id }}')" class="btn btn-icon btn-danger" title="Delete">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>

                            <div class="status-buttons">
                                @if($check_post_package['expired'] == "Expired")
                                    <!-- Expired ad actions -->
                                @elseif($post['sold_status'] == 0)
                                    <button wire:click="confirmSoldStatusChange('{{ $post['id'] }}', 1)" 
                                            wire:loading.attr="disabled"
                                            class="btn btn-outline btn-full">
                                        <i class="fas fa-check-circle"></i>
                                        {{__('p_myads.mark as sold')}}
                                    </button>
                                @else
                                    @php
                                    $check_buynow_order = App\Models\TblBuynowOrder::where('post_id', $post['id'])->orderBy('id', 'desc')->pluck('order_status')->first();
                                    @endphp
                                    @if(!empty($check_buynow_order) && $check_buynow_order != "delivered")
                                    <button class="btn btn-outline btn-full disabled" disabled>
                                        {{__('p_myads.back to sale')}}
                                    </button>
                                    @else
                                    <button wire:click="confirmSoldStatusChange('{{ $post['id'] }}', 0)" 
                                            wire:loading.attr="disabled"
                                            class="btn btn-outline btn-full">
                                        <i class="fas fa-undo"></i>
                                        {{__('p_myads.back to sale')}}
                                    </button>
                                    @endif
                                @endif

                                @if($check_post_package['ads_type'] != "free")
                                    @if($check_post_package['expired'] == "Expired")
                                    <a href="{{ URL::to('/selectPackage?post=' . $post['id'] . '') }}" class="btn btn-primary btn-full">
                                        <i class="fas fa-bolt"></i>
                                        {{__('post_detail.sell fast')}}
                                    </a>
                                    @endif
                                @else
                                    @php
                                    $check_pack_info = App\Models\Package::where('lft', 1)->first();
                                    @endphp
                                    @if(($check_post_package['expired'] == "Expired") && ($check_post_package['post_count'] < $check_pack_info->single_pack_limit))
                                    <button class="btn btn-primary btn-full republish" data-id='{{ $post["id"] }}'>
                                        <i class="fas fa-sync"></i>
                                        {{__('p_myads.re publish')}}
                                    </button>
                                    @endif
                                    <a href="{{ URL::to('/selectPackage?post=' . $post['id']) }}" class="btn btn-primary btn-full">
                                        <i class="fas fa-bolt"></i>
                                        {{__('post_detail.sell fast')}}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Pagination -->
            <div class="pagination-wrapper">
                {{ $list->links() }}
            </div>
            @else
            <div class="empty-state">
                <div class="empty-icon">
                    <i class="fas fa-ad"></i>
                </div>
                <h3>No Ads Found</h3>
                <p>You haven't created any advertisements yet.</p>
                <a href="{{ URL::to('/post-add') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i>
                    Create Your First Ad
                </a>
            </div>
            @endif
        </div>
    </div>

    <!-- Modern Modals -->
    @if($showDeleteModal)
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="modal-title">Delete Post</h3>
            </div>
            <div class="modal-body">
                <p class="modal-text">Are you sure you want to delete this post? This action cannot be undone.</p>
                <div class="modal-actions">
                    <button wire:click="$set('showDeleteModal', false)" class="btn btn-outline">
                        Cancel
                    </button>
                    <button wire:click="destroy()" wire:loading.attr="disabled" class="btn btn-danger">
                        <span wire:loading wire:target="destroy">Deleting...</span>
                        <span wire:loading.remove wire:target="destroy">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif

    @if($showSoldModal)
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-icon warning">
                    <i class="fas fa-question-circle"></i>
                </div>
                <h3 class="modal-title">Confirm Status Change</h3>
            </div>
            <div class="modal-body">
                <p class="modal-text">
                    @if($newStatusForPost == 1)
                    Are you sure you want to mark this post as sold?
                    @else
                    Are you sure you want to make this post available for sale again?
                    @endif
                </p>
                <div class="modal-actions">
                    <button wire:click="$set('showSoldModal', false)" class="btn btn-outline">
                        Cancel
                    </button>
                    <button wire:click="toggleSoldStatus()" wire:loading.attr="disabled" class="btn btn-warning">
                        <span wire:loading wire:target="toggleSoldStatus">Updating...</span>
                        <span wire:loading.remove wire:target="toggleSoldStatus">Confirm</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
    @if($showMultiDeleteModal)
    <div class="modal-overlay">
        <div class="modal">
            <div class="modal-header">
                <div class="modal-icon danger">
                    <i class="fas fa-exclamation-triangle"></i>
                </div>
                <h3 class="modal-title">Delete Selected Posts</h3>
            </div>
            <div class="modal-body">
                <p class="modal-text">
                    Are you sure you want to delete the selected 
                    <strong>{{ count($selectedPosts) }}</strong> 
                    posts? This action cannot be undone.
                </p>
                <div class="modal-actions">
                    <button wire:click="$set('showMultiDeleteModal', false)" class="btn btn-outline">
                        Cancel
                    </button>
                    <button wire:click="deleteSelected()" wire:loading.attr="disabled" class="btn btn-danger">
                        <span wire:loading wire:target="deleteSelected">Deleting...</span>
                        <span wire:loading.remove wire:target="deleteSelected">Delete</span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>

<style>
/* Modern CSS Variables */
  
:root {
    --primary: #f7981b;
    --primary-dark: #d97706;   /* Darker shade of the orange */
    --primary-light: #fffbeb;  /* Very light, creamy shade */

    /* == Secondary Color (Contrasting Blue) == */
    --secondary: #3b82f6;      /* A nice blue for contrast */
    --secondary-light: #dbeafe; /* Light version of the blue */

    /* == Status Colors == */
    --danger: #ef4444;         /* Kept the same standard red */
    --danger-light: #fecaca;   /* Kept the same light red */
    --warning: #f7981b;        /* Using the primary orange for warnings */
    --warning-light: #fffbeb;   /* Using the primary light for warning backgrounds */

    /* == Neutral Grays & Shadows (Unchanged) == */
    --gray-50: #fffbfa;
    --gray-100: #f3f4f6;
    --gray-200: #e5e7eb;
    --gray-300: #d1d5db;
    --gray-400: #9ca3af;
    --gray-500: #6b7280;
    --gray-600: #4b5563;
    --gray-700: #374151;
    --gray-800: #1f2937;
    --gray-900: #111827;
    --shadow-sm: 0 1px 2px 0 rgba(0, 0, 0, 0.05);
    --shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    --shadow-md: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
    --shadow-lg: 0 10px 15px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    --radius: 8px;
    --radius-lg: 12px;
}
body{
  	background:#fffbfa !important;
}
/* Modern Container */
.modern-ads-container {
    background-color: var(--gray-50);
    min-height: 100vh;
}

.container {
    max-width: 1200px;
    margin: 0 auto;
    padding: 0 1rem;
}

/* Modern Header */
.modern-header {
    background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 2rem 0;
    margin-bottom: 2rem;
    box-shadow: var(--shadow-md);
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.page-title {
    font-size: 1.875rem;
    font-weight: 700;
    margin-bottom: 0.5rem;
}

.page-subtitle {
    font-size: 1rem;
    opacity: 0.9;
    font-weight: 400;
}

/* Stats Grid */
.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}

.stat-card {
    background: #fffbfa;
    border-radius: var(--radius-lg);
    padding: 1.5rem;
    box-shadow: var(--shadow);
    display: flex;
    align-items: center;
    gap: 1rem;
    transition: transform 0.2s ease, box-shadow 0.2s ease;
}

.stat-card:hover {
    transform: translateY(-2px);
    box-shadow: var(--shadow-md);
}

.stat-icon {
    width: 3rem;
    height: 3rem;
    border-radius: 50%;
    background: var(--primary-light);
    display: flex;
    align-items: center;
    justify-content: center;
    color: var(--primary);
    font-size: 1.25rem;
}

.stat-number {
    font-size: 1.875rem;
    font-weight: 700;
    color: var(--gray-800);
    line-height: 1;
}

.stat-label {
    font-size: 0.875rem;
    color: var(--gray-600);
    margin-top: 0.25rem;
}

/* Main Card */
.main-card {
    background: #fffbfa;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow);
    overflow: hidden;
    margin-bottom: 2rem;
}

/* Bulk Actions */
.bulk-actions-bar {
    padding: 1rem 1.5rem;
    background: var(--gray-50);
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 1rem;
}

.bulk-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

/* Custom Checkbox */
.custom-checkbox {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    cursor: pointer;
}

.hidden-checkbox {
    display: none;
}

.checkmark {
    width: 1.125rem;
    height: 1.125rem;
    border: 2px solid var(--gray-400);
    border-radius: 4px;
    display: flex;
    align-items: center;
    justify-content: center;
    transition: all 0.2s ease;
}

.hidden-checkbox:checked + .checkmark {
    background: var(--primary);
    border-color: var(--primary);
}

.hidden-checkbox:checked + .checkmark::after {
    content: "✓";
    color: white;
    font-size: 0.75rem;
    font-weight: bold;
}

.checkbox-label {
    font-weight: 500;
    color: var(--gray-700);
}

/* Filters Section */
.filters-section {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    gap: 1rem;
    flex-wrap: wrap;
    align-items: center;
}

.filter-group {
    flex: 1;
    min-width: 200px;
}

.search-group {
    flex: 2;
}

/* Form Elements */
.select-wrapper, .search-wrapper {
    position: relative;
}

.form-select, .search-input {
    width: 100%;
    padding: 0.75rem 1rem;
    border: 1px solid var(--gray-300);
    border-radius: var(--radius);
    background: #fffbfa;
    font-size: 0.875rem;
    transition: all 0.2s ease;
}

.form-select:focus, .search-input:focus {
    outline: none;
    border-color: var(--primary);
    box-shadow: 0 0 0 3px rgba(16, 185, 129, 0.1);
}

.select-icon {
    position: absolute;
    right: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
    pointer-events: none;
}

.search-icon {
    position: absolute;
    left: 1rem;
    top: 50%;
    transform: translateY(-50%);
    color: var(--gray-400);
}

.search-input {
    padding-left: 2.5rem;
}

/* Buttons */
.btn {
    display: inline-flex;
    align-items: center;
    justify-content: center;
    padding: 0.5rem 1rem;
    border: none;
    border-radius: var(--radius);
    font-weight: 500;
    font-size: 0.875rem;
    text-decoration: none;
    cursor: pointer;
    transition: all 0.2s ease;
    gap: 0.5rem;
}

.btn-primary {
    background: var(--primary);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
    box-shadow: var(--shadow-md);
}

.btn-secondary {
    background: var(--secondary);
    color: var(--gray-800);
}

.btn-warning {
    background: var(--warning);
    color: white;
}

.btn-danger {
    background: var(--danger);
    color: white;
}

.btn-outline {
    background: transparent;
    border: 1px solid var(--gray-300);
    color: var(--gray-700);
}

.btn-outline:hover {
    background: var(--gray-50);
    border-color: var(--gray-400);
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.75rem;
}

.btn-lg {
    padding: 0.75rem 1.5rem;
    font-size: 1rem;
}

.btn-icon {
    padding: 0.5rem;
    width: 2.5rem;
    height: 2.5rem;
}

.btn-full {
    width: 100%;
    justify-content: center;
}

.btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none !important;
}

/* Ads Grid */
.ads-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(350px, 1fr));
    gap: 1.5rem;
    padding: 1.5rem;
}

.ad-card {
    background: #fffbfa;
    border: 1px solid var(--gray-200);
    border-radius: var(--radius-lg);
    overflow: hidden;
    transition: all 0.3s ease;
    position: relative;
}

.ad-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
}

.ad-checkbox {
    position: absolute;
    top: 1rem;
    left: 1rem;
    z-index: 10;
}

.ad-image-section {
    position: relative;
    overflow: hidden;
}

.ad-image {
    width: 100%;
    height: 200px;
    object-fit: cover;
    transition: transform 0.3s ease;
}

.ad-image-link:hover .ad-image {
    transform: scale(1.05);
}

.ad-overlay {
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.7);
    display: flex;
    align-items: center;
    justify-content: center;
    opacity: 0;
    transition: opacity 0.3s ease;
}

.ad-image-link:hover .ad-overlay {
    opacity: 1;
}

.view-btn {
    color: white;
    padding: 0.5rem 1rem;
    border: 1px solid white;
    border-radius: var(--radius);
    font-weight: 500;
}

.ad-content {
    padding: 1.25rem;
}

/* Badges */
.ad-badges {
    display: flex;
    gap: 0.5rem;
    margin-bottom: 0.75rem;
    flex-wrap: wrap;
}

.badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 600;
}

.badge-premium {
    background: var(--secondary-light);
    color: var(--gray-800);
}

.badge-expired {
    background: var(--danger-light);
    color: #b91c1c;
}

.badge-sold {
    background: var(--gray-200);
    color: var(--gray-700);
}

/* Ad Content */
.ad-title {
    font-size: 1.125rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    line-height: 1.4;
}

.ad-title a {
    color: var(--gray-800);
    text-decoration: none;
}

.ad-title a:hover {
    color: var(--primary);
}

.ad-price {
    font-size: 1.25rem;
    font-weight: 700;
    color: var(--primary);
    margin-bottom: 1rem;
}

.ad-meta {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
    margin-bottom: 1rem;
}

.meta-item {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.meta-item i {
    width: 1rem;
    color: var(--gray-400);
}

.ad-stats {
    display: flex;
    align-items: center;
    gap: 1rem;
    margin-bottom: 1rem;
    padding-top: 1rem;
    border-top: 1px solid var(--gray-200);
}

.stat {
    display: flex;
    align-items: center;
    gap: 0.5rem;
    font-size: 0.875rem;
    color: var(--gray-600);
}

.ad-actions {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.action-buttons {
    display: flex;
    gap: 0.5rem;
    justify-content: center;
}

.status-buttons {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

/* Empty State */
.empty-state {
    text-align: center;
    padding: 3rem 1rem;
}

.empty-icon {
    font-size: 4rem;
    color: var(--gray-300);
    margin-bottom: 1rem;
}

.empty-state h3 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 0.5rem;
    color: var(--gray-700);
}

.empty-state p {
    color: var(--gray-500);
    margin-bottom: 1.5rem;
}

/* Pagination */
.pagination-wrapper {
    padding: 1.5rem;
    border-top: 1px solid var(--gray-200);
    display: flex;
    justify-content: center;
}

/* Modals */
.modal-overlay {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);
    display: flex;
    align-items: center;
    justify-content: center;
    z-index: 1000;
    padding: 1rem;
}

.modal {
    background: white;
    border-radius: var(--radius-lg);
    box-shadow: var(--shadow-lg);
    width: 100%;
    max-width: 400px;
    overflow: hidden;
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid var(--gray-200);
    display: flex;
    align-items: center;
    gap: 0.75rem;
}

.modal-icon {
    width: 2.5rem;
    height: 2.5rem;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    flex-shrink: 0;
}

.modal-icon.danger {
    background: var(--danger-light);
    color: var(--danger);
}

.modal-icon.warning {
    background: var(--warning-light);
    color: var(--warning);
}

.modal-title {
    font-size: 1.25rem;
    font-weight: 600;
    color: var(--gray-800);
}

.modal-body {
    padding: 1.5rem;
}

.modal-text {
    color: var(--gray-600);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

.modal-actions {
    display: flex;
    gap: 0.75rem;
    justify-content: flex-end;
}

/* Success Popup */
.success-popup-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    display: flex;
    justify-content: center;
    align-items: center;
    z-index: 1000;
}

.success-popup-content {
    background: #fff;
    padding: 2.5rem;
    border-radius: var(--radius-lg);
    text-align: center;
    width: 90%;
    max-width: 400px;
    box-shadow: var(--shadow-lg);
}

.success-icon {
    width: 5rem;
    height: 5rem;
    background-color: var(--primary);
    border-radius: 50%;
    display: flex;
    justify-content: center;
    align-items: center;
    margin: 0 auto 1.5rem auto;
    color: #fff;
    font-size: 2rem;
}

.success-popup-content h2 {
    font-size: 1.5rem;
    font-weight: 600;
    margin-bottom: 1rem;
    color: var(--gray-800);
}

.success-popup-content p {
    color: var(--gray-600);
    margin-bottom: 1.5rem;
    line-height: 1.6;
}

/* Responsive Design */
@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .stats-grid {
        grid-template-columns: repeat(2, 1fr);
    }
    
    .ads-grid {
        grid-template-columns: 1fr;
        padding: 1rem;
    }
    
    .filters-section {
        flex-direction: column;
    }
    
    .filter-group {
        min-width: 100%;
    }
    
    .bulk-actions-bar {
        flex-direction: column;
        align-items: flex-start;
    }
    
    .bulk-left {
        width: 100%;
        justify-content: space-between;
    }
}

@media (max-width: 480px) {
    .stats-grid {
        grid-template-columns: 1fr;
    }
    
    .page-title {
        font-size: 1.5rem;
    }
    
    .ad-content {
        padding: 1rem;
    }
}
</style>

@push('scripts')
<script>
    window.addEventListener('show-toast', event => {
        toastr.options = {
            "closeButton": true,
            "progressBar": true
        }
        switch (event.detail.type) {
            case 'success':
                toastr.success(event.detail.message);
                break;
            case 'error':
                toastr.error(event.detail.message);
                break;
            case 'warning':
                toastr.warning(event.detail.message);
                break;
            case 'info':
                toastr.info(event.detail.message);
                break;
        }
    });
</script>
@endpush