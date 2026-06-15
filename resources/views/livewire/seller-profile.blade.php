<div>
    <div class="sp-container">
        {{-- ===== SELLER HEADER CARD ===== --}}
        <div class="sp-header-card">
            <div class="sp-header-bg"></div>
            <div class="sp-header-content">
                <div class="sp-avatar-wrap">
                    <img src="{{ $seller->profile_photo_url }}" alt="{{ $seller->name }}" class="sp-avatar">
                    <span class="sp-status-dot {{ $seller->current_chat_status == 'online' ? 'online' : 'offline' }}"></span>
                </div>
                <div class="sp-seller-info">
                    <h1 class="sp-seller-name">{{ $seller->name }}</h1>
                    <div class="sp-seller-meta">
                        <span class="sp-meta-item"><i class="fas fa-calendar-alt"></i> Joined {{ $seller->created_at->isoFormat('MMM YYYY') }}</span>
                        <span class="sp-meta-item"><i class="fas fa-circle {{ $seller->current_chat_status == 'online' ? 'text-green-500' : 'text-red-400' }}" style="font-size: 8px;"></i> {{ ucfirst($seller->current_chat_status) }}</span>
                    </div>
                    {{-- Rating --}}
                    <div class="sp-rating">
                        @for ($i = 1; $i <= 5; $i++)
                            <i class="fas fa-star {{ $avgRating >= $i ? 'star-filled' : 'star-empty' }}"></i>
                        @endfor
                        <span class="sp-rating-text">({{ $reviewsCount }} {{ $reviewsCount == 1 ? 'review' : 'reviews' }})</span>
                    </div>
                </div>
                <div class="sp-header-actions">
                    <a href="tel:{{ $seller->phone }}" class="sp-btn sp-btn-primary">
                        <i class="fas fa-phone"></i> Contact
                    </a>
                    @if ($isDifferentUser)
                        <button wire:click="toggleFollow" wire:loading.attr="disabled" class="sp-btn {{ $isFollowing ? 'sp-btn-outline-active' : 'sp-btn-outline' }}">
                            <span wire:loading.remove wire:target="toggleFollow">
                                <i class="fas {{ $isFollowing ? 'fa-user-check' : 'fa-user-plus' }}"></i> {{ $isFollowing ? 'Following' : 'Follow' }}
                            </span>
                            <span wire:loading wire:target="toggleFollow"><i class="fas fa-spinner fa-spin"></i></span>
                        </button>
                    @else
                        <button wire:click="$set('showInviteModal', true)" class="sp-btn sp-btn-outline">
                            <i class="fas fa-share"></i> Invite Friends
                        </button>
                    @endif
                </div>
            </div>

            {{-- Stats Bar --}}
            <div class="sp-stats-bar">
                <div class="sp-stat" wire:click="$set('showFollowersModal', true)">
                    <span class="sp-stat-num">{{ $followersCount }}</span>
                    <span class="sp-stat-label">Followers</span>
                </div>
                <div class="sp-stat" wire:click="$set('showFollowingModal', true)">
                    <span class="sp-stat-num">{{ $followingCount }}</span>
                    <span class="sp-stat-label">Following</span>
                </div>
                <div class="sp-stat" wire:click="$set('showReviewsModal', true)">
                    <span class="sp-stat-num">{{ $reviewsCount }}</span>
                    <span class="sp-stat-label">Reviews</span>
                </div>
                <div class="sp-stat">
                    <span class="sp-stat-num">{{ $totalAdsCount }}</span>
                    <span class="sp-stat-label">Listings</span>
                </div>
            </div>
        </div>

        {{-- ===== CONTENT AREA ===== --}}
        <div class="sp-content">
            {{-- Category Filter Pills --}}
            <div class="sp-filters">
                <div class="sp-filter-pills">
                    <button wire:click="filterByCategory(null)" class="sp-pill {{ !$selectedCategory ? 'active' : '' }}">
                        All ({{ $totalAdsCount }})
                    </button>
                    @foreach($categories as $category)
                        <button wire:click="filterByCategory({{ $category->id }})" class="sp-pill {{ $selectedCategory == $category->id ? 'active' : '' }}">
                            {{ $category->title }} ({{ $category->posts_count }})
                        </button>
                    @endforeach
                </div>

                {{-- Sort & View Controls --}}
                <div class="sp-controls">
                    <div class="sp-view-toggle">
                        <button wire:click="$set('view', 'grid')" class="sp-view-btn {{ $view === 'grid' ? 'active' : '' }}" title="Grid view">
                            <i class="fas fa-th-large"></i>
                        </button>
                        <button wire:click="$set('view', 'list')" class="sp-view-btn {{ $view === 'list' ? 'active' : '' }}" title="List view">
                            <i class="fas fa-list"></i>
                        </button>
                    </div>
                    <select wire:model.lazy="sortBy" class="sp-sort-select">
                        <option value="post-desc">Recently Posted</option>
                        <option value="price-asc">Price: Low to High</option>
                        <option value="price-desc">Price: High to Low</option>
                        <option value="most-viewed">Most Popular</option>
                    </select>
                </div>
            </div>

            {{-- Ads Grid/List --}}
            <div>
                @if($sellerPosts->count() > 0)
                    <div class="{{ $view === 'grid' ? 'sp-grid' : 'sp-list' }}">
                        @foreach($sellerPosts as $ad)
                            @if($view === 'grid')
                                {!! \App\Models\Setting::htmlAdBlock($ad->id) !!}
                            @else
                                {!! \App\Models\Setting::viewblock($ad->id) !!}
                            @endif
                        @endforeach
                    </div>
                @else
                    <div class="sp-empty">
                        <div class="sp-empty-icon">
                            <i class="fas fa-store-slash"></i>
                        </div>
                        <h3>No Listings Found</h3>
                        <p>This seller doesn't have any active listings{{ $selectedCategory ? ' in this category' : '' }}.</p>
                        @if($selectedCategory)
                            <button wire:click="filterByCategory(null)" class="sp-btn sp-btn-outline" style="margin-top: 12px;">
                                <i class="fas fa-times"></i> Clear Filter
                            </button>
                        @endif
                    </div>
                @endif
            </div>
            
            {{-- Pagination --}}
            @if($sellerPosts->hasPages())
            <div class="sp-pagination">
                {{ $sellerPosts->links() }}
            </div>
            @endif
        </div>

        {{-- Report Link --}}
        @if ($isDifferentUser)
        <div class="sp-report-link">
            <button wire:click="openReportModal" class="sp-report-btn">
                <i class="fas fa-flag"></i> Report this seller
            </button>
        </div>
        @endif
    </div>

    {{-- Modals --}}
    @include('livewire.seller-profile.modals.followers')
    @include('livewire.seller-profile.modals.following')
    @include('livewire.seller-profile.modals.invite')
    @include('livewire.seller-profile.modals.report')
    @include('livewire.seller-profile.modals.reviews')

    {{-- Toast Notification --}}
    <div id="toast-notification"
         class="fixed bottom-5 right-5 z-50 bg-green-500 text-white px-5 py-3 rounded-xl shadow-lg"
         style="display: none; opacity: 0; transform: translateY(0.5rem); transition: all 0.3s ease-out;">
        <div class="flex items-center gap-2">
            <i class="fas fa-check-circle"></i>
            <p id="toast-message" class="font-medium"></p>
        </div>
    </div>

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
        .seller-profile-page { background: #f8fafc; min-height: 60vh; }

        .sp-container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 24px 16px 60px;
        }

        /* ===== Header Card ===== */
        .sp-header-card {
            background: #fff;
            border-radius: 20px;
            overflow: hidden;
            box-shadow: 0 4px 20px rgba(0,0,0,0.06);
            margin-bottom: 24px;
            border: 1px solid #f1f5f9;
        }
        .sp-header-bg {
            height: 100px;
            background: linear-gradient(135deg, #F97316 0%, #fb923c 50%, #fdba74 100%);
            position: relative;
        }
        .sp-header-bg::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 40px;
            background: linear-gradient(transparent, rgba(255,255,255,0.4));
        }
        .sp-header-content {
            display: flex;
            align-items: center;
            gap: 20px;
            padding: 0 28px 24px;
            margin-top: -40px;
            position: relative;
            flex-wrap: wrap;
        }

        /* Avatar */
        .sp-avatar-wrap {
            position: relative;
            flex-shrink: 0;
        }
        .sp-avatar {
            width: 88px;
            height: 88px;
            border-radius: 50%;
            object-fit: cover;
            border: 4px solid #fff;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }
        .sp-status-dot {
            position: absolute;
            bottom: 4px;
            right: 4px;
            width: 16px;
            height: 16px;
            border-radius: 50%;
            border: 3px solid #fff;
        }
        .sp-status-dot.online { background: #22c55e; }
        .sp-status-dot.offline { background: #ef4444; }

        /* Seller Info */
        .sp-seller-info { flex: 1; min-width: 200px; }
        .sp-seller-name {
            font-size: 22px;
            font-weight: 700;
            color: #1e293b;
            margin: 0 0 6px;
        }
        .sp-seller-meta {
            display: flex;
            gap: 16px;
            flex-wrap: wrap;
            margin-bottom: 8px;
        }
        .sp-meta-item {
            font-size: 13px;
            color: #64748b;
            display: flex;
            align-items: center;
            gap: 6px;
        }
        .sp-meta-item i { color: #94a3b8; }

        /* Rating */
        .sp-rating { display: flex; align-items: center; gap: 3px; }
        .sp-rating .star-filled { color: #f59e0b; font-size: 14px; }
        .sp-rating .star-empty { color: #e2e8f0; font-size: 14px; }
        .sp-rating-text { font-size: 13px; color: #64748b; margin-left: 8px; }

        /* Action Buttons */
        .sp-header-actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .sp-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 20px;
            border-radius: 10px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
            border: none;
            text-decoration: none;
        }
        .sp-btn-primary {
            background: #F97316;
            color: #fff;
            box-shadow: 0 4px 12px rgba(249,115,22,0.25);
        }
        .sp-btn-primary:hover {
            background: #ea580c;
            transform: translateY(-1px);
            box-shadow: 0 6px 16px rgba(249,115,22,0.35);
        }
        .sp-btn-outline {
            background: #fff;
            color: #334155;
            border: 2px solid #e2e8f0;
        }
        .sp-btn-outline:hover {
            border-color: #F97316;
            color: #F97316;
        }
        .sp-btn-outline-active {
            background: #fff7ed;
            color: #F97316;
            border: 2px solid #F97316;
        }

        /* Stats Bar */
        .sp-stats-bar {
            display: flex;
            border-top: 1px solid #f1f5f9;
        }
        .sp-stat {
            flex: 1;
            text-align: center;
            padding: 16px 12px;
            cursor: pointer;
            transition: background 0.2s;
        }
        .sp-stat:hover { background: #fef7f0; }
        .sp-stat:not(:last-child) { border-right: 1px solid #f1f5f9; }
        .sp-stat-num {
            display: block;
            font-size: 20px;
            font-weight: 700;
            color: #1e293b;
        }
        .sp-stat-label {
            font-size: 12px;
            color: #64748b;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* ===== Content Area ===== */
        .sp-content {
            background: #fff;
            border-radius: 20px;
            padding: 24px;
            box-shadow: 0 4px 20px rgba(0,0,0,0.04);
            border: 1px solid #f1f5f9;
        }

        /* Filters */
        .sp-filters {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            gap: 16px;
            margin-bottom: 24px;
            flex-wrap: wrap;
        }
        .sp-filter-pills {
            display: flex;
            flex-wrap: wrap;
            gap: 8px;
            flex: 1;
        }
        .sp-pill {
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 500;
            border: 1px solid #e2e8f0;
            background: #fff;
            color: #475569;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .sp-pill:hover {
            border-color: #F97316;
            color: #F97316;
            background: #fff7ed;
        }
        .sp-pill.active {
            background: #F97316;
            color: #fff;
            border-color: #F97316;
        }

        /* Controls */
        .sp-controls {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-shrink: 0;
        }
        .sp-view-toggle {
            display: flex;
            background: #f8fafc;
            border-radius: 8px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
        }
        .sp-view-btn {
            padding: 8px 12px;
            background: transparent;
            border: none;
            cursor: pointer;
            color: #94a3b8;
            transition: all 0.2s;
        }
        .sp-view-btn.active {
            background: #F97316;
            color: #fff;
        }
        .sp-sort-select {
            padding: 8px 14px;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
            font-size: 13px;
            color: #334155;
            background: #fff;
            cursor: pointer;
            outline: none;
        }
        .sp-sort-select:focus { border-color: #F97316; }

        /* Loading */
        .sp-loading {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            padding: 60px 20px;
            color: #64748b;
        }
        .sp-loading-spinner {
            width: 24px;
            height: 24px;
            border: 3px solid #f1f5f9;
            border-top-color: #F97316;
            border-radius: 50%;
            animation: sp-spin 0.8s linear infinite;
        }
        @keyframes sp-spin { to { transform: rotate(360deg); } }

        /* Grid */
        .sp-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 20px;
        }
        .sp-list {
            display: flex;
            flex-direction: column;
            gap: 16px;
        }

        /* Empty State */
        .sp-empty {
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fef7f0 0%, #fff 100%);
            border-radius: 16px;
            border: 2px dashed #e2e8f0;
        }
        .sp-empty-icon {
            width: 64px;
            height: 64px;
            background: #fff7ed;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .sp-empty-icon i { font-size: 24px; color: #F97316; }
        .sp-empty h3 { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 8px; }
        .sp-empty p { font-size: 14px; color: #64748b; }

        /* Pagination */
        .sp-pagination { margin-top: 24px; padding-top: 20px; border-top: 1px solid #f1f5f9; }

        /* Report */
        .sp-report-link { text-align: center; margin-top: 20px; }
        .sp-report-btn {
            background: none;
            border: none;
            color: #94a3b8;
            font-size: 13px;
            cursor: pointer;
            transition: color 0.2s;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .sp-report-btn:hover { color: #ef4444; }

        /* ===== Responsive ===== */
        @media (max-width: 768px) {
            .sp-container { padding: 16px 12px 40px; }
            .sp-header-content { padding: 0 16px 20px; gap: 14px; }
            .sp-avatar { width: 68px; height: 68px; }
            .sp-seller-name { font-size: 18px; }
            .sp-header-actions { width: 100%; }
            .sp-header-actions .sp-btn { flex: 1; justify-content: center; }
            .sp-stats-bar { flex-wrap: wrap; }
            .sp-stat { min-width: 50%; }
            .sp-content { padding: 16px; }
            .sp-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .sp-filters { flex-direction: column; }
            .sp-controls { width: 100%; justify-content: space-between; }
            .sp-view-toggle { display: none; }
        }
        @media (max-width: 480px) {
            .sp-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .sp-header-bg { height: 80px; }
            .sp-header-content { margin-top: -30px; }
        }
    </style>
</div>
