
@if($insertMode)
	@include('livewire.admin.report_type.create')
@elseif($updateMode)
	@include('livewire.admin.report_type.edit')
@else
	@include('livewire.admin.report_type.show')
@endif