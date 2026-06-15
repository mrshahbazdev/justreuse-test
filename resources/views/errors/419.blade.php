@extends('layouts.frontendother')
@section('content')
	<div class="not-found text-center py-8 sm:py-12 md:py-16 lg:py-24 inline-block w-full">
		<div class="container mx-auto px-4">
			<div class="mb-4 md:mb-8">
				<img class="max-w-full w-20 sm:w-24 md:w-28 lg:w-32 rounded-full mx-auto" src="{{URL::to('images/page-not-found.png')}}" />
			</div>
			<h2 class="text-gray-400 text-2xl sm:text-3xl md:text-4xl lg:text-5xl font-semibold mb-2 md:mb-4">419</h2>
			<p class="text-base sm:text-lg lg:text-xl text-gray-400 font-medium mb-3 md:mb-4">Page Has Expired</p>
			<p class="max-w-md mx-auto text-xs sm:text-sm text-gray-400 font-medium md:leading-6">The Page you are looking for doesn't exists or an other error occurred. <a href="#">Go back,</a> or head over to <a href="{{URL::to('/')}}">{{env('APP_NAME')}}</a></p>
				
		</div>
	</div>
@endsection