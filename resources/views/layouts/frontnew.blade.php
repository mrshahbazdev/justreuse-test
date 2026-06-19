<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link rel="stylesheet" href="{{ URL::to('css/tailwind.min.css') }}">
    @include('partials.favicon')

    @yield('whatsapp_meta')
    <?php
        $settings = App\Models\Setting::get_logos();
        App\Models\Setting::set_last_visited_url();
    ?>

    <style>
        /* Root Colors (Updated) */
        :root {
            --primary: #f8991b;      /* Aapka green color */
            --primary-dark: #e68a07; /* Green ka thora dark shade (hover ke liye) */
            --secondary: #39763a;    /* Aapka orange color */
            --secondary-dark: #2c5f2d;/* Orange ka thora dark shade (hover ke liye) */
        }
        html, body {
            font-family:'Inter',system-ui,-apple-system,Segoe UI,Roboto,'Helvetica Neue',Arial,'Noto Sans';
            background:#fffbfa;
            color:#0f172a;
            overflow-x:hidden;
        }
        
        /* Primary and Secondary Color Classes */
        .bg-primary {background:var(--primary)}
        .text-primary {color:var(--primary)}
        .hover\:bg-primary-dark:hover{background:var(--primary-dark)}
        
        .bg-secondary { background: var(--secondary) }
        .text-secondary { color: var(--secondary) }
        .hover\:bg-secondary-dark:hover { background: var(--secondary-dark) }

        /* Header shadow */
        .header.scrolled {
            box-shadow:0 10px 30px -15px rgba(15,23,42,.25);
        }

        /* Reveal on view */
        .reveal {opacity:0;transform:translateY(22px);transition:opacity .6s ease,transform .6s ease}
        .reveal.active {opacity:1;transform:translateY(0)}

        /* Focus outline */
        :focus-visible {outline:2px solid var(--primary);outline-offset:2px}

        /* Lift effect */
        .lift {transition:transform .25s ease, box-shadow .25s ease}
        .lift:hover {transform:translateY(-6px)}

        /* Swiper Custom */
        .hero-swiper .swiper-slide{overflow:hidden;border-radius:1rem}
        .hero-swiper .swiper-pagination-bullet-active{background:var(--primary)}

        /* PRELOADER STYLING */
        #preloader {
            position: fixed;
            top: 0; left: 0;
            width: 100%;
            height: 100%;
            background: #fff;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
            z-index: 9999;
            transition: opacity 0.5s ease, visibility 0.5s ease;
        }

        #preloader.hide {
            opacity: 0;
            visibility: hidden;
        }

        /* Spinner Animation */
        .loader {
            border: 6px solid #f3f3f3;
            border-top: 6px solid var(--primary);
            border-radius: 50%;
            width: 70px;
            height: 70px;
            animation: spin 1s linear infinite;
            margin-bottom: 15px;
        }

        @keyframes spin {
            0%   { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Preloader Logo */
        #preloader img {
            width: 120px;
            margin-bottom: 15px;
            animation: fadeLogo 1s ease-in-out infinite alternate;
        }

        @keyframes fadeLogo {
            from { opacity: 0.4; transform: scale(0.95); }
            to { opacity: 1; transform: scale(1); }
        }
      	a.bg-white{
        	color: black;
      }
    </style>

    <title>@yield('meta_title', $settings['meta_title'])</title>
    <meta property="og:title" content="@yield('meta_title', $settings['meta_title'])">
    <meta property="og:type" content="website">
    <meta property="og:locale" content="en_US">
    <meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'])">
    <meta property="og:description" name="description" content="@yield('meta_description', $settings['meta_desc'])">

    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">

    @livewireScripts
</head>
<body class="loading">

    <div id="preloader">
        <img src="{{ asset('storage/'.$settings['logo']) }}" alt="Site Logo">
        <div class="loader"></div>
        <p class="text-primary text-lg font-semibold">Loading...</p>
    </div>
	
    @include('cookie-consent::index')
    @include('layouts.headernew')
  	@livewire('mini-chat')
    @yield('content')
    @include('layouts.footernewhome')

    {!! config('app.google_map_script') !!}

    <button id="toTop" class="hidden fixed bottom-5 right-5 bg-primary text-white w-12 h-12 rounded-full items-center justify-center shadow-lg hover:bg-primary-dark">
        <i class="fa-solid fa-arrow-up"></i>
    </button>

    <script>
        // === Preloader Hide on Full Page Load ===
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            preloader.classList.add('hide');
            document.body.classList.remove('loading');
        });

        // Back-to-top & header shadow
        const header = document.getElementById('siteHeader');
        const toTop  = document.getElementById('toTop');
        addEventListener('scroll', () => {
            const sc = scrollY > 8;
            header?.classList.toggle('scrolled', sc);
            toTop.classList.toggle('hidden', !sc);
            toTop.classList.toggle('flex', sc);
        });
        toTop.addEventListener('click', () => scrollTo({top:0, behavior:'smooth'}));

        // Mobile menu
        const mobileBtn = document.getElementById('mobileBtn');
        const mobileMenu = document.getElementById('mobileMenu');
        mobileBtn.addEventListener('click', ()=> mobileMenu.classList.toggle('hidden'));

        // Reveal on view
        const io = new IntersectionObserver((entries)=>{
          entries.forEach(e => { if(e.isIntersecting){ e.target.classList.add('active'); io.unobserve(e.target);} });
        },{threshold:.15});
        document.querySelectorAll('.reveal').forEach(el=> io.observe(el));

        // Swiper (Hero)
        const heroSwiper = new Swiper('.hero-swiper', {
          loop: true, speed: 700, preloadImages: false,
          lazy: { loadPrevNext: true, loadPrevNextAmount: 2 },
          keyboard: { enabled: true }, grabCursor: true,
          autoplay: { delay: 3800, disableOnInteraction: false },
          pagination: { el: '.hero-pagination', clickable: true, dynamicBullets: true },
          navigation: { nextEl: '.hero-next', prevEl: '.hero-prev' },
          slidesPerView: 1, spaceBetween: 16, effect: 'slide',
          breakpoints: {
            1024: {
              effect: 'coverflow',
              coverflowEffect: { rotate: 0, stretch: 0, depth: 140, modifier: 1, slideShadows: false },
              centeredSlides: true, slidesPerView: 1.35, spaceBetween: 22
            },
            1280: {
              effect: 'coverflow',
              coverflowEffect: { rotate: 0, stretch: 0, depth: 140, modifier: 1, slideShadows: false },
              centeredSlides: true, slidesPerView: 1.5, spaceBetween: 26
            }
          }
        });

        // Swiper (Categories)
        const categorySwiper = new Swiper('.category-swiper', {
            slidesPerView: 2.5,
            spaceBetween: 16,
            grabCursor: true,
            navigation: {
                nextEl: '.category-next',
                prevEl: '.category-prev',
            },
            breakpoints: {
                640: { slidesPerView: 2, },
                1024: { slidesPerView: 4, },
                1280: { slidesPerView: 4, }
            }
        });

        // Search suggestions
        jQuery(function ($) {
            const q = document.getElementById('q');
            const suggestionsBox = document.getElementById('suggestions');
            let typingTimer;

            // Input event listener
            q.addEventListener('input', function () {
                clearTimeout(typingTimer);
                const v = q.value.trim();

                if (!v) {
                    suggestionsBox.innerHTML = '';
                    suggestionsBox.classList.add('hidden');
                    return;
                }

                typingTimer = setTimeout(() => fetchSuggestions(v), 300);
            });

            // Fetch suggestions via AJAX
            function fetchSuggestions(searchTerm) {
                $.ajax({
                    url: "{{ URL::to('/search') }}",
                    dataType: "json",
                    data: { q: searchTerm },
                    success: function (response) {
                        const outputAr = response.data || [];

                        // Agar empty result hai to hide karo
                        if (outputAr.length === 0) {
                            suggestionsBox.innerHTML = '';
                            suggestionsBox.classList.add('hidden');
                            return;
                        }

                        // Sirf 7 results hi show karne hain
                        const limitedResults = outputAr.slice(0, 7);

                        // Suggestions list banani
                        suggestionsBox.innerHTML = '';
                        limitedResults.forEach(item => {
                            const title = item.value || '';
                            const category = item.category_id;
                            const row = document.createElement('button');
                            row.type = 'button';
                            row.className = 'w-full flex items-center gap-3 px-4 py-2 hover:bg-slate-50 text-left';
                            row.innerHTML = `
                                <i class="fa-solid fa-magnifying-glass text-slate-400"></i>
                                <span>${title}</span>
                            `;

                            // On click suggestion select
                            row.onclick = function () {
                                q.value = title;
                                $('#subcategory').val(category);
                                $("#category").val(category);
                                suggestionsBox.classList.add('hidden');
                            };

                            suggestionsBox.appendChild(row);
                        });

                        suggestionsBox.classList.remove('hidden');
                    },
                    error: function () {
                        suggestionsBox.innerHTML = '';
                        suggestionsBox.classList.add('hidden');
                    }
                });
            }

            // Outside click par hide karna
            addEventListener('click', (e) => {
                if (!suggestionsBox.contains(e.target) && e.target !== q) {
                    suggestionsBox.classList.add('hidden');
                }
            });
        });



        jQuery(function ($) {
            // Google Places Autocomplete Initialization
            var input = document.getElementById('location');
            var autocomplete = new google.maps.places.Autocomplete(input);

            google.maps.event.addDomListener(input, 'keydown', function (event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                }
            });

            google.maps.event.addListener(autocomplete, 'place_changed', function () {
                $('#cityName').val('');
                $('#countryName').val('');
                $('#stateName').val('');
                $('#locality').val('');
                var place = autocomplete.getPlace();
                var address = place.address_components;
                var city, state;

                address.forEach(function (component) {
                    var types = component.types;
                    if (types.indexOf('administrative_area_level_2') > -1) {
                        city = component.long_name;
                    }
                    if (types.indexOf('administrative_area_level_1') > -1) {
                        state = component.long_name;
                    }
                });

                const country = place.address_components.find(item => item.types.includes('country'));

                $('#locality').val(place.name);
                $('#cityName').val(city);
                $('#countryName').val(country.long_name);
                $('#currentCountry').val(country.long_name);
                $('#stateName').val(state);
                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());
            });

            // === GEOLOCATION BUTTON ===
            $('#geoBtn').on('click', function () {
                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(function (position) {
                        const lat = position.coords.latitude;
                        const lng = position.coords.longitude;

                        // Reverse Geocoding using Google Maps API
                        const geocoder = new google.maps.Geocoder();
                        const latlng = { lat: lat, lng: lng };

                        geocoder.geocode({ location: latlng }, function (results, status) {
                            if (status === "OK") {
                                if (results[0]) {
                                    $('#location').val(results[0].formatted_address);

                                    let city = "", state = "", country = "", locality = "";

                                    results[0].address_components.forEach(function (component) {
                                        const types = component.types;
                                        if (types.includes("locality")) {
                                            city = component.long_name;
                                        }
                                        if (types.includes("administrative_area_level_1")) {
                                            state = component.long_name;
                                        }
                                        if (types.includes("country")) {
                                            country = component.long_name;
                                        }
                                    });

                                    // Fill all fields dynamically
                                    $('#cityName').val(city);
                                    $('#stateName').val(state);
                                    $('#countryName').val(country);
                                    $('#currentCountry').val(country);
                                    $('#locality').val(locality || results[0].formatted_address);
                                    $('#latitude').val(lat);
                                    $('#longitude').val(lng);

                                    // Success Toast
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'success',
                                        title: 'Location detected successfully!',
                                        showConfirmButton: false,
                                        timer: 2500
                                    });
                                } else {
                                    Swal.fire({
                                        toast: true,
                                        position: 'top-end',
                                        icon: 'error',
                                        title: 'Unable to fetch location!',
                                        showConfirmButton: false,
                                        timer: 2500
                                    });
                                }
                            } else {
                                Swal.fire({
                                    toast: true,
                                    position: 'top-end',
                                    icon: 'error',
                                    title: 'Geocoder failed! Please try again.',
                                    showConfirmButton: false,
                                    timer: 2500
                                });
                            }
                        });
                    }, function () {
                        // Permission denied toast
                        Swal.fire({
                            toast: true,
                            position: 'top-end',
                            icon: 'warning',
                            title: 'Please enable your device location!',
                            text: 'Turn on GPS and try again.',
                            showConfirmButton: false,
                            timer: 3000
                        });
                    });
                } else {
                    Swal.fire({
                        toast: true,
                        position: 'top-end',
                        icon: 'error',
                        title: 'Geolocation not supported in this browser!',
                        showConfirmButton: false,
                        timer: 3000
                    });
                }
            });
        });



        // Tabs interaction
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabPanes = document.querySelectorAll('.tab-pane');
        tabBtns.forEach(btn => {
          btn.addEventListener('click', () => {
            tabBtns.forEach(b => b.dataset.active = 'false');
            btn.dataset.active = 'true';
            tabPanes.forEach(pane => {
              pane.classList.toggle('hidden', pane.id !== btn.dataset.tab);
            });
          });
        });
        ["search"].forEach(function (id) {
                document.getElementById(id).addEventListener("click", function () {


                    var path = "{{ URL::to('/') }}";

                    // Category & Subcategory Handling
                    var c = "";
                    if (document.getElementById("subcategory").value != "") {
                        c = document.getElementById("subcategory").value;
                    } else if (document.getElementById("category").value != "") {
                        c = document.getElementById("category").value;
                    }

                    // Term Handling
                    var term = "";
                    if (
                        document.getElementById("subcategory").value == "" &&
                        document.getElementById("q").value != ""
                    ) {
                        term = document.getElementById("q").value;

                    // Agar #term empty hai lekin #termd me value hai to use karo
                    } else if (
                        document.getElementById("subcategory").value == "" &&
                        document.getElementById("termd").value != ""
                    ) {
                        term = document.getElementById("termd").value;
                    }

                    // Location Handling
                    var result = "";
                    if (document.getElementById("location").value != "") {
                        var current_address = "";
                        if (document.getElementById("locality").value != "") {
                            current_address = removeSpace(document.getElementById("locality").value);
                        } else if (document.getElementById("cityName").value != "") {
                            current_address = removeSpace(document.getElementById("cityName").value);
                        } else if (document.getElementById("stateName").value != "") {
                            current_address = removeSpace(document.getElementById("stateName").value);
                        } else {
                            current_address = removeSpace(document.getElementById("currentCountry").value);
                        }

                        var locality = document.getElementById("cityName").value != "" ? document.getElementById("locality").value : "";
                        var city = document.getElementById("cityName").value || "";
                        var state = document.getElementById("stateName").value;
                        var country = document.getElementById("countryName").value;
                        var latitude = document.getElementById("latitude").value;
                        var longitude = document.getElementById("longitude").value;
                        var location = document.getElementById("location").value;

                        result =
                            path +
                            "/" +
                            current_address +
                            "?c=" +
                            c +
                            "&s=" +
                            term +
                            "&locality=" +
                            locality +
                            "&city=" +
                            city +
                            "&state=" +
                            state +
                            "&country=" +
                            country +
                            "&lat=" +
                            latitude +
                            "&lng=" +
                            longitude +
                            "&loc=" +
                            location +
                            "&d=5";
                    } else {
                        var country = document.getElementById("currentLocation").value;
                        var location = document.getElementById("currentLocation").value;
                        var current_address = document.getElementById("currentCountry").value;

                        result =
                            path +
                            "/" +
                            current_address +
                            "?country=" +
                            country +
                            "&loc=" +
                            location +
                            "&d=5";
                    }

                    // Final Redirect
                    window.location = result;
                });
            });
        function removeSpace(value) {
                var a = value.toLowerCase();
                // var b = a.replaceAll(' ', '_');
                var b = a.split(' ').join('_');
                return b;
            }
    </script>
</body>
</html>