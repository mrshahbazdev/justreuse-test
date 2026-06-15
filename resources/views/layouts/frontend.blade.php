<!DOCTYPE html>
<html lang="en">
	<head>
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		
		<!--whatsapp sharing -->
		@yield('whatsapp_meta')
		<?php
		$settings = App\Models\Setting::get_logos();
		//set session - last visited url
		App\Models\Setting::set_last_visited_url();
		// $slug = request()->segment(1);
		// dd($slug);
		
		?>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
		<!--meta data title and description start-->
		<title>@yield('meta_title', $settings['meta_title'])</title>
		<meta property="og:title" content="@yield('meta_title', $settings['meta_title'])">
		<meta property="og:type" content="website">
		<meta property="og:locale" content="en_US">
		<meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'])">
		<meta property="og:description" name="description" content="@yield('meta_description', $settings['meta_desc'])">
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
		@keyframes shine {
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
		<link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
		{{ do_action('apm_header_css') }}

		{{ do_action('apm_header_enqueue') }}

		@livewireScripts
	</head>

	<body class="antialiased overflow-x-hidden">
		{{ do_action('apm_before_main_content') }}

		@include('cookie-consent::index')

		{{ do_action('apm_header') }}
		{{ do_action("apm_top_nav") }}

		<main class="w-full inline-block">
		@include('layouts.demo_register')
		<!--content start-->
		@yield('content')
		{{ do_action("apm_main") }}
		{{ do_action("apm_sidebar") }}
		<!--content end-->
		</main>

		{{ do_action('apm_after_main') }}

		<!-- Footer start --->
		{{ do_action("apm_top_footer") }}
		{{ do_action("apm_footer") }}
		{{ do_action("apm_footer_bottom") }}
	</body>

</html>