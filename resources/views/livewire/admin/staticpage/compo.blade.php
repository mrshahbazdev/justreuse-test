@if($updateMode)
	@include('livewire.admin.staticpage.edit')
@elseif($insertMode)
	@include('livewire.admin.staticpage.create')
@else
	@include('livewire.admin.staticpage.show')
@endif