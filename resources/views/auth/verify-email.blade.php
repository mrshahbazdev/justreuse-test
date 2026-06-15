<x-guest-layout>

<?php 
	$get_meta = App\Models\TblOtherpage::get_meta('email-verify');
	$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

?>
    @if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif

    <x-jet-authentication-card>
        <x-slot name="logo">
            <!--<x-jet-authentication-card-logo />-->
        </x-slot>

        <div class="mb-6 text-sm text-gray-600">
           <p class="text-sm text-white text-center font-medium">{{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}</p>
        </div>

        <div class="bg-white w-full rounded-xl mb-2">
			<div class="py-6 sm:py-10 lg:py-16 mx-4 sm:mx-10">
			
				@if (session('status') == 'verification-link-sent')
					<div class="mb-4 sm:mb-6 font-semibold text-sm sm:text-base text-green-600 leading-5">
						{{ __('A new verification link has been sent to the email address you provided during registration.') }}
					</div>
				@endif
				
				<form method="POST" action="{{ route('verification.send') }}">
					@csrf

					<div class="flex items-center justify-center mx-auto mb-4 sm:mb-7">
						<x-jet-button type="submit">
							{{ __('Resend Verification Email') }}
						</x-jet-button>
					</div>
				</form>

				<form method="POST" action="{{ route('logout') }}">
					@csrf
					<div class="text-center pt-4 border-t-2 border-gray-200">
						<button type="submit" class="text-base px-6 py-3 rounded-lg md:text-lg text-white bg-green-500 poppins-500 capitalize">
							{{ __('Logout') }}
						</button>
					</div>
				</form>
			</div>
        </div>
    </x-jet-authentication-card>
</x-guest-layout>
