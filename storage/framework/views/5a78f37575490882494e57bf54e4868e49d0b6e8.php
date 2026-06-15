

<?php if($updateMode): ?>
	<?php echo $__env->make('livewire.admin.package.edit', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php elseif($insertMode): ?>
	<?php echo $__env->make('livewire.admin.package.create', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php else: ?>
	<?php echo $__env->make('livewire.admin.package.show', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php endif; ?>



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

</script><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/package/compo.blade.php ENDPATH**/ ?>