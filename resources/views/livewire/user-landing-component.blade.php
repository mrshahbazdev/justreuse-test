
    
       

        <main>
             @php
              $searchUrl = "";

              if (Session::has('Searchedurl')) {
                  // Priority 1: Use the user's last explicit search URL. This already contains all parameters.
                  $searchUrl = Session::get('Searchedurl');

              } elseif (Session::has('GetCountry')) {
                  // Priority 2: Use the user's detected location from the session.
                  $countryName = Session::get('GetCountry');
                  $locationName = Session::get('GetAddress') ?? $countryName;

                  $stateName = Session::get('GetState') ?? '';
                  $cityName = Session::get('GetCity') ?? '';
                  $latitude = Session::get('Getlat') ?? '';   // NEW: Get latitude from session
                  $longitude = Session::get('Getlng') ?? ''; // NEW: Get longitude from session

                  $path = str_replace(' ', '_', strtolower($countryName));

                  // Build the query with all location parameters, including lat and lng.
                  $queryParams = http_build_query([
                      'loc' => $locationName,
                      'country' => $countryName,
                      'state' => $stateName,
                      'city' => $cityName,
                      'lat' => $latitude,     // NEW: Add lat to query
                      'lng' => $longitude    // NEW: Add lng to query
                  ]);

                  $searchUrl = url($path) . '?' . $queryParams;

              } else {
                  // Priority 3: Fallback to the default country from your settings.
                  $countryName = App\Models\Setting::get_admin_default_country();
                  if (!empty($countryName)) {
                      $path = str_replace(' ', '_', strtolower($countryName));

                      // Build the fallback query with empty location details.
                      $searchUrl = url($path) . '?' . http_build_query([
                          'loc' => $countryName,
                          'country' => $countryName,
                          'state' => '',
                          'city' => '',
                          'lat' => '',
                          'lng' => ''
                      ]);
                  } else {
                      // Absolute fallback if no location info exists at all.
                      $searchUrl = url('/');
                  }
              }
          @endphp
          <div>
              <input type="hidden" id="category" value="{{ request()->c ?? '' }}">
              <input type="hidden" id="subcategory" value="">
              <input type="hidden" id="is_search" value="{{ session()->has('IsSearch') ?? '' }}">
          </div>
            <section class="hero-section">
                <div class="hero-text">
                    <h1>Find Great Deals. <span class="highlight">Buy & Sell</span> Smart.</h1>
                    <p>Search local listings or post your own quick, safe, and hassle-free.</p>
                </div>
                <div class="hero-image-grid">
                    @if(isset($random_categories) && $random_categories->count())
                        @foreach($random_categories as $category)
                            @php
                                $cat_img = !empty($category->image) ? asset('storage/' . $category->image) : 'https://img.icons8.com/color/96/generic-box.png';
                            @endphp
                            <a href="{{ $searchUrl . '&c=' . $category->slug }}" class="icon-card">
                                <img src="{{ $cat_img }}" alt="{{ $category->title }}">
                                <span class="category-title">{{ $category->title }}</span>
                            </a>
                        @endforeach
                    @endif
                </div>
            </section>

            <section class="listings-section">
                <h2 class="section-title">Browse by Category</h2>
                <div class="category-buttons">
                    @foreach($main_categories as $main_category)
                        @php
                            $cat_img = !empty($main_category->image) ? asset('storage/' . $main_category->image) : asset('storage/noimage150.png');
                        @endphp
                        <a href="{{ $searchUrl . '&c=' . $main_category->slug }}" class="category-btn">
                            <img src="{{ $cat_img }}" alt="{{ $main_category->title }}">
                            {{ $main_category->title }}
                        </a>
                    @endforeach
                </div>
            </section>
          	<div class="container mx-auto">
                <div class="ad-container">
                    <div class="h-32 bg-gray-100 animate-pulse rounded-lg"></div>
                </div>
            </div>
            <section class="listings-section">
                <h2 class="section-title">{{__('landing.top ads')}}</h2>
                 <div class="product-grid">
                    @forelse($top_ads_list as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @empty
                        <p>No popular ads found.</p>
                    @endforelse
                </div>
            </section>
            
			<div class="container mx-auto">
              <div class="ad-container">
                  <div class="h-32 bg-gray-100 animate-pulse rounded-lg"></div>
              </div>
          </div>
            <section class="listings-section">
                <h2 class="section-title">{{__('landing.feature ads')}}</h2>
                <div class="product-grid">
                    @forelse($featurs_ad_list as $ad)
                        {!! App\Models\Setting::htmlAdBlock($ad['id']) !!}
                    @empty
                        <p>No featured ads found.</p>
                    @endforelse
                </div>
            </section>
            
            <section class="features-section">
                <div class="feature-item">
                    <i class="fa-solid fa-shield-halved"></i>
                    Verified Sellers
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-lock"></i>
                    Secure Transactions
                </div>
                <div class="feature-item">
                   <i class="fa-solid fa-robot"></i>
                    AI-powered Fraud Detection
                </div>
                <div class="feature-item">
                    <i class="fa-solid fa-globe"></i>
                    Global Reach, Local Focus
                </div>
            </section>
            
            <section class="app-download-section">
                <div class="app-download-box">
                    <div class="app-download-text">
                        <h2>Get the JustReused App</h2>
                        <p>Manage your ads, chat with buyers, and discover deals on the go.</p>
                        <div class="app-buttons">
                            <a href="#" class="app-btn">
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
		<script>
        document.addEventListener('DOMContentLoaded', function() {
            window.addEventListener('load', function() {
                const adContainers = document.querySelectorAll('.ad-container');

                adContainers.forEach((container, index) => {
                    fetch(`/get-ad/home?position=${index + 1}`)
                        .then(response => response.text())
                        .then(html => {
                            container.innerHTML = html;
                        });
                });
            });
        });
        </script>
    <style>
        :root {
            --brand-orange: #F97316;
            --text-dark: #111827;
            --text-light: #6B7280;
            --bg-light: #fffbfa;
            --border-color: #E5E7EB;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Inter', sans-serif; }
        body { background-color: #fffbfa; color: var(--text-dark); -webkit-font-smoothing: antialiased; -moz-osx-font-smoothing: grayscale; }
        a { text-decoration: none; color: inherit; }
        .page-wrapper { background:#fffbfa; max-width: 1400px; margin: 20px auto; padding: 20px; border: 1px solid #F3F4F6; border-radius: 24px; box-shadow: 0 10px 40px -10px rgba(0,0,0,0.07); }
        header {  justify-content: space-between; align-items: center; padding: 10px 0; margin-bottom: 50px; }
        .logo { display: flex; align-items: center; font-size: 20px; font-weight: 800; color: var(--text-dark); gap: 8px; letter-spacing: -0.5px; }
        .logo-icon { display: flex; flex-direction: column; gap: 3px; }
        .logo-icon .bar1 { width: 18px; height: 8px; background-color: #34D399; border-radius: 2px; }
        .logo-icon .bar2 { width: 18px; height: 8px; background-color: var(--brand-orange); border-radius: 2px; }
        .search-container { flex-grow: 1; margin: 0 40px; position: relative; }
        .search-container input { width: 100%; padding: 12px 20px 12px 40px; border-radius: 8px; border: 1px solid #F3F4F6; background-color: var(--bg-light); font-size: 15px; font-weight: 500; }
        .search-container input::placeholder { color: #9CA3AF; }
        .search-container input:focus { outline: none; border-color: #D1D5DB; background-color: #fff; }
        .search-icon { position: absolute; left: 15px; top: 50%; transform: translateY(-50%); color: var(--text-light); }
        .sell-now-btn { background-color: var(--brand-orange); color: white; padding: 12px 24px; border: none; border-radius: 8px; font-weight: 600; font-size: 15px; cursor: pointer; transition: background-color 0.2s; }
        .sell-now-btn:hover { background-color: #EA580C; }
        .hero-section { display: flex; align-items: center; gap: 40px; margin-bottom: 60px; }
        .hero-text { flex-basis: 50%; }
        .hero-text h1 { font-size: 48px; font-weight: 800; line-height: 1.2; margin-bottom: 20px; letter-spacing: -1.5px; }
        .hero-text h1 .highlight { color: var(--brand-orange); }
        .hero-text p { font-size: 16px; color: var(--text-light); max-width: 450px; line-height: 1.6; }
        .hero-image-grid { flex-basis: 50%; display: grid; grid-template-columns: repeat(2, 1fr); gap: 15px; }
        
        /* UPDATED CSS for Hero Grid Cards */
        .hero-image-grid .icon-card { background-color: var(--bg-light); border-radius: 16px; display: flex; flex-direction: column; align-items: center; justify-content: center; padding: 20px 15px; text-align: center; gap: 10px; transition: all 0.2s; border: 1px solid var(--border-color); }
        .hero-image-grid .icon-card:hover { background-color: #fff; transform: translateY(-4px); box-shadow: 0 8px 15px rgba(0,0,0,0.06); border-color: #d1d5db;}
        .hero-image-grid .icon-card img { width: 50px; height: 50px; object-fit: contain; }
        .hero-image-grid .icon-card .category-title { font-weight: 600; font-size: 14px; color: var(--text-dark); }
        
        .listings-section { margin-bottom: 50px; }
        .section-title { font-size: 22px; font-weight: 700; margin-bottom: 20px; }
        .category-buttons { display: flex; flex-wrap: wrap; gap: 12px; }
        .category-btn { display: flex; align-items: center; gap: 10px; padding: 10px 20px; border: 1px solid var(--border-color); border-radius: 8px; background-color: #fffbfa; font-size: 14px; font-weight: 500; cursor: pointer; transition: all 0.2s; }
        .category-btn:hover { background-color: var(--bg-light); border-color: #d1d5db; }
        .category-btn img { width: 24px; height: 24px; object-cover; border-radius: 4px; }
        .product-grid { display: grid; grid-template-columns: repeat(4, 1fr); gap: 20px; }
        .product-card { border: 1px solid var(--border-color); border-radius: 12px; overflow: hidden; transition: box-shadow 0.2s, transform 0.2s; background-color: #fff; }
        .product-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.08); }
        .product-card .product-image { width: 100%; height: 180px; background-size: cover; background-position: center; background-color: var(--bg-light); position: relative; }
        .product-details { padding: 16px; }
        .product-details h3 { font-size: 18px; font-weight: 600; margin-bottom: 4px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .product-price { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: var(--brand-orange); }
        .product-location { font-size: 14px; color: var(--text-light); }
        .banner-ad-section { margin: 60px 0; background-color: var(--bg-light); border: 1px solid var(--border-color); border-radius: 16px; padding: 30px; display: flex; align-items: center; justify-content: space-between; gap: 30px; overflow: hidden; }
        .banner-text h3 { font-size: 24px; font-weight: 700; color: var(--text-dark); }
        .banner-text p { margin-top: 8px; color: var(--text-light); max-width: 400px; }
        .banner-btn { margin-top: 16px; display: inline-block; background-color: var(--brand-orange); color: #fff; padding: 10px 20px; border-radius: 8px; font-weight: 600; transition: background-color 0.2s; }
        .banner-btn:hover { background-color: #EA580C; }
        .banner-image img { max-width: 200px; height: auto; border-radius: 12px; }
        .features-section { display: flex; justify-content: space-around; flex-wrap: wrap; padding: 40px 0; border-top: 1px solid var(--border-color); border-bottom: 1px solid var(--border-color); margin-top: 20px; margin-bottom: 60px; gap: 20px; }
        .feature-item { display: flex; align-items: center; gap: 12px; font-size: 15px; font-weight: 500; }
        .feature-item i { font-size: 22px; color: var(--text-dark); width: 25px; text-align: center; }
        .app-download-box { display: flex; align-items: center; gap: 30px; border-radius: 16px; background-color: var(--brand-orange); color: white; padding: 40px; overflow: hidden; position: relative; }
        .app-download-text h2 { font-size: 32px; font-weight: 800; }
        .app-download-text p { margin-top: 10px; opacity: 0.9; }
        .app-buttons { margin-top: 25px; display: flex; gap: 15px; }
        .app-btn { background-color: #fff; color: var(--text-dark); padding: 10px 20px; border-radius: 8px; display: flex; align-items: center; gap: 10px; font-weight: 600; transition: background-color 0.2s; }
        .app-btn:hover { background-color: #f0f0f0; }
        .app-btn i { font-size: 24px; }
        .app-btn-text { text-align: left; }
        .app-btn-text span { display: block; }
        .app-btn-text .subtitle { font-size: 11px; }
        .app-btn-text .title { font-size: 16px; }
        .app-image { position: relative; flex-shrink: 0; }
        .app-image img { width: 250px; transform: rotate(10deg); }
        footer { display: flex; justify-content: space-between; align-items: center; padding: 40px 0 10px 0; border-top: 1px solid var(--border-color); margin-top: 50px; }
        footer .footer-links {  gap: 25px; }
        footer .footer-links a { text-decoration: none; color: var(--text-light); font-size: 15px; font-weight: 500; transition: color 0.2s; }
        footer .footer-links a:hover { color: var(--text-dark); }
        @media (max-width: 1024px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); }
            .hero-section { flex-direction: column; gap: 50px; }
            .hero-text { text-align: center; }
            .hero-text p { margin: 0 auto; }
            .features-section { justify-content: flex-start; padding-left: 20px; }
            .app-download-box { flex-direction: column; text-align: center; }
            .app-buttons { justify-content: center; }
        }
        @media (max-width: 768px) {
            .page-wrapper { margin: 0; border: none; box-shadow: none; border-radius: 0; padding: 15px; }
            header { flex-direction: column; gap: 15px; margin-bottom: 30px;}
            .search-container { margin: 0; width: 100%; }
            .hero-text h1 { font-size: 40px; }
            .product-grid { grid-template-columns: 1fr; }
            .banner-ad-section { flex-direction: column; text-align: center; }
            .features-section { flex-direction: column; align-items: flex-start; }
            footer { flex-direction: column; gap: 20px; }
        }
      
    </style>
          <script>
    document.addEventListener('DOMContentLoaded', function () {
        Livewire.on('locationSet', () => {
            setTimeout(() => window.location.reload(), 250);
        });

        function detectUserLocation() {
            // We ONLY run this if the user has NOT made a choice yet in THIS VISIT
            if (sessionStorage.getItem('locationPrompted')) {
                return;
            }

            if (navigator.geolocation) {
                navigator.geolocation.getCurrentPosition(
                    // SUCCESS: User clicked "Allow"
                    function(position) {
                        sessionStorage.setItem('locationPrompted', 'true'); // Remember we've prompted
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
                    // ERROR: User clicked "Block"
                    function(error) {
                        sessionStorage.setItem('locationPrompted', 'true'); // Remember we've prompted
                        // Now we tell the backend to use the IP address as a fallback
                        Livewire.emit('setLocationFromIp');
                    }
                );
            }
        }

        detectUserLocation();
    });
             jQuery(function ($) {
            const searchInput = document.getElementById('q');
            const suggestionsBox = document.getElementById('suggestions');
            
            if (searchInput && suggestionsBox) {
                let typingTimer;

                // Listen for user input
                searchInput.addEventListener('input', function () {
                    clearTimeout(typingTimer);
                    const searchTerm = searchInput.value.trim();

                    if (!searchTerm) {
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.classList.add('hidden');
                        return;
                    }
                    // Wait 300ms after user stops typing to fetch suggestions
                    typingTimer = setTimeout(() => fetchSuggestions(searchTerm), 300);
                });

                // Fetch suggestions via AJAX call
                function fetchSuggestions(searchTerm) {
                    $.ajax({
                        url: "{{ URL::to('/search') }}",
                        dataType: "json",
                        data: { q: searchTerm },
                        success: function (response) {
                            const results = response.data || [];

                            if (results.length === 0) {
                                suggestionsBox.classList.add('hidden');
                                return;
                            }

                            // Build the suggestions list
                            suggestionsBox.innerHTML = '';
                            results.slice(0, 7).forEach(item => { // Show max 7 results
                                const title = item.value || '';
                                const category = item.category_id;
                                const row = document.createElement('button');
                                row.type = 'button';
                                row.className = 'w-full flex items-center gap-3 px-4 py-2 hover:bg-slate-50 text-left';
                                row.innerHTML = `<i class="fa-solid fa-magnifying-glass text-slate-400"></i><span>${title}</span>`;

                                // When a suggestion is clicked
                                row.onclick = function () {
                                    searchInput.value = title;
                                    $('#subcategory').val(category);
                                    $('#category').val(category);
                                    suggestionsBox.classList.add('hidden');
                                };
                                suggestionsBox.appendChild(row);
                            });

                            suggestionsBox.classList.remove('hidden');
                        },
                        error: function () {
                            suggestionsBox.classList.add('hidden');
                        }
                    });
                }

                // Hide suggestions when clicking outside
                document.addEventListener('click', (e) => {
                    if (!suggestionsBox.contains(e.target) && e.target !== searchInput) {
                        suggestionsBox.classList.add('hidden');
                    }
                });
            }
        });
</script>
        </main>
        