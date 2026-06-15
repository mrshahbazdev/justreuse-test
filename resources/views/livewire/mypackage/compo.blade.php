@if ($message = Session::get('message'))
<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 z-50">
	<span class="text-xl inline-block mr-5 align-middle"><i class="fas fa-bell"></i></span>
	<span class="inline-block align-middle mr-8"><b class="capitalize"></b> {{ $message }}</span>
	<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
</div>
@endif

<script>
	function closeAlert(event) {
		let element = event.target;
		while (element.nodeName !== "BUTTON") {
			element = element.parentNode;
		}
		element.parentNode.parentNode.removeChild(element.parentNode);
	}
</script>

@if($updateMode)
@include('livewire.mypackage.edit')
@elseif($insertMode)
@include('livewire.mypackage.add')
@else
@include('livewire.mypackage.show')
@endif