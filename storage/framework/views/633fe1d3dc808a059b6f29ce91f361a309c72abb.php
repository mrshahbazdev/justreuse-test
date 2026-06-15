<?php $__env->startSection('content'); ?>
<?php 
		// meta datas
		$get_meta = App\Models\TblOtherpage::get_meta('my-banner-ads');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
		// $meta_final_title = auth()->user()->name. " | ". $meta_title;

		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_float_r = ($dir_rtl=="true")?'':'float-right';
		$class_dir_mar_rl = ($dir_rtl=="true")?'ml-2':'mr-2';

?>

	<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
		<?php $__env->startSection('meta_title', $meta_title); ?>
		<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
		<?php $__env->startSection('meta_description', $meta_description); ?>
	<?php endif; ?>

<div class="w-full inline-block" <?php echo e($class_dir); ?>>
    
	<?php if($dir_rtl=="false"): ?>
	<?php if($message = Session::get('message')): ?>
	<div class="text-white py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-<?php echo e(Session::get('class')); ?> z-50">
		<div class="container px-4 xl:px-6 mx-auto relative">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fa fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> <?php echo e($message); ?></span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
    </div>
	<?php endif; ?>
	<?php else: ?>
	<?php if($message = Session::get('message')): ?>
	<div class="text-white py-4 border-0 rounded relative mb-4 bg-yellow-500 alert-<?php echo e(Session::get('class')); ?> z-50">
		<div class="container px-4 xl:px-6 mx-auto relative">
			<span class="text-xl inline-block ml-5 align-middle"><i class="fa fa-bell"></i></span>
			<span class="inline-block align-middle ml-8"><b class="capitalize"></b> <?php echo e($message); ?></span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none left-0 top-0 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
    </div>
	<?php endif; ?>
	<?php endif; ?>
	
	<div class="w-full inline-block">
		<div class="container mx-auto px-4">
			<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"><?php echo e(__('p_my_banner_ads.my banner ads')); ?></h1>
		</div>
	</div>
	
	<div class="w-full inline-block md:mt-6 mt-3">
		<div class="container mx-auto px-4">
			<div class="bg-white relative w-full inline-block mb-8 sm:mb-12 md:mb-16">
				<div class="w-full inline-block">
					<h4 class="font-bold uppercase float-left md:<?php echo e($class_dir_float_r); ?> mb-4 md:mb-8">
						<?php
						$post_methods = App\Models\TblPostMethod::get_active_post_methods();
						if (!empty($post_methods)) {
							$check_banner_ads = $post_methods->pluck('name')->toArray();
							if (in_array("bannerads", $check_banner_ads)) { ?>
								<a target="_blank" href="<?php echo URL::to('/banner-advertise'); ?>" class="float-left text-white hover:text-green-500 bg-green-500 rounded-md hover:bg-white border border-green-500 focus:outline-none ease-linear transition-all duration-150 text-sm lg:text-base font-semibold px-3 py-2 md:px-4 md:py-3"><i class="fa fa-plus-circle mr-1 sm:mr-2"></i> <?php echo e(__('p_my_banner_ads.banner advertisement')); ?></a>
						<?php }
						} ?>
					</h4>
				</div>
				<div class="overflow-auto align-middle w-full">
					<?php if(!empty($bannerads[0])): ?>
					<table class="w-full table-auto">
						<thead>
							<tr>
								<th class="sm:w-1/4"></th>
								<th class="sm:w-1/4"></th>
								<th class="sm:w-1/4"></th>
								<th class="sm:w-2/6"></th>
								<th class="sm:w-1/12"></th>
							</tr>
						</thead>
						<tbody class="bg-white">
							<?php $__currentLoopData = $bannerads; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $bannerad): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
							<?php $check_expired = App\Models\TblBannerAdvertisement::check_is_expired($bannerad->id);
							$currency_symbol = App\Models\TblDefaultCurrency::where('id', $bannerad->currency_id)->pluck('currency_hex')->first();
							$bg_class = "";
							if ($check_expired == 1) {
								$bg_class = "bg-gray-300";
							}
							?>
							<tr>
								<td class="px-2 py-3 align-top border border-gray-300 md:border-0">
									<div class="w-24 sm:w-auto">
										<!--<img src="<?php //echo URL::to('storage/' . $bannerad->web_banner); ?>" class="w-20 h-10 md:h-28 sm:w-full sm:max-w-xs lg:h-36 xl:h-44 object-cover mx-auto" />-->
										<img src="<?php echo URL::to('storage/' . $bannerad->web_banner); ?>" class="w-24 h-16 sm:w-full md:h-20 lg:h-28 xl:h-44 object-cover object-center" />
										<a target="_blank" href="<?php echo $bannerad->web_link; ?>" class="text-blue-500 mt-4 text-sm sm:font-medium"><?php echo e(__('p_my_banner_ads.web link')); ?></a>
									</div>
								</td>
								<td class="px-2 py-3 align-top border border-gray-300 md:border-0">
									<div class="w-24 sm:w-auto">
										<!--<img src="<?php //echo URL::to('storage/' . $bannerad->app_banner); ?>" class="w-20 h-10 md:h-28 sm:w-full sm:max-w-xs lg:h-36 xl:h-44 object-cover mx-auto" />-->
										<img src="<?php echo URL::to('storage/' . $bannerad->app_banner); ?>" class="w-24 h-16 sm:w-full md:h-20 lg:h-28 xl:h-44 object-cover object-center" />
										<a target="_blank" href="<?php echo $bannerad->app_link; ?>" class="text-blue-500 mt-4 text-sm sm:font-medium"><?php echo e(__('p_my_banner_ads.app link')); ?></a>
									</div>
								</td>
								<td class="px-2 py-3 align-top border border-gray-300 md:border-0">
									<?php if($bannerad->status == "pending"): ?>
									<p class="text-sm text-black font-medium"><i class="fa fa-check bg-yellow-400 text-white p-1 rounded-full <?php echo e($class_dir_mar_rl); ?>" aria-hidden="true"></i><?php echo e(__('p_my_banner_ads.pending')); ?></p>
									<?php elseif($bannerad->status == "approved"): ?>
									<p class="text-sm text-black font-medium"><i class="fa fa-check bg-green-500 text-white p-1 rounded-full <?php echo e($class_dir_mar_rl); ?>" aria-hidden="true"></i><?php echo e(__('p_my_banner_ads.approved')); ?></p>
									<?php else: ?>
									<p class="text-sm text-black font-medium"><i class="fa fa-times bg-red-500 text-white p-1 px-1.5 rounded-full <?php echo e($class_dir_mar_rl); ?>" aria-hidden="true"></i><?php echo e(__('p_my_banner_ads.cancelled')); ?></p>
									<small class="mt-4 text-sm text-gray-500 leading-tight"><?php echo e(__('p_my_banner_ads.your amount has been refunded successfully')); ?>!</small>
									<?php endif; ?>
									<?php if($bannerad->approved_lately == 1): ?>
									<p class="mt-4 text-sm text-gray-500 leading-tight font-normal lg:font-medium"><?php echo e(__('p_my_banner_ads.your request approved lately, so that your banner shown start date end date will be changed')); ?>.</p>
									<?php endif; ?>
								</td>
								<td class="px-2 py-3 align-top border border-gray-300 md:border-0">
									<?php if($bannerad->approved_lately == 1): ?>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.start date')); ?> : <b class="text-black font-medium"><?php echo date('d M Y', strtotime($bannerad->approved_start_date)); ?></b></p>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.end date')); ?> : <b class="text-black font-medium"><?php echo date('d M Y', strtotime($bannerad->approved_end_date)); ?></b></p>
									<?php else: ?>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.start date')); ?> : <b class="text-black font-medium"><?php echo date('d M Y', strtotime($bannerad->start_date)); ?></b></p>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.end date')); ?> : <b class="text-black font-medium"><?php echo date('d M Y', strtotime($bannerad->end_date)); ?></b></p>
									<?php endif; ?>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.payment type')); ?> : <b class="text-black font-medium"><?php echo e($bannerad->payment_type); ?></b></p>
									<p class="mb-1 whitespace-nowrap text-xs md:text-sm text-gray-500 font-medium"><?php echo e(__('p_my_banner_ads.live days')); ?> : <b class="text-black font-medium"><?php echo e($bannerad->live_days); ?></b></p>
								</td>
								<td class="px-2 py-3 align-top border border-gray-300 md:border-0">
									<p class="text-lg md:text-xl lg:text-2xl mb-1 whitespace-nowrap text-green-500 font-bold uppercase text-center"><?php echo $currency_symbol; ?><?php echo e($bannerad->total_amount); ?></p>
								</td>
							</tr>
							<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
						</tbody>
					</table>
					<div class="mt-4">
						<?php echo e($bannerads->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
				</div>

			</div>
		</div>
	</div>
</div>
<style>
    .alert-error span {
        color: #000;
    }

    .alert-error {
        background: #ffc4c4;
        border-top: 2px solid palevioletred;
    }
</style>
<!--------------- flash begin -------------------->
<script>
    function closeAlert(event) {
        let element = event.target;
        while (element.nodeName !== "BUTTON") {
            element = element.parentNode;
        }
        element.parentNode.parentNode.removeChild(element.parentNode);
    }
</script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/extra/postplugins/bannerads/src/my_banner_ads.blade.php ENDPATH**/ ?>