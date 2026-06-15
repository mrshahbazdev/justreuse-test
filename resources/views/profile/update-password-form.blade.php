<?php 

	$auth = auth()->user()->roles()->get()[0]->name; 
	
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
	$class_dir_m_rl = ($dir_rtl=="true")?'ml-4':'mr-4';
?>

<x-jet-form-section submit="updatePassword">

@if($auth == "User")

	<div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
		<x-slot name="title">
			{{ __('p_profile.update password') }}
		</x-slot>

		<x-slot name="description">
			{{ __('p_profile.update password info') }}
		</x-slot>
	</div>
	
    <x-slot name="form">
        <div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
            <x-jet-label for="current_password" value="{{ __('p_profile.current password') }}" />
            <x-jet-input id="current_password" type="password" class="appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block" wire:model.defer="state.current_password" autocomplete="current-password" />
            <x-jet-input-error for="current_password" class="mt-2" />
        </div>

        <div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
            <x-jet-label for="password" value="{{ __('p_profile.new password') }}" />
            <x-jet-input id="password" type="password" class="password-strength appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block" wire:model.defer="state.password" autocomplete="new-password" />
			
			<div class="w-full inline-block mt-4">
				<div class="meter bg-gray-300 inline-block align-middle w-8/12 sm:w-1/2 {{$class_dir_m_rl}}" id="meter">
					<div class="meter-bar" id="meter-bar"></div>
				</div>
				<div class="meter-text inline-block align-middle" id="meter-text">
					<span class="meter-status capitalize text-base font-semibold" id="meter-status"></span>
				</div>
			</div>
			
            <x-jet-input-error for="password" class="mt-2" />
        </div>

        <div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
            <x-jet-label for="password_confirmation" value="{{ __('p_profile.confirm password') }}" />
            <x-jet-input id="password_confirmation" type="password" class="appearance-none border-l-2 border-gray-400 bg-gray-100 rounded w-full py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
            <x-jet-input-error for="password_confirmation" class="mt-2" />
        </div>
    </x-slot>

    <x-slot name="actions">
        <div class="w-full sm:flex sm:justify-between ">
			
			
			<div class="text-center sm:w-auto w-full">
				<x-jet-action-message class="sm:ml-3 inline-block text-base text-green-500 uppercase font-semibold sm:float-right sm:mt-2 mb-2 sm:mb-0" on="saved">
					{{ __('p_profile.saved') }}
				</x-jet-action-message>
				
				<x-jet-button>
				{{ __('p_profile.save') }}
				</x-jet-button>
				
			</div>
			<div class="relative sm:w-auto w-full sm:mt-0 mt-3">
				<a class="inline-block sm:w-auto w-full" href="{{ URL::to('my-profile') }}">
					<button type="button" class="inline-block px-6 py-2 pb-3 text-white hover:text-gray-700 border-2 border-gray-700 hover:bg-white bg-gray-700 text-sm font-bold uppercase rounded tracking-widest active:bg-green-500 focus:outline-none disabled:opacity-25 transition ease-in-out duration-700 sm:w-auto w-full">
					{{__('p_profile.back')}}
					</button>
				</a>
			</div>
		</div>
    </x-slot>

@else
	<div class="col-span-12 sm:col-span-12">
		<x-slot name="title">
			Update Password
		</x-slot>

		<x-slot name="description">
		Ensure your account is using a long, random password to stay secure
		</x-slot>
	</div>
    <x-slot name="form">
			<div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
				<x-jet-label for="current_password" value="Current Password" />
				<x-jet-input id="current_password" type="password" class="rounded-md shadow-sm appearance-none border-l-2 border-gray-400 bg-gray-100 py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block w-full" wire:model.defer="state.current_password" autocomplete="current-password" />
				<x-jet-input-error for="current_password" class="mt-2" />
			</div>

			<div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
				<x-jet-label for="password" value="New Password" />
				<x-jet-input id="password" type="password" class="rounded-md shadow-sm appearance-none border-l-2 border-gray-400 bg-gray-100 py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block w-full" wire:model.defer="state.password" autocomplete="new-password" />
				<x-jet-input-error for="password" class="mt-2" />
			</div>

			<div class="mb-4 sm:mb-6 md:mb-8 float-left w-full">
				<x-jet-label for="password_confirmation" value="Confirm Password" />
				<x-jet-input id="password_confirmation" type="password" class="rounded-md shadow-sm appearance-none border-l-2 border-gray-400 bg-gray-100 py-4 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline block w-full" wire:model.defer="state.password_confirmation" autocomplete="new-password" />
				<x-jet-input-error for="password_confirmation" class="mt-2" />
			</div>
    </x-slot>

    <x-slot name="actions">
		<div class="w-full float-left">
			<div class="sm:float-left float-none text-center sm:w-auto w-full">
				<x-jet-action-message class="mr-3" on="saved">
				Saved
				</x-jet-action-message>

				<x-jet-button>
				Save
				</x-jet-button>
			</div>
			
			<div class="relative sm:float-right float-none sm:w-auto w-full sm:mt-0 mt-3">
				<a href="{{ URL::to('admin/my-profile') }}">
					<button type="button" class="inline-block px-6 py-2 pb-3 text-white hover:text-gray-700 border-2 border-gray-700 hover:bg-white bg-gray-700 text-sm font-bold rounded uppercase tracking-widest active:bg-gray-900 focus:outline-none focus:shadow-outline disabled:opacity-25 transition-all ease-in-out duration-700 sm:w-auto w-full">Back</button>
				</a>
			</div>
		</div>
    </x-slot>
    @endif

</x-jet-form-section>
