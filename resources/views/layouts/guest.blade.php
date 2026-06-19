<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <meta name="robots" content="noindex, nofollow" />
    <?php
    $settings = App\Models\Setting::get_logos();
    ?>
    <!--meta data title and description start-->
        <title>@yield('meta_title', $settings['meta_title'])</title>
        <meta name="keywords" content="@yield('meta_keywords', $settings['meta_keywords'])">
        <meta name="description" content="@yield('meta_description', $settings['meta_desc'])">
    <!--meta data title and description end-->

    <!-- favicon -->
    <link rel="shortcut icon" href="{{ URL::to('/storage/'.$settings['fav_icon']) }}">
    @include('partials.favicon')

    <!-- Fonts -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap">

    <!-- Styles -->
    <link rel="stylesheet" href="{{ URL::to('css/app.css') }}">
    <link rel="stylesheet" href="{{ URL::to('css/fontawesome.min.css') }}">
    <link rel="stylesheet" href="{{ URL::to('css/intlTelInput.css') }}" />
    <link rel="stylesheet" href="{{ URL::to('css/toastr.css') }}">
	 <link rel="stylesheet" href="{{ URL::to('css/custom.css') }}">

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/gh/alpinejs/alpine@v2.7.3/dist/alpine.js" defer></script>
    <script src="{{ URL::to('js/jquery.min.js') }}"></script>
    <script src="{{ URL::to('js/toastr.min.js') }}"></script>
    <script src="{{ URL::to('js/intlTelInput.min.js') }}"></script>
<style>
.forest-bg-green{
	background-color: #39763a;
}
.bg-green-500 {
    background-color: #39763a;
}
.border-green-500 {
    border-color: #39763a;
}

.bg-gray-500{
    background-color: #ecebeb;
}
.font-bold {
    font-family: 'Poppins-Medium';
    font-weight: 100;
}
.bg-orange{
	background-color: #f8991b;
}
.footer_logo {
    background-color: #fff;
}
</style>	
</head>

