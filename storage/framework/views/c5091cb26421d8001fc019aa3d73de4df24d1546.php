<?php if (isset($component)) { $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\GuestLayout::class, []); ?>
<?php $component->withName('guest-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>

<?php 
	$get_meta = App\Models\TblOtherpage::get_meta('email-verify');
	$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

?>
    <?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
        <?php $__env->startSection('meta_title', $meta_title); ?>
        <?php $__env->startSection('meta_keywords', $meta_keywords); ?>
        <?php $__env->startSection('meta_description', $meta_description); ?>
	<?php endif; ?>

    <?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.authentication-card','data' => []]); ?>
<?php $component->withName('jet-authentication-card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
         <?php $__env->slot('logo'); ?> 
            <!--<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.authentication-card-logo','data' => []]); ?>
<?php $component->withName('jet-authentication-card-logo'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>-->
         <?php $__env->endSlot(); ?>

        <div class="mb-6 text-sm text-gray-600">
           <p class="text-sm text-white text-center font-medium"><?php echo e(__('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.')); ?></p>
        </div>

        <div class="bg-white w-full rounded-xl mb-2">
			<div class="py-6 sm:py-10 lg:py-16 mx-4 sm:mx-10">
			
				<?php if(session('status') == 'verification-link-sent'): ?>
					<div class="mb-4 sm:mb-6 font-semibold text-sm sm:text-base text-green-600 leading-5">
						<?php echo e(__('A new verification link has been sent to the email address you provided during registration.')); ?>

					</div>
				<?php endif; ?>
				
				<form method="POST" action="<?php echo e(route('verification.send')); ?>">
					<?php echo csrf_field(); ?>

					<div class="flex items-center justify-center mx-auto mb-4 sm:mb-7">
						<?php if (isset($component)) { $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4 = $component; } ?>
<?php $component = $__env->getContainer()->make(Illuminate\View\AnonymousComponent::class, ['view' => 'jetstream::components.button','data' => ['type' => 'submit']]); ?>
<?php $component->withName('jet-button'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes(['type' => 'submit']); ?>
							<?php echo e(__('Resend Verification Email')); ?>

						 <?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
					</div>
				</form>

				<form method="POST" action="<?php echo e(route('logout')); ?>">
					<?php echo csrf_field(); ?>
					<div class="text-center pt-4 border-t-2 border-gray-200">
						<button type="submit" class="text-base px-6 py-3 rounded-lg md:text-lg text-white bg-green-500 poppins-500 capitalize">
							<?php echo e(__('Logout')); ?>

						</button>
					</div>
				</form>
			</div>
        </div>
     <?php if (isset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4)): ?>
<?php $component = $__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4; ?>
<?php unset($__componentOriginalc254754b9d5db91d5165876f9d051922ca0066f4); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
 <?php if (isset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015)): ?>
<?php $component = $__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015; ?>
<?php unset($__componentOriginalc3251b308c33b100480ddc8862d4f9c79f6df015); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/auth/verify-email.blade.php ENDPATH**/ ?>