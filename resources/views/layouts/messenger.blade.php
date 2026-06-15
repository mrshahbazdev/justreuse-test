<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>My Messages</title>
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
    @livewireStyles
</head>
<?php
		use App\Models\TblPost;
		use App\Models\TblCurrency;
		use App\Models\TblCategory;
		use App\Models\FeaturesMappingGroup;
		$settings = App\Models\Setting::get_logos();
		//set session - last visited url
		App\Models\Setting::set_last_visited_url();
		$slug = request()->segment(1);
		$post = TblPost::where('slug', $slug)->first();
		$currency = !empty($post->currency_id) ? TblCurrency::find($post->currency_id) : null;
		$category = !empty($post->category_id) ? TblCategory::find($post->category_id) : null;
		$currency_code = $currency ? $currency->short_code : '';
		$price = isset($post->price) && is_numeric($post->price) ? (float) $post->price : 0;
		$locality = $post->locality ?? '';
		$category_name = $category ? $category->title : '';
		$get_features = [];
		if (isset($post) && !empty($post->category_id)) {
			$get_features = FeaturesMappingGroup::where('cat_id', $post->category_id)
				->orderBy('list_order', "asc")
				->limit(5)
				->pluck('features_title')
				->toArray();
		}
		$keywords = !empty($get_features) ? implode(', ', $get_features) : $settings['meta_keywords'];
		$description_preview = '';
		if (!empty($post->description)) {
			// Stop at first period or limit to 160 characters if no period
			$firstPeriod = strpos($post->description, '.');
			$description_preview = $firstPeriod !== false 
				? substr($post->description, 0, $firstPeriod + 1) 
				: Str::limit(strip_tags($post->description), 160);
		}
		$product_condition = "";
		if(!empty($post->product_condition)){
			if($post->product_condition == 1){
				$product_condition = 'Like New';
			}
			else if($post->product_condition == 2){
				$product_condition = 'Lightly used';
			}
			else if($post->product_condition == 3){
				$product_condition = 'Heavily used';
			}
		}
		if(!empty($post->images)){
			$images = $post->images;
		}else{
			$images = '';
		}
		if(!empty($post->slug)){
			$slug = $post->slug;
		}else{
			$slug = '';
		}
		?>
<body class=" bg-gray-100">

    @include('layouts.headernew')
	
    <main>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
		<script src="https://maps.googleapis.com/maps/api/js?key={{ config('services.google.maps_api_key') }}&libraries=places" defer></script>

        {{ $slot }}
    </main>
    @livewireScripts
</body>
</html>