<body>
    <?php
		$settings = App\Models\Setting::get_logos();
		$slug = request()->segment(1);
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
    ?>
	<?php if($slug=='register' || $slug=='login') { ?>
    <div class="font-sans text-gray-900 antialiased">
        <div class="w-full mt-10 mb-10 lg:mt-28 lg:mb-20 " {{$class_dir}}>
            <div class="container px-4 mx-auto">
                <div class="block lg:flex  lg:flex-row-reverse lg:space-x-8 lg:space-x-reverse ...">
                    <div class="border-0 pb-6  my-4 bg-gray-100 w-full  mx-auto overflow-hidden  lg:w-6/12 items-center">
                        <div class="w-full">
                            <div class="bg-orange py-2"></div>
                            <div class="w-full inline-block text-center mt-8 mb-4">
                                <a href="{{ URL::to('/') }}"><img class="mx-auto w-48 object-contain" src="{{URL::to('storage/'.$settings['logo'])}}"></a>
                            </div>
                            {{ $slot }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
	<?php } else { ?>
	
	<!--Forgot Password and Login with OTP - Page-->
	
	<div class="font-sans text-gray-900 antialiased">
        <div class="w-full mt-8 mb-10 lg:mt-16 lg:mb-20 " {{$class_dir}}>
            <div class="container px-4 mx-auto">
                <div class="block lg:flex  lg:flex-row-reverse lg:space-x-8 lg:space-x-reverse ...">
					<div class="w-full lg:w-8/12 xl:w-7/12 mx-auto">
						<div class="text-center mb-16">
							<a class="inline-block outline-none focus:outline-none" href="{{ URL::to('/') }}"><img class="mx-auto w-48 object-contain" src="{{URL::to('storage/'.$settings['logo'])}}"></a>
						</div>
						<div class="border-0 forest-bg-green rounded-lg w-full  overflow-hidden items-center">
							<div class="w-full">
								{{ $slot }}
							</div>
						</div>
					</div>
                </div>
            </div>
        </div>
    </div>
	<?php } ?>
    <!-- Footer start --->
    <?php
		use Illuminate\Support\Facades\Session;
	    $post_methods = App\Models\TblPostMethod::get_active_post_methods();
		$get_country = session::get('GetCountry');

		// Initialize variables to avoid errors
		$poplocations = [];
		$pop_locations = [];

		if(!empty($get_country)){
			$country = App\Models\TblCountry::where('name', $get_country)->first();

			// Check if the country was found to prevent errors
			if ($country) {
				$states = App\Models\TblState::where('country_id', $country->id)->get();
				
				// *** FIX: Initialize $count as an empty array ***
				$count = [];

				foreach($states as $state){
					$getcity = App\Models\TblCity::where('state_id', $state->id)->first();
				
					if(!empty($getcity)){
						$post_exists = App\Models\TblPost::where('city', $getcity->id)->exists();
						if($post_exists){
							$post_count = App\Models\TblPost::where('city', $getcity->id)->count();
							$count[$getcity->name] = $post_count;
						}
					}
				}

				// Only sort if there are items in the array
				if (!empty($count)) {
					// arsort() sorts the array by reference
					arsort($count); 
					$poplocations = array_slice($count, 0, 5, true); // Use array_slice on the sorted $count array
				}
			}
		}
		
		// Fallback to default popular locations if the country-specific search yields no results
		if(empty($poplocations)) {
			$pop_locations = App\Models\TblPost::get_populor_loc();
		}

		if(!empty($get_country)){
			$trending_locations = App\Models\TblPost::get_trending_loc();
		}else{
			$trending_locations = App\Models\TblPost::get_trending_loc_default();
		}
?>
    <div class="w-full forest-bg-green flex flex-col" {{$class_dir}}>
			<div class="container px-4 mx-auto md:flex md:flex-row">

			
	<?php if($dir_rtl=="false"){ ?>
			<!--Begin EN Alignment-->
			<div class="py-2 pt-6 md:pt-10 md:w-4/12">
				<div class="footer_logo_section w-full lg:w-3/4">
					<div class="footer_logo text-center">
						<img class="inline-block " src="{{URL::to('storage/'.$settings['logo'])}}">
					</div>

					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<button class="text-white px-2  focus:outline-none rounded py-2 btn-gradiant  rounded-sm mt-8 w-full  ease-linear transition-all duration-500">
								<a href="{{ URL::to('/pages/banner-ads') }}">
									{{__('messages.banner advertise')}}
								</a>
							</button>
					<?php }
					} ?>

					<ul class="w-full mt-8 text-center md:text-left">
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.facebook.com/" target="_blank"><img src="{{ URL::to('images/frontend/fb.png') }}"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.instagram.com/" target="_blank"><img src="{{ URL::to('images/frontend/insta.png') }}"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.twitter.com/" target="_blank"><img src="{{ URL::to('images/frontend/twitter.png') }}"></a></li>
					</ul>
					</div>
				</div>
			<!--End EN Alignment-->
	<?php } else { ?>
			<!--Begin AR Alignment-->
			<div class="py-2 pt-6 md:pt-10 md:w-4/12">
					<div class="footer_logo_section w-full lg:w-3/4">
					<div class="footer_logo text-center ">
						<img class="inline-block " src="{{URL::to('storage/'.$settings['logo'])}}">
					</div>

					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<button class="text-white px-2  focus:outline-none rounded py-2 btn-gradiant  rounded-sm mt-8 w-full  ease-linear transition-all duration-500">
								<a href="{{ URL::to('/pages/banner-ads') }}">
									{{__('messages.banner advertise')}}
								</a>
							</button>
					<?php }
					} ?>

					<ul class="w-full mt-8 text-center md:text-right">
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.facebook.com/" target="_blank"><img src="{{ URL::to('images/frontend/fb.png') }}"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.instagram.com/" target="_blank"><img src="{{ URL::to('images/frontend/insta.png') }}"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" class="text-base font-semibold text-white"></a><a href="https://www.twitter.com/" target="_blank"><img src="{{ URL::to('images/frontend/twitter.png') }}"></a></li>
					</ul>
				</div>
				</div>
			<!--END AR Alignment-->
	<?php } ?>



				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
				    <p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 ">{{__('messages.popular locations')}}</p>
				    <ul>
				        @if(!empty($poplocations))
				            {{-- This loop is for when a country is selected and locations are found --}}
				            @foreach($poplocations as $key => $pop_location)
				                <li class="pb-1.5"><a href="#" class="text-base font-semibold text-white">{{ $key }}</a></li>
				            @endforeach
				        @elseif(!empty($pop_locations))
				             {{-- This loop is for the default popular locations --}}
				            @foreach($pop_locations as $pop_location)
				                <?php $popular_loc = URL::to('/' . str_replace(' ', '_', strtolower($pop_location)) . "?loc=" . $pop_location . "&country=&state=" . $pop_location . "&city="); ?>
				                <li class="pb-1.5"><a href="{{$popular_loc}}" class="text-base font-semibold text-white">{{$pop_location}}</a></li>
				            @endforeach
				        @endif
				    </ul>
				</div>
				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
					<p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 ">{{__('messages.trending locations')}}</p>
					@if(!empty($get_country))
					<ul>
						@foreach($trending_locations as $trending_location)
						<?php
						$trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($get_country)) . "?loc=" . $get_country . "&country=&state=" . $get_country . "&city=".$trending_location['city_name']."");
						?>
						<li class="pb-1.5"><a href="{{$trending_loc}}" class="text-base font-semibold text-white">{{$trending_location['city_name']}}</a></li>
						@endforeach
					</ul>
					@else
					<ul>
						@foreach($trending_locations as $trending_location)
						<?php
						$trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($get_country)) . "?loc=" . $get_country . "&country=&state=" . $get_country . "&city=".$trending_location."");
						?>
						<li class="pb-1.5"><a href="{{$trending_loc}}" class="text-base font-semibold text-white">{{$trending_location}}</a></li>
						@endforeach
					</ul>
					@endif
				</div>
				<div class="py-2 pt-6 md:pt-10 md:w-3/12">
					<p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 ">{{__('messages.about us')}}</p>
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
				<div class="container mx-auto border border-t-2 border-b-0 border-l-0 border-r-0 mt-4 lg:mt-8">
				<p class="text-base py-6 text-white capitalize"> {{__('messages.copywrite')}} © <span id="get-current-year"></span> <a href="#">Justreuesd</a></p>
				</div>
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
    <!-- Favourate product process begin -->
    <script type="text/javascript">
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    <!-- Favourate product process end -->
    <script>
        /* Make dynamic date appear */
        (function() {
            if (document.getElementById("get-current-year")) {
                document.getElementById(
                    "get-current-year"
                ).innerHTML = new Date().getFullYear();
            }
        })();
    </script>
    <!--for tab design, dropdown, tabs, popover-->
</body>

</html>