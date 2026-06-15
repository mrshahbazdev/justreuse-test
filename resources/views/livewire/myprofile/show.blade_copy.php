	<?php
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";

	$class_dir_text_lr = ($dir_rtl == "true") ? 'text-right' : 'text-left';
	$class_dir_space_r = ($dir_rtl == "true") ? 'md:space-x-reverse' : '';
	$class_dir_space_r_sm = ($dir_rtl == "true") ? 'sm:space-x-reverse' : '';
	$class_dir_space_r_lg = ($dir_rtl == "true") ? 'lg:space-x-reverse' : '';
	$class_dir_m_rl = ($dir_rtl == "true") ? 'ml-2' : 'mr-2';
	$class_dir_popup_btn = ($dir_rtl == "true") ? '' : 'sm:space-x-reverse';
	?>

	<div class="w-full float-left" {{$class_dir}}>
		<link rel="stylesheet" href="{{ URL::to('/css/intlTelInput.css') }}" />
		<script src="{{ URL::to('/js/intlTelInput.min.js') }}"></script>

		<?php
		$get_meta = App\Models\TblOtherpage::get_meta('edit-profile');
		$meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");

		?>

		@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
		@section('meta_title', $meta_title)
		@section('meta_keywords', $meta_keywords)
		@section('meta_description', $meta_description)
		@endif


		@if ($message = Session::get('message'))
		<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-{{Session::get('class')}}">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none close" onclick="closeAlert(event)"><span>×</span></button>
		</div>
		@endif

		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase">{{__('p_profile.update user profile')}}</h1>
			</div>
		</div>


		<!-- begin - delete account confirmation -->
		<div class="fixed z-50 inset-0 overflow-y-auto" id="confirm_popup" style="display:none">
			<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" {{$class_dir}}>
				<div class="fixed inset-0 transition-opacity" aria-hidden="true">
					<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
				</div>
				<!-- This element is to trick the browser into centering the modal contents. -->
				<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
				<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
					<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
						<div class="mb-6">
							<div class="{{$class_dir_text_lr}}">
								<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
									{{__('messages.delete_account_confirmation')}}
								</h3>
								<div class="py-2">
									<label class="block text-base text-black mb-2 sm:mb-4 leading-relaxed">
										{{__('messages.delete_account_popup_message')}}
									</label>


								</div>
							</div>
						</div>
						<div class="sm:flex sm:flex-row-reverse sm:space-x-3 {{$class_dir_popup_btn}}">
							<button type="button" id="close_popup" class="w-full inline-flex justify-center rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 mt-3 sm:w-auto sm:text-sm transition-all ease-linear duration-500">{{__('post_detail.cancel')}}</button>
							<button type="button" class="w-full inline-flex justify-center rounded-md border-2 border-transparent shadow-sm px-4 py-2 pb-3 bg-red-500 text-base font-semibold text-white hover:bg-white hover:text-red-500 hover:border-red-500 focus:outline-none mt-3 sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500" wire:click.prevent="deleteAccount()">{{__('messages.delete_account')}}</button>
						</div>
					</div>
				</div>
			</div>
		</div>
		<!-- end - delete account confirmation id="confirm-delete"-->


		<div class="w-full float-left sm:mt-6 mt-3">
			<div class="container mx-auto px-4">
				<div class="bg-white relative w-full float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20">
					<div class="w-full float-left">
						<form id="form">
							<div class="w-full float-left">
								<div class="md:flex md:items-center md:flex-wrap mb-4 md:mb-8 lg:mb-12 md:text-left text-center justify-left w-full md:w-auto md:space-x-12 {{$class_dir_space_r}}">
									<div class="bg-gray-100 p-4 rounded-3xl">
										<!--<div class="border-8 border-solid rounded-3xl w-64 h-52 flex items-center justify-center mx-auto">-->
										<div class="rounded-3xl flex items-center justify-center mx-auto w-60 h-60">
											<?php
											if (!empty($this->profile_photo_path)) {
												$imageUrl = URL::asset('storage/' . $this->profile_photo_path);
											} else {
												$imageUrl = URL::asset('storage/profile-avatar.jpg');
											}
											?>
											<!--<img src="{{$imageUrl}}" class="max-h-full mx-auto" alt="your profile" />-->
											<img src="{{$imageUrl}}" class="border-8 border-solid max-h-full h-60 w-60 object-cover object-center rounded-3xl" alt="your profile" />
										</div>
									</div>

									<div class="my-4 md:m-4 md:float-left">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.profile image')}} <span class="text-red-500">*</span></label>
										<div class="w-full bg-gray-100 border-green-800 border-l-2 px-6 pt-3 pb-3 my-2 rounded-lg choose-file float-left h-14 text-left max-w-full">
											<input type="file" accept="image/*" wire:model="new_profile_photo_path" class="text-base text-gray-700 italic max-w-full" />
										</div>
										@error('new_profile_photo_path') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
								</div>
							</div>

							<div class="w-full float-left">
								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full">
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.name')}}:</label>
										<input type="text" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('contact-us.name')}}" wire:model="name">
										@error('name') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.email')}}:</label>
										<input type="text" readonly="readonly" disabled wire:model="email" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.email')}}">
										@error('email') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
								</div>

								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full">
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.first name')}}:</label>
										<input type="text" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.enter first name')}}" wire:model="first_name">
										@error('first_name') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.last name')}}:</label>
										<input type="text" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.enter last name')}}" wire:model="last_name">
										@error('last_name') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
								</div>
								
								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full">
								@if(isset($info_location) && !empty($info_location) && isset($info_location[0])) 
								<div class="w-full sm:w-6/12 mb-4 sm:mb-00">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.address line')}} 1:</label>
										<input type="text" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-gray-400 bg-gray-100 focus:outline-none  placeholder-black" value="{{ isset($info_location[0]) && !empty($info_location[0]->name)  ? $info_location[0]->name : ''}}" id="searchTextField" name="text-city-sst"  wire:ignore>
									
									
                                       
										<input type="hidden" id="cName" name="city_name" size="50" value="{{$info_location[0]->locality }}" wire:ignore/>
										<input type="hidden" id="cityMain" name="main_city_name" size="50" value="{{ $info_location[0]->name }}" wire:ignore/>
										<input type="hidden" id="cityLat" name="city_lat" size="50" value="{{ $info_location[0]->latitude }}" wire:ignore/>
										<input type="hidden" id="cityLag" name="city_lag" size="50" value="{{ $info_location[0]->logitude }}" wire:ignore/>
										<input type="hidden" id="country_long" name="country_long" size="50" value="{{ $info_location[0]->country_long }}" wire:ignore/>
										<input type="hidden" id="country_short" name="country_short" size="50" value="{{ $info_location[0]->country_short }}" wire:ignore/>
										<input type="hidden" id="state_long" name="state_long" size="50" value="{{ $info_location[0]->state_long }}" wire:ignore/>
										<input type="hidden" id="state_short" name="state_short" size="50" value="{{ $info_location[0]->state_short }}" wire:ignore/>
										</div>
									@else
									<div class="w-full sm:w-6/12 mb-4 sm:mb-00">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.address line')}} 1:</label>
										<input type="text" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-gray-400 bg-gray-100 focus:outline-none  placeholder-black" value="" id="searchTextField" name="text-city-sst"  wire:ignore>

									<input type="hidden" id="cName" name="city_name" size="50"  wire:ignore/>
										<input type="hidden" id="cityMain" name="main_city_name" size="50"  wire:ignore/>
										<input type="hidden" id="cityLat" name="city_lat" size="50"  wire:ignore/>
										<input type="hidden" id="cityLag" name="city_lag" size="50"  wire:ignore/>
										<input type="hidden" id="country_long" name="country_long" size="50"  wire:ignore/>
										<input type="hidden" id="country_short" name="country_short" size="50"  wire:ignore/>
										<input type="hidden" id="state_long" name="state_long" size="50"  wire:ignore/>
										<input type="hidden" id="state_short" name="state_short" size="50"  wire:ignore/>
										</div>
									@endif

                                      
									

									<!-- <div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.address line')}} 1:</label>
										<input type="text" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.enter address')}}" wire:model="address_line1">
										@error('address_line1') <span class="text-red-500">{{ $message }}</span>@enderror
									</div> -->
									<!-- <div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.address line')}} 2:</label>
										<input type="text" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.enter address')}}" wire:model="address_line2">
										@error('address_line2') <span class="text-red-500">{{ $message }}</span>@enderror
									</div> -->
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.phone')}} :</label>
										<input type="number" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none"  wire:model="phone">
										@error('phone') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>
											
									
								</div>

								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full">
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.date of birth')}}:</label>
										<input type="date" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" wire:model="date_of_birth">
										@error('date_of_birth') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>

									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<?php
										$select_language = App\Models\TblLanguage::where('active', '1')->orderBy('default', 'desc')->get(); ?>
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">Language</label>
										<select class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black" wire:model="p_lang">
											<option value="">Select Prefered Language</option>
											@foreach($select_language as $row)
											<option value="{{$row->abbr}}">{{$row->native}}</option>
											@endforeach

										</select>
									</div>

								</div>
								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full items-center">
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<?php
										$currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
										$settings = App\Models\Setting::get_logos();
										$default_curr = $settings['default_currency']; ?>
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">Currency</label>
										<select class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black" wire:model="p_curr">
											<option value="">Select Prefered Currency</option>
											<?php
											foreach ($currency as $currency) {
											?>
												<option value="<?php echo $currency->id ?>" <?php echo ($default_curr == $currency->id) ? 'selected="selected"' : ""; ?>><?php echo $currency->short_code . " (" . $currency->currency_hex . ")" ?></option>
											<?php //$loop++;
											} ?>

										</select>
									</div>
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.gender')}}:</label>
										<ul class="flex flex-wrap content-start">
											@foreach($gendArr as $key=>$value)
											<label class="inline-flex items-center mr-6">
												<input wire:model="gender" type="radio" class="form-radio" value="{{$key}}" <?php echo ($this->gender == $key) ? "checked" : ""; ?>><span class="ml-2 mr-1">{{$value}}</span>
											</label>
											@endforeach
										</ul>
										@error('gender') <span class="text-red-500">{{ $message }}</span>@enderror
									</div>

								</div>




								<?php
								$result = App\Models\Setting::where('active', 1)->where('key', 'twilio_sms')->first();
								$enabled = 0;
								if (!empty($result)) {
									$value = json_decode($result->value, true);
									if ($value['enable_sms'] == 1) {
										$enabled = 1;
									}
								}
								?>
								@if($enabled == 1)
								<div class="mb-4 sm:mb-6 md:mb-8 lg:mb-10 w-full float-left hidden " wire:ignore>
									<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.phone')}}:
										@if($this->mobile_verified == 0)
										<span class="text-red-500"> <i class="fa fa-mobile" aria-hidden="true"></i> {{__('p_profile.not verified')}}!</span>
										@else
										<span class="text-green-500"> <i class="fa fa-mobile" aria-hidden="true"></i> {{__('p_profile.verified')}}!</span>
										@endif
									</label>
									<div class="relative w-full mb-3 sm:inline-flex">
										<input type="text" name="phone" value="<?php echo $this->phone; ?>" <?php echo !empty($this->phone) ? "readonly='true'" : ""; ?> id="update_phone" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-base shadow focus:outline-none focus:shadow-outline w-full mb-4 sm:mb-0" placeholder="{{__('p_profile.enter phone')}}">
										<input type="hidden" id="is-otp-sent" />
										@if(empty($this->phone))
										<input type="button" value="{{__('p_profile.send')}} OTP" class="sendotp bg-green-500 cursor-pointer text-white sm:ml-2 text-base md:text-lg font-semibold rounded pt-2 px-14 pb-3 focus:outline-none n ease-in-out duration-150 border-2 border-green-500 hover:bg-white hover:text-green-500 sm:mt-0 mt-4  sm:w-auto w-full" />
										@else
										<input style="display: none;" type="button" value="{{__('p_profile.send')}} OTP" class="sendotp bg-green-500 cursor-pointer text-white sm:ml-2 text-base md:text-lg font-semibold pt-2 px-14 pb-3 rounded focus:outline-none n ease-in-out duration-150 border-2 border-green-500 hover:bg-white hover:text-green-500 sm:mt-0 mt-4 sm:w-auto w-full" />
										<input type="button" value="{{__('p_profile.change')}}" class="profile_chg_phone bg-yellow-500 text-base md:text-lg font-semibold rounded outline-none focus:outline-none ease-linear transition-all duration-150 sm:ml-2 mt-4 sm:mt-0 pt-3 px-14 pb-3 text-white cursor-pointer hover:bg-white hover:text-black border-2 border-yellow-500 sm:w-auto w-full" />
										@endif
									</div>
									<div class="relative w-full mb-3 otp-visible">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4" for="grid-password">OTP</label>
										<input type="text" id="otp" name="otp" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="OTP">
									</div>
									<input type="button" class="resend-otp w-full text-left" value="{{__('p_profile.resend')}} OTP" />
									@if($this->mobile_verified == 0)
									<div class="inline-flex mt-4">
										<div class="relative inline-block w-10 {{$class_dir_m_rl}} align-middle select-none transition duration-200 ease-in">
											<input type="checkbox" disabled readonly wire:model="show_mobile" value="<?php echo $this->show_mobile; ?>" <?php echo $this->show_mobile == "1" ? "checked" : ""; ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" />
											<label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
										</div>
										{{__('p_profile.visible mobile number')}}
									</div>
									@else
									<div class="inline-flex mt-4">
										<div class="relative inline-block w-10 {{$class_dir_m_rl}} align-middle select-none transition duration-200 ease-in">
											<input type="checkbox" wire:model="show_mobile" value="<?php echo $this->show_mobile; ?>" <?php echo $this->show_mobile == "1" ? "checked" : ""; ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" />
											<label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
										</div>
										{{__('p_profile.visible mobile number')}}
									</div>
									@endif
								</div>
								@else
								<div class="mb-2 md:mb-4">
									<label class="block text-base text-black font-semibold mb-2 sm:mb-4">{{__('p_profile.phone')}}:</label>
									<input type="text" wire:model="phone" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="{{__('p_profile.enter phone')}}">
									@error('phone') <span class="text-red-500">{{ $message }}</span>@enderror
									<div class="inline-flex mt-4">
										<div class="relative inline-block w-10 {{$class_dir_m_rl}} align-middle select-none transition duration-200 ease-in">
											<input type="checkbox" wire:model="show_mobile" value="<?php echo $this->show_mobile; ?>" <?php echo $this->show_mobile == "1" ? "checked" : ""; ?> class="toggle-checkbox absolute block w-6 h-6 rounded-full bg-white border-4 appearance-none cursor-pointer switchttr" />
											<label for="toggle" class="toggle-label block overflow-hidden h-6 rounded-full bg-gray-300 cursor-pointer"></label>
										</div>
										{{__('p_profile.visible mobile number')}}
									</div>
								</div>
								@endif
								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:mb-6 md:mb-8 lg:mb-10 sm:float-none float-left w-full">
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">Stripe {{__('p_profile.private key')}}:</label>
										<input type="text" wire:model="stripe_private_key" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="Stripe {{__('p_profile.private key')}}">
									</div>
									<div class="w-full sm:w-6/12 mb-4 sm:mb-0">
										<label class="block text-base text-black font-semibold mb-2 sm:mb-4">Stripe {{__('p_profile.public key')}}:</label>
										<input type="text" wire:model="stripe_public_key" class="appearance-none border-l-2 border-green-800 bg-gray-100 rounded w-full py-4 px-3 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="Stripe {{__('p_profile.public key')}}" />
									</div>
								</div>

								<div class="sm:flex sm:space-x-2 {{$class_dir_space_r_sm}} lg:space-x-6 {{$class_dir_space_r_lg}} sm:float-none float-left w-full mt-2 sm:mt-0 sm:justify-between">

									<button wire:click.prevent="store()" type="button" class="saved-trigger bg-green-500 pt-2 px-14 pb-3 inline-block rounded-md text-white text-base md:text-lg font-normal outline-none focus:outline-none hover:text-green-500 border-2 border-green-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full">
										{{__('p_profile.update')}}
									</button>




									<a class="inline-block w-full sm:w-auto mt-4 sm:mt-0" href="{{ URL::to('change-password') }}">
										<button type="button" class="saved-trigger bg-green-500 pt-2 px-14 pb-3 inline-block rounded-md text-white text-base md:text-lg font-normal outline-none focus:outline-none hover:text-green-500 border-2 border-green-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full">
											{{__('p_profile.change password')}}
										</button>
									</a>

								</div>

								<div class="float-left w-full mt-4 md:mt-6">
									<div>
										<button type="button" class="delete-account inline-block rounded-md text-red-500 text-base md:text-lg font-normal outline-none focus:outline-none  transition ease-in-out duration-700 sm:w-auto w-full">
											{{__('messages.delete_account')}}
										</button>
									</div>
								</div>


							</div>
						</form>
						{!! config('app.google_map_script') !!}
						<script>
							// location with autocomplte
							jQuery(function($) {
								var options = {
									types: ['(regions)']
								};
								//  componentRestrictions: {country: "us"} //restric country if need
								//for location search map
								var input = document.getElementById('searchTextField');
								var autocomplete = new google.maps.places.Autocomplete(input);
								google.maps.event.addDomListener(input, 'keydown', function(event) {
									if (event.keyCode === 13) {
										event.preventDefault();
									}
								});
								google.maps.event.addListener(autocomplete, 'place_changed', function() {
									$('#cName').val('');
									$('#cityMain').val('');
									$('#cityLat').val('');
									$('#cityLag').val('');
									$('#country_long').val('');
									$('#country_short').val('');
									$('#state_long').val('');
									$('#state_short').val('');
									
							
									var place = autocomplete.getPlace();

									const country = place.address_components.find(item => item.types.includes('country'));
									const state = place.address_components.find(item => item.types.includes('administrative_area_level_1'));
									// Find city based on administrative_area_level_1
									const cityComponent = place.address_components.find(item => item.types.includes('locality') || item.types.includes('administrative_area_level_2') || item.types.includes('administrative_area_level_1'));

									const city = cityComponent ? cityComponent : '';
									console.log(city);
							
									@this.set('city_name', place.name);
								@this.set('city_lat', place.geometry.location.lat());
								@this.set('city_lag', place.geometry.location.lng());
								@this.set('main_city_name', city.long_name);
								@this.set('country_long', country.long_name);
								@this.set('country_short', country.short_name);
								@this.set('state_long', state.long_name);
								@this.set('state_short', state.short_name);

									$('#cName').val(place.name);
									$('#cityMain').val(city.long_name);
									$('#cityLat').val(place.geometry.location.lat());
									$('#cityLag').val(place.geometry.location.lng());
									$('#country_long').val(country.long_name);
									$('#country_short').val(country.short_name);
									$('#state_long').val(state.long_name);
									$('#state_short').val(state.short_name);
								});
								//for location search map
								//getting
								var getUserDataBtn = document.getElementById("post_json_insert");
								getUserDataBtn.addEventListener("click", () => {

									if ($("#text-title-sst").val() == "") {
										$("#text-title-sst").focus();
										return false;
									}
									if ($("#number-price-sst").val() == "") {
										$("#number-price-sst").focus();
										return false;
									}
									if ($("#textarea-desc-sst").val() == "") {
										$("#textarea-desc-sst").focus();
										return false;
									}
									if ($("#product-condition-sst").val() == "") {
										$("#product-condition-sst").focus();
										return false;
									}


								}, false);


							});
							// autocomplte end
							$(".delete-account").click(function(e) {
								$("#confirm_popup").show();
							});
							$("#close_popup").click(function(e) {
								$("#confirm_popup").hide();
							});
							$("#confirm-delete").click(function(e) {
								$("#confirm_popup").hide();
							});

							$(".switchttr").click(function(e) {
								var state = $(this).attr('value');
								var finalvalue = (state == "0") ? "1" : "0";
								$(this).attr('value', finalvalue);
								$(".saved-trigger").trigger("click");
							});

							function closeAlert(event) {
								let element = event.target;
								while (element.nodeName !== "BUTTON") {
									element = element.parentNode;
								}
								element.parentNode.parentNode.removeChild(element.parentNode);
								location.reload();
							}
							setTimeout(function() {
								jQuery('.close').trigger('click');
							}, 1000);


							// const phoneInputField = document.querySelector("#update_phone");
							// const phoneInput = window.intlTelInput(phoneInputField, {
							// 	initialCountry: "in",
							// 	separateDialCode: true,
							// 	geoIpLookup: getIp,
							// 	utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
							// });

							// function getIp(callback) {
							// 	fetch('https://ipinfo.io/json', {
							// 			headers: {
							// 				'Accept': 'application/json'
							// 			}
							// 		})
							// 		.then((resp) => resp.json())
							// 		.catch(() => {
							// 			return {
							// 				country: 'in',
							// 			};
							// 		})
							// 		.then((resp) => callback(resp.country));
							// }
							const phoneInputField = document.querySelector("#update_phone");

							// Assuming the stored phone number format is like +9197********
							const storedPhoneNumber = "<?php echo $this->phone; ?>";
							const storedCountryCode = storedPhoneNumber.substring(1, 4); // Extracting the country code

							const phoneInput = window.intlTelInput(phoneInputField, {
								initialCountry: storedCountryCode,
								separateDialCode: true,
								geoIpLookup: getIp,
								utilsScript: "https://cdnjs.cloudflare.com/ajax/libs/intl-tel-input/17.0.13/js/utils.js"
							});

							function getIp(callback) {
								fetch('https://ipinfo.io/json', {
										headers: {
											'Accept': 'application/json'
										}
									})
									.then((resp) => resp.json())
									.catch(() => {
										return {
											country: 'az',
										};
									})
									.then((resp) => {
										// Use the country code from the API response if available, otherwise use the stored country code
										const countryCode = resp.country || storedCountryCode;
										callback(countryCode);
									});
							}

							$(".otp-visible").hide();
							$(".resend-otp").hide();
							$(document).ready(function() {

								$(".profile_chg_phone").on('click', function(e) {
									$("#update_phone").val("");
									$("#update_phone").attr("readonly", false);
									$(".profile_chg_phone").hide();
									$(".sendotp").show();
								});

								$('#update_phone').on('keypress', function(e) {
									var $this = $(this);
									var regex = new RegExp("^[0-9\b]+$");
									var str = String.fromCharCode(!e.charCode ? e.which : e.charCode);
									// for 10 digit number only
									if ($this.val().length > 9) {
										e.preventDefault();
										return false;
									}
									if (e.charCode < 54 && e.charCode > 47) {
										if ($this.val().length == 0) {
											e.preventDefault();
											return false;
										} else {
											return true;
										}

									}
									if (regex.test(str)) {
										return true;
									}
									e.preventDefault();
									return false;
								});

							});


							$(document).ready(function() {
								$(".sendotp").click(function(e) {
									var phone = $("#update_phone").val();
									var is_otp_sent = $("#is-otp-sent").val();
									if (is_otp_sent == "") {
										if (phone != "") {
											const phoneNumber = phoneInput.getNumber();
											send_otp(phone, phoneNumber);
										} else {
											toastr.warning("Please enter Phone Number");
										}
									} else {
										var otp = $("#otp").val();
										if (otp == "") {
											toastr.warning("Please enter OTP");
										} else {
											const phoneNumber = phoneInput.getNumber();
											verify_otp(phoneNumber, otp);
										}
									}

								});

								$(document).ready(function() {
									$(".resend-otp").click(function(e) {
										var phone = $("#update_phone").val();
										if (phone != "") {
											const phoneNumber = phoneInput.getNumber();
											send_otp(phone, phoneNumber);
										} else {
											toastr.warning("Please enter Phone Number");
										}
									});
								});
								19031967


								function send_otp(phone, phoneNumber) {
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: "{{ URL::to('profile_send_otp') }}",
										data: {
											phone: phone,
											e164: phoneNumber
										},
										success: function(data) {
											if (data.result == "error") {
												toastr.warning(data.message);
											} else {
												$(".resend-otp").show(1000);
												$(".otp-visible").show(1000);
												$("#is-otp-sent").val(1);
												$(".sendotp").val("Verify OTP");
												toastr.success(data.message);
											}

										}
									});
								}

								function verify_otp(phone, otp) {
									$.ajax({
										type: 'POST',
										dataType: 'json',
										url: "{{ URL::to('profile_verify_otp') }}",
										data: {
											phone: phone,
											otp: otp
										},
										success: function(data) {
											if (data.result == "error") {
												toastr.warning(data.message);
											} else {
												toastr.success(data.message);
												window.location.href = data.return_url;
											}
										}
									});
								}
							});
						</script>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class=" flex items-center justify-center">



		<style>
			.toggle-checkbox:checked {
				right: 0;
				border-color: white;
			}

			.toggle-checkbox:checked+.toggle-label {
				background-color: #6875F5;
			}

			.iti.iti--allow-dropdown.iti--separate-dial-code {
				width: 100%;
			}

			input.resend-otp {
				background: transparent;
				cursor: pointer;
				font-size: 14px;
				margin-top: 10px;
				text-decoration: underline;
			}

			.iti__country-list {
				width: 304px;
			}

			.iti__selected-flag {
				border-left: 2px solid #929292e7;
				height: 50px;
				background: #e6e6e6 !important;
				border-radius: 5px;
			}

			.iti__country {
				padding: 3px 8px;
				font-size: 14px;
			}

			input#update_phone {
				margin-left: 2px;
				height: 50px;
			}
		</style>

	</div>