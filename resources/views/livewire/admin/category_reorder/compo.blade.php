@if($updateMode)
	@include('livewire.category.edit')
@elseif($insertMode)
	@include('livewire.category.add')
@else
	@include('livewire.category.show')
@endif
