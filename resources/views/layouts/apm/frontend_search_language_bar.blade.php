<?php
// default language set from admin
$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
$locale = config('app.locale');

if (!Session::has('locale')) {
	if (auth()->user()) {
		$get_default = App\Models\User::find(auth()->user()->id);
		
		$locale = $get_default['preferred_language'];
		if ($locale != "") {
			App::setLocale($locale);
			$lang_url = URL::to('locale') . '/' . $locale;
			echo '<script>window.location.href="' . $lang_url . '";</script>';
		}
	} else {
		$get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
		$locale = $get_default['locale'];
		App::setLocale('en');
	}
}else{
	// if(config('app.locale')!="en"){
	// $get_default = App\Models\User::find(auth()->user()->id);
	// 	$locale = $get_default['locale'];
	// 	App::setLocale($get_default->preferred_language);
	// }
	if (Session::has('locale')==true){
		$getlocale=Session::get('locale');
		App::setLocale($getlocale);

	}else{
		if (auth()->user()) {
			$get_default = App\Models\User::find(auth()->user()->id);
			
			$locale = $get_default['preferred_language'];
		
			if ($locale != "") {
				App::setLocale($locale);
				// $lang_url = URL::to('locale') . '/' . $locale;
				// echo '<script>window.location.href="' . $lang_url . '";</script>';
			}
		} else {
			$get_default = App\Models\TblLanguage::where('active', '1')->where('default', '1')->first()->toArray();
			$locale = $get_default['locale'];
			App::setLocale('en');
		}
	}


}
// Top position tracking code
$advertising = App\Models\TblAdvertising::where('position', 'top')->where('active', '1')->first();
if (!empty($advertising)) {
	echo $advertising->tracking_code;
}
?>
<div class="relative h-full z-50" style="display:none" id="overlay">
	<div class="fixed z-50 top-0 left-0 right-0 bottom-0 flex items-center justify-center">
		<div class="text-center mt-auto mb-auto">
			<p class="text-white font-medium text-base sm:text-lg md:text-xl lg:text-2xl">Loading...</p>
		</div>
	</div>
	<div class="bg-gray-500 opacity-80 fixed top-0 left-0 right-0 bottom-0"></div>
