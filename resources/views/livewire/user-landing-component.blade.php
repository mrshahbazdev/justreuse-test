

    
       

        <main>
             @php
              $searchUrl = "";

              if (Session::has('Searchedurl')) {
                  $searchUrl = Session::get('Searchedurl');
              } elseif (Session::has('GetCountry')) {
                  $countryName = Session::get('GetCountry');
                  $locationName = Session::get('GetAddress') ?? $countryName;
                  $stateName = Session::get('GetState') ?? '';
                  $cityName = Session::get('GetCity') ?? '';
                  $latitude = Session::get('Getlat') ?? '';
                  $longitude = Session::get('Getlng') ?? '';
                  $path = str_replace(' ', '_', strtolower($countryName));
                  $queryParams = http_build_query([
                      'loc' => $locationName,
                      'country' => $countryName,
                      'state' => $stateName,
                      'city' => $cityName,
                      'lat' => $latitude,
                      'lng' => $longitude
                  ]);
                  $searchUrl = url($path) . '?' . $queryParams;
              } else {
                  $countryName = App\Models\Setting::get_admin_default_country();
                  if (!empty($countryName)) {
                      $path = str_replace(' ', '_', strtolower($countryName));
                      $searchUrl = url($path) . '?' . http_build_query([
                          'loc' => $countryName,
                          'country' => $countryName,
                          'state' => '',
                          'city' => '',
                          'lat' => '',
                          'lng' => ''
                      ]);
                  } else {
                      $searchUrl = url('/');
                  }
              }
          @endphp
          <div>
              <input type="hidden" id="category" value="{{ request()->c ?? '' }}">
              <input type="hidden" id="subcategory" value="">
              <input type="hidden" id="is_search" value="{{ session()->has('IsSearch') ?? '' }}">
          </div>

            {{-- ===== HERO SECTION ===== --}}
            <section class="hero-section">
                <div class="hero-text">
                    <span class="hero-badge"><i class="fas fa-bolt"></i> Marketplace</span>
                    <h1>Find Great Deals.<br><span class="highlight">Buy & Sell</span> Smart.</h1>
                    <p>Search local listings or post your own — quick, safe, and hassle-free.</p>
                    <div class="hero-cta-row">
                        <a href="{{ $searchUrl }}" class="hero-btn-primary"><i class="fas fa-search"></i> Browse Listings</a>
                        @if(auth()->check())
                            <a href="{{ url('/post/add') }}" class="hero-btn-secondary"><i class="fas fa-plus"></i> Post an Ad</a>
                        @else
                            <a href="{{ url('/login') }}" class="hero-btn-secondary"><i class="fas fa-plus"></i> Post an Ad</a>
                        @endif
                    </div>
                </div>
                <div class="hero-image-grid">
                    @if(isset($random_categories) && $random_categories->count())
                        @foreach($random_categories as $category)
                            @php
                                $cat_img = !empty($category->image) ? asset('storage/' . $category->image) : 'https://img.icons8.com/color/96/generic-box.png';
                            @endphp
                            <a href="{{ $searchUrl . '&c=' . $category->slug }}" class="icon-card">
                                <div class="icon-card-img">
                                    <img src="{{ $cat_img }}" alt="{{ $category->title }}">
                                </div>
                                <span class="category-title">{{ $category->title }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </section>

            {{-- ===== BROWSE BY CATEGORY ===== --}}
            <section class="listings-section reveal">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-th-large"></i> Browse by Category</h2>
                    <a href="{{ $searchUrl }}" class="section-view-all">View All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="category-buttons">
                    @foreach($main_categories as $main_category)
                        @php
                            $cat_img = !empty($main_category->image) ? asset('storage/' . $main_category->image) : asset('storage/noimage150.png');
                        @endphp
                        <a href="{{ $searchUrl . '&c=' . $main_category->slug }}" class="category-btn">
                            <img src="{{ $cat_img }}" alt="{{ $main_category->title }}">
                            <span>{{ $main_category->title }}</span>
                        </a>
                    @endforeach
                </div>
            </section>

            {{-- ===== USER ADVERTISEMENT – TOP BANNER ===== --}}
            <x-display-ad page-location="landing_top_banner" />

            {{-- ===== BANNER ADS CAROUSEL ===== --}}
            @if(!empty($banner_ads) && count($banner_ads) > 0)
            <section class="banner-section reveal">
                <div class="banner-carousel" id="bannerCarousel">
                    @foreach($banner_ads as $index => $banner)
                        <a href="{{ $banner['url'] ?? '#' }}" class="banner-carousel-slide {{ $index === 0 ? 'active' : '' }}" target="_blank" rel="noopener">
                            <img src="{{ $banner['image'] }}" alt="Advertisement" loading="lazy">
                        </a>
                    @endforeach
                    @if(count($banner_ads) > 1)
                    <div class="banner-carousel-dots">
                        @foreach($banner_ads as $index => $banner)
                            <button class="carousel-dot {{ $index === 0 ? 'active' : '' }}" data-index="{{ $index }}"></button>
                        @endforeach
                    </div>
                    @endif
                </div>
            </section>
            @endif

            {{-- ===== TOP ADS ===== --}}
            <section class="listings-section reveal">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-fire"></i> {{__('landing.top ads')}}</h2>
                    <a href="{{ $searchUrl }}" class="section-view-all">See More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="product-grid">
                    @forelse($top_ads_list as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @empty
                        <div class="empty-ads-state">
                            <div class="empty-ads-icon">
                                <i class="fas fa-fire-flame-curved"></i>
                            </div>
                            <h4>No Top Ads Yet</h4>
                            <p>Premium listings will appear here. <a href="{{ url('/post/add') }}">Post your ad</a> to get started!</p>
                        </div>
                    @endforelse
                </div>
            </section>
            
            {{-- Banner ads are now shown in the single carousel above --}}

            {{-- ===== USER ADVERTISEMENT – MIDDLE BANNER ===== --}}
            <x-display-ad page-location="landing_mid_banner" />

            {{-- ===== FEATURED ADS ===== --}}
            <section class="listings-section reveal">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-star"></i> {{__('landing.feature ads')}}</h2>
                    <a href="{{ $searchUrl }}" class="section-view-all">See More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="product-grid">
                    @forelse($featurs_ad_list as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @empty
                        <div class="empty-ads-state">
                            <div class="empty-ads-icon">
                                <i class="fas fa-star"></i>
                            </div>
                            <h4>No Featured Ads Yet</h4>
                            <p>Featured listings will appear here. <a href="{{ url('/post/add') }}">Post your ad</a> to get featured!</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ===== PAID / PROMOTED ADS ===== --}}
            <section class="listings-section reveal">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-bullhorn"></i> Promoted Ads</h2>
                    <a href="{{ $searchUrl }}" class="section-view-all">See More <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="product-grid">
                    @forelse($paid_ads_list as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @empty
                        <div class="empty-ads-state">
                            <div class="empty-ads-icon">
                                <i class="fas fa-bullhorn"></i>
                            </div>
                            <h4>No Promoted Ads Yet</h4>
                            <p>Boost your listing to appear here. <a href="{{ url('/post/add') }}">Post your ad</a> and promote it!</p>
                        </div>
                    @endforelse
                </div>
            </section>

            {{-- ===== USER ADVERTISEMENT – SIDEBAR / INLINE ===== --}}
            <x-display-ad page-location="landing_sidebar" />

            {{-- ===== LATEST ADS ===== --}}
            @if(!empty($posts) && count($posts) > 0)
            <section class="listings-section reveal">
                <div class="section-header">
                    <h2 class="section-title"><i class="fas fa-clock"></i> Latest Listings</h2>
                    <a href="{{ $searchUrl }}" class="section-view-all">Browse All <i class="fas fa-arrow-right"></i></a>
                </div>
                <div class="product-grid">
                    @foreach($posts as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @endforeach
                </div>
            </section>
            @endif
            
            {{-- ===== TRUST FEATURES ===== --}}
            <section class="features-section reveal">
                <div class="feature-item">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-shield-halved"></i>
                    </div>
                    <div>
                        <strong>Verified Sellers</strong>
                        <span>Trusted community</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-lock"></i>
                    </div>
                    <div>
                        <strong>Secure Transactions</strong>
                        <span>Safe payments</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-robot"></i>
                    </div>
                    <div>
                        <strong>AI Fraud Detection</strong>
                        <span>Smart protection</span>
                    </div>
                </div>
                <div class="feature-item">
                    <div class="feature-icon-wrap">
                        <i class="fa-solid fa-globe"></i>
                    </div>
                    <div>
                        <strong>Global Reach</strong>
                        <span>Local focus</span>
                    </div>
                </div>
            </section>
            
            {{-- ===== APP DOWNLOAD ===== --}}
            <section class="app-download-section reveal">
                <div class="app-download-box">
                    <div class="app-download-text">
                        <h2>Get the JustReused App</h2>
                        <p>Manage your ads, chat with buyers, and discover deals on the go.</p>
                        <div class="app-buttons">
                            <a href="https://apps.apple.com/pk/app/justreused/id6499257286" class="app-btn" target="_blank" rel="noopener">
                                <i class="fab fa-apple"></i>
                                <div class="app-btn-text">
                                    <span class="subtitle">Download on the</span>
                                    <span class="title">App Store</span>
                                </div>
                            </a>
                            <a href="https://play.google.com/store/apps/details?id=com.justreused&hl=en" class="app-btn">
                                <i class="fab fa-google-play"></i>
                                <div class="app-btn-text">
                                    <span class="subtitle">GET IT ON</span>
                                    <span class="title">Google Play</span>
                                </div>
                            </a>
                        </div>
                    </div>
                    <div class="app-image">
                        <img src="https://www.pngmart.com/files/15/Apple-iPhone-12-PNG-Image.png" alt="App Preview">
                    </div>
                </div>
            </section>

    <style>
        /* ===== Landing Page Variables ===== */
        :root {
            --brand-orange: #F97316;
            --brand-orange-dark: #EA580C;
            --brand-green: #39763a;
            --text-dark: #111827;
            --text-light: #6B7280;
            --text-muted: #9CA3AF;
            --bg-light: #fffbfa;
            --bg-warm: #fef7f0;
            --border-color: #E5E7EB;
            --border-light: #F3F4F6;
            --card-shadow: 0 4px 16px rgba(0,0,0,0.06);
            --card-hover-shadow: 0 12px 28px rgba(0,0,0,0.1);
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #fffbfa; color: var(--text-dark); -webkit-font-smoothing: antialiased; }
        a { text-decoration: none; color: inherit; }

        .page-wrapper { background:#fffbfa; max-width: 1400px; margin: 20px auto; padding: 20px 30px; border: 1px solid var(--border-light); border-radius: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.07); }

        /* ===== Hero Section ===== */
        .hero-section {
            display: flex;
            align-items: center;
            gap: 50px;
            margin-bottom: 60px;
            padding: 40px 0;
        }
        .hero-text {
            flex-basis: 50%;
        }
        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            background: var(--bg-warm);
            color: var(--brand-orange);
            padding: 6px 14px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 16px;
            border: 1px solid #fde8d0;
        }
        .hero-badge i { font-size: 12px; }
        .hero-text h1 {
            font-size: 46px;
            font-weight: 800;
            line-height: 1.15;
            margin-bottom: 16px;
            letter-spacing: -1.5px;
            color: var(--text-dark);
        }
        .hero-text h1 .highlight {
            background: linear-gradient(135deg, var(--brand-orange), #fb923c);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }
        .hero-text p {
            font-size: 17px;
            color: var(--text-light);
            max-width: 450px;
            line-height: 1.7;
            margin-bottom: 24px;
        }
        .hero-cta-row {
            display: flex;
            gap: 12px;
            flex-wrap: wrap;
        }
        .hero-btn-primary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: var(--brand-orange);
            color: #fff;
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 14px rgba(249,115,22,0.3);
        }
        .hero-btn-primary:hover {
            background: var(--brand-orange-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(249,115,22,0.4);
        }
        .hero-btn-secondary {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            background: #fff;
            color: var(--text-dark);
            padding: 14px 28px;
            border-radius: 12px;
            font-weight: 600;
            font-size: 15px;
            border: 2px solid var(--border-color);
            transition: all 0.25s ease;
        }
        .hero-btn-secondary:hover {
            border-color: var(--brand-orange);
            color: var(--brand-orange);
            transform: translateY(-2px);
        }

        /* Hero Category Grid */
        .hero-image-grid {
            flex-basis: 50%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .icon-card {
            background: #fff;
            border-radius: 16px;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            padding: 28px 20px;
            text-align: center;
            gap: 12px;
            transition: all 0.3s ease;
            border: 1px solid var(--border-color);
            position: relative;
            overflow: hidden;
        }
        .icon-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: linear-gradient(90deg, var(--brand-orange), #fb923c);
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        .icon-card:hover {
            transform: translateY(-6px);
            box-shadow: var(--card-hover-shadow);
            border-color: #d1d5db;
        }
        .icon-card:hover::before { opacity: 1; }
        .icon-card-img {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .icon-card-img img {
            width: 50px;
            height: 50px;
            object-fit: contain;
        }
        .icon-card .category-title {
            font-weight: 600;
            font-size: 14px;
            color: var(--text-dark);
        }

        /* ===== Section Titles ===== */
        .listings-section { margin-bottom: 50px; }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .section-title {
            font-size: 22px;
            font-weight: 700;
            display: flex;
            align-items: center;
            gap: 10px;
        }
        .section-title i {
            color: var(--brand-orange);
            font-size: 20px;
        }
        .section-view-all {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 14px;
            font-weight: 500;
            color: var(--brand-orange);
            transition: all 0.2s ease;
        }
        .section-view-all:hover {
            gap: 10px;
            color: var(--brand-orange-dark);
        }

        /* ===== Category Buttons ===== */
        .category-buttons {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
        }
        .category-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 10px 18px;
            border: 1px solid var(--border-color);
            border-radius: 10px;
            background: #fff;
            font-size: 14px;
            font-weight: 500;
            cursor: pointer;
            transition: all 0.25s ease;
        }
        .category-btn:hover {
            background: var(--bg-warm);
            border-color: #fcd5b0;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.04);
        }
        .category-btn img {
            width: 26px;
            height: 26px;
            object-fit: cover;
            border-radius: 6px;
        }

        /* ===== Banner Ads Carousel ===== */
        .banner-section {
            margin-bottom: 40px;
        }
        .banner-carousel {
            position: relative;
            border-radius: 16px;
            overflow: hidden;
            width: 100%;
            aspect-ratio: 16 / 4;
        }
        .banner-carousel-slide {
            position: absolute;
            inset: 0;
            opacity: 0;
            transition: opacity 0.6s ease-in-out;
            pointer-events: none;
        }
        .banner-carousel-slide.active {
            opacity: 1;
            pointer-events: auto;
        }
        .banner-carousel-slide img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border-radius: 16px;
        }
        .banner-carousel-dots {
            position: absolute;
            bottom: 14px;
            left: 50%;
            transform: translateX(-50%);
            display: flex;
            gap: 8px;
            z-index: 5;
        }
        .carousel-dot {
            width: 10px;
            height: 10px;
            border-radius: 50%;
            border: 2px solid #fff;
            background: transparent;
            cursor: pointer;
            transition: background 0.3s;
            padding: 0;
        }
        .carousel-dot.active {
            background: #fff;
        }

        /* ===== User Advertisement Zones ===== */
        .user-advertisement {
            margin: 32px 0;
            border-radius: 16px;
            overflow: hidden;
        }
        .user-advertisement a {
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }
        .user-advertisement a:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 24px rgba(0,0,0,0.12);
        }

        /* ===== Product Grid ===== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
        }

        /* ===== Empty Ads State ===== */
        .empty-ads-state {
            grid-column: 1 / -1;
            text-align: center;
            padding: 60px 20px;
            background: linear-gradient(135deg, #fef7f0 0%, #fff 100%);
            border-radius: 16px;
            border: 2px dashed var(--border-color);
        }
        .empty-ads-state .empty-ads-icon {
            width: 64px;
            height: 64px;
            background: var(--bg-warm);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 16px;
        }
        .empty-ads-state .empty-ads-icon i {
            font-size: 24px;
            color: var(--brand-orange);
        }
        .empty-ads-state h4 {
            font-size: 18px;
            font-weight: 600;
            color: var(--text-dark);
            margin-bottom: 8px;
        }
        .empty-ads-state p {
            font-size: 14px;
            color: var(--text-light);
            max-width: 360px;
            margin: 0 auto;
        }
        .empty-ads-state p a {
            color: var(--brand-orange);
            font-weight: 500;
        }
        .empty-ads-state p a:hover {
            text-decoration: underline;
        }

        /* ===== Features Section ===== */
        .features-section {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            padding: 40px 0;
            border-top: 1px solid var(--border-color);
            border-bottom: 1px solid var(--border-color);
            margin: 20px 0 50px;
        }
        .feature-item {
            display: flex;
            align-items: center;
            gap: 14px;
        }
        .feature-icon-wrap {
            width: 48px;
            height: 48px;
            background: var(--bg-warm);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }
        .feature-icon-wrap i {
            font-size: 20px;
            color: var(--brand-orange);
        }
        .feature-item strong {
            display: block;
            font-size: 14px;
            font-weight: 600;
            color: var(--text-dark);
        }
        .feature-item span {
            font-size: 12px;
            color: var(--text-muted);
        }

        /* ===== App Download ===== */
        .app-download-section { margin-bottom: 50px; }
        .app-download-box {
            display: flex;
            align-items: center;
            gap: 40px;
            border-radius: 20px;
            background: linear-gradient(135deg, var(--brand-orange), #fb923c);
            color: white;
            padding: 50px;
            overflow: hidden;
            position: relative;
        }
        .app-download-box::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 400px;
            height: 400px;
            background: rgba(255,255,255,0.08);
            border-radius: 50%;
        }
        .app-download-text { position: relative; z-index: 1; }
        .app-download-text h2 { font-size: 30px; font-weight: 800; margin-bottom: 10px; }
        .app-download-text p { opacity: 0.9; font-size: 16px; line-height: 1.6; max-width: 450px; }
        .app-buttons { margin-top: 24px; display: flex; gap: 12px; flex-wrap: wrap; }
        .app-btn {
            background: #fff;
            color: var(--text-dark);
            padding: 12px 22px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            gap: 10px;
            font-weight: 600;
            transition: all 0.25s ease;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        .app-btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 18px rgba(0,0,0,0.12);
        }
        .app-btn i { font-size: 22px; }
        .app-btn-text { text-align: left; }
        .app-btn-text span { display: block; }
        .app-btn-text .subtitle { font-size: 10px; text-transform: uppercase; letter-spacing: 0.5px; color: var(--text-muted); }
        .app-btn-text .title { font-size: 15px; font-weight: 700; }
        .app-image { position: relative; flex-shrink: 0; z-index: 1; }
        .app-image img { width: 240px; transform: rotate(8deg); filter: drop-shadow(0 20px 40px rgba(0,0,0,0.2)); }

        /* ===== Responsive ===== */
        @media (max-width: 1024px) {
            .product-grid { grid-template-columns: repeat(3, 1fr); }
            .features-section { grid-template-columns: repeat(2, 1fr); }
            .hero-section { flex-direction: column; gap: 40px; }
            .hero-text { text-align: center; flex-basis: auto; }
            .hero-text p { margin: 0 auto 24px; }
            .hero-cta-row { justify-content: center; }
            .hero-image-grid { flex-basis: auto; width: 100%; max-width: 500px; }
            .app-download-box { flex-direction: column; text-align: center; padding: 40px 30px; }
            .app-buttons { justify-content: center; }
            .app-image img { width: 200px; }
        }
        @media (max-width: 768px) {
            .page-wrapper { margin: 0; border: none; box-shadow: none; border-radius: 0; padding: 15px; }
            .hero-text h1 { font-size: 34px; }
            .hero-image-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .icon-card { padding: 20px 12px; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 12px; }
            .features-section { grid-template-columns: 1fr; gap: 16px; padding: 30px 0; }
            .category-buttons { gap: 8px; }
            .category-btn { padding: 8px 14px; font-size: 13px; }
            .app-download-box { padding: 30px 20px; }
            .app-download-text h2 { font-size: 24px; }
            .app-image img { width: 160px; }
            .hero-btn-primary, .hero-btn-secondary { padding: 12px 22px; font-size: 14px; width: 100%; justify-content: center; }
            .banner-carousel { aspect-ratio: 16 / 5; }
        }
        @media (max-width: 480px) {
            .hero-text h1 { font-size: 28px; letter-spacing: -1px; }
            .hero-text p { font-size: 15px; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .hero-image-grid { grid-template-columns: repeat(2, 1fr); }
            .banner-carousel { aspect-ratio: 16 / 6; }
        }
    </style>

    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // ─── Banner Carousel Auto-Slide ───
        (function() {
            const carousel = document.getElementById('bannerCarousel');
            if (!carousel) return;
            const slides = carousel.querySelectorAll('.banner-carousel-slide');
            const dots = carousel.querySelectorAll('.carousel-dot');
            if (slides.length <= 1) return;

            let current = 0;
            let interval;

            function goTo(index) {
                slides[current].classList.remove('active');
                dots[current] && dots[current].classList.remove('active');
                current = (index + slides.length) % slides.length;
                slides[current].classList.add('active');
                dots[current] && dots[current].classList.add('active');
            }

            function startAutoSlide() {
                interval = setInterval(() => goTo(current + 1), 4000);
            }

            dots.forEach(dot => {
                dot.addEventListener('click', () => {
                    clearInterval(interval);
                    goTo(parseInt(dot.dataset.index));
                    startAutoSlide();
                });
            });

            startAutoSlide();
        })();

        Livewire.on('locationSet', () => {
            setTimeout(() => window.location.reload(), 250);
        });

        function detectUserLocation() {
            if (sessionStorage.getItem('locationPrompted')) {
                return;
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    function(position) {
                        sessionStorage.setItem('locationPrompted', 'true');
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;
                        
                        const geocoder = new google.maps.Geocoder();
                        geocoder.geocode({ 'location': { lat, lng } }, function(results, status) {
                            if (status === 'OK' && results[0]) {
                                let city = '', state = '', country = '', address = results[0].formatted_address;
                                results[0].address_components.forEach(c => {
                                    if (c.types.includes("locality")) city = c.long_name;
                                    if (c.types.includes("administrative_area_level_1")) state = c.long_name;
                                    if (c.types.includes("country")) country = c.long_name;
                                });
                                Livewire.emit('setLocation', lat, lng, city, state, country, address);
                            }
                        });
                    },
                    function(error) {
                        sessionStorage.setItem('locationPrompted', 'true');
                        Livewire.emit('setLocationFromIp');
                    }
                );
            }
        }

        detectUserLocation();
    });
    (function () {
        const searchInput = document.getElementById('q');
        const suggestionsBox = document.getElementById('suggestions');

        if (!searchInput || !suggestionsBox) return;

        let typingTimer;

        searchInput.addEventListener('input', function () {
            clearTimeout(typingTimer);
            const searchTerm = searchInput.value.trim();

            if (!searchTerm) {
                suggestionsBox.innerHTML = '';
                suggestionsBox.classList.add('hidden');
                return;
            }
            typingTimer = setTimeout(() => fetchSuggestions(searchTerm), 300);
        });

        function fetchSuggestions(searchTerm) {
            fetch(`{{ URL::to('/search') }}?q=${encodeURIComponent(searchTerm)}`)
                .then(response => {
                    if (!response.ok) throw new Error('Network error');
                    return response.json();
                })
                .then(data => {
                    const results = data.data || [];

                    if (results.length === 0) {
                        suggestionsBox.classList.add('hidden');
                        return;
                    }

                    suggestionsBox.innerHTML = '';
                    results.slice(0, 7).forEach(item => {
                        const title = item.value || '';
                        const category = item.category_id;
                        const row = document.createElement('button');
                        row.type = 'button';
                        row.className = 'w-full flex items-center gap-3 px-4 py-2 hover:bg-slate-50 text-left';
                        row.innerHTML = `<i class="fa-solid fa-magnifying-glass text-slate-400"></i><span>${title}</span>`;

                        row.onclick = function () {
                            searchInput.value = title;
                            const subCat = document.getElementById('subcategory');
                            const cat = document.getElementById('category');
                            if (subCat) subCat.value = category;
                            if (cat) cat.value = category;
                            suggestionsBox.classList.add('hidden');
                        };
                        suggestionsBox.appendChild(row);
                    });

                    suggestionsBox.classList.remove('hidden');
                })
                .catch(() => {
                    suggestionsBox.classList.add('hidden');
                });
        }

        document.addEventListener('click', (e) => {
            if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
                suggestionsBox.classList.add('hidden');
            }
        });
    })();
</script>
        </main>
        
