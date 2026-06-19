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
    <title>{{$settings['name']}}</title>
    <meta name="title" content="{{$settings['meta_title']}}">
    <meta name="description" content="{{$settings['meta_desc']}}">
    <!--meta data title and description end-->

    <!--  favicon -->
    <link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
    @include('partials.favicon')
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
    <script src="{{ URL::to('js/common.js') }}"></script>
    <script src="{{ URL::to('js/stimulus.umd.js') }}"></script>
    <script src="{{ URL::to('js/tailwind_popper.js') }}" defer></script>

	@include('livewire.common.seo')
    @include('livewire.common.location_default')
    @livewireScripts
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
			opacity:0;
		}
		button:hover:after {
			animation: shine 1.6s ease;
			animation: shine 2s forwards;
			opacity:1;
			    
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
			left: -50%;
			background: linear-gradient(to bottom, rgba(229, 172, 142, 0), rgb(255 255 255 / 35%) 50%, rgba(229, 172, 142, 0));
			transform: rotateZ(60deg) translate(-5em, 7.5em);
			opacity:0;
		}
		button:hover {
			color: #fff !important;
			transition: 0.5s;
			opacity:1;
			background-color: rgba(16,185,129) !important;
		}
		</style>
</head>

<body class="antialiased overflow-x-hidden">
    <?php
    // default language set from admin
    $select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
    $locale = config('app.locale');
    if (!Session::has('locale')) {
        $get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
        $locale = $get_default['locale'];
        App::setLocale($locale);
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
    <div class="bg-white shadow top-0 z-10 w-full flex flex-wrap items-center justify-between px-2 py-4 navbar-expand-lg">
<div class="fixed z-10 inset-0 overflow-y-auto inset-0 bg-gray-500 opacity-75" style="display:none;pointer-events:none;cursor:default;" id="overlay" >
<div style="border-top-color:transparent" class="w-32 h-32 border-8 border-green-500 border-dashed rounded-full animate-spin"></div>
<p>Loading...</p>
</div>
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
        <div class="container px-4 mx-auto flex flex-wrap items-center justify-between">
            <div class="w-full relative flex justify-between lg:w-auto lg:static lg:block lg:justify-start">
                <a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white w-48" href="{{ URL::to('/') }}">
                    <img src="{{URL::to('storage/'.$settings['logo'])}}">
                </a>
                <button class="cursor-pointer text-xl leading-none px-3 py-1 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none  focus:outline-none" type="button" onclick="toggleNavbar('example-collapse-navbar')">
                    @if(auth()->user())
                    <a class="px-3 py-4 lg:py-2 flex justify-center items-center text-sm" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-8 w-8" width="30" height="30" alt="your profile" />{{ auth()->user()->name }} <i class="fa fa-angle-down ml-2" fa-lg></i></a>
                    @else
                    <a class="flex justify-center items-center text-md mt-6" href="#pablo"><i class="text-green-500 fa fa-bars"></i></a>
                    @endif
                </button>
            </div>
            @if(auth()->user())
            <div id="example-collapse-navbar" class="hidden block bg-white lg:shadow-none absolute mt-20 lg:mt-0 z-50 top-0 lg:relative lg:rounded-none rounded-lg border-gray border-2 lg:border-0 inset-x-0 right-0">
                <a href="{{ URL::to('post') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-address-book-o" aria-hidden="true"></i> {{__('messages.my ads')}}</a>
                @foreach($post_methods as $post_method)
                @if($post_method->name == "exchange")
                <a href="{{ URL::to('/my-exchange/incoming') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-exchange" aria-hidden="true"></i> {{__('messages.my exchanges')}}</a>
                @endif
                @if($post_method->name == "bannerads")
                <a href="{{ URL::to('/my-banner-ads') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-address-book-o" aria-hidden="true"></i> {{__('messages.my banner ads')}}</a>
                @endif
                @if($post_method->name == "buynow")
                <a href="{{ URL::to('/my-buynow/orders') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-calendar-check-o" aria-hidden="true"></i> {{__('messages.my orders & sales')}}</a>
                @endif
                @endforeach
                <a href="{{ URL::to('favourite') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-heart-o" aria-hidden="true"></i> {{__('messages.favourite ads')}}</a>
                <a href="{{ URL::to('/my-profile') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"> <i class="fa fa-user-o" aria-hidden="true"></i> {{__('messages.my profile')}}</a>
                <a href="{{ URL::to('/my-followers') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-users" aria-hidden="true"></i> {{__('messages.my followers & followings')}}</a>
                <a href="{{ URL::to('/selectPackageMultiple') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> {{__('messages.buy business packs')}}</a>
                <a href="{{ URL::to('/mypackage') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-thumbs-o-up" aria-hidden="true"></i> {{__('messages.my business packs')}}</a>
                <a href="{{ URL::to('/chat') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-commenting-o" aria-hidden="true"></i> {{__('messages.My Chat')}}</a>
                <p>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <a href="{{ route('logout') }}" id="logout_trig" class="text-sm py-2 px-4 font-normal block w-full whitespace-no-wrap bg-transparent text-gray-800" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1" fa-lg></i> {{__('messages.logout')}}</a>
                </form>
                </p>
            </div>
            @else
            <div id="example-collapse-navbar" class="hidden bg-white lg:shadow-none absolute mt-20 lg:mt-0 z-50 top-0 lg:relative lg:rounded-none rounded-lg border-gray border-2 lg:border-0 inset-x-0">
                <a href="{{ route('login') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"><i class="fa fa-address-book-o" aria-hidden="true"></i> {{__('messages.post add')}}</a>
                <a href="{{ route('register') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"> <i class="fa fa-user-o" aria-hidden="true"></i> {{__('messages.register')}}</a>
                <a href="{{ route('login') }}" class="text-sm py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800"> <i class="fa fa-user-o" aria-hidden="true"></i> {{__('messages.login')}}</a>
            </div>
            @endif
            <div class="w-8/12 hidden md:block">
                <ul class="md:text-right w-full">
                    @if(auth()->user())
                    <?php
                    /* Notifiations count and list */
                    $notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
                    ?>
                    <li class="inline-block relative mr-4 -top-2">
                        <a class="px-10 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150" href="{{route('post-add')}}"><i class="fa fa-plus-circle"></i> {{__('messages.post add')}}</a>
                    </li>
                    <li class="inline-block relative md:w-48">
                        <a class="px-3 py-4 lg:py-2 flex justify-center items-center text-sm" href="#pablo" onclick="openDropdown(event, 'demo-pages-dropdown')"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-8 w-8" width="30" height="30" alt="your profile" />{{ auth()->user()->name }} <i class="fa fa-angle-down ml-2" fa-lg></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="demo-pages-dropdown">
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
                            <a href="{{ URL::to('/chat') }}" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500"><i class="fa fa-commenting-o mr-1" aria-hidden="true"></i> {{__('messages.My Chat')}}</a>
                            <p>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <a href="{{ route('logout') }}" id="logout_trig" class="text-sm py-2 px-4 block w-full whitespace-nowrap text-gray-800 hover:bg-gray-100 hover:text-green-500" onclick="event.preventDefault(); this.closest('form').submit();"><i class="fa fa-sign-out mr-1"></i> {{__('messages.logout')}}</a>
                            </form>
                            </p>
                        </div>
                    </li>
                    <!-- notification -->
                    <li class="inline-block relative m-auto">
                        <a href="{{ URL::to('/notifications') }}">
                            <img class="" src="{{ URL::to('images/frontend/Notification.png') }}" alt="Notification.png">
                            <span class="flex h-3 w-3 absolute -top-1 right-0">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500"><?php echo count($notifications); ?></span>
                            </span>
                        </a>
                    </li>
                    @else
                    <li class="inline-block relative">
                        <a class="px-10 py-3 mr-4 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150" href="{{route('login')}}">{{__('messages.post add')}}</a>
                    </li>
                    <li class="inline-block mr-4">
                        <span class="inline-block align-middle pr-2">
                            <img src="{{ URL::to('/images/frontend/avatar-1.png') }}" alt="avatar-1.png">
                        </span>
                        <span class="hidden md:hidden lg:inline-block align-middle">
                            <a href="{{route('login')}}" class="text-sm text-green-500 font-bold pt-4">{{__('messages.login')}}</a>
                            <a href="{{route('register')}}" class="text-sm text-green-500 font-bold pt-4">/ {{__('messages.register')}}</a>
                        </span>
                    </li>
                    <!-- language -->
                    <?php
                    $select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
                    ?>
                    <li class="inline-block relative top-1">
                        <a class="text-green-500 font-semibold" href="#pablo" onclick="openDropdown(event, 'demo-language-dropdown')"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2"></i></a>
                        <div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48" id="demo-language-dropdown">
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
        </div>
    </div>


    
    
	  	<!--begin search area-->	
      <!--banner image-->
      <div class="w-full float-left relative">
      <div class="inner_banner_image">
      <img class="w-full h-72 object-cover object-center" src="images/banner.jpg" alt="banner.jpg" />
      </div>
      <span id="blackOverlay" class="w-full h-full absolute z-0 top-0 bg-opacity-30 bg-black"></span>
      @include('layouts.search-bar')
      </div>
     	<!--end search area-->	



    <!-- categories section start --->
	<div class="w-full shadow-md mt-4 lg:mt-0 md:bg-gray-100  lg:shadow-sm float-left">


   
         <div class="container mx-auto px-4 lg:px-0">
            <div x-data="{ open: false }" class="block w-full-float-left">
				
              <div class=" bg-white  block p-4 flex flex-row md:hidden items-center justify-between">
				  <a class="text-lg font-semibold tracking-widest text-gray-900 uppercase rounded-lg dark-mode:text-white focus:outline-none focus:shadow-outline cursor-pointer" @click="open = !open">All Category</a>
				  <button class="md:hidden rounded-lg focus:outline-none focus:shadow-outline" @click="open = !open">

					<svg fill="currentColor" viewBox="0 0 20 20" class="w-6 h-6">
					  <path x-show="!open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
					  <path x-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
					</svg>
				  </button>
				</div>
				<div :class="{'flex p-5 border-t border-gray-200': open, 'hidden': !open}" class="shadow-xl bg-white md:shadow-none md:bg-gray-100 w-full lg:w-full hidden md:block  lg:mt-0 lg:text-left float-left">
					<div class="list-reset md:flex flex-1 items-center">
						@foreach($main_categories as $main_category)
							<a class="inline-block w-full text-base md:text-sm lg:text-lg xl:text-xl text-left md:text-center font-normal no-underline py-2 lg:px-4 xl:p-6 md:hover:bg-green-500 md:hover:text-white text-gray-700 no-underline" href="<?php echo Session::get('Searchedurl') . "&c=$main_category->slug"; ?>">{{ $main_category->title }}</a>
						@endforeach
					</div>
				</div>
			   </div>
            </div>
         </div>
	

    <!-- categories section end --->
    <main>
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
    <div class="w-full bg-gray-800 flex flex-col">
        <div class="container px-4 mx-auto px-4 lg:px-0 md:flex md:flex-row">
            <div class="py-2 pt-8 md:w-4/12">
                <div class="footer_logo text-center lg:text-left">
                    <img class="inline-block w-48 lg:w-min" src="{{URL::to('storage/'.$settings['logo'])}}">
                </div>

                <?php
                if (!empty($post_methods)) {
                    $check_banner_ads = $post_methods->pluck('name')->toArray();
                    if (in_array("bannerads", $check_banner_ads)) { ?>
                        <button class="text-white px-2 border focus:outline-none rounded py-1 bg-green-500 border-green-600 rounded-sm mt-8 md:ml-2 w-full md:w-4/6 hover:bg-white hover:text-green-500 hover:border-green-2">
                            <a href="{{ URL::to('/pages/banner-ads') }}">
                                {{__('messages.banner advertise')}}
                            </a>
                        </button>
                <?php }
                } ?>

                <ul class="w-full mt-8 text-center lg:text-left">
                    <li class="inline-block pr-6 lg:pr-10"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/fb.png') }}"></a></li>
                    <li class="inline-block pr-6 lg:pr-10"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/insta.png') }}"></a></li>
                    <li class="inline-block pr-6 lg:pr-10"><a href="#" class="text-base font-semibold text-white"></a><a href="#"><img src="{{ URL::to('images/frontend/twitter.png') }}"></a></li>
                </ul>
            </div>
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
            <p class="text-base py-4 text-white capitalize"> {{__('messages.copywrite')}} © <span id="get-current-year"></span></p>
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
    <!-- Favourate product process begin -->
    <script type="text/javascript">
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
    Session::forget('payment_nofy');
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