</div>
<?php
//check for chat enabled
$currentURL = Request::getRequestUri();
$chat_seg =  request()->segment(1);
$chat_url = App\Models\Setting::check_active_chat_system($currentURL, $chat_seg);
//$chat_url = URL::to('/chatting');
$dir_rtl =  App\Models\Setting::is_dir_rtl();
$settings = App\Models\Setting::get_logos();
// get activated post method plugins // exchange method, buynow method
$post_methods = App\Models\TblPostMethod::get_active_post_methods();
?>
<!--Begin - EN Alignment -->
<?php if ($dir_rtl == "false") {  ?>
	<div class="bg-white shadow-md top-0 z-40 w-full flex flex-wrap items-center justify-between navbar-expand-lg">
		@if(auth()->user())
		<?php
		$userImg = auth()->user()->profile_photo_path;
		if (!empty($userImg)) {
			$userImgUrl = URL::asset('storage/' . $userImg);
		} else {
			$userImgUrl = URL::asset('storage/profile-avatar.webp');
		}
		?>
		@endif
		<div class="container px-4 mx-auto">
			<div class="w-full float-left lg:flex lg:items-center lg:justify-between pb-0.5 pt-2 lg:pb-4 lg:pt-4 lg:py-4 relative">
				<div class="w-full lg:w-1/6 relative float-left lg:pr-8 xl:pr-0 logo_section">
					<a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white w-36 sm:w-36 lg:w-full outline-none hover:outline-none block" href="{{ URL::to('/') }}">
						<img src="{{URL::to('storage/'.$settings['logo'])}}" alt="logo">
					</a>
					<div class="mobile_head_right float-right xl:hidden">
						<button class="cursor-pointer text-xl leading-none px-1 sm:px-3 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none float-right mt-2 sm:mt-2.5 user-icon-dropdown" type="button">
							@if(auth()->user())
							<a class="flex justify-center items-center text-sm -mt-0.5 sm:-mt-1" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-9 w-9 sm:h-8 sm:w-8" alt="your profile" /><span class="hidden lg:inline-block">{{ auth()->user()->name }} </span><i class="fa fa-angle-down ml-1 sm:ml-2"></i></a>
							@else
							<a class="relative -top-2 flex justify-center items-center text-2xl hamburger_icon" href="#pablo"><img src="{{URL::to('images/home/hamburger.png')}}" width="34" height="34" alt="menu"></a>
							@endif
						</button>
						<!-- language -->
						<?php
						$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
						?>
						<div class="inline-block relative align-middle mr-2 sm:mr-6 mt-2 align-middle float-right mb_lang">
							<a class="text-green-500 flex font-semibold lang-dropdown-mob text-base sm:text-xl" href="#pablo"><img class="mr-1 object-contain" src="{{URL::to('images/home/language.png')}}" width="25" height="25" alt="lang">{{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-1 sm:ml-2"></i></a>
							<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-mob-open max-h-60 overflow-y-auto" style="display:none;">
								@foreach($select_language as $row)
								<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
								<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block"> {{$row->native}}</a>
								@endforeach
							</div>
						</div>
						<!-- end language -->
						<div class="search-icon lg:hidden float-right mr-4 sm:mr-10 mt-2 cursor-pointer">
							<div class="text-green-500 font-semibold text-base sm:text-xl"><img src="{{URL::to('images/home/search_icon.png')}}" width="28" height="28" alt="menu" class="object-contain"></div>
						</div>
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
					?>
					@if(auth()->user())
					<a href="{{ route('post-add') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
					@else
					<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
					@endif
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
				<!-- Desktop Login-after Pages -->
				<div class="hidden lg:block w-5/12 xl:w-5/12 float-left">
					<ul class="w-full float-left text-right">
						@if(auth()->user())
						<?php
						/* Notifiations count and list */
						$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
						?>
						<li class="inline-block relative">
							<a class="bg-orange text-white text-md  poppins-600 rounded-full px-6 py-2 mt-1 inline-block test4" href="{{route('post-add')}}"> {{__('messages.post add')}}</a>
						</li>
						<li class="inline-block relative align-middle">
							<a class="px-3 flex justify-center items-center text-sm login-pages " href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full mr-1 h-12 w-12" alt="your profile" />
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
							<a href="{{ URL::to('/notifications') }}" class="text-2xl">
								<i class="fa fa-bell-o" aria-hidden="true"></i>
								<span class="flex h-4 w-4 absolute -top-1 -right-px">
									<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
									<span class="notification_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-1 pb-1.5"><?php echo count($notifications); ?></span>
								</span>
							</a>
						</li>
						<li class="inline-block relative align-middle mx-3 ">
							<a href="{{ $chat_url }}" class=" text-base md:text-3xl px-4 font-normal block w-full whitespace-nowrap text-green-500 mb-1"><i class="fa fa-commenting-o" aria-hidden="true"></i> <!--{{__('messages.My Chat')}}-->
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
							<a class="text-green-500 font-semibold lang-dropdown text-xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2"></i></a>
							<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-open max-h-60 overflow-y-auto" style="display:none">
								@foreach($select_language as $row)
								<?php $lang_url = URL::to('locale') . '/' . $row->locale ?>
								<a href="{{ $lang_url }}" class="text-sm py-2 px-4 font-normal whitespace-nowrap text-gray-800 hover:text-green-500 block"><i class="fa fa-language"></i> {{$row->native}}</a>
								@endforeach
							</div>
						</li>
						<!-- end language -->
						@else
						<li class="inline-block relative">
						<a class="bg-orange-600 text-white text-md poppins-600 rounded-full px-6 py-2 border-2 border-orange-800 test1" href="{{route('login')}}">
							{{__('messages.post add')}}</a>
						</li>
						<li class="inline-block px-6 pr-0 align-text-bottom">
							<span class="inline-block align-middle pr-2 hidden">
								<img src="{{ URL::to('/images/frontend/avatar-1.png') }}" alt="avatar-1.png">
							</span>
							<span class="hidden md:hidden lg:inline-block align-middle">
								<a href="{{route('login')}}" class="text-sm mx-1 font-bold border border-black text-black px-4 rounded-full py-1.5 poppins-600">{{__('messages.login')}}</a>
								<a href="{{route('register')}}" class="text-sm  mx-1 font-bold border border-black text-white px-4 rounded-full py-1.5 poppins-600 forest-bg-green"> {{__('messages.register')}}</a>
							</span>
						</li>
						<!-- language -->
						<?php
						$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
						?>
						<li class="inline-block relative align-middle hidden">
							<a class="text-green-500 font-semibold lang-dropdown text-xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down ml-2"></i></a>
							<div class="hidden bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-open max-h-60 overflow-y-auto" style="display:none">
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
	<div class="px-2 py-2 w-full bg-black continueapp lg:hidden" id="myDIV">
		<div class="notificationapp flex items-center justify-between">
			<p onclick="myFunction()"><svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 12 12" class="bg-gray-600" fill="currentColor" aria-hidden="true">
					<path d="M3.506 2.258a.882.882 0 0 0-1.248 1.248L4.753 6 2.258 8.494a.882.882 0 0 0 1.248 1.248L6 7.247l2.494 2.495a.882.882 0 0 0 1.248-1.248L7.247 6l2.495-2.494a.882.882 0 0 0-1.248-1.248L6 4.753z"></path>
				</svg></p>
			<div class="flex items-center gap-2 w-3/4">
				<span><img src="https://www.justreused.com/storage/continueapplogon.webp" style="height: 50px; width: 50px;" loading="lazy" alt="continue_applogo" class="object-contain"></span>
				<div class="">
					<h5 class="text-white text-sm font-bold">Continue in the app</h5>
					<p class="text-white text-xs">Get real-time notifications about listings</p>
				</div>
			</div>
			<?php
			$userAgent = $_SERVER['HTTP_USER_AGENT'];
			if (strpos($userAgent, 'iPhone') !== false || strpos($userAgent, 'iPad') !== false || strpos($userAgent, 'iPod') !== false) {
			?>
				<a href="https://apps.apple.com/us/app/justreused/id6499257286"><button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Open</button></a>
			<?php } elseif (strpos($userAgent, 'Android') !== false) {
			?>
				<a href="https://play.google.com/store/apps/details?id=com.justreused&hl=en"><button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Open</button></a>
			<?php
			} else { ?>
				<a href="https://play.google.com/store/apps/details?id=com.justreused&hl=en"><button type="button" class="text-white bg-gray-800 hover:bg-gray-900 focus:outline-none focus:ring-4 focus:ring-gray-300 font-medium rounded-full text-sm px-5 py-2.5 me-2 dark:bg-gray-800 dark:hover:bg-gray-700 dark:focus:ring-gray-700 dark:border-gray-700">Open</button></a>
			<?php
			}
			?>
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
			$userImgUrl = URL::asset('storage/profile-avatar.webp');
		}
		?>
		@endif
		<div class="container px-4 mx-auto">
			<div class="w-full float-left lg:flex lg:items-center lg:justify-between pt-3 md:pt-4 py-2 md:py-4 relative">
				<div class="w-full lg:w-1/6 relative float-left lg:pl-0 xl:pl-0">
					<a class="text-xl leading-relaxed inline-block whitespace-no-wrap text-white w-36 sm:w-36 lg:w-full outline-none hover:outline-none block mt-3" href="{{ URL::to('/') }}">
						<img src="{{URL::to('storage/'.$settings['logo'])}}" alt="logo">
					</a>
					<button class="cursor-pointer text-xl leading-none px-1 sm:px-3 border border-solid border-transparent rounded bg-transparent block lg:hidden outline-none focus:outline-none float-left mt-2 sm:mt-2.5 user-icon-dropdown" type="button">
						@if(auth()->user())
						<a class="flex justify-center items-center text-sm -mt-0.5 sm:-mt-1" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full ml-1 h-9 w-9 sm:h-8 sm:w-8" alt="your profile" /><span class="hidden lg:inline-block">{{ auth()->user()->name }} </span><i class="fa fa-angle-down mr-1 sm:mr-2"></i></a>
						@else
						<a class="flex justify-center items-center text-2xl" href="#pablo"><i class="text-green-500 fa fa-bars"></i></a>
						@endif
					</button>
					<!-- language -->
					<?php
					$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get();
					?>
					<div class=" inline-block relative align-middle ml-2 sm:ml-6 mt-2 align-middle float-left">
						<a class="text-green-500 font-semibold lang-dropdown-mob text-base sm:text-xl lg:hidden" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-1 sm:mr-2"></i></a>
						<div class="bg-white text-base z-50 float-left py-2 list-none text-left rounded shadow-lg min-w-48 absolute right-0 lang-dropdown-mob-open max-h-60 overflow-y-auto" style="display:none;">
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
					?>
					@if(auth()->user())
					<a href="{{ route('post-add') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> {{__('messages.post add')}}</a>
					@else
					<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> {{__('messages.post add')}}</a>
					@endif
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
					@if(auth()->user())
					<a href="{{ route('post-add') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
					@else
					<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"><i class="fa fa-plus-circle ml-2" aria-hidden="true"></i> {{__('messages.post add')}}</a>
					@endif
					<a href="{{ route('register') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-user-o ml-2" aria-hidden="true"></i> {{__('messages.register')}}</a>
					<a href="{{ route('login') }}" class="text-base py-2 px-4 font-normal block w-full whitespace-nowrap text-gray-800 hover:text-green-500"> <i class="fa fa-sign-in ml-2" aria-hidden="true"></i> {{__('messages.login')}}</a>
				</div>
				@endif
				<div class="hidden lg:block w-5/12 xl:5/12 float-left">
					<ul class="w-full float-left text-left">
						@if(auth()->user())
						<?php
						/* Notifiations count and list */
						$notifications = App\Models\TblNotifications::where('to_id', auth()->user()->id)->where('read_status', 0)->get();
						?>
						<li class="inline-block relative">
							<a class="bg-orange text-white text-md  poppins-600 rounded-full px-6 py-2 mt-1 inline-block test4" href="{{route('post-add')}}">{{__('messages.post add')}}</a>
						</li>
						<li class="inline-block relative align-middle">
							<a class="px-3 flex justify-center items-center text-sm login-pages lg:py-2" href="#pablo"><img src="{{$userImgUrl}}" class="bg-white rounded-full ml-1 h-12 w-12" alt="your profile" />
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
							<a href="{{ URL::to('/notifications') }}" class="text-2xl">
								<i class="fa fa-bell-o" aria-hidden="true"></i>
								<span class="flex h-4 w-4 absolute -top-1 -right-px">
									<span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-500 opacity-65"></span>
									<span class="notification_count rounded-full bg-green-500 flex items-center justify-center text-white text-sm font-semibold leading-tight px-1 py-1 pb-1.5"><?php echo count($notifications); ?></span>
								</span>
							</a>
						</li>
						<li class="inline-block relative align-middle mx-3">
							<a href="{{ $chat_url }}" class="text-base md:text-3xl px-4 font-normal block w-full whitespace-nowrap text-green-500 mb-1"><i class="fa fa-commenting-o" aria-hidden="true"></i> <!--{{__('messages.My Chat')}}-->
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
							<a class="text-green-500 font-semibold lang-dropdown text-xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-2"></i></a>
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
							<a class="px-4 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-xs font-bold uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block leading-normal test2" href="{{route('login')}}"><i class="fa fa-plus-circle ml-1"></i> {{__('messages.post add')}}</a>
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
							<a class="text-green-500 font-semibold lang-dropdown text-2xl" href="#pablo"><i class="fa fa-language"></i> {{ strtoupper(config('app.locale')) }}<i class="fa fa-angle-down mr-2"></i></a>
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
<script>
	function myFunction() {
		var x = document.getElementById("myDIV");
		x.style.display = "none";
	}
</script>