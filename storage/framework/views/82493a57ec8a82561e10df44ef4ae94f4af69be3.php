	<?php
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_sm_space_r = ($dir_rtl=="true")?'sm:space-x-reverse':'';
		$class_dir_sm_text_lr = ($dir_rtl=="true")?'sm:text-right':'sm:text-left';
		$class_dir_md_text_lr = ($dir_rtl=="true")?'md:text-right':'md:text-left';
		$class_dir_md_text_rl = ($dir_rtl=="true")?'md:text-left':'md:text-right';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
		$class_dir_mar_lr = ($dir_rtl=="true")?'ml-10':'mr-10';
		
	?>
	
	
	<div class="w-full float-left" <?php echo e($class_dir); ?>>

	<?php 
		$get_meta = App\Models\TblOtherpage::get_meta('single-package');
		$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");

	?>

		<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
			<?php $__env->startSection('meta_title', $meta_title); ?>
			<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
			<?php $__env->startSection('meta_description', $meta_description); ?>
		<?php endif; ?>

		<?php if($message = Session::get('message')): ?>
		<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fas fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> <?php echo e($message); ?></span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
		<?php endif; ?>
		
		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"><?php echo e(__('p_choose_package.single package')); ?></h1>
			</div>
		</div>
		
		<?php
		$currency_symbol = App\Models\Setting::get_admin_default_currency();
		$postimg = App\Models\TblChat::getPostImgForList($post_info[0]->id);
		?>
		
		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<div class="bg-green-50 shadow-md rounded relative mx-auto lg:max-w-4xl w-full mb-8 sm:mb-12 md:mb-16 lg:mb-20 md:p-8 lg:p-12 p-4 ">
					<div class="w-full float-left sm:float-none sm:flex sm:items-center sm:flex-wrap sm:space-x-8 <?php echo e($class_dir_sm_space_r); ?>">
						<div class="sm:float-left md:float-none sm:mb-0 mb-4">
							<div class="flex items-center justify-center mx-auto h-36 w-36">
								<img class="w-full h-36 rounded-lg mx-auto object-cover object-center" src="<?php echo e($postimg); ?>" />
							</div>
						</div>
						<div class="<?php echo e($class_dir_sm_text_lr); ?> text-center">
							<h1 class="text-lg sm:text-xl lg:text-2xl text-black mb-2 sm:mb-3"><?php echo e($post_info[0]->title); ?></h1>
							<p class="text-lg lg:text-xl text-black font-bold">
								<?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?> <span id=""><?php echo e($post_info[0]['price']); ?></span>
							</p>
						</div>
					</div>
					
					<div class="inline-block md:block mt-5 md:mt-8 lg:mt-10 sm:mb-0 mb-4">
						<h2 class="text-gray-500 font-bold text-lg md:text-2xl mb-6 lg:mb-8 mx-auto"><?php echo e(__('p_choose_package.choose package')); ?></h2>
						<div class="mt-2">
							<?php
							$i = 0;
							foreach ($list_of_packs as $k) {
								$checkString = ($i == 0) ? "checked=checked" : "";
								// $class = ($i == 0) ? "mr-4" : "";
								$i++;
							?>
								<div class="inline-block align-middle">
									<label class="<?php echo e($class_dir_mar_lr); ?> inline-flex items-center mb-3 sm:mb-6">
										<input type="radio" class="form-radio text-indigo-600 w-4 h-4 md:w-6 md:h-6" name="radio_pack" data-pack-id="<?php echo e($k['id']); ?>" data-pack-value="<?php echo e($k['price']); ?>" data-pack-days="<?php echo e($k['duration']); ?>" value="<?php echo e($k['id']); ?>" <?php echo e($checkString); ?>>
										<span class="mx-2 text-base text-black font-medium"><?php echo e($k['name']); ?> ( <?php echo e($k['duration']); ?> <?php echo e(__('p_choose_package.days')); ?> )</span>
									</label>
								</div>
							<?php
								$i++;
							}
							?>
						</div>
						<input type="hidden" id="selected_pack_id" value="<?php echo e($list_of_packs[0]['id']); ?>" />
						<input type="hidden" id="selected_pack_amount" value="<?php echo e($list_of_packs[0]['price']); ?>">
						<input type="hidden" id="selected_pack_days" value="<?php echo e($list_of_packs[0]['duration']); ?>">
					</div>
					
					<div class="block md:mt-4 lg:mt-8">
						<h2 class="text-gray-500 font-bold text-lg md:text-2xl mb-3 sm:mb-6 lg:mb-8"><?php echo e(__('p_choose_package.payment type')); ?></h2>
						<div class="mt-3 md:flex 	">
							<?php
							$i = 0;
							foreach ($payment_methods as $p) {
								$checkString = ($i == 0) ? "checked=checked" : "";
								// $class = ($i == 0) ? "mr-4" : "";
								$i++;
							?>
								<div class="inline-block align-middle">
									<label class="<?php echo e($class_dir_mar_lr); ?> inline-flex items-center mb-2">
										<input type="radio" class="form-radio text-indigo-600 w-4 h-4 md:w-6 md:h-6" name="payment_type" value="<?php echo e($p['name']); ?>" <?php echo e($checkString); ?>>
										<?php if(strtolower($p['display_name']) == "paypal"): ?>
										<span><img src="<?php echo e(URL::to('/images/web_paypal.png')); ?>" class="h-10 md:h-12 mx-2" /></span>
										<?php elseif(strtolower($p['display_name']) == "stripe"): ?>
										<span><img src="<?php echo e(URL::to('/images/stripe_logo.png')); ?>" class="h-10 md:h-12 mx-2" /></span>
										<?php else: ?>
										<span class="ml-2"><?php echo e($p['display_name']); ?></span>
										<?php endif; ?>
									</label>
								</div>
							<?php } ?>
							<div>
							</div>
						</div>
					</div>
					<div class="sm:flex sm:flex-wrap items-center sm:justify-between py-3">
						<h1 class="text-base font-medium capitalize"><?php echo e(__('p_choose_package.more discounts for business purpose')); ?></h1>
						<div class="mt-2 md:mt-0">
							<button class="w-full sm:w-44 bg-green-500 text-base text-white font-medium rounded md:rounded-lg outline-none focus:outline-none shadow hover:shadow-md outline-none hover:bg-white ease-linear transition-all duration-500 border-2 border-green-500 hover:text-green-500"><a class="block pt-2 pb-3 " href="<?php echo e(URL::to('/selectPackageMultiple')); ?>"><?php echo e(__('p_choose_package.view')); ?></a></button>
						</div>
					</div>
					<div class="">
						<div class="md:flex md:flex-wrap text-center <?php echo e($class_dir_md_text_lr); ?> items-center  md:py-3">
							<div class="w-full flex-grow flex-1"><button id="show_coupon" class="text-base font-medium outline-none focus:outline-none  underline pt-2 pb-2"><?php echo e(__('p_choose_package.do you have coupon code')); ?></button></div>
							<div class="w-full flex-grow flex-1 <?php echo e($class_dir_md_text_rl); ?>"><span class='text-right'><input style="display:none" id="coupon_code" class="uppercase text-base font-medium focus:outline-none border-1 focus:border-green-500 border border-green-500 text-center px-3 py-2 rounded-lg sm:w-72 lg:w-96 h-12 w-full" type="text" name="coupon_code" placeholder="<?php echo e(__('p_choose_package.enter coupon code')); ?>" /></span></div>
						</div>
						<div class="rounded-lg px-4 py-3 bg-white text-sm mt-3" style="display:none" id="sub_window_fixed">
							<div class="flex flex-wrap items-center mb-3">
								<input type="hidden" id="coupon_id" value="" />
								<div class="relative w-full px-4 max-w-full flex-grow flex-1"><p class="text-base text-black font-medium" id="coupon_title"><?php echo e(__('p_choose_package.discount')); ?></p></div>
								<div class="relative w-full px-4 max-w-full flex-grow flex-1 <?php echo e($class_dir_text_rl); ?>"><p class="text-base text-black font-medium">- <?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?> <span id="coupon_value">0.00</span></p></div>
							</div>
							<div class="flex flex-wrap items-center mb-3">
								<div class="relative w-full px-4 max-w-full flex-grow flex-1"><p class="text-base text-black font-medium"><?php echo e(__('p_choose_package.sub total')); ?></p></div>
								<div class="relative w-full px-4 max-w-full flex-grow flex-1 <?php echo e($class_dir_text_rl); ?>"><p class="text-base text-black font-medium"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?> <span id="sub_total"><?php echo e($list_of_packs[0]['price']); ?></span></p></div>
							</div>
							<div class="flex flex-wrap items-center mb-3">
								<div class="relative w-full px-4 max-w-full flex-grow flex-1"><p class="text-base text-black font-medium" id="tax_title"><?php echo e(__('p_choose_package.tax')); ?></p></div>
								<div class="relative w-full px-4 max-w-full flex-grow flex-1 <?php echo e($class_dir_text_rl); ?>"><p class="text-base text-black font-medium"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?><span id="tax_amt">0.00</span></p></div>
							</div>
							<div class="flex flex-wrap items-center border-b border-t border-gray-800 py-3">
								<div class="relative w-full px-4 max-w-full flex-grow flex-1"><p class="text-base text-black font-medium"><?php echo e(__('p_choose_package.total')); ?></p></div>
								<div class="relative w-full px-4 max-w-full flex-grow flex-1 <?php echo e($class_dir_text_rl); ?>"><p class="text-base text-black font-medium"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?> <span id="final_total_amount"><?php echo e($list_of_packs[0]['price']); ?></span></p></div>
							</div>
						</div>
					</div>
					<div class="pt-3 sm:pt-5 w-full">
						<?php if (!empty($currency_symbol) && !empty($payment_methods)) { ?>
							<button id="pay_proceed" class="rounded w-full p-2 pb-3 md:p-3 md:pb-4 lg:p-4 lg:pb-5 bg-green-500 text-base sm:text-lg md:text-xl lg:text-2xl font-bold text-white shadow outline-none focus:outline-none ease-linear transition-all duration-500 border-2 border-green-500 hover:text-green-500 hover:bg-white"><?php echo e(__('p_choose_package.pay')); ?> <span class="text-base sm:text-lg md:text-xl lg:text-2xl font-bold">
									<?php echo $currency_symbol['currency_hex']; ?> <span id="show_price_2"><?php echo e($list_of_packs[0]['price']); ?></span></span></button>
						<?php } else { ?>
							<button class="cursor-not-allowed not-allowed w-full rounded p-4 bg-green-500 text-2xl font-bold text-white shadow outline-none focus:outline-none ease-linear transition-all duration-500 border-2 border-green-500 hover:text-green-500 hover:bg-white"><?php echo e(__('p_choose_package.pay')); ?>

							</button>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div id="overlay"></div>
	<script>
		$(document).ready(function() {
			$('input[type=radio][name=radio_pack]').change(function(e) {
				var p_id = $(this).attr('data-pack-id');
				var p_val = $(this).attr('data-pack-value');
				var p_days = $(this).attr('data-pack-days');
				// $("#show_price_1").text(p_val);
				$("#show_price_2").text(p_val);
				$("#selected_pack_id").val(p_id);
				$("#selected_pack_amount").val(p_val);
				$("#selected_pack_days").val(p_days);
				$("#sub_total").text(p_val);
				$("#final_total_amount").text(p_val);
				if ($("#coupon_code").val() != "") {
					$("#coupon_code").focusout();
				}
			});
			$("#show_coupon").click(function() {
				$("#coupon_code").toggle();
				$("#sub_window_fixed").toggle();
			});
		});
		$("#coupon_code").focusout(function() {
			var coup_code = $("#coupon_code").val();
			var pack_price = $("#selected_pack_amount").val();
			$.ajax({
				type: 'POST',
				dataType: 'json',
				url: "<?php echo e(URL::to('get_pack_info')); ?>",
				data: {
					code: coup_code
				},
				success: function(data) {
					if (data.result == "success") {
						//var data1 = JSON.stringify(data.data);
						var arr_data = data.array_data;
						var type = arr_data['type'];
						var value = arr_data['value'];
						var tax = arr_data['tax'];
						var coupon_id = arr_data['id'];
						if (type == "fixed") {
							$("#coupon_id").val(coupon_id);
							var dis_title = "Discount Amount";
							$("#coupon_title").text(dis_title);
							var tax_title = "Tax (" + tax + "%)";
							$("#tax_title").text(tax_title);
							var new_price = pack_price - value;
							var final_price = parseFloat(new_price).toFixed(2);
							$("#sub_total").text(final_price);
							$("#coupon_value").text(value);
							var grand_tot = final_price;
							if (tax != "0.00") {
								var tax_calculation_amt = parseFloat((final_price / 100) * tax).toFixed(2);
								grand_tot = parseFloat(parseFloat(final_price) + parseFloat(tax_calculation_amt)).toFixed(2);
								$("#tax_amt").text(tax_calculation_amt);
							} else {
								$("#tax_amt").text("0.00");
							}
							$("#show_price_2").text(grand_tot);
							$("#final_total_amount").text(grand_tot);
						} else if (type == "percentage") {
							$("#coupon_id").val(coupon_id);
							var dis_title = "Discount Amount (" + value + "%)";
							$("#coupon_title").text(dis_title);
							var tax_title = "Tax (" + tax + "%)";
							$("#tax_title").text(tax_title);
							var percent_val = (pack_price / 100) * value;
							var new_price = pack_price - percent_val;
							var final_price = parseFloat(new_price).toFixed(2);
							$("#sub_total").text(final_price);
							$("#coupon_value").text(parseFloat(percent_val).toFixed(2));
							var grand_tot = final_price;
							if (tax != "0.00") {
								var tax_calculation_amt = parseFloat((final_price / 100) * tax).toFixed(2);
								grand_tot = parseFloat(parseFloat(final_price) + parseFloat(tax_calculation_amt)).toFixed(2);
								$("#tax_amt").text(tax_calculation_amt);
							} else {
								$("#tax_amt").text("0.00");
							}
							$("#show_price_2").text(grand_tot);
							$("#final_total_amount").text(grand_tot);
							//$("#show_price_2").text(final_price);
							//$("#tax_amt").text(tax);
							//$("#final_total_amount").text(final_price);
						} else {
							$("#coupon_id").val("");
							$("#coupon_title").text("Discount");
							$("#tax_title").text("Tax");
							$("#show_price_2").text(pack_price);
							$("#sub_total").text(pack_price);
							$("#final_total_amount").text(pack_price);
							$("#tax_amt").text('0.00');
							$("#coupon_value").text('0.00');
						}
						//toastr.success(data.result);
					} else {
						$("#coupon_id").val("");
						$("#coupon_title").text("Discount");
						$("#tax_title").text("Tax");
						$("#show_price_2").text(pack_price);
						$("#sub_total").text(pack_price);
						$("#final_total_amount").text(pack_price);
						$("#tax_amt").text('0.00');
						$("#coupon_value").text('0.00');
						toastr.warning(data.message);
					}
				}
			});
		});
		$(document).on("click", ".not-allowed", function(e) {
			toastr.warning("Opps something went wrong!, please contact admin.");
		});
	</script>
	<?php
	if (!empty($payment_methods)) {
		foreach ($payment_methods as $p) {
			$base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/view.php';
			if (is_file($base_path)) {
				include_once($base_path);
			}
		}
	}
	?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/choose_package_step/step-one.blade.php ENDPATH**/ ?>