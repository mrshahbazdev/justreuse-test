<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!--whatsapp sharing -->
		<?php echo $__env->yieldContent('whatsapp_meta'); ?>
		<?php
		$settings = App\Models\Setting::get_logos();
		//set session - last visited url
		App\Models\Setting::set_last_visited_url();
		// $slug = request()->segment(1);
		// dd($slug);
		
		?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!--meta data title and description start-->
		<title><?php echo $__env->yieldContent('meta_title', $settings['meta_title']); ?></title>
		<meta property="og:title" content="<?php echo $__env->yieldContent('meta_title', $settings['meta_title']); ?>">
		<meta property="og:type" content="website">
		<meta property="og:locale" content="en_US">
		<meta name="keywords" content="<?php echo $__env->yieldContent('meta_keywords', $settings['meta_keywords']); ?>">
		<meta property="og:description" name="description" content="<?php echo $__env->yieldContent('meta_description', $settings['meta_desc']); ?>">
		<!--meta data title and description end-->
		<style>
		button {
			overflow: hidden;
			position: relative;
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
			left: -100%;
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
		<?php echo e(do_action('apm_header_css')); ?>


		<?php echo e(do_action('apm_header_enqueue')); ?>


		<?php echo \Livewire\Livewire::scripts(); ?>

	</head>

	<body class="antialiased overflow-x-hidden">
		<?php echo e(do_action('apm_before_main_content')); ?>


		<?php echo $__env->make('cookie-consent::index', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

		<?php echo e(do_action('apm_header')); ?>

		<?php echo e(do_action("apm_top_nav")); ?>


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
		<?php echo e(do_action("apm_top_footer")); ?>

		<?php echo e(do_action("apm_footer")); ?>

		<?php echo e(do_action("apm_footer_bottom")); ?>

	</body>

</html><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/frontend.blade.php ENDPATH**/ ?>