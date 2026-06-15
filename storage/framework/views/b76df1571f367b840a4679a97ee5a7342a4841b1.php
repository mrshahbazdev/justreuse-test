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
		<link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Poppins:wght@500;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
		 <link rel="stylesheet" href="<?php echo e(asset('css/app.css')); ?>">
		<style>
        /* === UPDATED COLORS === */
        :root {
            --color-primary: #f8991b;      /* Your Green */
            --color-primary-dark: #e68a07; /* Darker Green */
            --color-secondary: #39763a;    /* Your Orange */
            --color-secondary-dark: #2c5f2d;/* Darker Orange */
            --color-text-primary: #1f2937; /* Gray 800 */
            --color-text-secondary: #4b5563; /* Gray 600 */
            --color-surface: #fffbfa;
            --color-background: #fffbfa; /* Slate 50 - slightly cooler */
            --color-border: #e2e8f0; /* Slate 200 */
        }

        html { scroll-behavior: smooth; }
        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--color-background);
            color: var(--color-text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        h1, h2, h3, .font-poppins { font-family: 'Poppins', sans-serif; }

        .card {
            background-color: var(--color-surface);
            border-radius: 1rem;
            border: 1px solid var(--color-border);
            box-shadow: 0 1px 2px 0 rgb(0 0 0 / 0.05);
        }

        
        .btn-primary {
            background-color: var(--color-primary); color: white;
            box-shadow: 0 4px 14px rgba(57, 118, 58, 0.15);
        }
        .btn-primary:hover {
            background-color: var(--color-primary-dark); transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(57, 118, 58, 0.2);
        }
        .btn-secondary {
            background-color: var(--color-surface); color: var(--color-text-primary);
            border-color: var(--color-border);
        }
        .btn-secondary:hover { background-color: var(--color-background); border-color: #cbd5e1; }

        .thumbnail-container.active {
            border-color: var(--color-primary);
            box-shadow: 0 0 0 3px rgba(57, 118, 58, 0.3);
        }
        
        .skeleton-loader {
            background-color: #e2e8f0; /* slate-200 */
            animation: pulse 2s cubic-bezier(0.4, 0, 0.6, 1) infinite;
        }
        @keyframes  pulse {
            50% { opacity: .5; }
        }

        /* Tab Styles */
        .tab-btn {
            position: relative;
            padding-bottom: 1rem;
            font-weight: 600;
            color: var(--color-text-secondary);
            transition: color 0.3s ease;
        }
        .tab-btn:after {
            content: '';
            position: absolute;
            bottom: -1px;
            left: 0;
            right: 0;
            height: 3px;
            background-color: var(--color-primary);
            transform: scaleX(0);
            transition: transform 0.3s cubic-bezier(0.25, 0.46, 0.45, 0.94);
        }
        .tab-btn.active { color: var(--color-primary); }
        .tab-btn.active:after { transform: scaleX(1); }
        
        .tab-content { display: none; }
        .tab-content.active { display: block; animation: fadeIn 0.5s ease; }
        @keyframes  fadeIn {
            from { opacity: 0; transform: translateY(10px); } to { opacity: 1; transform: translateY(0); }
        }

        /* For the feature list toggle */
        .features-hidden { max-height: 0; overflow: hidden; transition: max-height 0.5s cubic-bezier(0.4, 0, 0.2, 1); }
        .features-visible { max-height: 1000px; /* Large enough for content */ transition: max-height 0.7s cubic-bezier(0.4, 0, 0.2, 1); }
    </style>
		<!--  favicon -->
       <link rel="shortcut icon" href="<?php echo e(URL::to('/storage/'.$settings['fav_icon'])); ?>">
		

	
		<?php echo \Livewire\Livewire::scripts(); ?>

     
	</head>

	<body class="pb-24 md:pb-0">
		
		<?php echo $__env->make('cookie-consent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>


		<?php echo $__env->make('layouts.headernew', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		<!--content start-->

		<?php echo $__env->yieldContent('content'); ?>
		<?php echo e(do_action("apm_main")); ?>

		<!--content end-->
	

		<?php echo e(do_action('apm_after_main')); ?>

		<!-- Footer start --->
		<?php echo $__env->make('layouts.footernewhome', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
		 <?php
if (! isset($_instance)) {
    $html = \Livewire\Livewire::mount('mini-chat')->html();
} elseif ($_instance->childHasBeenRendered('LgYHOyJ')) {
    $componentId = $_instance->getRenderedChildComponentId('LgYHOyJ');
    $componentTag = $_instance->getRenderedChildComponentTagName('LgYHOyJ');
    $html = \Livewire\Livewire::dummyMount($componentId, $componentTag);
    $_instance->preserveRenderedChild('LgYHOyJ');
} else {
    $response = \Livewire\Livewire::mount('mini-chat');
    $html = $response->html();
    $_instance->logRenderedChild('LgYHOyJ', $response->id(), \Livewire\Livewire::getRootElementTagName($html));
}
echo $html;
?>
	</body>

</html><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/post-detail-new.blade.php ENDPATH**/ ?>