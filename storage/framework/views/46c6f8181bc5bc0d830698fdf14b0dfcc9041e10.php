<?php $__env->startSection('content'); ?>

	
	<?php
	
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_space_r = ($dir_rtl=="true")?'space-x-reverse':'';
		$class_dir_post_os = ($dir_rtl=="true")?'sm:space-x-reverse':'';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
		$class_dir_sm_text_lr = ($dir_rtl=="true")?'sm:text-right':'sm:text-left';
		$class_dir_text_rl = ($dir_rtl=="true")?'sm:text-left':'sm:text-right';
		$class_dir_popup_btn = ($dir_rtl=="true")?'':'sm:space-x-reverse';
		
	?>
	
	
	<div class="w-full float-left" <?php echo e($class_dir); ?>>
		<div class="w-full float-left">
			<div class="m-auto container px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"> <?php echo e(__('p_my_orders_sales.my orders & sales')); ?></h1>
			</div>
		</div>
		
		<div class="w-full float-left bg-gray-100 h-48">
			<div class="container mx-auto px-4">
				<?php
				$seg = !empty(request()->segment('2')) ? request()->segment('2') : "";
				$active_class = "text-black";
				?>
				<ul class="flex flex-wrap mt-6 md:mt-14">
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-buynow/orders") ?>' class="text-sm sm:text-base lg:text-xl  text-black font-bold  <?php echo ($seg == 'orders') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_my_orders_sales.my orders')); ?></a>
						<?php if($seg == 'orders'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-buynow/sales") ?>' class="text-sm sm:text-base lg:text-xl  text-black font-bold  <?php echo ($seg == 'sales') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_my_orders_sales.my sales')); ?></a>
						<?php if($seg == 'sales'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>
		
		<div class="w-full float-left">
			<div class="container m-auto px-4">
				<div class="w-full bg-white float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 relative -mt-20 mt-4">
					<!---- my orders start --->
					<?php if($seg == "orders"): ?>
					<?php 
						$get_meta = App\Models\TblOtherpage::get_meta('my-buynow-orders');
						$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
						$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
						$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
					?>

					<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
							<?php $__env->startSection('meta_title', $meta_title); ?>
							<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
							<?php $__env->startSection('meta_description', $meta_description); ?>
					<?php endif; ?>

					<?php if(!empty(count($orders) > 0)): ?>
					<?php $__currentLoopData = $orders; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $order): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$post_info = App\Models\TblPost::where('id', $order->post_id)->withTrashed()->first();
					$post_img = App\Models\TblChat::getPostImgForList($order->post_id);
					$post_url = App\Models\TblPost::get_post_slug($post_info->slug);
					$user_info = App\Models\User::where('id', $post_info->user_id)->withTrashed()->first();
					$check_post_package = App\Models\TblPost::check_post_expired($order->post_id);

					// conditions
					$user_deleted = !empty($user_info->deleted_at) ? 1 : 0;
					$user_blocked = $user_info->is_blocked;
					// dd($user_deleted, $user_blocked);
					$is_deleted_post = !empty($post_info->deleted_at) ? $post_info->deleted_at: "" ;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<div class="hidden sm:visible absolute top-0 inset-x-2/4">
							<div class="triangle-down"></div>
						</div>
						
						<div class="flex items-center justify-between mb-4">
							<?php
							if ($order->order_status == "pending") {
								$status_class = "text-yellow-400";
							} else if ($order->order_status == "cancelled") {
								$status_class = "text-red-500";
							} else if ($order->order_status == "delivered") {
								$status_class = "text-green-500";
							} else if ($order->order_status == "shipped") {
								$status_class = "text-pink-500";
							} else if ($order->order_status == "processing") {
								$status_class = "text-blue-500";
							}
							?>
									  
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_my_banner_ads.status')); ?> : <span class="uppercase rounded p-1 <?php echo e($status_class); ?>"><?php echo e($order->order_status); ?></span></p>
							
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_my_orders_sales.order initiated on')); ?> : <?php echo date('d M Y', strtotime($order->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_post) || $check_post_package['expired'] == "Expired")
							{
								$post_url = "#";
							}else{
								$post_url = $post_url;
							}
						?>
						<div class="w-full sm:flex items-center ">
							<div class="w-full sm:w-8/12">
								<div class="sm:flex items-center sm:m-4 sm:mt-0 sm:space-x-4 text-center <?php echo e($class_dir_sm_text_lr); ?> sm:space-x-4 md:justify-start <?php echo e($class_dir_post_os); ?>">
									<div class="h-24 w-24 sm:h-20 sm:w-20 lg:h-24 lg:w-24 mx-auto sm:m-0 mb-2">
										<a class="h-full w-full inline-block" href="<?php echo e($post_url); ?>">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($post_img); ?>" />
										</a>
									</div>
									<div>
										<?php 
											if($user_deleted == 1 || $user_blocked == 1)
											{
												$profileUrl = "#";
											}else{
												$profileUrl = URL::to('/seller-profile/' . $order->seller_id);
											}
										?>
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_my_orders_sales.seller')); ?> : <a class="text-green-500 font-semibold" href="<?php echo $profileUrl; ?>" target="_blank"><?php echo e($user_info->name); ?></a>
											<?php if($user_deleted == 1): ?>
												<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
												<?php endif; ?>
												<?php if($user_blocked == 1): ?>
												<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
									</p>
										<a href="<?php echo e($post_url); ?>">
											<h3 class="text-xl font-semibold hover:text-green-500"><?php echo e($post_info->title); ?>

												<?php
												$product_condition = App\Models\TblPost::get_product_condition($order->post_id);
												?>
												<?php if(!empty($product_condition)): ?>
												<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
												<?php endif; ?>
												<?php if(!empty($is_deleted_post)): ?>
												<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
												<?php endif; ?>
												<?php if($check_post_package['expired'] == "Expired"): ?>
												<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
												<?php endif; ?>
											</h3>
										</a>
										<?php if(!empty($post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2"> <?php echo e($post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</div>
							<div class="w-full sm:w-4/12 text-center <?php echo e($class_dir_text_rl); ?> sm:m-auto">
								<div class="sm:m-4 sm:mt-0 mb-4 mt-4">
									<h3 class="text-lg text-gray-900 mb-2 md:mb-4"><?php echo e(__('p_my_orders_sales.order id')); ?> : <b><?php echo e($order->id); ?></b></h3>
									<a href="<?php echo URL::to('/vieworder/' . $order->orderId); ?>" class="bg-green-500 text-white text-base font-medium uppercase px-4 py-2 rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block hover:bg-white hover:text-green-500 border-2 border-green-500"><?php echo e(__('p_myads.view')); ?></a>
								</div>
							</div>
						</div>
						<?php if($order->order_status == "pending" || $order->order_status == "shipped"): ?>
						<?php //if(empty($is_deleted_post) && ($check_post_package['expired'] != "Expired") && ($user_deleted == 0) && ($user_blocked == 0)) { 
						// 		$bgColor = "bg-green-50";
						// }else{
						// 	if($order->order_status == "pending"){
						// 	$bgColor = "bg-green-50";
						// 	}else{
						// 		$bgColor = "";
						// 	}
						// 	}

							?>
						<div class="bg-green-50 py-2 px-4">
							<div class="flex flex-wrap space-x-2 justify-end <?php echo e($class_dir_space_r); ?>">
								<?php if($order->order_status == "pending"): ?>
								<button class="update_order_status text-white bg-red-500 border border-red-500 text-sm font-bold uppercase rounded shadow outline-none hover:bg-white hover:text-red-500 focus:outline-none ease-linear transition-all duration-500 p-2" data-id="<?php echo e($order->id); ?>" data-status="cancelled"><?php echo e(__('post_detail.cancel')); ?></button>
								<?php endif; ?>
								<?php 
						
							// if(empty($is_deleted_post) && ($check_post_package['expired'] != "Expired") && ($user_deleted == 0) && ($user_blocked == 0)){
							?>
								<?php if($order->order_status == "shipped"): ?>
								<button class="update_order_status text-white bg-green-500 border border-green-500 text-sm font-bold uppercase rounded shadow outline-none hover:bg-white hover:text-green-500 focus:outline-none ease-linear transition-all duration-500 p-2" data-id="<?php echo e($order->id); ?>" data-status="delivered"><?php echo e(__('p_my_orders_sales.delivered')); ?></button>
								<?php endif; ?>
								<?php //} ?>
							</div>
						</div>
						
						<?php endif; ?>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4 mm">
						<?php echo e($orders->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!----- my orders end -->



					<!---- my sales start --->
					<?php if($seg == "sales"): ?>
				<?php 
				$get_meta = App\Models\TblOtherpage::get_meta('my-buynow-sales');
				$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
				$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
				$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
				?>

				<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
						<?php $__env->startSection('meta_title', $meta_title); ?>
						<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
						<?php $__env->startSection('meta_description', $meta_description); ?>
				<?php endif; ?>

					<?php if(!empty(count($sales) > 0)): ?>
					<?php $__currentLoopData = $sales; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sale): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$post_info = App\Models\TblPost::where('id', $sale->post_id)->withTrashed()->first();
					$post_img = App\Models\TblChat::getPostImgForList($sale->post_id);
					$post_url = App\Models\TblPost::get_post_slug($post_info->slug);
					$user_info = App\Models\User::where('id', $sale->user_id)->withTrashed()->first();
					$check_post_package = App\Models\TblPost::check_post_expired($sale->post_id);

					// conditions
					$user_deleted = !empty($user_info->deleted_at) ? 1 : 0;
					$user_blocked = $user_info->is_blocked;
					// dd($user_deleted, $user_blocked);
					$is_deleted_post = !empty($post_info->deleted_at) ? $post_info->deleted_at: "" ;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<div class="hidden sm:visible absolute top-0 inset-x-2/4">
							<div class="triangle-down"></div>
						</div>
						<div class="flex items-center justify-between mb-4">
							<?php
							if ($sale->order_status == "pending") {
								$status_class = "text-yellow-500";
							} else if ($sale->order_status == "cancelled") {
								$status_class = "text-red-500";
							} else if ($sale->order_status == "delivered") {
								$status_class = "text-green-500";
							} else if ($sale->order_status == "shipped") {
								$status_class = "text-pink-500";
							} else if ($sale->order_status == "processing") {
								$status_class = "text-blue-500";
							}
							?>
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_my_banner_ads.status')); ?> : <span class="uppercase rounded p-1 <?php echo e($status_class); ?>"><?php echo e($sale->order_status); ?></span></p>
							
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_my_orders_sales.order initiated on')); ?> : <?php echo date('d M Y', strtotime($sale->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_post) || $check_post_package['expired'] == "Expired")
							{
								$post_url = "#";
							}else{
								$post_url = $post_url;
							}
						?>
						<div class="w-full sm:flex items-center">
							<div class="w-full sm:w-8/12">
								<div class="sm:flex items-center sm:m-4 sm:mt-0 sm:space-x-4 text-center <?php echo e($class_dir_sm_text_lr); ?> sm:space-x-4 md:justify-start <?php echo e($class_dir_post_os); ?>">
									<div class="h-24 w-24 sm:h-20 sm:w-20 lg:h-24 lg:w-24 mx-auto sm:m-0 mb-2">
										<a class="h-full w-full inline-block" href="<?php echo e($post_url); ?>">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($post_img); ?>" />
										</a>
									</div>
									<div>
										<a href="<?php echo e($post_url); ?>">
											<h3 class="text-xl font-semibold hover:text-green-500 mb-2"><?php echo e($post_info->title); ?>

												<?php
												$product_condition = App\Models\TblPost::get_product_condition($sale->post_id);
												?>
												<?php if(!empty($product_condition)): ?>
												<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
												<?php endif; ?>
												<?php if(!empty($is_deleted_post)): ?>
												<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
												<?php endif; ?>
												<?php if($check_post_package['expired'] == "Expired"): ?>
												<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
												<?php endif; ?>
											</h3>
										</a>
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_profile.name')); ?> : <?php echo e($user_info->name); ?>

										<?php if($user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
											<?php if($user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
										<?php endif; ?>
										</p>
										<?php 
											$hide_email = App\Models\TblPost::hide_email_address($user_info->email);
											?>
										<p class="text-base font-semibold text-gray-500 mb-2"><?php echo e(__('p_profile.email')); ?> : <?php echo e($hide_email); ?></p>
									</div>
								</div>
							</div>
							<div class="w-full sm:w-4/12 text-center <?php echo e($class_dir_text_rl); ?> sm:m-auto">
								<div class="sm:m-4 sm:mt-0 mb-4 mt-4">
									<h3 class="text-lg text-gray-900 mb-2 md:mb-4"><?php echo e(__('p_my_orders_sales.order id')); ?> : <b><?php echo e($sale->id); ?></b></h3>
									<a href="<?php echo URL::to('/vieworder/' . $sale->orderId); ?>" class="bg-green-500 text-white text-base font-medium uppercase px-4 py-2 rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 inline-block hover:bg-white hover:text-green-500 border-2 border-green-500"><?php echo e(__('p_myads.view')); ?></a>
								</div>
							</div>
						</div>
						<?php if($sale->order_status == "pending" || $sale->order_status == "processing" || $sale->order_status == "shipped"): ?>
						<div class="bg-green-50 py-2 px-4">
							<div class="flex flex-wrap space-x-2 justify-end <?php echo e($class_dir_space_r); ?>">
								<?php if($sale->order_status == "pending"): ?>
								<button class="update_order_status text-black bg-yellow-500 text-base font-medium uppercase rounded shadow inline-block outline-none focus:outline-none ease-linear transition-all duration-500 px-4 py-2 pb-3 hover:bg-white hover:text-yellow-500 border-2 border-yellow-500" data-id="<?php echo e($sale->id); ?>" data-status="processing"><?php echo e(__('p_my_orders_sales.mark as processing')); ?></button>
								<?php endif; ?>
								<?php if($sale->order_status == "processing"): ?>
								<button class="text-white bg-pink-500 text-base font-medium uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 px-4 py-2 hover:bg-white hover:text-pink-500 border-2 border-pink-500 view_courier_info" data-popup-id="courier_info_popup" data-id="<?php echo e($sale->id); ?>"><?php echo e(__('p_my_orders_sales.mark as shipped')); ?></button>
								<?php endif; ?>
								<?php if($sale->order_status == "shipped"): ?>
								<button class="text-white bg-green-500 text-base font-medium uppercase rounded shadow outline-none focus:outline-none ease-linear transition-all duration-500 px-4 py-2 pb-3 border-2 border-green-500  hover:bg-white hover:text-green-500 view_courier_info" data-popup-id="<?php echo e($sale->id); ?>_courier_info_popup" data-id="<?php echo e($sale->id); ?>"><?php echo e(__('p_my_orders_sales.edit tracking details')); ?></button>
								<?php endif; ?>
							</div>
						</div>
						<?php endif; ?>
					</div>
					<?php if($sale->order_status == "shipped"): ?>
					<?php
					$get_courier = App\Models\TblCourierInfo::where('order_id', $sale->id)->first();
					?>
					<!--- update courier info start --->
					<div class="fixed z-50 inset-0 overflow-y-auto" id="<?php echo e($sale->id); ?>_courier_info_popup" style="display:none">
						<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" <?php echo e($class_dir); ?>>
							<div class="fixed inset-0 transition-opacity" aria-hidden="true">
								<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
							</div>
							<!-- This element is to trick the browser into centering the modal contents. -->
							<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
							<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
								<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
									<div class="<?php echo e($class_dir_sm_text_lr); ?>">
										<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
											<?php echo e(__('p_my_orders_sales.shipping details')); ?>

										</h3>
										<div class="w-full inline-block mt-2">
											<div class="form-group mb-4">
												<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment date')); ?> : <span class="text-red-500">*</span></label>
												<input type="date" placeholder="Shipment date" value="<?php echo e($get_courier->shipping_date); ?>" class="s_date text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none focus:shadow-outline mt-1" />
											</div>
											<div class="form-group mb-4">
												<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment method')); ?> : <span class="text-red-500">*</span></label>
												<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.enter the courier')); ?>" value="<?php echo e($get_courier->courier_name); ?>" class="s_method text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
											</div>
											<div class="form-group mb-4">
												<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment service')); ?> : <span class="text-red-500">*</span></label>
												<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.shipping services')); ?>" value="<?php echo e($get_courier->courier_service); ?>" class="s_service text-sm md:text-base appearance-none border-l-2 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
											</div>
											<div class="form-group mb-4">
												<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.tracking id')); ?> : <span class="text-red-500">*</span></label>
												<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.enter tracking id')); ?>" value="<?php echo e($get_courier->tracking_id); ?>" class="s_track_id text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
											</div>
											<div class="form-group mb-4">
												<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.additional notes')); ?> : </label>
												<textarea rows="3" class="s_add_notes text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1"><?php echo e($get_courier->more_info); ?></textarea>	
											</div>
											<input type="hidden" class="selected_oid" />
										</div>
									</div>
									<div class="sm:flex sm:flex-row-reverse sm:space-x-3 <?php echo e($class_dir_popup_btn); ?>">
										<button type="button" class="w-full inline-block rounded-md border-2 border-green-500 shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="save_courier">
											<?php echo e(__('messages.submit')); ?>

										</button>
										<button data-popup-id="<?php echo e($sale->id); ?>_courier_info_popup" type="button" class="cancel_update_courier mt-3 w-full inline-block rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 mt-3 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
											<?php echo e(__('post_detail.cancel')); ?>

										</button>
									</div>
								</div>
							</div>
						</div>
					</div>
					<!--- update courier info end --->
					<?php endif; ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4">
						<?php echo e($sales->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!----- my sales end -->
					<!--- courier info start --->
					<div class="fixed z-50 inset-0 overflow-y-auto" id="courier_info_popup" style="display:none">
						<div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0" <?php echo e($class_dir); ?>>
							<div class="fixed inset-0 transition-opacity" aria-hidden="true">
								<div class="absolute inset-0 bg-gray-500 opacity-75"></div>
							</div>
							<!-- This element is to trick the browser into centering the modal contents. -->
							<span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
							<div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg w-full" role="dialog" aria-modal="true" aria-labelledby="modal-headline">
								<div class="bg-white px-6 py-4 sm:px-12 sm:py-8">
									<div class="mb-6">
										<div class="<?php echo e($class_dir_sm_text_lr); ?>">
											<h3 class="block text-xl text-black font-semibold mb-2 sm:mb-4" id="modal-headline">
												<?php echo e(__('p_my_orders_sales.shipping details')); ?>

											</h3>
											<div class="w-full inline-block mt-2">
												<div class="form-group mb-4">
													<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment date')); ?> : <span class="text-red-500">*</span></label>
													<input type="date" placeholder="Shipment date" required class="s_date text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
												</div>
												<div class="form-group mt-4">
													<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment method')); ?> : <span class="text-red-500">*</span></label>
													<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.enter the courier')); ?>" required class="s_method text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
												</div>
												<div class="form-group mt-4">
													<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.shipment service')); ?> : <span class="text-red-500">*</span></label>
													<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.shipping services')); ?>" required class="s_service text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1"/>
												</div>
												<div class="form-group mt-4">
													<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.tracking id')); ?> : <span class="text-red-500">*</span></label>
													<input type="text" placeholder="<?php echo e(__('p_my_orders_sales.enter tracking id')); ?>" required class="s_track_id text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1" />
												</div>
												<div class="form-group mt-4">
													<label class="block text-sm md:text-base text-gray-500 font-semibold mb-1"><?php echo e(__('p_my_orders_sales.additional notes')); ?> : </label>
													<textarea rows="3" class="s_add_notes text-sm md:text-base appearance-none border-l-2 border-gray-400 rounded w-full px-2 py-3 bg-gray-100 text-black leading-tight focus:outline-none mt-1"></textarea>
												</div>
												<input type="hidden" class="selected_oid" />
											</div>
										</div>
									</div>
									
									<div class="sm:flex sm:flex-row-reverse sm:space-x-3 <?php echo e($class_dir_popup_btn); ?>">
										<button type="button" class="w-full inline-block rounded-md border-2 border-green-500 shadow-sm px-4 py-2 pb-3 bg-green-500 text-base font-semibold text-white hover:bg-white hover:text-green-500 focus:outline-none sm:w-auto sm:text-sm transition-all ease-linear duration-500" id="save_courier">
											<?php echo e(__('messages.submit')); ?>

										</button>
										<button type="button" data-popup-id="courier_info_popup" class="cancel_courier mt-3 w-full inline-block rounded-md border-2 border-gray-300 shadow-sm px-4 py-2 pb-3 bg-white text-base font-semibold text-gray-700 hover:bg-gray-200 focus:outline-none sm:mt-0 sm:w-auto sm:text-sm transition-all ease-linear duration-500">
											<?php echo e(__('post_detail.cancel')); ?>

										</button>
									</div>
									
								</div>
							</div>
						</div>
					</div>
					<!--- courier info end --->
					<script>
						$("#overlay").hide();
						$(".update_order_status").click(function(e) {
							$("#overlay").show();
							e.preventDefault();
							var status = $(this).attr("data-status");
							var id = $(this).attr("data-id");
							var s_date = "";
							var s_method = "";
							var s_service = "";
							var s_track_id = "";
							var s_add_notes = "";
							if (status == "cancelled") {
								s_refund(id, "buynow");
							} else {
								update_order_status(status, id, s_date, s_method, s_service, s_track_id, s_add_notes);
							}
						});
						$('.view_courier_info').on('click', function(e) {
							e.preventDefault();
							var popup_id = "#" + $(this).attr("data-popup-id");
							$(".selected_oid").val($(this).attr("data-id"));
							$(popup_id).show();
						});
						$('.cancel_courier').on('click', function(e) {
							e.preventDefault();
							var popup_id = $(this).attr("data-popup-id");
							document.querySelector("#courier_info_popup").style.display = "none";
							$(popup_id).hide();
							// courier_info_popup
							// window.location.reload();
						});

						$('.cancel_update_courier').on('click', function(e) {
							e.preventDefault();
							var popup_id = "#" + $(this).attr("data-popup-id");
							$( popup_id ).css( "display", "none" );
							$(popup_id).hide();
							// 30_courier_info_popup
							// window.location.reload();
						});
						
						$('#save_courier').on('click', function(e) {
							e.preventDefault();
							var s_date = $('.s_date').val();
							var id = $(".selected_oid").val();
							var s_method = $.trim($('.s_method').val());
							var s_service = $.trim($('.s_service').val());
							var s_track_id = $.trim($('.s_track_id').val());
							var s_add_notes = $.trim($('.s_add_notes').val());
							if (s_date == "" || s_method == "" || s_service == "" || s_track_id == "") {
								toastr.warning('Please fill all the required fields!');
								return false;
							} else {
								update_order_status(status, id, s_date, s_method, s_service, s_track_id, s_add_notes);
							}
						});

						function update_order_status(status, id, s_date, s_method, s_service, s_track_id, s_add_notes) {
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: "<?php echo e(route('update_order_status')); ?>",
								data: {
									status: status,
									oid: id,
									s_date: s_date,
									s_method: s_method,
									s_service: s_service,
									s_track_id: s_track_id,
									s_add_notes: s_add_notes
								},
								success: function(data) {
									toastr.success(data.message);
									$('.s_date').val("");
									$('.s_method').val("");
									$('.s_service').val("");
									$('.s_track_id').val("");
									$('.s_add_notes').val("");
									$(".selected_oid").val("");
									location.reload();
								}
							});
						}

						function s_refund(id, type) {
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: "<?php echo e(URL::to('refund_payment_stripe')); ?>",
								data: {
									id: id,
									type: type
								},
								success: function(data) {
									if (data == "success") {
										toastr.success("Your oder has been cancelled and your amount refunded successfully!");
										window.location.reload();
									} else {
										toastr.warning("cancel has been failed, please try again later!");
										window.location.reload();
									}

								}
							});
						}
					</script>
					<style>
						.triangle-down {
							width: 0;
							height: 0;
							border-left: 25px solid transparent;
							border-right: 25px solid transparent;
							border-top: 50px solid #e4e4e4;
							position: relative;
							right: 20px;
						}
					</style>
				</div>
			</div>
		</div>
		<div id="overlay"></div>
	</div>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/extra/postplugins/buynow/src/my-orders-sales.blade.php ENDPATH**/ ?>