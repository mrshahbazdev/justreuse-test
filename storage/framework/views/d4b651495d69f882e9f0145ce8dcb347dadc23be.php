<!DOCTYPE html>
<html>

	<head>
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow" />
		<!--whatsapp sharing -->
		<?php echo $__env->yieldContent('whatsapp_meta'); ?>
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
		<!--meta data title and description start-->
		<title><?php echo e(isset($post) && !empty($post->title) ? $post->title : $settings['meta_title']); ?> - <?php echo e($product_condition); ?> Condition <?php if(isset($post) && !empty($post->locality)): ?>| <?php echo e($post->locality); ?><?php endif; ?> </title>
		<meta property="og:title" content="<?php echo e(isset($post) && !empty($post->title) ? $post->title : $settings['meta_title']); ?> - <?php echo e($product_condition); ?> Condition <?php if(isset($post) && !empty($post->locality)): ?>| <?php echo e($post->locality); ?><?php endif; ?>">
		<meta property="og:type" content="website">
		<meta property="og:locale" content="en_US">
		<meta name="keywords" content="<?php echo e($keywords); ?>">
		<meta property="og:description" name="description" content="<?php echo e(isset($post->title) ? 'Buy ' . $post->title : 'Buy best products'); ?> for <?php echo e($currency_code . number_format($price, 0)); ?> in <?php echo e($locality ?? 'your area'); ?>. Best deals on <?php echo e($product_condition); ?> <?php echo e($category_name ?? 'products'); ?> at JustReused.">
		<?php if(!empty($images)): ?><meta property="og:image" content="<?php echo e(URL::to('/storage/'.$images)); ?>"><?php endif; ?>
		<?php if(!empty($slug)): ?><meta property="og:url" content="<?php echo e($slug); ?>"><?php endif; ?>
		<!--meta data title and description end-->
		<!-- Font Awesome CDN -->
		<link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css" rel="stylesheet">

		<style>
		button {
			overflow: hidden;
			position: relative;
          background-color:#f39c12;
		}
		button:after {
			content: '';
			position: absolute;
			top: 0;
			left: -200%;
			/* width: 200%; */
			/* height: 100%; */
			transform: skewX(-20deg);
			background-image: linear-gradient(to right, #0000001f, rgb(255 255 255 / 52%), transparent);
		}
		button:hover:after {
			animation: shine 1.6s ease;
			animation: shine 2s forwards;
		}
		@keyframes  shine {
		  100% {
			transform: rotateZ(60deg) translate(1em, -9em);
		  }
		}


		button:after {
			content: '';
			position: absolute;
			top: -21%;
			right: -50%;
			bottom: -50%;
			left: -50%;
			background: linear-gradient(to bottom, rgba(229, 172, 142, 0), rgb(255 255 255 / 35%) 50%, rgba(229, 172, 142, 0));
			transform: rotateZ(60deg) translate(-5em, 7.5em);
		}
		button:hover {
			color: #fff !important;
			transition: 0.5s;
			
			background-color: rgba(16,185,129) !important;
		}
		</style>
		<!--  favicon -->
       <link rel="shortcut icon" href="<?php echo e(URL::to('/storage/'.$settings['fav_icon'])); ?>">
		
	   <?php echo e(do_action('apm_header_css','other')); ?>

	   <?php echo e(do_action('apm_header_enqueue','other')); ?>

	  
        
	
		<?php echo \Livewire\Livewire::scripts(); ?>

	</head>

	<body class="antialiased overflow-x-hidden">
		<?php echo e(do_action('apm_before_main_content')); ?>

		
		<?php echo $__env->make('cookie-consent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<?php echo $__env->make('layouts.headernew', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		
		<!-- do_action("apm_top_nav","other") -->

		<!-- categories section end --->
		<main class="w-full inline-block">
		<?php echo $__env->make('layouts.demo_register', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<!--content start-->

		<?php echo $__env->yieldContent('content'); ?>
		<?php echo e(do_action("apm_main")); ?>

		<?php echo e(do_action("apm_sidebar")); ?>

		<!--content end-->
		</main>

		<?php echo e(do_action('apm_after_main')); ?>

		<!-- Footer start --->
		<?php echo $__env->make('layouts.footernew', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

	</body>

</html><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/frontendother.blade.php ENDPATH**/ ?>