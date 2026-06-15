@if($updateMode)
	@include('livewire.admin.home-banner.edit')
@elseif($insertMode)
	@include('livewire.admin.home-banner.create')
@else
	@include('livewire.admin.home-banner.show')
@endif