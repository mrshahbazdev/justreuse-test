	<?php 
		$get_meta = App\Models\TblOtherpage::get_meta('packages');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
	?>
	<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
        <?php $__env->startSection('meta_title', $meta_title); ?>
        <?php $__env->startSection('meta_keywords', $meta_keywords); ?>
        <?php $__env->startSection('meta_description', $meta_description); ?>
	<?php endif; ?>

	<div class="relative mt-4 mb-6 sm:mb-8 md:mb-12 lg:mb-16 w-full float-left" <?php echo e($class_dir); ?>>
		<div class="container m-auto px-4">
			<h1 class="text-center font-bold text-2xl lg:text-3xl xl:text-4xl p-3 text-gray-700"><?php echo e(__('p_packages.packages')); ?></h1>
			<p class="text-center text-base text-black mt-1"><?php echo e(__('p_packages.package quotes')); ?>.</p>
			<div class="max-w-6xl mx-auto mb-6 md:mb-10">
				<div class="flex flex-wrap flex-justify mt-2 mb-2">
					<?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="w-full sm:w-6/12 md:w-4/12 lg:w-3/12 md:mb-0 mb-2 px-4 py-2">
						<div class="rounded-tr-lg rounded-tl-lg mt-4 border-2 border-gray-300 shadow-xl">
							<div class="p-3 pb-4 rounded-md text-xl lg:text-2xl text-white font-bold text-center bg-green-500">
							<?php echo e($row->name); ?>

							</div>
							<div class="text-center p-4">
								<h1 class="text-xl text-black font-bold">

								<select class="text-center text-xl font-bold appearance-none" name="currency_id" disabled="">
								<?php
								$settings = App\Models\Setting::get_logos();
								$currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
								foreach ($currency as $currency) {
								?>
								<option value="<?php echo $currency->id ?>" <?php echo ($currency->id == $settings['default_currency']) ? "selected" : ""; ?>><?php echo $currency->currency_hex; ?></option>
								<?php } ?>
								</select><?php echo round($row->price); ?>

								<span class="text-gray font-sm"> / <?php echo e(__('p_packages.ad')); ?></span>
								</h1>
							</div>
							<div class="border-0 border-grey-light border-t border-solid text-sm">
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
								<p class="text-base font-normal"><?php echo e(__('p_packages.availability')); ?> <span class="font-bold"><?php echo e($row->duration); ?> <?php echo e(__('p_packages.days')); ?></span></p>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			</div>
		</div>
		
		<div class="container m-auto px-4">
			<h1 class="text-center font-bold text-2xl lg:text-3xl xl:text-4xl px-3 py-0 lg:py-3 text-gray-700"><?php echo e(__('p_packages.business package')); ?></h1>
			<div class="max-w-6xl mx-auto mb-6 md:mb-10">
				<div class="flex flex-wrap flex-justify mt-2 mb-2">
					<?php $__currentLoopData = $business_packs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="w-full sm:w-6/12 md:w-4/12 lg:w-3/12 md:mb-0 mb-2 px-4 py-2">
						<div class="rounded-tr-lg rounded-tl-lg mt-4 border-2 border-gray-300 shadow-xl">
							<div class="p-3 pb-4 rounded-md text-xl lg:text-2xl text-white font-bold text-center bg-green-500">
							<?php echo e($row->name); ?>

							</div>
							<div class="text-center p-4">
								<h1 class="text-xl text-black font-bold">

								<select class="text-center text-xl font-bold appearance-none" name="currency_id" disabled="">
									<?php
									$settings = App\Models\Setting::get_logos();
									$currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
									foreach ($currency as $currency) {
									?>
									<option value="<?php echo $currency->id ?>" <?php echo ($currency->id == $settings['default_currency']) ? "selected" : ""; ?>><?php echo $currency->currency_hex; ?></option>
									<?php } ?>
								</select><?php echo round($row->price); ?>

								</h1>
							</div>
							<div class="border-0 border-grey-light border-t border-solid text-sm">
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
								<p class="text-base font-normal"><?php echo e(__('p_packages.ad limit')); ?> <span class="font-bold"><?php echo e($row->bulk_limit); ?></span></p>
								</div>
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
								<p class="text-base font-normal"><?php echo e(__('p_packages.ad type')); ?> <span class="font-bold"><?php echo str_replace('_', ' ', strtoupper($row->ad_type)); ?></span></p>
								</div>
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
								<p class="text-base font-normal"><?php echo e(__('p_packages.availability')); ?> <span class="font-bold"><?php echo e($row->duration); ?> <?php echo e(__('p_packages.days')); ?></span></p>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			</div>
		</div>


		<div class="container m-auto px-4">
			<?php if (!empty($coupons[0])) { ?>
			<h1 class="text-center font-bold text-4xl p-3 mt-6 pb-0"><?php echo e(__('p_packages.coupons')); ?></h1>
			<div class="max-w-6xl mx-auto mb-10">
				<div class="flex flex-wrap flex-justify mt-2 mb-2">
					<?php $__currentLoopData = $coupons; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $coupon): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<div class="w-full sm:w-6/12 md:w-4/12 lg:w-3/12 md:mb-0 mb-2 px-4 py-2">
						<div class="rounded-tr-lg rounded-tl-lg mt-4 border-2 border-gray-300 shadow-xl">
							<div class="p-3 rounded-t-md rounded-l-md text-2xl border-0 font-bold text-center bg-yellow-500">
								<p class="text-base text-black"><?php echo e($coupon->coupon_title); ?></p>
							</div>
							<div class="text-center p-4">
								<h1 class="text-xl text-black font-bold">
								<span class="text-sm font-normal"><?php echo e(__('p_packages.coupon code')); ?> :</span> <?php echo e($coupon->coupon_code); ?>

								</h1>
							</div>
							<div class="border-0 border-grey-light border-t border-solid text-sm">
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
									<p class="text-base font-normal"><?php echo e(__('p_packages.offer')); ?> :
										<span class="font-bold">
										<?php if ($coupon->type == "fixed") { ?>
										<?php echo "$" . $coupon->value; ?>
										<?php } else { ?>
										<?php echo $coupon->value . " %"; ?>
										<?php } ?>
										</span>
									</p>
								</div>
								<div class="text-center border-0 border-grey-light border-b border-solid py-4">
									<p class="text-base font-normal"><?php echo e(__('p_packages.validity')); ?> : <span class="font-bold"><?php echo date("d M Y", strtotime($coupon->start_date)); ?> - <?php echo date("d M Y", strtotime($coupon->end_date)); ?></span></p>
								</div>
							</div>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
				</div>
			</div>
			<?php } ?>
		</div>
	</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/packages-list.blade.php ENDPATH**/ ?>