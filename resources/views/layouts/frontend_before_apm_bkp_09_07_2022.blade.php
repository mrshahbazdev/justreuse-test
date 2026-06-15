<!DOCTYPE html>
<html>

	<head>
		<meta name="csrf-token" content="{{ csrf_token() }}">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="robots" content="noindex, nofollow" />
		<!--whatsapp sharing -->
		@yield('whatsapp_meta')
		<?php
		$settings = App\Models\Setting::get_logos();
		//set session - last visited url
		App\Models\Setting::set_last_visited_url();
		?>
		<!--meta data title and description start-->
		<title>@yield('meta_title', $settings['meta_title'])</title>
		<meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'])">
		<meta name="description" content="@yield('meta_description', $settings['meta_desc'])">
		<!--meta data title and description end-->

		<!--  favicon -->
		<link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
		<link rel="stylesheet" href="{{ URL::to('css/slick.css') }}">

		<link rel="stylesheet" href="{{ URL::to('css/tailwind.min.css') }}">
		<link rel="stylesheet" href="{{ URL::to('css/fontawesome.min.css') }}">
		<link href="{{ URL::to('css/jquery-ui.css') }}" rel="stylesheet">
		<link href="{{ URL::to('css/toastr.css') }}" rel="stylesheet">
		<link rel="stylesheet" href="{{ URL::to('css/custom.css') }}">


		<script src="{{ URL::to('js/jquery.min.js') }}"></script>
		<script src="{{ URL::to('js/jquery-ui.js') }}"></script>
		<script src="{{ URL::to('js/tailwindcss-stimulus-components.umd.js') }}"></script>
		<script src="{{ URL::to('js/slick.min.js') }}"></script>
		
		<script src="{{ URL::to('js/alpine.min.js') }}" defer></script>
		<script src="{{ URL::to('js/toastr.min.js') }}"></script>

		<script src="{{ URL::to('js/stimulus.umd.js') }}"></script>
		<script src="{{ URL::to('js/tailwind_popper.js') }}" defer></script>
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital@1&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Roboto:ital,wght@1,700&display=swap" rel="stylesheet">
		<link rel="preconnect" href="https://fonts.googleapis.com">
		<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital@1&display=swap" rel="stylesheet">
		<link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@700&display=swap" rel="stylesheet">
		@include('livewire.common.seo')
		@include('livewire.common.location_default')
		@livewireScripts
	</head>

	<body class="antialiased overflow-x-hidden">
		<?php 
			$dir_rtl =  App\Models\Setting::is_dir_rtl();
			$class_dir = ($dir_rtl=="true")?'dir=rtl':""; 
			echo "<script>var dir_rtl = ".$dir_rtl."</script>";
		?>
	
		<?php 
			//check for chat enabled
			$currentURL = Request::getRequestUri();
			$chat_seg =  request()->segment(1);
			$chat_url = App\Models\Setting::check_active_chat_system($currentURL,$chat_seg);
			//$chat_url = URL::to('/chatting');
		?>
		
		@include('cookie-consent::index')
		
		<?php
			// default language set from admin
			$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
			$locale = config('app.locale');
			if (!Session::has('locale')) {

				if(auth()->user())
				{
					$get_default = App\Models\User::find(auth()->user()->id);
					$locale = $get_default['preferred_language'];
					if($locale!="")
					{
						App::setLocale($locale);
						$lang_url = URL::to('locale') . '/' . $locale;
						echo '<script>window.location.href="'.$lang_url.'";</script>';
					}
				}
				else{
					$get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
					$locale = $get_default['locale'];
					App::setLocale($locale);
				}
				   

				// $get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
				// $locale = $get_default['locale'];
				// App::setLocale($locale);
			}
			// Top position tracking code
			$advertising = App\Models\TblAdvertising::where('position', 'top')->where('active', '1')->first();
			if (!empty($advertising)) {
				echo $advertising->tracking_code;
			}
			// get activated post method plugins // exchange method, buynow method
			$post_methods = App\Models\TblPostMethod::get_active_post_methods();
			// get main categories
			$main_categories = App\Models\TblCategory::get_all_main_categories();
		?>
		
		<div class="relative h-full z-50" style="display:none" id="overlay">
			
		
			<div class="fixed z-50 top-0 left-0 right-0 bottom-0 flex items-center justify-center">
				<div class="text-center mt-auto mb-auto">
					<p class="text-white font-medium text-base sm:text-lg md:text-xl lg:text-2xl">Loading...</p>
				</div>
			</div>
			
			<div class="bg-gray-500 opacity-80 fixed top-0 left-0 right-0 bottom-0">
			
			</div>
		</div>
		

		<!--Begin - EN Alignment -->
		<?php if($dir_rtl =="false"){  ?>
		<div class="bg-white shadow-md top-0 z-40 w-full flex flex-wrap items-center justify-between navbar-expand-lg">
			
			@if(auth()->user())
			<?php
				$userImg = auth()->user()->profile_photo_path;
				if (!empty($userImg)) {
					$userImgUrl = URL::asset('storage/' . $userImg);
				} else {
					$userImgUrl = URL::asset('storage/profile-avatar.jpg');
				}
			?>
			@endif
			<div class="container px-4 mx-auto">
				<div class="w-full float-left lg:flex lg:items-center lg:justify-between py-4 relative">
					<div class="w-full lg:w-1/6 relative float-left lg:pr-8 xl:pr-12">
						<a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white w-24 sm:w-36 lg:w-full outline-none hover:outline-none block" href="{{ URL::to('/') }}">
							<img src="{{URL::to('storage/'.$settings['logo'])}}" alt="logo">
						</a>
						
						<button class="cursor-pointer text-xl leading-none px-1 sm:px-3 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none float-right mt-2 sm:mt-2.5 user-icon-dropdown" type="button">
							@if(auth()->user())
							<a class="flex justify-center items-center text-sm -mt-0.5 sm:-mt-1" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-7 w-7 sm:h-8 sm:w-8" alt="your profile" /><span class="hidden lg:inline-block">{{ auth()->user()->name }} </span><i class="fa fa-angle-down ml-1 sm:ml-2"></i></a>
							@else
							<a class="flex justify-center items-center text-2xl" href="#pablo"><i class="text-green-500 fa fa-bars"></i></a>
							@endif
						</button>
						
						<!-- language -->
						<?php
						$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
						?>
						<div class="lg:hidden inline-block relative align-middle mr-2 sm:mr-6 mt-2 align-middle float-right">
							<a class="text-green-500 font-semibold lang-dropdown-mob text-base sm:text-xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-1 sm:ml-2"></i></a>
							<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-mob-open" style="display:none;">
								@foreach($select_language as $row)
								<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
								<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block"><i class="fa fa-language"></i> {{$row->native}}</a>
								@endforeach
							</div>
						</div>
						<!-- end language -->
						
						<div class="search-icon lg:hidden float-right mr-4 sm:mr-10 mt-2 cursor-pointer">
							<div class="text-green-500 font-semibold text-base sm:text-xl"><i class="fa fa-search"></i></div>
						</div>
							
					</div>
						
					@include('layouts.header-search-bar')
					
					
					@if(auth()->user())
						
					<!-- Responsive Login Pages -->
					<div class="bg-white border-gray border-2 border-l-4 pl-2 pt-12 pb-4 user-icon-dropdown-open lg:hidden">
						<div class="user-icon-close text-2xl text-green-500 font-semibold absolute top-5 right-5 z-50 cursor-pointer">X</div>
						<?php
							/* Notifiations count and list */
							$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();

							// chat count
							$userid = auth()->user()->id;

							$chatlists = App\Models\TblChat::where('tbl_chats.from_id', $userid)
							->join('tbl_posts', function ($join){
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
						
							foreach($chatlists as $chatlist)
							{
								$visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);
						
								if(!empty($visible_posts)) {
						
									$sender = (($userid == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
									$unread_count = App\Models\TblChat::getUnreadCount($userid, $sender, $chatlist->post_id);
									$total_unread_count += $unread_count;
								}
							}
						?>
						
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
						
						<a href="{{ URL::to('post') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o mr-2" aria-hidden="true"></i> {{__('messages.my ads')}}</a>
						
						@foreach($post_methods as $post_method)
						@if($post_method->name == "exchange")
						<a href="{{ URL::to('/my-exchange/incoming') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-exchange mr-2" aria-hidden="true"></i> {{__('messages.my exchanges')}}</a>
						@endif
						
						@if($post_method->name == "bannerads")
						<a href="{{ URL::to('/my-banner-ads') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o mr-2" aria-hidden="true"></i> {{__('messages.my banner ads')}}</a>
						@endif
						
						@if($post_method->name == "buynow")
						<a href="{{ URL::to('/my-buynow/orders') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-calendar-check-o mr-2" aria-hidden="true"></i> {{__('messages.my orders & sales')}}</a>
						@endif
						@endforeach
						
						<a href="{{ URL::to('favourite') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-heart-o mr-2" aria-hidden="true"></i> {{__('messages.favourite ads')}}</a>
						
						<a href="{{ URL::to('/my-profile') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-user-o mr-2" aria-hidden="true"></i> {{__('messages.my profile')}}</a>
						
						<a href="{{ URL::to('/my-followers') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-users mr-2" aria-hidden="true"></i> {{__('messages.my followers & followings')}}</a>
						
						<a href="{{ URL::to('/selectPackageMultiple') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-2" aria-hidden="true"></i> {{__('messages.buy business packs')}}</a>
						
						<a href="{{ URL::to('/mypackage') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-2" aria-hidden="true"></i> {{__('messages.my business packs')}}</a>
						
						<a href="{{ $chat_url }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-commenting-o mr-2" aria-hidden="true"></i> {{__('messages.My Chat')}} <span class="mbl_chat_count mr-1 inline-block">(<?php echo $total_unread_count; ?>)</span></a>
						
						<a href="{{ route('notifications') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o mr-2" aria-hidden="true"></i> Notifications <span class="mbl_notification_count mr-1 inline-block">(<?php echo count($notifications); ?>)</span></a>
						
						<p>
							<form method="POST" action="{{ route('logout') }}">
								@csrf
								<a href="{{ route('logout') }}" id="logout_trig" class="text-base py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800 hover:text-green-500" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-2" fa-lg></i> {{__('messages.logout')}}</a>
							</form>
						</p>
					</div>
					<!-- Responsive Login Pages -->
					
					@else
					<div class="bg-white border-gray border-2 border-l-4 pl-2 pt-12 pb-4 user-icon-dropdown-open lg:hidden user-icon-dropdown-open">
						<div class="user-icon-close text-2xl text-green-500 font-semibold absolute top-5 right-5 z-50 cursor-pointer">X</div>
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle mr-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
						<a href="{{ route('register') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-user-o mr-2" aria-hidden="true"></i> {{__('messages.register')}}</a>
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-sign-in mr-2" aria-hidden="true"></i> {{__('messages.login')}}</a>
					</div>
					@endif
					
					<!-- Desktop Login Pages -->
					
					<div class="hidden lg:block w-5/12 xl:w-1/3 float-left">
						<ul class="w-full float-left text-right">
							@if(auth()->user())
							<?php
							/* Notifiations count and list */
							$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
							?>
							<li class="inline-block relative">
								<a class="px-4 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block leading-normal" href="{{route('post-add')}}"><i class="fa fa-plus-circle mr-1"></i> {{__('messages.post add')}}</a>
							</li>
							<li class="inline-block relative align-middle">
								<a class="px-5 flex justify-center items-center text-sm login-pages lg:py-2" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-8 w-8" alt="your profile" />
								<!--{{ auth()->user()->name }}--> 
								<i class="fa fa-angle-down ml-2" fa-lg></i></a>
								<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute -right-3 login-pages-open" style="display:none">
									<a href="{{ URL::to('post') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-address-book-o mr-1" aria-hidden="true"></i> {{__('messages.my ads')}}</a>
									@foreach($post_methods as $post_method)
									@if($post_method->name == "exchange")
									<a href="{{ URL::to('/my-exchange/incoming') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-exchange mr-1" aria-hidden="true"></i> {{__('messages.my exchanges')}}</a>
									@endif
									@if($post_method->name == "bannerads")
									<a href="{{ URL::to('/my-banner-ads') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-address-book-o mr-1" aria-hidden="true"></i> {{__('messages.my banner ads')}}</a>
									@endif
									@if($post_method->name == "buynow")
									<a href="{{ URL::to('/my-buynow/orders') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-calendar-check-o mr-1" aria-hidden="true"></i> {{__('messages.my orders & sales')}}</a>
									@endif
									@endforeach
									<a href="{{ URL::to('favourite') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-heart-o mr-1" aria-hidden="true"></i> {{__('messages.favourite ads')}}</a>
									<a href="{{ URL::to('/my-profile') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-user-o mr-1" aria-hidden="true"></i> {{__('messages.my profile')}}</a>
									<a href="{{ URL::to('/my-followers') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-users mr-1" aria-hidden="true"></i> {{__('messages.my followers & followings')}}</a>
									<a href="{{ URL::to('/selectPackageMultiple') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-1" aria-hidden="true"></i> {{__('messages.buy business packs')}}</a>
									<a href="{{ URL::to('/mypackage') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-1" aria-hidden="true"></i> {{__('messages.my business packs')}}</a>
									<!--<a href="{{ $chat_url }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-commenting-o mr-1" aria-hidden="true"></i> {{__('messages.My Chat')}}</a>-->
									
									<form method="POST" action="{{ route('logout') }}">
										@csrf
										<a href="{{ route('logout') }}" id="logout_trig" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1"></i> {{__('messages.logout')}}</a>
									</form>
									
								</div>
							</li>
							<!-- notification -->
							<li class="inline-block relative align-middle">
								<a href="{{ URL::to('/notifications') }}">
									<img class="" src="{{ URL::to('images/frontend/Notification.png') }}" alt="Notification.png">
									<span class="flex h-4 w-4 absolute -top-1 -right-px">
										<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
										<span class="notification_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-1 pb-1.5"><?php echo count($notifications); ?></span>
									</span>
								</a>
							</li>
							
							
							<li class="inline-block relative align-middle mx-3">
								<a href="{{ $chat_url }}" class="text-base md:text-2xl px-4 font-normal block w-full whitespace-nowrap text-green-500 mb-1"><i class="fa fa-commenting-o" aria-hidden="true"></i> <!--{{__('messages.My Chat')}}-->
									<span class="flex justify-center h-4 w-4 absolute top-0.5 right-2">
										<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
										<span class="chat_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-0.5 pb-1"><?php echo $total_unread_count; ?></span>
									</span>
								</a>
							</li>
							
							<!-- language -->
							<?php
							$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
							?>
							<li class="inline-block relative align-middle">
								<a class="text-green-500 font-semibold lang-dropdown" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2"></i></a>
								<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-open" style="display:none">
									@foreach($select_language as $row)
									<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
									<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block"><i class="fa fa-language"></i> {{$row->native}}</a>
									@endforeach
								</div>
							</li>
							<!-- end language -->
							@else
							<li class="inline-block relative">
								<a class="px-4 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block leading-normal" href="{{route('login')}}"><i class="fa fa-plus-circle mr-1"></i> {{__('messages.post add')}}</a>
							</li>
							<li class="inline-block px-6">
								<span class="inline-block align-middle pr-2">
									<img src="{{ URL::to('/images/frontend/avatar-1.png') }}" alt="avatar-1.png">
								</span>
								<span class="hidden md:hidden lg:inline-block align-middle">
									<a href="{{route('login')}}" class="text-sm text-green-500 font-bold">{{__('messages.login')}}</a>
									<a href="{{route('register')}}" class="text-sm text-green-500 font-bold">/ {{__('messages.register')}}</a>
								</span>
							</li>
							<!-- language -->
							<?php
							$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
							?>
							<li class="inline-block relative align-middle">
								<a class="text-green-500 font-semibold lang-dropdown" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2"></i></a>
								<div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-open" style="display:none">
									@foreach($select_language as $row)
									<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
									<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block"><i class="fa fa-language"></i> {{$row->native}}</a>
									@endforeach
								</div>
							</li>
							<!-- end language -->
							@endif
						</ul>
					</div>
					<!-- Desktop Login Pages -->
					
				</div>
			</div>
		</div>
		
		<!--End - EN Alignment -->
		
		<?php } else { ?>
		
		<!--Begin - AR Alignment -->
		
		<div class="bg-white shadow-md top-0 z-40 w-full flex flex-wrap items-center justify-between navbar-expand-lg" dir="rtl">
			
			@if(auth()->user())
			<?php
			$userImg = auth()->user()->profile_photo_path;
			if (!empty($userImg)) {
				$userImgUrl = URL::asset('storage/' . $userImg);
			} else {
				$userImgUrl = URL::asset('storage/profile-avatar.jpg');
			}
			?>
			@endif
			<div class="container px-4 mx-auto">
				<div class="w-full float-left lg:flex lg:items-center lg:justify-between py-4 relative">
					<div class="w-full lg:w-1/6 relative float-left lg:pl-8 xl:pl-12">
						<a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white w-24 sm:w-36 lg:w-full outline-none hover:outline-none block" href="{{ URL::to('/') }}">
							<img src="{{URL::to('storage/'.$settings['logo'])}}" alt="logo">
						</a>
						
						<button class="cursor-pointer text-xl leading-none px-1 sm:px-3 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none float-left mt-2 sm:mt-2.5 user-icon-dropdown" type="button">
							@if(auth()->user())
							<a class="flex justify-center items-center text-sm -mt-0.5 sm:-mt-1" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full ml-1 h-7 w-7 sm:h-8 sm:w-8" alt="your profile" /><span class="hidden lg:inline-block">{{ auth()->user()->name }} </span><i class="fa fa-angle-down mr-1 sm:mr-2"></i></a>
							@else
							<a class="flex justify-center items-center text-2xl" href="#pablo"><i class="text-green-500 fa fa-bars"></i></a>
							@endif
						</button>
						
						<!-- language -->
						<?php
						$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
						?>
						<div class="lg:hidden inline-block relative align-middle ml-2 sm:ml-6 mt-2 align-middle float-left">
							<a class="text-green-500 font-semibold lang-dropdown-mob text-base sm:text-xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-1 sm:mr-2"></i></a>
							<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-mob-open" style="display:none;">
								@foreach($select_language as $row)
								<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
								<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block text-right"><i class="fa fa-language"></i> {{$row->native}}</a>
								@endforeach
							</div>
						</div>
						<!-- end language -->
						
						<div class="search-icon lg:hidden float-left ml-4 sm:ml-10 mt-2 cursor-pointer">
							<div class="text-green-500 font-semibold text-base sm:text-xl"><i class="fa fa-search"></i></div>
						</div>
						
							
					</div>
						
					@include('layouts.header-search-bar')
					
					
					@if(auth()->user())
					<div class="bg-white border-gray border-2 border-r-4 pr-2 pt-12 pb-4 rtl user-icon-dropdown-open lg:hidden">
						<div class="user-icon-close text-2xl text-green-500 font-semibold absolute top-5 left-5 z-50 cursor-pointer">X</div>
						<?php
							/* Notifiations count and list */
							$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();

							// chat count
							$userid = auth()->user()->id;

							$chatlists = App\Models\TblChat::where('tbl_chats.from_id', $userid)
							->join('tbl_posts', function ($join){
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
						
							foreach($chatlists as $chatlist)
							{
								$visible_posts = App\Models\TblPost::check_payment_pack_expired($chatlist->post_id);
						
								if(!empty($visible_posts)) {
						
									$sender = (($userid == $chatlist->from_id) ? $chatlist->to_id : $chatlist->from_id);
									$unread_count = App\Models\TblChat::getUnreadCount($userid, $sender, $chatlist->post_id);
									$total_unread_count += $unread_count;
								}
							}

						?>
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
						<a href="{{ URL::to('post') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o ml-2" aria-hidden="true"></i> {{__('messages.my ads')}}</a>
						@foreach($post_methods as $post_method)
						@if($post_method->name == "exchange")
						<a href="{{ URL::to('/my-exchange/incoming') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-exchange ml-2" aria-hidden="true"></i> {{__('messages.my exchanges')}}</a>
						@endif
						@if($post_method->name == "bannerads")
						<a href="{{ URL::to('/my-banner-ads') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o ml-2" aria-hidden="true"></i> {{__('messages.my banner ads')}}</a>
						@endif
						@if($post_method->name == "buynow")
						<a href="{{ URL::to('/my-buynow/orders') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-calendar-check-o ml-2" aria-hidden="true"></i> {{__('messages.my orders & sales')}}</a>
						@endif
						@endforeach
						<a href="{{ URL::to('favourite') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-heart-o ml-2" aria-hidden="true"></i> {{__('messages.favourite ads')}}</a>
						<a href="{{ URL::to('/my-profile') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-user-o ml-2" aria-hidden="true"></i> {{__('messages.my profile')}}</a>
						<a href="{{ URL::to('/my-followers') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-users ml-2" aria-hidden="true"></i> {{__('messages.my followers & followings')}}</a>
						<a href="{{ URL::to('/selectPackageMultiple') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-thumbs-o-up ml-2" aria-hidden="true"></i> {{__('messages.buy business packs')}}</a>
						<a href="{{ URL::to('/mypackage') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-thumbs-o-up ml-2" aria-hidden="true"></i> {{__('messages.my business packs')}}</a>
						<a href="{{ $chat_url }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-commenting-o ml-2" aria-hidden="true"></i> {{__('messages.My Chat')}} <span class="mbl_chat_count ml-1 inline-block">(<?php echo $total_unread_count; ?>)</span></a>
						<a href="{{ route('notifications') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-address-book-o ml-2" aria-hidden="true"></i> Notifications <span class="mbl_notification_count ml-1 inline-block">(<?php echo count($notifications); ?>)</span></a>
						<p>
						<form method="POST" action="{{ route('logout') }}">
							@csrf
							<a href="{{ route('logout') }}" id="logout_trig" class="text-base py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800 hover:text-green-500" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out ml-2" fa-lg></i> {{__('messages.logout')}}</a>
						</form>
						</p>
					</div>
					@else
					<div class="bg-white border-gray border-2 border-r-4 pr-2 pt-12 pb-4 user-icon-dropdown-open lg:hidden rtl user-icon-dropdown-open">
						<div class="user-icon-close text-2xl text-green-500 font-semibold absolute top-5 left-5 z-50 cursor-pointer">X</div>
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
						<a href="{{ route('register') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-user-o ml-2" aria-hidden="true"></i> {{__('messages.register')}}</a>
						<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-sign-in ml-2" aria-hidden="true"></i> {{__('messages.login')}}</a>
					</div>
					@endif
					
					
					<div class="hidden lg:block w-5/12 xl:w-1/3 float-left">
						<ul class="w-full float-left text-left">
							@if(auth()->user())
							<?php
							/* Notifiations count and list */
							$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
							?>
							<li class="inline-block relative">
								<a class="px-4 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block leading-normal" href="{{route('post-add')}}"><i class="fa fa-plus-circle ml-1"></i> {{__('messages.post add')}}</a>
							</li>
							<li class="inline-block relative align-middle">
								<a class="px-5 flex justify-center items-center text-sm login-pages lg:py-2" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full ml-1 h-8 w-8" alt="your profile" />
								<!--{{ auth()->user()->name }}--> 
								<i class="fa fa-angle-down mr-2" fa-lg></i></a>
								<div class="bg-white text-base z-50 float-left py-2 list-none text-right rounded shadow-lg min-w-48 absolute left-3 login-pages-open" style="display:none">
									<a href="{{ URL::to('post') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-address-book-o mr-1" aria-hidden="true"></i> {{__('messages.my ads')}}</a>
									@foreach($post_methods as $post_method)
									@if($post_method->name == "exchange")
									<a href="{{ URL::to('/my-exchange/incoming') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-exchange mr-1" aria-hidden="true"></i> {{__('messages.my exchanges')}}</a>
									@endif
									@if($post_method->name == "bannerads")
									<a href="{{ URL::to('/my-banner-ads') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-address-book-o mr-1" aria-hidden="true"></i> {{__('messages.my banner ads')}}</a>
									@endif
									@if($post_method->name == "buynow")
									<a href="{{ URL::to('/my-buynow/orders') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-calendar-check-o mr-1" aria-hidden="true"></i> {{__('messages.my orders & sales')}}</a>
									@endif
									@endforeach
									<a href="{{ URL::to('favourite') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-heart-o mr-1" aria-hidden="true"></i> {{__('messages.favourite ads')}}</a>
									<a href="{{ URL::to('/my-profile') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-user-o mr-1" aria-hidden="true"></i> {{__('messages.my profile')}}</a>
									<a href="{{ URL::to('/my-followers') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-users mr-1" aria-hidden="true"></i> {{__('messages.my followers & followings')}}</a>
									<a href="{{ URL::to('/selectPackageMultiple') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-1" aria-hidden="true"></i> {{__('messages.buy business packs')}}</a>
									<a href="{{ URL::to('/mypackage') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-thumbs-o-up mr-1" aria-hidden="true"></i> {{__('messages.my business packs')}}</a>
									<!--<a href="{{ $chat_url }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-commenting-o mr-1" aria-hidden="true"></i> {{__('messages.My Chat')}}</a>-->
									<p>
									<form method="POST" action="{{ route('logout') }}">
										@csrf
										<a href="{{ route('logout') }}" id="logout_trig" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1"></i> {{__('messages.logout')}}</a>
									</form>
									</p>
								</div>
							</li>
							<!-- notification -->
							<li class="inline-block relative align-middle">
								<a href="{{ URL::to('/notifications') }}">
									<img class="" src="{{ URL::to('images/frontend/Notification.png') }}" alt="Notification.png">
									<span class="flex h-4 w-4 absolute -top-1 -right-px">
										<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
										<span class="notification_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-1 pb-1.5"><?php echo count($notifications); ?></span>
									</span>
								</a>
							</li>
							
							
							<li class="inline-block relative align-middle mx-3">
								<a href="{{ $chat_url }}" class="text-base md:text-2xl px-4 font-normal block w-full whitespace-nowrap text-green-500 mb-1"><i class="fa fa-commenting-o" aria-hidden="true"></i> <!--{{__('messages.My Chat')}}-->
									<span class="flex justify-center h-4 w-4 absolute top-0.5 right-2">
										<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
										<span class="chat_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-0.5 pb-1"><?php echo $total_unread_count; ?></span>
									</span>
								</a>
							</li>
							
							<!-- language -->
							<?php
							$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
							?>
							<li class="inline-block relative align-middle">
								<a class="text-green-500 font-semibold lang-dropdown" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-2"></i></a>
								<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute left-0 lang-dropdown-open" style="display:none">
									@foreach($select_language as $row)
									<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
									<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block text-right"><i class="fa fa-language"></i> {{$row->native}}</a>
									@endforeach
								</div>
							</li>
							<!-- end language -->
							@else
							<li class="inline-block relative">
								<a class="px-4 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block leading-normal" href="{{route('login')}}"><i class="fa fa-plus-circle ml-1"></i> {{__('messages.post add')}}</a>
							</li>
							<li class="inline-block px-6">
								<span class="inline-block align-middle pl-2">
									<img src="{{ URL::to('/images/frontend/avatar-1.png') }}" alt="avatar-1.png">
								</span>
								<span class="hidden md:hidden lg:inline-block align-middle">
									<a href="{{route('login')}}" class="text-sm text-green-500 font-bold">{{__('messages.login')}}</a>
									<a href="{{route('register')}}" class="text-sm text-green-500 font-bold">/ {{__('messages.register')}}</a>
								</span>
							</li>
							<!-- language -->
							<?php
							$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
							?>
							<li class="inline-block relative align-middle">
								<a class="text-green-500 font-semibold lang-dropdown" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-2"></i></a>
								<div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute left-0 lang-dropdown-open" style="display:none">
									@foreach($select_language as $row)
									<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
									<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block text-right"><i class="fa fa-language"></i> {{$row->native}}</a>
									@endforeach
								</div>
							</li>
							<!-- end language -->
							@endif
						</ul>
					</div>
				</div>
			</div>
		</div>
			<!--End - AR Alignment -->
			<?php } ?>
		
		
		<main class="w-full inline-block">
		@include('layouts.demo_register')
			<!--content start-->
			@yield('content')
			<!--content end-->
		</main>
		<!-- Footer start --->
		<?php
		$pop_locations = App\Models\TblPost::get_populor_loc();
		$trending_locations = App\Models\TblPost::get_trending_loc();
		?>
		<div class="w-full bg-gray-800 flex flex-col" {{$class_dir}}>
			<div class="container px-4 mx-auto md:flex md:flex-row">

			
	<?php if($dir_rtl=="false"){ ?>
			<!--Begin EN Alignment-->
			<div class="py-2 pt-8 md:w-4/12">
					<div class="footer_logo text-center md:text-left">
						<img class="inline-block w-48 lg:w-min" src="{{URL::to('storage/'.$settings['logo'])}}">
					</div>

					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<button class="text-white px-2 border focus:outline-none rounded py-1 bg-green-500 border-green-600 rounded-sm mt-8 w-full md:w-4/6 hover:bg-white hover:text-green-500 hover:border-green-2 ease-linear transition-all duration-500">
								<a href="{{ URL::to('/pages/banner-ads') }}">
									{{__('messages.banner advertise')}}
								</a>
							</button>
					<?php }
					} ?>

					<ul class="w-full mt-8 text-center md:text-left">
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/fb.png') }}"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/insta.png') }}"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/twitter.png') }}"></a></li>
					</ul>
				</div>
			<!--End EN Alignment-->
	<?php } else { ?>
			<!--Begin AR Alignment-->
			<div class="py-2 pt-8 md:w-4/12">
					<div class="footer_logo text-center md:text-right">
						<img class="inline-block w-48 lg:w-min" src="{{URL::to('storage/'.$settings['logo'])}}">
					</div>

					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<button class="text-white px-2 border focus:outline-none rounded py-1 bg-green-500 border-green-600 rounded-sm mt-8 w-full md:w-4/6 hover:bg-white hover:text-green-500 hover:border-green-2 ease-linear transition-all duration-500">
								<a href="{{ URL::to('/pages/banner-ads') }}">
									{{__('messages.banner advertise')}}
								</a>
							</button>
					<?php }
					} ?>

					<ul class="w-full mt-8 text-center md:text-right">
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/fb.png') }}"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/insta.png') }}"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/twitter.png') }}"></a></li>
					</ul>
				</div>
			<!--END AR Alignment-->
	<?php } ?>



				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
					<p class="text-xl font-semibold text-white pb-2.5 uppercase">{{__('messages.popular locations')}}</p>
					@if(!empty($pop_locations))
					<ul>
						@foreach($pop_locations as $pop_location)
						<?php
						$popular_loc = URL::to('/' . str_replace(' ', '_', strtolower($pop_location)) . "?loc=" . $pop_location . "&country=&state=" . $pop_location . "&city=");
						?>
						<li class="pb-1.5"><a href="{{$popular_loc}}" class="text-base font-semibold text-white">{{$pop_location}}</a></li>
						@endforeach
					</ul>
					@endif
				</div>
				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
					<p class="text-xl font-semibold text-white pb-2.5 uppercase">{{__('messages.trending locations')}}</p>
					@if(!empty($trending_locations))
					<ul>
						@foreach($trending_locations as $trending_location)
						<?php
						$trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($trending_location)) . "?loc=" . $trending_location . "&country=&state=" . $trending_location . "&city=");
						?>
						<li class="pb-1.5"><a href="{{$trending_loc}}" class="text-base font-semibold text-white">{{$trending_location}}</a></li>
						@endforeach
					</ul>
					@endif
				</div>
				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
					<p class="text-xl font-semibold text-white pb-2.5 uppercase">{{__('messages.about us')}}</p>
					<ul>
						<li class="pb-1.5"><a href="{{ URL::to('/contact-us') }}" class="text-base font-semibold text-white">{{__('messages.contact us')}}</a></li>
						<li class="pb-1.5"><a href="{{ URL::to('/pages/help') }}" class="text-base font-semibold text-white">{{__('messages.help')}}</a></li>
						<li class="pb-1.5"><a href="{{ URL::to('/packages') }}" class="text-base font-semibold text-white">{{__('messages.packages')}}</a></li>
						<li class="pb-1.5"><a href="{{ URL::to('/pages/terms-conditions') }}" class="text-base font-semibold text-white">{{__('messages.terms & conditions')}}</a></li>
						<li class="pb-1.5"><a href="{{ URL::to('/pages/privacy-policy') }}" class="text-base font-semibold text-white">{{__('messages.privacy policy')}}</a></li>
					</ul>
				</div>
			</div>
			<div class="text-center">
				<p class="text-base py-4 text-white capitalize"> {{__('messages.copywrite')}} © <span id="get-current-year"></span> <a href="https://www.appcodemonster.com/">Appcodemonster</a></p>
			</div>
		</div>
		<?php
		// Bottom position tracking code
		$advertising = App\Models\TblAdvertising::where('position', 'bottom')->where('active', '1')->first();
		if (!empty($advertising)) {
			echo $advertising->tracking_code;
		}
		?>
		<!-- Footer end -->
		<!-- fixed header -->
	
		<script>
			$(window).scroll(function() {
				var bodyheight = $(document).scrollTop() + 100;
				if (bodyheight >= 250) {
					$(".navbar-expand-lg").addClass("fixed");
				} else {
					$(".navbar-expand-lg").removeClass("fixed");
				}
			});
		</script>
		
			<?php 
			if (!empty(auth()->user())) {
				$auth_id = auth()->user()->id;
			}else{
				$auth_id = "";
			}
		?>
		
		<script type="text/javascript">
		
		// notification count update area
			setInterval(function(){ 
				
				var authId = "{{$auth_id}}";
				if(authId != "" || authId != false)
				{
					// console.log("no id");
					$.ajax({
						type: 'get',
						dataType: 'json',
						url: "{{ URL::to('get-notification-count') }}",
						success: function(data) {
							$(".notification_count").html(data.count);
							$(".mbl_notification_count").html("("+data.count+")");
							console.log("count: "+data.count);
						},
						error:function(data,error){
							$(".notification_count").html(0);
							$(".mbl_notification_count").html("(0)");
						}
					});


					$.ajax({
						type: 'get',
						dataType: 'json',
						url: "{{ URL::to('get-unread-chat-count') }}",
						success: function(data) {
							$(".chat_count").html(data.count);
							$(".mbl_chat_count").html("("+data.count+")");
							// console.log("chat-count: "+data.count);
						},
						error:function(data,error){
							$(".chat_count").html(0);
							$(".mbl_chat_count").html("(0)");
						}
					});

				}

			}, 15000);
	// notification count update area
			// Favourate product process begin
		
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$("body").on("click", ".save_favourate", function(e) {
				var val = $(this).attr("data-fav-post-id");
				$.ajax({
					type: 'POST',
					dataType: 'json',
					url: "{{ route('savepost') }}",
					data: {
						post_id: val
					},
					success: function(data) {
						if (data.result == "failed" && data.flag == "0") {
							toastr.warning(data.message);
						} else if (data.result == "success" && data.flag == "1") {
							$("#favourate_post_id_" + val + " i").removeClass("fa fa-heart-o");
							$("#favourate_post_id_" + val + " i").addClass("fa fa-heart");
							toastr.success(data.message);
						} else {
							$("#favourate_post_id_" + val + " i").removeClass("fa fa-heart");
							$("#favourate_post_id_" + val + " i").addClass("fa fa-heart-o");
							toastr.success(data.message);
						}

					}
				});
			});
		</script>
		<!-- Favourate product process end -->
		<script>
			// for taba modal alert slidover
			const application = Stimulus.Application.start();
			application.register('dropdown', TailwindcssStimulusComponents.Dropdown);
			application.register('modal', TailwindcssStimulusComponents.Modal);
			application.register('tabs', TailwindcssStimulusComponents.Tabs);
			application.register('popover', TailwindcssStimulusComponents.Popover);
			application.register('alert', TailwindcssStimulusComponents.Alert);
			application.register('slideover', TailwindcssStimulusComponents.Slideover);
		</script>
		@stack('modals')
		<script>
            @if (session('error'))
            toastr.error("{{ session('error') }}");
       		 @endif
			/* Make dynamic date appear */
			(function() {
				if (document.getElementById("get-current-year")) {
					document.getElementById(
						"get-current-year"
					).innerHTML = new Date().getFullYear();
				}
			})();
			/* Function for opning navbar on mobile */
			function toggleNavbar(collapseID) {
				document.getElementById(collapseID).classList.toggle("hidden");
				document.getElementById(collapseID).classList.toggle("block");
			}
			/* Function for dropdowns */
			function openDropdown(event, dropdownID) {
				let element = event.target;
				while (element.nodeName !== "A") {
					element = element.parentNode;
				}
				Popper.createPopper(element, document.getElementById(dropdownID), {
					placement: "bottom-start",
				});
				document.getElementById(dropdownID).classList.toggle("hidden");
				document.getElementById(dropdownID).classList.toggle("block");
			}
		</script>
		<!--for tab design, dropdown, tabs, popover-->
		<!-- begin - force logout if user if user blocked -->
		<?php
		$isBlocked = "0";
		if (!empty(auth()->user())) {
			$isBlocked = auth()->user()->is_blocked;
		}
		?>
		<script>
			(function() {
				var blocked = '{{$isBlocked}}';
				if (blocked == "1") {
					document.getElementById('logout_trig').click();
				}
			})();
		</script>
		<!-- end - force logout if user if user blocked -->
		<?php
		//from paypal success(/)failure
		$payment_nofy = Session('payment_nofy');
		//Session::forget('payment_nofy');
		?>
		<script type="text/javascript">
			var payment_nofy = "{{$payment_nofy}}";
			if (payment_nofy != "") {
				toastr.info(payment_nofy);
			}
		</script>
		<style>
			.pac-logo:after {
				display: none;
			}
		</style>
	</body>

</html>