@if($insertMode)
	@include('livewire.admin.email-template.add')
@elseif($updateMode)
	@include('livewire.admin.email-template.edit')
@else
    @include('livewire.admin.email-template.show')
@endif