<x-guest-layout>
			
<?php 
    $get_meta = App\Models\TblOtherpage::get_meta('reset-password');
    $meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
    $meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
    $meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
  ?>

    @if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	  @endif

    <div class="mx-4 sm:mx-12 md:mx-20 xl:mx-28 py-6 sm:py-10 lg:py-16">

        <div class="w-full bg-white w-full rounded-xl mb-2">
			<div class="py-6 sm:py-10 lg:py-16 mx-4 sm:mx-10">
				<x-jet-validation-errors class="mb-4" />
				<form method="POST" action="{{ route('password.update') }}">
					@csrf

					<input type="hidden" name="token" value="{{ $request->route('token') }}">

					<div class="block mb-3 sm:mb-6">
						<x-jet-label for="email" value="{{ __('Email') }}" />
						<x-jet-input id="email" class="block mt-1 w-full appearance-none rounded border-0 border-l-2 bg-gray-100 border-gray-400 text-gray-700 px-3 py-3 md:px-6 rounded outline-none focus:outline-none" type="email" name="email" :value="old('email', $request->email)" required autofocus />
					</div>

					<div class="block mb-3 sm:mb-6">
						<x-jet-label for="password" value="{{ __('Password') }}" />
						<x-jet-input id="password" class="password-strength block mt-1 w-full appearance-none border-0 border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-3 md:px-6 placeholder-gray-700 text-gray-700 focus:outline-none" type="password" name="password" required autocomplete="new-password" />
						
						<div class="w-full inline-block mt-4">
							@if($dir_rtl =="false")
							<div class="meter bg-gray-300 inline-block align-middle w-8/12 sm:w-1/2 mr-4" id="meter">
								<div class="meter-bar" id="meter-bar"></div>
							</div>
							@else
							<div class="meter bg-gray-300 inline-block align-middle w-8/12 sm:w-1/2 ml-4" id="meter">
								<div class="meter-bar" id="meter-bar"></div>
							</div>	
							@endif
							<div class="meter-text inline-block align-middle" id="meter-text">
								<span class="meter-status capitalize text-base font-semibold" id="meter-status"></span>
							</div>
						</div>
						
					</div>

					<div class="block mb-3 sm:mb-6">
						<x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
						<x-jet-input id="password_confirmation" class="block mt-1 w-full appearance-none border-0 border-l-2 border-gray-400 bg-gray-100 rounded w-full py-3 px-3 md:px-6 placeholder-gray-700 text-gray-700 focus:outline-none" type="password" name="password_confirmation" required autocomplete="new-password" />
					</div>

					<div class="text-center inline-block w-full sm:mt-1">
						<x-jet-button>
							{{ __('Reset Password') }}
						</x-jet-button>
					</div>
				</form>
			</div>
        </div>
    </div>

</x-guest-layout>

<style>		
	.meter{
	height:10px;
	border-radius:3px;
	background: rgba(#111,.1);
	overflow: hidden; 
	}		
	
	.meter-bar{
	display: block;
	width:0;
	height:100%;
	transition:width .4s ease-in-out , transform .4s ease-in-out;
	}
</style>

<script>
		function checkStrength(password){
			let meterBar = $("#meter").find("#meter-bar");
			let meterStatus = $("#meter-text").find("#meter-status");
			let strength = 0;
			if(password.length < 6){
				meterBar.css({
					"background":"#6B778D",
					"width":"10%"
				});
				meterStatus.css("color","#6B778D");
				meterStatus.text("too short") ;
			}
			if(password.length > 7) strength += 1;
			if (password.match(/([a-z].*[A-Z])|([A-Z].*[a-z])/)) strength += 1
			if (password.match(/([a-zA-Z])/) && password.match(/([0-9])/)) strength += 1
			if (password.match(/([!,%,&,@,#,$,^,*,?,_,~])/)) strength += 1
			if(strength < 2){
				meterBar.css({
					"background":"#ef4444",
					"width":"25%"
				});
				meterStatus.css("color","#ef4444");
				meterStatus.text("weak");
			}else if(strength == 2){
				meterBar.css({
					"background":"#f59e0b",
					"width":"75%"
				});
				meterStatus.css("color","#f59e0b");
				meterStatus.text("good");
			}else{
				meterBar.css({
				"background":"#10b981",
				"width":"100%"
				});
				meterStatus.css("color","#10b981");
				meterStatus.text("strong");
			}
		}

		$(".password-strength").on("keyup",function(){
			checkStrength($(this).val());
		});

		</script>