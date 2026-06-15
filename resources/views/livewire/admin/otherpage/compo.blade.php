@if($updateMode)
	@include('livewire.admin.otherpage.edit')
@elseif($insertMode)
	@include('livewire.admin.otherpage.create')
@else
	@include('livewire.admin.otherpage.show')
@endif