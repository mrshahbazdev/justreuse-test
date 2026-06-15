	
	<?php $__env->startSection('content'); ?>
	
	<?php
		
		$dir_rtl =  App\Models\Setting::is_dir_rtl();
		$class_dir = ($dir_rtl=="true")?'dir=rtl':"";
		$class_dir_exch_btn = ($dir_rtl=="true")?'space-x-reverse':'';
		$class_dir_post_req = ($dir_rtl=="true")?'sm:space-x-reverse':'';
		$class_dir_padding_rl = ($dir_rtl=="true")?'lg:pl-6 xl:pl-16':'lg:pr-6 xl:pr-16';
		$class_dir_padding_lr = ($dir_rtl=="true")?'lg:pr-6 xl:pr-16':'lg:pl-6 xl:pl-16';
		$class_dir_add_cls = ($dir_rtl=="true")?'ar':"";
		$class_dir_text_lr = ($dir_rtl=="true")?'sm:text-right':'sm:text-left';
		$class_dir_text_rl = ($dir_rtl=="true")?'text-left':'text-right';
	?>
	
	<div class="w-full float-left" <?php echo e($class_dir); ?>>
		<div class="w-full float-left">
			<div class="m-auto container px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"><?php echo e(__('p_myexchange.my exchange')); ?></h1>
			</div>
		</div>
		
		<div class="w-full float-left bg-gray-100 h-48">
			<?php

				use function PHPUnit\Framework\isNull;

				$seg = !empty(request()->segment('2')) ? request()->segment('2') : "";
				$active_class = "text-black";
			?>
			<div class="container mx-auto px-4">
				<ul class="flex flex-wrap mt-6 md:mt-14">
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-exchange/incoming") ?>' class="text-sm sm:text-base lg:text-xl  <?php echo ($seg == 'incoming') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_myexchange.incoming requests')); ?></a>
						<?php if($seg == 'incoming'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-exchange/outgoing") ?>' class="text-sm sm:text-base lg:text-xl  <?php echo ($seg == 'outgoing') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_myexchange.outgoing requests')); ?></a>
						<?php if($seg == 'outgoing'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-exchange/successful") ?>' class="text-sm sm:text-base lg:text-xl <?php echo ($seg == 'successful') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_myexchange.successful exchanges')); ?></a>
						<?php if($seg == 'successful'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
					<li class="p-2 px-0 md:p-0 flex-auto text-center w-1/2 md:w-auto">
						<a href='<?php echo url("/my-exchange/failed") ?>' class="text-sm sm:text-base lg:text-xl <?php echo ($seg == 'failed') ? 'text-black' : 'text-gray-400'; ?> font-bold"><?php echo e(__('p_myexchange.failed exchanges')); ?></a>
						<?php if($seg == 'failed'): ?>
						<p class="border-green-500 border-b-4 mt-2 w-24 m-auto"></p>
						<?php endif; ?>
					</li>
				</ul>
			</div>
		</div>
		<div class="w-full float-left">
			<div class="container px-4 mx-auto">
				<div class="w-full bg-white float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 relative -mt-12 md:-mt-20 mt-4">

					<!---- incoming requests start --->
					<?php if($seg == "incoming"): ?>
					<!-- meta data -->

					<?php 
						// meta datas
						$get_meta = App\Models\TblOtherpage::get_meta('my-exchange-incoming');
						$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
						$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
						$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
						// $meta_final_title = auth()->user()->name. " | ". $meta_title;
					?>

					<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
						<?php $__env->startSection('meta_title', $meta_title); ?>
						<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
						<?php $__env->startSection('meta_description', $meta_description); ?>
					<?php endif; ?>

					<?php if(!empty(count($incomings) > 0)): ?>
					<?php $__currentLoopData = $incomings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $incoming): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
						
						//from 
					$from_post_info = App\Models\TblPost::where('id', $incoming->post_id)->withTrashed()->first();
					
					// if(!is_null($from_post_info)){
					$from_post_user_info = App\Models\User::where('id', $incoming->post_owner_id)->withTrashed()->first();

					$from_postimg = App\Models\TblChat::getPostImgForList($incoming->post_id);
					$exchange_postimg = App\Models\TblChat::getPostImgForList($incoming->exchanged_post_id);
					$exchange_post_info = App\Models\TblPost::where('id', $incoming->exchanged_post_id)->withTrashed()->first();  //to 
					$from_slug = App\Models\TblPost::get_post_slug($from_post_info->slug);
					$exchange_slug =  App\Models\TblPost::get_post_slug($exchange_post_info->slug);
					$exchange_post_user_info = App\Models\User::where('id', $incoming->user_id)->withTrashed()->first();

					$is_deleted_from = !empty($from_post_info->deleted_at) ? $from_post_info->deleted_at: "" ;
					$is_deleted_exchange = !empty($exchange_post_info->deleted_at) ? $exchange_post_info->deleted_at : "";
					// dd($is_deleted_from, $is_deleted_exchange);
					$check_post_package_from = App\Models\TblPost::check_post_expired($incoming->post_id);
					
					$check_post_package_exchange = App\Models\TblPost::check_post_expired($incoming->exchanged_post_id);

                    $from_user_deleted = !empty($from_post_user_info->deleted_at) ? 1 : 0;
                    $from_user_blocked = $from_post_user_info->is_blocked;
                    $exchange_user_deleted = !empty($exchange_post_user_info->deleted_at) ? 1 : 0;
                    $exchange_user_blocked = $exchange_post_user_info->is_blocked;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<?php
						if ($incoming->status == "pending") {
							$status_class = "text-yellow-400";
						} else if ($incoming->status == "cancel" || $incoming->status == "declined") {
							$status_class = "text-red-500";
						} else if ($incoming->status == "success" || $incoming->status == "accepted") {
							$status_class = "text-green-500";
						}
						?>
						<div class="hidden sm:block absolute top-0 inset-x-2/4">
							<div class="<?php echo e($class_dir_add_cls); ?> triangle-down"></div>
						</div>
						
						<div class="flex items-center justify-between mb-4">
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_myexchange.current status')); ?> : <span class="uppercase p-1 inline-block <?php echo e($status_class); ?>"><?php echo e($incoming->status); ?></span></p>

							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_myexchange.exchange initiated on')); ?> : <?php echo date('d M Y', strtotime($incoming->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_from) || $check_post_package_from['expired'] == "Expired")
							{
								$postUrl = "#";
							}else{
								$postUrl = $from_slug;
							}
						?>
						<div class="w-full md:flex items-center">
							<a href="<?php echo e($postUrl); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_rl); ?> block">
								<div class="sm:flex items-center sm:m-4 mb-4 mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($from_postimg); ?>" />
										</div>
									</div>	
										
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?>: <?php echo e($from_post_user_info->name); ?>

										  <?php if($from_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
											<?php if($from_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($from_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($incoming->post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_from)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_from['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>

										</h3>
										<?php if(!empty($from_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($from_post_info->locality); ?></p>
										<?php endif; ?>

									</div>
								</div>
							</a>

							<?php 	
							if(!empty($is_deleted_exchange) || $check_post_package_exchange['expired'] == "Expired")
								{
									$postUrlExchange = "#";
								}else{
									$postUrlExchange = $exchange_slug;
								}
							?>

							<div class="w-1/12 m-auto transform rotate-90 md:rotate-0 text-center text-green-500 text-3xl"><i class="fa fa-exchange" aria-hidden="true"></i></div>
							<a href="<?php echo e($postUrlExchange); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_lr); ?> block">
								<div class="sm:flex items-center sm:m-4 my-4 md:mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($exchange_postimg); ?>" />
										</div>
									</div>
										
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e($exchange_post_user_info->name); ?>

										<?php if($exchange_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
										<?php if($exchange_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($exchange_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($incoming->exchanged_post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_exchange)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_exchange['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($exchange_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($exchange_post_info->locality); ?></p>
										<?php endif; ?>
										
									</div>
								</div>
							</a>
						</div>
						<?php 
						
						if(empty($is_deleted_from) && ($check_post_package_from['expired'] != "Expired") && ($from_user_deleted == 0) && ($from_user_blocked == 0) && ($exchange_user_deleted == 0) && ($exchange_user_blocked == 0) && empty($is_deleted_exchange) && ($check_post_package_exchange['expired'] != "Expired")){
							?>
						<div class="bg-green-50 p-2 mt-6">
							<div class="flex flex-wrap space-x-2 justify-between md:justify-start <?php echo e($class_dir_exch_btn); ?>">
							
								<?php if($incoming->status == "pending"): ?>
								<?php $already_bought = App\Models\TblExchangedPost::where('post_id', $incoming->post_id)->pluck("status")->toArray(); ?>
								<?php if($from_post_info->sold_status == 0): ?>
								<button class="update_exchange_status text-white bg-yellow-600 hover:bg-yellow-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($incoming->id); ?>" data-status="accepted"><?php echo e(__('p_myexchange.accept')); ?></button>
								<?php endif; ?>
								<button class="update_exchange_status text-white bg-gray-500 hover:bg-gray-800 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($incoming->id); ?>" data-status="declined"><?php echo e(__('p_myexchange.decline')); ?></button>
								<?php elseif($incoming->status == "accepted"): ?>
								<button class="update_exchange_status text-white bg-green-500 hover:bg-green-800 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($incoming->id); ?>" data-status="success"><?php echo e(__('p_myexchange.success')); ?></button>
								<button class="update_exchange_status text-white bg-red-500 hover:bg-red-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($incoming->id); ?>" data-status="failed"><?php echo e(__('p_myexchange.failed')); ?></button>
								<?php endif; ?>
							
							</div>
						</div>
						<?php  } ?>
					</div>
					<?php // } ?>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4">
						<?php echo e($incomings->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!----- incoming requests end -->

					<!-- outgoing request start --->
					<?php if($seg == "outgoing"): ?>
					<!-- meta data -->

					<?php 
						// meta datas
						$get_meta = App\Models\TblOtherpage::get_meta('my-exchange-outgoing');
						$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
						$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
						$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
						// $meta_final_title = auth()->user()->name. " | ". $meta_title;
					?>

					<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
						<?php $__env->startSection('meta_title', $meta_title); ?>
						<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
						<?php $__env->startSection('meta_description', $meta_description); ?>
					<?php endif; ?>

					<?php if(!empty(count($outgoings) > 0)): ?>
					<?php $__currentLoopData = $outgoings; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $outgoing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$from_post_info = App\Models\TblPost::where('id', $outgoing->post_id)->withTrashed()->first();
					$from_post_user_info = App\Models\User::where('id', $outgoing->post_owner_id)->withTrashed()->first();
					$from_postimg = App\Models\TblChat::getPostImgForList($outgoing->post_id);
					$exchange_postimg = App\Models\TblChat::getPostImgForList($outgoing->exchanged_post_id);
					$exchange_post_info = App\Models\TblPost::where('id', $outgoing->exchanged_post_id)->withTrashed()->first();
					$from_slug = App\Models\TblPost::get_post_slug($from_post_info->slug);
					$exchange_slug = App\Models\TblPost::get_post_slug($exchange_post_info->slug);
					$exchange_post_user_info = App\Models\User::where('id', $outgoing->user_id)->withTrashed()->first();

					// conditions
					$is_deleted_from = !empty($from_post_info->deleted_at) ? $from_post_info->deleted_at: "" ;
					$is_deleted_exchange = !empty($exchange_post_info->deleted_at) ? $exchange_post_info->deleted_at : "";
					// dd($is_deleted_from, $is_deleted_exchange);
					$check_post_package_from = App\Models\TblPost::check_post_expired($outgoing->post_id);
					
					$check_post_package_exchange = App\Models\TblPost::check_post_expired($outgoing->exchanged_post_id);

                    $from_user_deleted = !empty($from_post_user_info->deleted_at) ? 1 : 0;
                    $from_user_blocked = $from_post_user_info->is_blocked;
                    $exchange_user_deleted = !empty($exchange_post_user_info->deleted_at) ? 1 : 0;
                    $exchange_user_blocked = $exchange_post_user_info->is_blocked;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<div class="hidden sm:block absolute top-0 inset-x-2/4">
							<div class="<?php echo e($class_dir_add_cls); ?> triangle-down"></div>
						</div>
						
						<div class="flex items-center justify-between mb-4">
							<?php
							if ($outgoing->status == "pending") {
								$status_class = "text-yellow-400";
							} else if ($outgoing->status == "cancel" || $outgoing->status == "declined") {
								$status_class = "text-red-500";
							} else if ($outgoing->status == "success" || $outgoing->status == "accepted") {
								$status_class = "text-green-500";
							}
							?>
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_myexchange.current status')); ?> : <span class="uppercase p-1 <?php echo e($status_class); ?>"><?php echo e($outgoing->status); ?></span></p>
							
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_myexchange.exchange initiated on')); ?> : <?php echo date('d M Y', strtotime($outgoing->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_from) || $check_post_package_from['expired'] == "Expired")
							{
								$postUrl = "#";
							}else{
								$postUrl = $from_slug;
							}
						?>
						<div class="w-full md:flex items-center">
							<a href="<?php echo e($postUrl); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_rl); ?> block">
								<div class="sm:flex items-center sm:m-4 mb-4 mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($from_postimg); ?>" />
										</div>
									</div>
									
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($from_post_user_info->name); ?>

										<?php if($from_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
											<?php if($from_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($from_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($outgoing->post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_from)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_from['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($from_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($from_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
							<?php 	
							if(!empty($is_deleted_exchange) || $check_post_package_exchange['expired'] == "Expired")
								{
									$postUrlExchange = "#";
								}else{
									$postUrlExchange = $exchange_slug;
								}
							?>
							<div class="w-1/12 m-auto transform rotate-90 md:rotate-0 text-center text-green-500 text-3xl"><i class="fa fa-exchange" aria-hidden="true"></i></div>
							<a href="<?php echo e($postUrlExchange); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_lr); ?> block">
								<div class="sm:flex items-center sm:m-4 my-4 md:mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($exchange_postimg); ?>" />
										</div>
									</div>
											
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($exchange_post_user_info->name); ?>

										<?php if($exchange_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
										<?php if($exchange_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($exchange_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($outgoing->exchanged_post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_exchange)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_exchange['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($exchange_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($exchange_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
						</div>
						<?php 
						
							if(empty($is_deleted_from) && ($check_post_package_from['expired'] != "Expired") && ($from_user_deleted == 0) && ($from_user_blocked == 0) && ($exchange_user_deleted == 0) && ($exchange_user_blocked == 0) && empty($is_deleted_exchange) && ($check_post_package_exchange['expired'] != "Expired")){
						?>
						<div class="bg-green-50 p-2 mt-6">
							<div class="flex flex-wrap space-x-2 justify-between md:justify-start <?php echo e($class_dir_exch_btn); ?>">
								<?php if($outgoing->status == "pending"): ?>
								<button class="update_exchange_status text-white bg-gray-500 hover:bg-gray-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($outgoing->id); ?>" data-status="cancelled"><?php echo e(__('p_myexchange.cancel')); ?></button>
								<?php elseif($outgoing->status == "accepted"): ?>
								<button class="update_exchange_status text-white bg-green-500 hover:bg-green-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($outgoing->id); ?>" data-status="success"><?php echo e(__('p_myexchange.success')); ?></button>
								<button class="update_exchange_status text-white bg-red-500 hover:bg-red-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2" data-id="<?php echo e($outgoing->id); ?>" data-status="failed"><?php echo e(__('p_myexchange.failed')); ?></button>
								<?php endif; ?>
							</div>
						</div>
						<?php  } ?>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4">
						<?php echo e($outgoings->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!-- outgoing request end --->

					<!-- success exchange start --->
					<?php if($seg == "successful"): ?>
					<!-- meta data -->
					<?php 
						// meta datas
						$get_meta = App\Models\TblOtherpage::get_meta('my-exchange-successful');
						$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
						$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
						$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
						
					?>

					<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
						<?php $__env->startSection('meta_title', $meta_title); ?>
						<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
						<?php $__env->startSection('meta_description', $meta_description); ?>
					<?php endif; ?>

					<?php if(!empty(count($successes) > 0)): ?>
					<?php $__currentLoopData = $successes; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $success): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$from_post_info = App\Models\TblPost::where('id', $success->post_id)->withTrashed()->first();
					$from_post_user_info = App\Models\User::where('id', $success->post_owner_id)->withTrashed()->first();
					$from_postimg = App\Models\TblChat::getPostImgForList($success->post_id);
					$exchange_postimg = App\Models\TblChat::getPostImgForList($success->exchanged_post_id);
					$exchange_post_info = App\Models\TblPost::where('id', $success->exchanged_post_id)->withTrashed()->first();
					$from_slug = App\Models\TblPost::get_post_slug($from_post_info->slug);
					$exchange_slug = App\Models\TblPost::get_post_slug($exchange_post_info->slug);
					$exchange_post_user_info = App\Models\User::where('id', $success->user_id)->withTrashed()->first();

					// conditions
					$is_deleted_from = !empty($from_post_info->deleted_at) ? $from_post_info->deleted_at: "" ;
					$is_deleted_exchange = !empty($exchange_post_info->deleted_at) ? $exchange_post_info->deleted_at : "";
					// dd($is_deleted_from, $is_deleted_exchange);
					$check_post_package_from = App\Models\TblPost::check_post_expired($success->post_id);
					
					$check_post_package_exchange = App\Models\TblPost::check_post_expired($success->exchanged_post_id);

                    $from_user_deleted = !empty($from_post_user_info->deleted_at) ? 1 : 0;
                    $from_user_blocked = $from_post_user_info->is_blocked;
                    $exchange_user_deleted = !empty($exchange_post_user_info->deleted_at) ? 1 : 0;
                    $exchange_user_blocked = $exchange_post_user_info->is_blocked;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<div class="hidden sm:block absolute top-0 inset-x-2/4">
							<div class="<?php echo e($class_dir_add_cls); ?> triangle-down"></div>
						</div>
						
						<div class="flex items-center justify-between mb-4">
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_myexchange.current status')); ?> : <span class="uppercase rounded p-1 text-green-500"><?php echo e($success->status); ?></span></p>
							
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_myexchange.exchange initiated on')); ?> : <?php echo date('d M Y', strtotime($success->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_from) || $check_post_package_from['expired'] == "Expired")
							{
								$postUrl = "#";
							}else{
								$postUrl = $from_slug;
							}
						?>
						<div class="w-full md:flex items-center">
							<a href="<?php echo e($postUrl); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_rl); ?> block">
								<div class="sm:flex items-center sm:m-4 mb-4 mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($from_postimg); ?>" />
											<!--<img class="rounded-full sm:mr-4 h-24 w-24 md:h-20 md:w-20 object-cover object-center mx-auto border border-gray-400" src="<?php echo e($from_postimg); ?>" />-->
										</div>
									</div>
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($from_post_user_info->name); ?>

										<?php if($from_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
											<?php if($from_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
										<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($from_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($success->post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_from)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_from['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($from_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($from_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
							<?php 	
							if(!empty($is_deleted_exchange) || $check_post_package_exchange['expired'] == "Expired")
								{
									$postUrlExchange = "#";
								}else{
									$postUrlExchange = $exchange_slug;
								}
							?>
							<div class="w-1/12 m-auto transform rotate-90 md:rotate-0 text-center text-green-500 text-3xl"><i class="fa fa-exchange" aria-hidden="true"></i></div>
							<a href="<?php echo e($postUrlExchange); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_lr); ?> block">
								<div class="sm:flex items-center sm:m-4 my-4 md:mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($exchange_postimg); ?>" />
											<!--<img class="rounded-full sm:mr-4 h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 object-cover object-center mx-auto border border-gray-400" src="<?php echo e($exchange_postimg); ?>" />-->
										</div>
									</div>
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($exchange_post_user_info->name); ?>

											<?php if($exchange_user_deleted == 1): ?>
												<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
												<?php endif; ?>
											<?php if($exchange_user_blocked == 1): ?>
												<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($exchange_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($success->exchanged_post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_exchange)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_exchange['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($exchange_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($exchange_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
						</div>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4">
						<?php echo e($successes->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!-- success exchange end --->

					<!---- failed exchange start --->
					<?php if($seg == "failed"): ?>
					<!-- meta data -->
					<?php 
						// meta datas
						$get_meta = App\Models\TblOtherpage::get_meta('my-exchange-failed');
						$meta_title = (!empty($get_meta->meta_title) ?$get_meta->meta_title : "");
						$meta_keywords = (!empty($get_meta->meta_key) ?$get_meta->meta_key : "");
						$meta_description = (!empty($get_meta->meta_description) ?$get_meta->meta_description : "");
						
					?>

					<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
						<?php $__env->startSection('meta_title', $meta_title); ?>
						<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
						<?php $__env->startSection('meta_description', $meta_description); ?>
					<?php endif; ?>

					<?php if(!empty(count($faileds) > 0)): ?>
					<?php $__currentLoopData = $faileds; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $failed): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
					<?php
					$from_post_info = App\Models\TblPost::where('id', $failed->post_id)->withTrashed()->first();
					$from_post_user_info = App\Models\User::where('id', $failed->post_owner_id)->withTrashed()->first();
					$from_postimg = App\Models\TblChat::getPostImgForList($failed->post_id);
					$exchange_postimg = App\Models\TblChat::getPostImgForList($failed->exchanged_post_id);
					$exchange_post_info = App\Models\TblPost::where('id', $failed->exchanged_post_id)->withTrashed()->first();
					$from_slug = App\Models\TblPost::get_post_slug($from_post_info->slug);
					$exchange_slug = App\Models\TblPost::get_post_slug($exchange_post_info->slug);
					$exchange_post_user_info = App\Models\User::where('id', $failed->user_id)->withTrashed()->first();

					// conditions
					$is_deleted_from = !empty($from_post_info->deleted_at) ? $from_post_info->deleted_at: "" ;
					$is_deleted_exchange = !empty($exchange_post_info->deleted_at) ? $exchange_post_info->deleted_at : "";
					// dd($is_deleted_from, $is_deleted_exchange);
					$check_post_package_from = App\Models\TblPost::check_post_expired($failed->post_id);
					
					$check_post_package_exchange = App\Models\TblPost::check_post_expired($failed->exchanged_post_id);

					$from_user_deleted = !empty($from_post_user_info->deleted_at) ? 1 : 0;
					$from_user_blocked = $from_post_user_info->is_blocked;
					$exchange_user_deleted = !empty($exchange_post_user_info->deleted_at) ? 1 : 0;
					$exchange_user_blocked = $exchange_post_user_info->is_blocked;

					?>
					<div class="bg-white shadow border border-gray-100 mb-4 px-4 sm:px-6 lg:px-12 xl:px-16 py-4 md:py-6 lg:py-9 relative">
						<div class="sm:block absolute top-0 inset-x-2/4">
							<div class="<?php echo e($class_dir_add_cls); ?> triangle-down"></div>
						</div>
						
						<div class="flex items-center justify-between mb-4">
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2"><?php echo e(__('p_myexchange.current status')); ?> : <span class="uppercase text-black rounded p-1 text-red-500"><?php echo e($failed->status); ?></span></p>
							
							<p class="text-black text-xs sm:text-sm md:text-base font-semibold w-1/2 <?php echo e($class_dir_text_rl); ?>"><?php echo e(__('p_myexchange.exchange initiated on')); ?> : <?php echo date('d M Y', strtotime($failed->created_at)); ?></p>
						</div>
						<?php 	
						if(!empty($is_deleted_from) || $check_post_package_from['expired'] == "Expired")
							{
								$postUrl = "#";
							}else{
								$postUrl = $from_slug;
							}
						?>
						<div class="w-full md:flex items-center">
							<a href="<?php echo e($postUrl); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_rl); ?> block">
								<div class="sm:flex items-center sm:m-4 mb-4 mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($from_postimg); ?>" />
										</div>
									</div>
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($from_post_user_info->name); ?>

											<?php if($from_user_deleted == 1): ?>
												<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
												<?php endif; ?>
												<?php if($from_user_blocked == 1): ?>
												<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($from_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($failed->post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_from)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_from['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($from_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($from_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
							<?php 	
							if(!empty($is_deleted_exchange) || $check_post_package_exchange['expired'] == "Expired")
								{
									$postUrlExchange = "#";
								}else{
									$postUrlExchange = $exchange_slug;
								}
							?>
							<div class="w-1/12 m-auto transform rotate-90 md:rotate-0 text-center text-green-500 text-3xl"><i class="fa fa-exchange" aria-hidden="true"></i></div>
							<a href="<?php echo e($postUrlExchange); ?>" class="w-full md:w-6/12 <?php echo e($class_dir_padding_lr); ?> block">
								<div class="sm:flex items-center sm:m-4 my-4 md:mt-0 text-center <?php echo e($class_dir_text_lr); ?> sm:space-x-4 sm:justify-center md:justify-start <?php echo e($class_dir_post_req); ?>">
									<div class="w-full sm:w-auto mb-2 sm:mb-0">
										<div class="flex items-center flex-wrap h-24 w-24 md:h-20 md:w-20 lg:h-24 lg:w-24 mx-auto">
											<img class="rounded-full object-cover object-center mx-auto border border-gray-400 h-full max-w-full w-full" src="<?php echo e($exchange_postimg); ?>" />
										</div>
									</div>
									<div class="w-full sm:w-auto">
										<p class="text-base font-semibold text-gray-500 mb-2 capitalize"><?php echo e(__('p_myexchange.owner')); ?> : <?php echo e($exchange_post_user_info->name); ?>

										<?php if($exchange_user_deleted == 1): ?>
											<span class="bg-yellow-500 text-white rounded text-xs p-1 inline-block">User Deleted</span>
											<?php endif; ?>
										<?php if($exchange_user_blocked == 1): ?>
											<span class="bg-yellow-400 text-white rounded text-xs p-1 inline-block">User Blocked</span>
											<?php endif; ?>
										</p>
										<h3 class="text-xl mb-2 font-semibold hover:text-green-500"><?php echo e($exchange_post_info->title); ?>

											<?php
											$product_condition = App\Models\TblPost::get_product_condition($failed->exchanged_post_id);
											?>
											<?php if(!empty($product_condition)): ?>
											<span class="bg-gray-500 text-white rounded text-xs p-1 inline-block"><?php echo e($product_condition); ?></span>
											<?php endif; ?>
											<?php if(!empty($is_deleted_exchange)): ?>
											<span class="bg-red-500 text-white rounded text-xs p-1 inline-block">Post Deleted</span>
											<?php endif; ?>
											<?php if($check_post_package_exchange['expired'] == "Expired"): ?>
											<span class="bg-red-400 text-white rounded text-xs p-1 inline-block">Post Expired</span>
											<?php endif; ?>
										</h3>
										<?php if(!empty($exchange_post_info->locality)): ?>
										<p class="text-sm md:text-base font-semibold text-gray-500 mt-2">
										<!--<span class="mr-1"><img class="w-4" src="<?php echo e(URL::to('images/frontend/Group111.png')); ?>" /></span>-->
										<?php echo e($exchange_post_info->locality); ?></p>
										<?php endif; ?>
									</div>
								</div>
							</a>
						</div>
						<?php $currentUserId = auth()->user()->id; ?>
						<?php if($currentUserId == $failed->post_owner_id): ?>
						<?php 
						
							if(empty($is_deleted_from) && ($check_post_package_from['expired'] != "Expired") && ($from_user_deleted == 0) && ($from_user_blocked == 0) && ($exchange_user_deleted == 0) && ($exchange_user_blocked == 0) && empty($is_deleted_exchange) && ($check_post_package_exchange['expired'] != "Expired")){
						?>
						<div class="bg-green-50 p-2 mt-6">
							<div class="flex flex-wrap space-x-2 justify-between md:justify-start <?php echo e($class_dir_exch_btn); ?>">
								<?php if($failed->block_exchange == 1): ?>
								<button class="text-white bg-gray-500 hover:bg-gray-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2 mr-2 update_exchange_status" data-status="unblock" data-id="<?php echo e($failed->id); ?>"><?php echo e(__('p_myexchange.unblock exchange')); ?></button>
								<?php else: ?>
								<button class="text-white bg-gray-500 hover:bg-gray-700 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150 p-2 mr-2 update_exchange_status" data-status="block" data-id="<?php echo e($failed->id); ?>"><?php echo e(__('p_myexchange.block exchange')); ?></button>
								<?php endif; ?>
							</div>
						</div>
						<?php  } ?>
						<?php endif; ?>
					</div>
					<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
					<div class="bg-white sm:px-6 mt-4">
						<?php echo e($faileds->links()); ?>

					</div>
					<?php else: ?>
					<div class="w-full text-center">
						<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto rounded-full w-40" />
						<p class="text-lg md:text-xl lg:text-2xl pl-2 pb-1 font-bold"><?php echo e(__('p_myexchange.no data found')); ?>!</p>
					</div>
					<?php endif; ?>
					<?php endif; ?>
					<!----- failed requests end -->
					<script>
						$("#overlay").hide();
						$(".update_exchange_status").click(function(e) {
							var status = $(this).attr("data-status");
							var id = $(this).attr("data-id");
							$("#overlay").show();
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: "<?php echo e(route('update_exchange_status')); ?>",
								data: {
									status: status,
									exchange_id: id
								},
								success: function(data) {
									toastr.success(data.message);
									location.reload();
								}
							});
						});
					</script>
				</div>
			</div>
		</div>
		
		<div id="overlay"></div>

	</div>
	<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.frontendother', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/extra/postplugins/exchange/src/my-exchange.blade.php ENDPATH**/ ?>