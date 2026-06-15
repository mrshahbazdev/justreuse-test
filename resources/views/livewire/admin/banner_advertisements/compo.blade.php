@if($bannerads_view_mode)
	@include('livewire.admin.banner_advertisements.view')
@else
	@include('livewire.admin.banner_advertisements.show')
@endif