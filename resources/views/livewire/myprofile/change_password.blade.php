@extends('layouts.frontendother')
@section('content')

<?php 
		$get_meta = App\Models\TblOtherpage::get_meta('change-password');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
	?>
	
	@if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
        @section('meta_title', $meta_title)
        @section('meta_keywords', $meta_keywords)
        @section('meta_description', $meta_description)
	@endif


<!-- update password form -->
	<div class="w-full float-left" {{$class_dir}}>
		<div class="container mx-auto px-4">
			@if (Laravel\Fortify\Features::enabled(Laravel\Fortify\Features::updatePasswords()))
			<div class="w-full inline-block">
				@livewire('profile.update-password-form')
			</div>

			<x-jet-section-border />
			@endif
		</div>
	</div>

@endsection
