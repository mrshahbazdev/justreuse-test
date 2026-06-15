@if($updateMode)
	@include('livewire.admin.blacklist.edit')
@elseif($insertMode)
	@include('livewire.admin.blacklist.create')
@else
	@include('livewire.admin.blacklist.show')
@endif