	<?php
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		
	?>
	
	<div class="w-full float-left" <?php echo e($class_dir); ?>>
	<?php if (!empty($list)) { 
		
		$meta_title = (!empty($list->meta_title) ?$list->meta_title : "");
		$meta_keywords = (!empty($list->meta_key) ?$list->meta_key : "");
		$meta_description = (!empty($list->meta_description) ?$list->meta_description : "");


		
	?>

	<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
			<?php $__env->startSection('meta_title', $meta_title); ?>
			<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
			<?php $__env->startSection('meta_description', $meta_description); ?>
	<?php endif; ?>

	<div class="w-full float-left common_page_style">
		<div class="w-full float-left my-3 sm:mt-6 sm:mb-4">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black uppercase"><?php echo e($list->title); ?></h1>
			</div>
		</div>
		<div class="w-full float-left mb-3 sm:mb-6">
			<div class="container mx-auto px-4">
				<div class="text-gray-700 text-base md:text-lg font-normal"><?php echo $list->content; ?></div>
			</div>
		</div>
	</div>
	<?php } else { ?>
	<div class="text-center p-12">
		<div class="p-8">
			<div class="text-2xl pb-8"><p class="text-base font-bold"><?php echo e(__('messages.page not found')); ?>!</p></div>
		</div>
	</div>
	<?php } ?>
	</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/staticpages-list.blade.php ENDPATH**/ ?>