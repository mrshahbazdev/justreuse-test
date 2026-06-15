@if($updateMode)
	@include('livewire.admin.advertising.edit')
@else
    @include('livewire.admin.advertising.show')
@endif