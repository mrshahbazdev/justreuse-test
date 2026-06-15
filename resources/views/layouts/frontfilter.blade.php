<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Just Reused - Modern Classifieds</title>
 	 <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://unpkg.com/swiper/swiper-bundle.min.css"/>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://unpkg.com/swiper/swiper-bundle.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>
    <script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>
    <link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
 	
 	@include('livewire.common.seo')
    @livewire('mini-chat')
    @livewireStyles
    @yield('whatsapp_meta')
    <?php
        $settings = App\Models\Setting::get_logos();
        App\Models\Setting::set_last_visited_url();
    ?>
    <style>
        /* Modern CSS Reset and Variables (Updated Colors) */
        :root {
            --primary: #f8991b;      /* Green Color */
            --primary-dark: #e68a07; /* Darker Green */
            --secondary: #39763a;    /* Orange Color */
            --secondary-dark: #2c5f2d;/* Darker Orange */
            --success: #28a745;
            --success-dark: #218838;
            --light: #f8f9fa;
            --dark: #212529;
            --gray: #6c757d;
            --light-gray: #e9ecef;
            --border-radius: 12px;
            --shadow: 0 10px 25px rgba(0, 0, 0, 0.08);
            --transition: all 0.3s ease;
            --light-bg: #f5f7fb;
            --card-bg: #fffbfa;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
		html, body {
            height: 100% !important ;
        }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: var(--dark);
            line-height: 1.6;
            font-size: 16px;
          	min-height: 100vh !important ;
          	display: grid; /* Grid layout enable karein */
            grid-template-rows: auto 1fr auto;
        }

        /* Modern Header */
        .main-header {
            background-color: var(--card-bg);
            padding: 15px 30px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .logo img {
            height: 40px;
        }

        .header-actions {
            display: flex;
            align-items: center;
            gap: 15px;
        }

        .login-btn, .post-ad-btn {
            background-color: var(--primary);
            color: white;
            padding: 10px 20px;
            border-radius: 25px;
            text-decoration: none;
            font-weight: 600;
            transition: var(--transition);
            display: flex;
            align-items: center;
            gap: 8px;
            box-shadow: 0 4px 10px rgba(57, 118, 58, 0.3); /* Updated shadow color */
            border: none;
        }

        .login-btn:hover, .post-ad-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(57, 118, 58, 0.4); /* Updated shadow color */
        }
        
        /* Modern, Multi-product Classifieds Search Section */
        .search-section {
            background-color: var(--card-bg);
            padding: 2.5rem 1.5rem;
            margin: 25px auto;
            max-width: 1200px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--light-gray);
            position: relative;
        }
        
        .search-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-image: url('https://cdn.pixabay.com/photo/2017/08/07/21/21/audi-2607147_1280.jpg');
            background-size: cover;
            background-position: center;
            opacity: 0.1;
            border-radius: var(--border-radius);
            z-index: 0;
        }

        .search-content {
            position: relative;
            z-index: 1;
        }

        .search-title {
            text-align: center;
            font-size: 2.2rem;
            font-weight: 700;
            color: var(--dark);
            margin-bottom: 0.5rem;
        }

        .search-subtitle {
            text-align: center;
            font-size: 1.2rem;
            color: var(--gray);
            margin-bottom: 1.5rem;
        }

        .search-form {
            display: flex;
            gap: 10px;
            background: var(--light);
            padding: 8px;
            border-radius: 10px;
            border: 2px solid var(--primary);
            transition: box-shadow 0.3s ease;
        }
        
        .search-form:focus-within {
            box-shadow: 0 0 15px rgba(57, 118, 58, 0.3); /* Updated shadow color */
        }

        .search-input-group {
            display: flex;
            align-items: center;
            flex: 1;
            background-color: #fffbfa;
            padding: 10px 15px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
        }

        .search-input-group i {
            color: var(--gray);
            margin-right: 10px;
        }

        .search-input {
            width: 100%;
            border: none;
            outline: none;
            font-size: 16px;
            background: transparent;
        }

        .search-input::placeholder {
            color: var(--gray);
        }
        
        .category-select, .location-select {
            border: none;
            outline: none;
            background: #fffbfa;
            font-size: 16px;
            color: var(--dark);
            cursor: pointer;
            padding-left: 10px;
            flex-grow: 1;
        }
        
        .location-input-group {
            border-right: 1px solid var(--light-gray);
        }

        .search-btn {
            background-color: var(--primary);
            color: #fffbfa;
            border: none;
            border-radius: 8px;
            padding: 12px 30px;
            font-size: 18px;
            font-weight: 600;
            cursor: pointer;
            transition: background-color 0.3s ease;
            box-shadow: 0 4px 10px rgba(57, 118, 58, 0.3); /* Updated shadow color */
        }
        
        .search-btn:hover {
            background-color: var(--primary-dark);
        }

        /* Main Container */
        .container {
            display: flex;
            gap: 25px;
            padding: 25px;
            max-width: 1200px;
            margin: 30px auto;
        }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background-color: var(--card-bg);
            padding: 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            height: fit-content;
            position: sticky;
            top: 90px;
            border: 1px solid var(--light-gray);
        }

        .sidebar-title {
            font-size: 20px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-gray);
        }

        .filter-section {
            margin-bottom: 25px;
            display: none; /* Hide all by default */
        }
        
        .filter-section.active {
            display: block; /* Show active filter sections */
        }
        
        .filter-section h2 {
            font-size: 16px;
            font-weight: 600;
            color: var(--dark);
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .filter-section h2 i {
            color: var(--primary);
        }

        .filter-section ul {
            list-style: none;
        }

        .filter-section li {
            margin-bottom: 10px;
            padding: 5px 0;
            transition: var(--transition);
        }

        .filter-section li:hover {
            background-color: var(--light);
            border-radius: 6px;
            padding-left: 5px;
        }

        .filter-section label {
            font-size: 15px;
            color: var(--dark);
            cursor: pointer;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-section input[type="checkbox"] {
            accent-color: var(--success);
            transform: scale(1.2);
        }

        .count {
            background-color: var(--light-gray);
            color: var(--gray);
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 12px;
            margin-left: auto;
        }

        .price-filter, .distance-filter {
            border-top: 1px solid var(--light-gray);
            padding-top: 25px;
        }

        .price-range-slider, .distance-range-slider {
            position: relative;
            height: 5px;
            background: var(--light-gray);
            border-radius: 5px;
            margin: 25px 0;
        }

        .price-range-slider .track, .distance-range-slider .track {
            position: absolute;
            height: 100%;
            background: var(--success);
            border-radius: 5px;
            z-index: 1;
        }

        .price-range-slider input[type="range"], .distance-range-slider input[type="range"] {
            -webkit-appearance: none;
            width: 100%;
            height: 5px;
            position: absolute;
            background: transparent;
            pointer-events: none;
            z-index: 2;
        }

        .price-range-slider input[type="range"]::-webkit-slider-thumb,
        .distance-range-slider input[type="range"]::-webkit-slider-thumb {
            -webkit-appearance: none;
            height: 18px;
            width: 18px;
            background-color: var(--success);
            border: 2px solid white;
            border-radius: 50%;
            cursor: pointer;
            pointer-events: all;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.2);
        }

        .price-values, .distance-values {
            display: flex;
            justify-content: space-between;
            font-size: 14px;
            color: var(--gray);
            margin-top: 15px;
        }

        .apply-btn {
            background-color: var(--success);
            color: white;
            width: 100%;
            padding: 12px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            margin-top: 20px;
            box-shadow: 0 4px 10px rgba(40, 167, 69, 0.3);
        }

        .apply-btn:hover {
            background-color: var(--success-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
        }

        .main-content {
            flex-grow: 1;
        }

        .top-bar {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            background: var(--card-bg);
            padding: 15px 20px;
            border-radius: var(--border-radius);
            box-shadow: var(--shadow);
            border: 1px solid var(--light-gray);
        }

        .results-count {
            font-size: 15px;
            color: var(--gray);
        }

        .sort-options {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .sort-options label {
            font-size: 15px;
            color: var(--dark);
        }

        .sort-options select {
            padding: 8px 12px;
            border-radius: 8px;
            border: 1px solid var(--light-gray);
            font-size: 14px;
            background-color: #fffbfa;
            cursor: pointer;
            transition: var(--transition);
        }

        .sort-options select:focus {
            border-color: var(--primary);
            outline: none;
            box-shadow: 0 0 0 3px rgba(57, 118, 58, 0.1); /* Updated shadow color */
        }

        .card-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 25px;
        }
        
        /* New hidden class for JavaScript functionality */
        .card.hidden {
            display: none;
        }

        .card {
            background-color: var(--card-bg);
            border-radius: var(--border-radius);
            overflow: hidden;
            box-shadow: var(--shadow);
            transition: var(--transition);
            position: relative;
            border: 1px solid var(--light-gray);
        }

        .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.12);
        }

        .card-img-container {
            position: relative;
            height: 200px;
            overflow: hidden;
        }

        .card-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform 0.5s ease;
        }

        .card:hover .card-img {
            transform: scale(1.1);
        }

        .verified-badge {
            position: absolute;
            top: 12px;
            left: 12px;
            background-color: var(--success);
            color: white;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            display: flex;
            align-items: center;
            gap: 5px;
            z-index: 2;
        }

        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .card-body h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 5px;
            color: var(--dark);
        }

        .price {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
            margin-bottom: 10px;
        }

        .card-details {
            display: flex;
            align-items: center;
            gap: 10px;
            color: var(--gray);
            font-size: 14px;
            margin-bottom: 15px;
        }

        .location i {
            color: var(--primary);
        }

        .card-actions {
            margin-top: auto;
            display: flex;
            gap: 10px;
        }

        .view-btn {
            flex: 1;
            padding: 12px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
        }

        .view-btn:hover {
            background-color: #555;
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .wishlist-btn {
            width: 45px;
            display: flex;
            align-items: center;
            justify-content: center;
            background-color: var(--light);
            border: none;
            border-radius: 8px;
            color: var(--gray);
            cursor: pointer;
            transition: var(--transition);
        }

        .wishlist-btn:hover {
            background-color: #ffebee;
            color: #f72585;
        }
        
        .load-more-btn-container {
            display: flex;
            justify-content: center;
            margin-top: 25px;
        }
        
        .load-more-btn {
            background-color: var(--primary);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: var(--transition);
            box-shadow: 0 4px 10px rgba(57, 118, 58, 0.3); /* Updated shadow color */
            display: flex;
            align-items: center;
            gap: 8px;
        }
        
        .load-more-btn:hover {
            background-color: var(--primary-dark);
            transform: translateY(-2px);
            box-shadow: 0 6px 15px rgba(57, 118, 58, 0.4); /* Updated shadow color */
        }

       
        .social-icons {
            display: flex;
            gap: 15px;
        }

        .social-icons a {
            display: flex;
            align-items: center;
            justify-content: center;
            width: 40px;
            height: 40px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            color: white;
            font-size: 18px;
            transition: var(--transition);
        }

        .social-icons a:hover {
            background: var(--primary);
            transform: translateY(-3px);
        }

        .footer-bottom {
            text-align: center;
            margin-top: 50px;
            font-size: 14px;
            color: rgba(255, 255, 255, 0.6);
            border-top: 1px solid rgba(255, 255, 255, 0.1);
            padding-top: 20px;
        }

        .back-to-top {
            position: fixed;
            bottom: 30px;
            right: 30px;
            background-color: var(--success);
            color: white;
            width: 50px;
            height: 50px;
            border-radius: 50%;
            border: none;
            font-size: 18px;
            cursor: pointer;
            box-shadow: 0 6px 15px rgba(40, 167, 69, 0.4);
            display: flex;
            justify-content: center;
            align-items: center;
            transition: var(--transition);
            z-index: 99;
            opacity: 0;
            visibility: hidden;
        }

        .back-to-top.visible {
            opacity: 1;
            visibility: visible;
        }

        .back-to-top:hover {
            background-color: var(--success-dark);
            transform: translateY(-5px);
        }

        /* Responsive Design */
        @media (max-width: 992px) {
            .container {
                flex-direction: column;
            }
            
            .sidebar {
                width: 100%;
                position: static;
            }
            
            .search-section {
                padding: 1.5rem;
            }
            
            .search-form {
                flex-direction: column;
            }
            
            .search-input-group {
                border-right: none;
                border-bottom: 1px solid var(--light-gray);
                padding: 10px 15px;
            }
            
            .search-btn {
                width: 100%;
            }
        }

        @media (max-width: 768px) {
            .header-content {
                flex-direction: column;
                gap: 15px;
            }
            
            .top-bar {
                flex-direction: column;
                gap: 15px;
                align-items: flex-start;
            }
            
            .footer-grid {
                grid-template-columns: 1fr;
                text-align: center;
            }
            
            .footer-section h3::after {
                left: 50%;
                transform: translateX(-50%);
            }
            
            .social-icons {
                justify-content: center;
            }
        }
        
        /* Animation for cards loading */
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .card {
            animation: fadeIn 0.5s ease forwards;
        }

        .card:nth-child(2) { animation-delay: 0.1s; }
        .card:nth-child(3) { animation-delay: 0.2s; }
        .card:nth-child(4) { animation-delay: 0.3s; }
        .card:nth-child(5) { animation-delay: 0.4s; }
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
    </style>
</head>
<body class="loading">
 
	<?php
			
		if (!empty(request()->loc)) {
			$lc = request()->loc;
		} else if (Session::has("GetAddress")) {
			$lc = Session::get("GetAddress");
		} else {
			$lc = Session::get("GetCountry");
		}
		if (!empty(request()->loc)) {
			$ci = !empty(request()->city) ? request()->city : Session::get("GetCity");
		} else if (Session::has("GetCity")) {
			$ci = Session::get("GetCity");
		} else {
			$ci = "";
		}

		if (!empty(request()->loc)) {
			$locality = !empty(request()->locality) ? request()->locality : Session::get("GetLocality");
		} else if (Session::has("GetLocality")) {
			$locality = Session::get("GetLocality");
		} else {
			$locality = "";
		}
		if (!empty(request()->loc)) {
			$lat = !empty(request()->lat) ? request()->lat : Session::get("Getlat");
		} else if (Session::has("Getlat")) {
			$lat = Session::get("Getlat");
		} else {
			$lat = "";
		}
		if (!empty(request()->loc)) {
			$lng = !empty(request()->lng) ? request()->lng : Session::get("Getlng");
		} else if (Session::has("Getlng")) {
			$lng = Session::get("Getlng");
		} else {
			$lng = "";
		}
		if (!empty(request()->loc)) {
			$st = !empty(request()->state) ? request()->state : Session::get("GetState");
		} else if (Session::has("GetState")) {
			$st = Session::get("GetState");
		} else {
			$st = "";
		}
		if (!empty(request()->loc)) {
			$cut = !empty(request()->country) ? request()->country : Session::get("GetCountry");
		} else if (Session::has("GetCountry")) {
			$cut = Session::get("GetCountry");
		} else {
			$cut = "";
		}
		if (Session::has("GetSearched")) {
			$cconty = Session::get("GetSearched");
		} else if (!empty(request()->country)) {
			$cconty = str_replace(' ', '_', strtolower(request()->country));
		} else {
			$cconty = str_replace(' ', '_', strtolower(Session::get("GetCountry")));
		}
			$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
			// get activated post method plugins // exchange method, buynow method
			$post_methods = App\Models\TblPostMethod::get_active_post_methods();
			$currentURL = Request::getRequestUri();
			$chat_seg =  request()->segment(1);
			$chat_url = App\Models\Setting::check_active_chat_system($currentURL, $chat_seg);

                        
			?>
		@if(auth()->user())
			<?php
			 /* Notifiations count and list */
                        $notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();

                        // chat count
                        $userid = auth()->user()->id;

                        $chatlists = App\Models\TblChat::where('tbl_chats.from_id', $userid)
                            ->join('tbl_posts', function ($join) {
                                $join->on('tbl_chats.post_id', '=', 'tbl_posts.id')
                                    ->whereNull('tbl_posts.deleted_at')
                                    ->where('tbl_posts.sold_status', 0);
                            })
                            ->orWhere('tbl_chats.to_id', $userid)
                            ->whereNotNull('tbl_chats.msg')
                            ->whereNull('tbl_chats.deleted_at')
                            ->groupBy('tbl_chats.post_id', 'tbl_chats.receiver')
                            ->orderBy('tbl_chats.created_at', 'desc')
                            ->get(['tbl_chats.*', 'tbl_posts.title as post_name']);

                        $total_unread_count = 0;

                        foreach ($chatlists as $chatlist) {
                            $visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);

                            if (!empty($visible_posts)) {

                                $sender = (($userid == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
                                $unread_count = App\Models\TblChat::getUnreadCount($userid, $sender, $chatlist->post_id);
                                $total_unread_count += $unread_count;
                            }
                        }
			$userImg = auth()->user()->profile_photo_path;
			if (!empty($userImg)) {
				$userImgUrl = URL::asset('storage/' . $userImg);
			} else {
				$userImgUrl = URL::asset('storage/profile-avatar.jpg');
			}
			?>
		@endif
    @include('layouts.headernew')
 	<input id="category" type="hidden" value="<?php echo (!empty(request()->c) ? request()->c : "") ?>">
				<input type="hidden" id="cityName" value="<?php echo $ci; ?>">
				<input type="hidden" id="locality" value="<?php echo $locality; ?>">
				<input type="hidden" id="countryName" value="<?php echo $cut ?>">
				<input type="hidden" id="stateName" value="<?php echo $st; ?>">
				<input type="hidden" id="currentCountry" value="<?php echo $cconty; ?>">
				<input type="hidden" id="currentLocation" value="<?php echo Session::get("GetCountry"); ?>">
				<input type="hidden" id="latitude" value="<?php echo $lat; ?>" />
				<input type="hidden" id="longitude" value="<?php echo $lng; ?>" />
				<input type="hidden" id="is_location_allowed" value="0" />
				<input type="hidden" id="subcategory" value="">
				<input type="hidden" id="is_search" value="<?php echo Session::get("IsSearch"); ?>">
	@include('cookie-consent::index')
    @yield('content')
	
    @include('layouts.footernewhome')
	{!! config('app.google_map_script') !!}

    <script>
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
      // === Preloader Hide on Full Page Load ===
        window.addEventListener('load', function() {
            const preloader = document.getElementById('preloader');
            preloader.classList.add('hide');
            document.body.classList.remove('loading');
        });
        // Back to top button functionality
        const backToTopButton = document.querySelector('.back-to-top');
        
        window.addEventListener('scroll', () => {
            if (window.pageYOffset > 300) {
                backToTopButton.classList.add('visible');
            } else {
                backToTopButton.classList.remove('visible');
            }
        });
        
        backToTopButton.addEventListener('click', () => {
            window.scrollTo({
                top: 0,
                behavior: 'smooth'
            });
        });
        
        // Price range slider functionality
        function setupPriceSlider(minSlider, maxSlider, track, priceValues) {
            function updateSlider() {
                const minVal = parseInt(minSlider.value);
                const maxVal = parseInt(maxSlider.value);
                
                if (minVal > maxVal) {
                    minSlider.value = maxVal;
                    maxSlider.value = minVal;
                    updateSlider();
                    return;
                }
                
                const minPercent = (minVal / parseInt(minSlider.max)) * 100;
                const maxPercent = (maxVal / parseInt(maxSlider.max)) * 100;
                
                track.style.left = minPercent + '%';
                track.style.width = (maxPercent - minPercent) + '%';
                
                priceValues.querySelector('span:first-child').textContent = '$' + minVal.toLocaleString();
                priceValues.querySelector('span:last-child').textContent = '$' + maxVal.toLocaleString();
            }
            
            minSlider.addEventListener('input', updateSlider);
            maxSlider.addEventListener('input', updateSlider);
            updateSlider();
        }

        const defaultPriceSlider = document.querySelector('#filter-vehicles .price-range-slider');
        if (defaultPriceSlider) {
            setupPriceSlider(
                defaultPriceSlider.querySelector('.slider-min'),
                defaultPriceSlider.querySelector('.slider-max'),
                defaultPriceSlider.querySelector('.track'),
                document.querySelector('#filter-vehicles .price-values')
            );
        }

        // Wishlist button functionality
        const wishlistButtons = document.querySelectorAll('.wishlist-btn');
        
        wishlistButtons.forEach(button => {
            button.addEventListener('click', function() {
                const icon = this.querySelector('i');
                if (icon.classList.contains('far')) {
                    icon.classList.remove('far');
                    icon.classList.add('fas');
                    this.style.color = '#f72585';
                } else {
                    icon.classList.remove('fas');
                    icon.classList.add('far');
                    this.style.color = '';
                }
            });
        });

        // Dynamic Filter Functionality
        const mainCategorySelect = document.getElementById('mainCategorySelect');
        const sidebar = document.querySelector('.sidebar');
        const filterSections = document.querySelectorAll('.filter-section');

        mainCategorySelect.addEventListener('change', function() {
            const selectedCategory = this.value;

            // Hide all filter sections
            filterSections.forEach(section => {
                section.classList.remove('active');
            });

            // Show filters based on the selected category
            if (selectedCategory === 'vehicles') {
                document.getElementById('filter-vehicles').classList.add('active');
            } else if (selectedCategory === 'electronics') {
                document.getElementById('filter-electronics').classList.add('active');
            } else {
                // Default to showing 'All Categories' and other common filters
                document.getElementById('filter-all').classList.add('active');
            }
        });
        
        // Load More Button Functionality
        const loadMoreBtn = document.querySelector('.load-more-btn');
        const hiddenCards = document.querySelectorAll('.card.hidden');
        const cardsToShow = 4; // Number of cards to show on each click
        let cardsShown = 0;

        loadMoreBtn.addEventListener('click', () => {
            for (let i = 0; i < cardsToShow; i++) {
                if (hiddenCards[cardsShown]) {
                    hiddenCards[cardsShown].classList.remove('hidden');
                    cardsShown++;
                } else {
                    loadMoreBtn.style.display = 'none'; // Hide the button when all cards are shown
                    break;
                }
            }
        });
	
    </script>
  @livewireScripts
</body>
</html>