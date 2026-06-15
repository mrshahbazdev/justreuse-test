@if($updateMode)
	@include('livewire.admin.coupon.edit')
@elseif($insertMode)
	@include('livewire.admin.coupon.create')
@else
	@include('livewire.admin.coupon.show')
@endif