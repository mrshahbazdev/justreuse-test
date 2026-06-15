	<?php
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";

	?>

	<div class="root-element-div" <?php echo e($class_dir); ?>>
	   <?php
	   $currency_symbol = App\Models\Setting::get_admin_default_currency();

	   $get_meta = App\Models\TblOtherpage::get_meta('buy-business-packs');
	   $meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
	   $meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
	   $meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
	   ?>
	   
	   <?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
			<?php $__env->startSection('meta_title', $meta_title); ?>
			<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
			<?php $__env->startSection('meta_description', $meta_description); ?>
		<?php endif; ?>

		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"><?php echo e(__('p_choose_package.heavy discount')); ?></h1>
			</div>
		</div>
	   
	   <div class="w-full bg-gray-100 h-48 float-left">
		  
	   </div>
	   <?php
	   $html = "";
	   $h_style = "style=display:none";
	   $tot_amt = "0";
	   $checked_ids = [];
	   if (Session::get('cart-selected-bulk-packs')) {
		  $sess_ar = Session::get('cart-selected-bulk-packs');
		  if (count($sess_ar) > 0) {
			 $h_style = "style=display:block";
			 $checked_ids = $sess_ar;
			 $amount = 0;
			 $i = 0;
			 foreach ($sess_ar as $k) {
				$i++;
				$amount = $amount + (float) $k['price'];
			 }
			 $currency = !empty($currency_symbol) ? $currency_symbol['currency_hex'] : "";
			 $tot_amt = $amount;
			 $html .= "Packs-" . $i . " | ";
			 $html .= "Pay " . $currency . number_format((float) $amount, 2, '.', '');
		  }
	   }
	   ?>
		
		<div class="w-full float-left">
			<div class="container px-4 m-auto">
				<div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative -mt-36">
					<?php if (count($list_of_packs) > 0) { ?>
						<h4 class="text-xl font-bold pb-4"><?php echo e(__('p_choose_package.top ad for days')); ?></h4>
					<?php $__currentLoopData = $list_of_packs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$ch_vl = "";
					if (array_key_exists($j['id'], $checked_ids)) {
					$ch_vl = "checked=checked";
					}
					?>
					<div class="w-full bg-green-50 px-6 pt-4 pb-3 my-4 flex flex-wrap items-center justify-between">
						<p class="text-lg"><?php echo e($j['bulk_limit']); ?> Ads / <span class="font-bold"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?><?php echo e($j['price']); ?></span></p>

						<div class="">
							<input type="checkbox" class="checked_pack sm:w-5 sm:h-5" id="html" name="radio" value="<?php echo e($j['id']); ?>" <?php echo e($ch_vl); ?>>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<?php } ?>
					<!-- top ads bulck package end -->
					<!-- feature ads bulck package start -->
					<?php if (count($list_of_packs_fea) > 0) { ?>
					<div class="w-full feature_after pt-4 float-left">
						<h4 class="m-0 text-xl font-bold pb-4"><?php echo e(__('p_choose_package.feature ad for days')); ?></h4>
						<?php $__currentLoopData = $list_of_packs_fea; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $j): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
						<?php
						$ch_vl = "";
						if (array_key_exists($j['id'], $checked_ids)) {
						$ch_vl = "checked=checked";
						}
						?>
						<div class="w-full bg-green-50 px-6 pt-4 pb-3 my-2 flex flex-wrap items-center justify-between">
							<p class="text-lg"><?php echo e($j['bulk_limit']); ?> Ads / <span class="font-bold"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?><?php echo e($j['price']); ?></span></p>

							<div class="">
								<input type="checkbox" id="html" name="radio" class="checked_pack sm:w-5 sm:h-5" value="<?php echo e($j['id']); ?>" <?php echo e($ch_vl); ?>>
							</div>
						</div>
						<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					</div>
					<?php } ?>
					<!-- feature ads bulck package end -->
					<div class="w-full float-left p-4">
						<input type="hidden" id="final_total_amount" value="<?php echo e($tot_amt); ?>" />
						<div id="payment_area" <?php echo e($h_style); ?>>
							<div class="w-full choose_payment">
								<h4 class="text-xl font-bold pb-4"><?php echo e(__('p_choose_package.payment type')); ?></h4>
								<?php
								$i = 0;
								foreach ($payment_methods as $p) {
								$checkString = ($i == 0) ? "checked=checked" : "";
								$class = ($i == 0) ? "mr-4" : "";
								$i++;
								?>
								<label class="inline-flex items-center mb-2 <?php echo e($class); ?>">
									<input type="radio" name="payment_type" value="<?php echo e($p['name']); ?>" <?php echo e($checkString); ?>>
									<?php if(strtolower($p['display_name']) == "paypal"): ?>
									<span><img src="<?php echo e(URL::to('/images/web_paypal.png')); ?>" class="h-10 md:h-12 ml-3 -mt-1" /></span>
									<?php elseif(strtolower($p['display_name']) == "stripe"): ?>
									<span><img src="<?php echo e(URL::to('/images/stripe_logo.png')); ?>" class="h-10 md:h-12 ml-2" /></span>
									<?php else: ?>
									<span class="ml-2"><?php echo e($p['display_name']); ?></span>
									<?php endif; ?>
								</label>
								<?php } ?>

							</div>
							<?php if (!empty($currency_symbol)) { ?>
							<div class="w-full text-center mt-8">
								<a href="#" id="pay_proceed" class="w-full px-4 py-2 pb-3 md:px-8 md:pt-3 md:pb-4 rounded-lg bg-green-500 text-base md:text-lg lg:text-xl font-semibold text-white shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 border-2 border-green-500 hover:text-green-500 hover:bg-white"><?php echo $html; ?>
								</a>
							</div>
							<?php } else { ?>
							<div class="w-full text-center mt-8">	
								<a href="#" id="pay_proceed" class="cursor-not-allowd not-allowed w-full px-4 py-2 pb-3 md:px-8 md:pt-3 md:pb-4 rounded-lg bg-green-500 text-base md:text-lg lg:text-xl font-semibold text-white shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 border-2 border-green-500 hover:text-green-500 hover:bg-white"><?php echo $html; ?>
								</a>
							</div>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>

	   <script>
		  $(".checked_pack").click(function() {
			 var mode = 0;
			 mode = ($('.checked_pack').is(":checked")) ? 1 : 0;
			 var html = "";
			 var pack_id = $(this).val();
			 $.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo e(URL::to('update_bulk_pack_cart')); ?>",
				data: {
				   pack_id: pack_id,
				   mode: mode
				},
				success: function(data) {
				   if (data.result == "success") {
					  var arr = data.data;
					  var amount = "0";
					  var i = 0;
					  $.each(arr, function(key, value) {
						 i++;
						 var price = value['price'];
						 amount = parseFloat(parseFloat(amount) + parseFloat(price)).toFixed(2);
					  });
					  var currency = "<?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?>";
					  html += "Packs-" + i + " | ";
					  html += "Pay " + currency + amount;
					  $("#pay_proceed").html(html);
					  var disp = (i == 0) ? 'none' : 'block';
					  $("#payment_area").css('display', disp);
					  var tot_amt = (i == 0) ? '0' : amount;
					  $("#final_total_amount").val(tot_amt);
				   }
				}
			 });
		  });
		  $(document).on("click", ".not-allowed", function(e) {
			 toastr.warning("Opps something went wrong!, please contact admin.");
		  });
	   </script>

	</div>
	<?php
	foreach ($payment_methods as $p) {
	   $base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/bulk_view.php';
	   if (is_file($base_path)) {
		  include_once($base_path);
	   }
	}
	?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/choose_multiple_package/show.blade.php ENDPATH**/ ?>