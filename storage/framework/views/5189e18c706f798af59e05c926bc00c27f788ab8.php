<?php

	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl=="true")?'dir=rtl':"";

?>


<div class="w-full float-left" <?php echo e($class_dir); ?>>
    <?php if($message = Session::get('message')): ?>
    <?php
    echo "<script>toastr.success('$message');</script>";
    ?>
    <?php endif; ?>
<?php 
	$get_meta = App\Models\TblOtherpage::get_meta('contact-us');
	$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

?>	
	<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
        <?php $__env->startSection('meta_title', $meta_title); ?>
        <?php $__env->startSection('meta_keywords', $meta_keywords); ?>
        <?php $__env->startSection('meta_description', $meta_description); ?>
	<?php endif; ?>
	<!--<div class="w-full float-left bg-gray-100 h-52 mt-6">
		<div class="container mx-auto px-4">
			<h1 class="text-xl md:text-2xl font-bold text-black m-3 lg:m-6 lg:ml-0 uppercase pt-6 text-center"><?php echo e(__('contact-us.contact us')); ?></h1>
		</div>
	</div>-->
	
	<div class="w-full float-left mt-6">
		<div class="w-full float-left bg-gray-100 pt-5 pb-12 sm:pb-16 lg:pb-28 sm:pt-8 lg:pt-14">
			<h4 class="text-lg sm:text-xl lg:text-2xl font-bold text-black uppercase text-center mb-4"><?php echo e(__('contact-us.contact us')); ?></h4>
		</div>
	
	</div>
	
	
	<div class="w-full float-left -mt-10 sm:-mt-12 lg:-mt-16 xl:-mt-20">
		<div class="container mx-auto px-4">
			<div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative">
				<form wire:submit.prevent="store" id="form">
					<div class="md:flex">
						<div class="my-4 sm:m-4 md:w-6/12">
							<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.name')); ?>:</label>
							<input type="text" wire:model="name" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-3 md:px-6 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="<?php echo e(__('contact-us.name')); ?>">
							<?php $__errorArgs = ['name'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
						<div class="my-4 sm:m-4 md:w-6/12">
							<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.email')); ?>:</label>
							<input type="email" wire:model="email" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-3 md:px-6 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="<?php echo e(__('contact-us.email')); ?>">
							<?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					<div class="md:flex">
						<div class="my-4 sm:m-4 md:w-6/12">
							<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.ad link')); ?>:</label>
							<input type="text" wire:model="ad_link" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-3 md:px-6 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="<?php echo e(__('contact-us.ad link')); ?>">
							<?php $__errorArgs = ['ad_link'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
						<div class="my-4 sm:m-4 md:w-6/12">
							<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.phone')); ?>:</label>
							<input type="text" wire:model="phone" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-3 md:px-6 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none" placeholder="<?php echo e(__('contact-us.phone')); ?>">
							<?php $__errorArgs = ['phone'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					<div class="md:flex">
						<div class="my-4 sm:m-4 md:w-full">
							<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.description')); ?></label>
							<textarea wire:model="description" placeholder="<?php echo e(__('contact-us.description')); ?>" rows="8" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-3 md:px-6 placeholder-gray-700 text-gray-700 leading-tight focus:outline-none resize-none"></textarea>
							<?php $__errorArgs = ['description'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
						</div>
					</div>
					
					<div class="my-4 sm:m-4 w-auto">
						<label class="block text-base text-black font-semibold mb-2 sm:mb-4"><?php echo e(__('contact-us.attachment')); ?>:</label>
						<input type="file" wire:model="attachment" class="appearance-none border-l-2 border-green-800 bg-gray-100  w-full py-4 px-6 placeholder-gray-700 text-green-500 font-semibold focus:outline-none text-base italic text-left cursor-pointer ">
						<?php $__errorArgs = ['attachment'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> <span class="text-red-500"><?php echo e($message); ?></span><?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
					</div>
					
					<?php
					$recaptcha_sitekey = getenv("GOOGLE_RECAPTCHA_SITEKEY");
					$recaptcha_secret = getenv("GOOGLE_RECAPTCHA_SECRETKEY");
                                                   
                     ?>
					<div class="text-left pl-4" wire:ignore>
						<div class="g-recaptcha" data-tabindex=0 data-sitekey="<?php echo e($recaptcha_sitekey); ?>"></div>
					</div>
					
					<div class="flex justify-center mt-4 sm:mt-6 md:mt-12">
						<button id="submit" type="submit" class="focus:outline-none text-base text-white poppins-500 w-full sm:w-auto hover:text-green-500 bg-green-500 py-3 px-12 pb-3 rounded-md hover:bg-white border border-green-500 transition-all ease-linear duration-500">
							<?php echo e(__('contact-us.submit')); ?>

						</button>
					</div>
					
					<script src="https://www.google.com/recaptcha/api.js" async defer="defer"></script>
					<script>
						document.getElementById("submit").addEventListener("click", function() {
							var $recaptcha = document.querySelector('#g-recaptcha-response');
							if ($recaptcha) {
								$recaptcha.setAttribute("required", "required");
							}
						});
						$(function() {
							function rescaleCaptcha() {
								var width = $('.g-recaptcha').parent().width();
								var scale;
								if (width < 302) {
									scale = width / 302;
								} else {
									scale = 1.0;
								}

								$('.g-recaptcha').css('transform', 'scale(' + scale + ')');
								$('.g-recaptcha').css('-webkit-transform', 'scale(' + scale + ')');
								$('.g-recaptcha').css('transform-origin', '0 0');
								$('.g-recaptcha').css('-webkit-transform-origin', '0 0');
							}

							rescaleCaptcha();
							$(window).resize(function() {
								rescaleCaptcha();
							});

						});
					</script>
				</form>
			</div>
		</div>
	</div>
    <style>
        #g-recaptcha-response {
            display: block !important;
            position: absolute;
            margin: -78px 0 0 0 !important;
            width: 302px !important;
            height: 76px !important;
            z-index: -999999;
            opacity: 0;
        }
    </style>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/contact-us/show.blade.php ENDPATH**/ ?>