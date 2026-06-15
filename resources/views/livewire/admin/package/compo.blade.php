

@if($updateMode)
	@include('livewire.admin.package.edit')
@elseif($insertMode)
	@include('livewire.admin.package.create')
@else
	@include('livewire.admin.package.show')
@endif



<script>

//     $("body").delegate("#bulk","click",function(){

// 		var bulkVal = ($('input[id="bulk"]:checked'))?'block':'none';
// 		$(".bulk-limit").css('display',bulkVal);


// // // alert('clicked');
// // // var bulkVal = $('input[id="bulk"]:checked').val();
// // // if(bulkVal == 1)
// // // {
// // // document.querySelector(".bulk-limit").style.display = "block";
// // // }
// // // else{
// // // 	document.querySelector(".bulk-limit").style.display = "none";
// // // }
// });

</script>