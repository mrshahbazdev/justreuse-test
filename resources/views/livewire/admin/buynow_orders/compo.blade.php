@if($buynoworder_view_mode)
	@include('livewire.admin.buynow_orders.view')
@else
	@include('livewire.admin.buynow_orders.show')
@endif