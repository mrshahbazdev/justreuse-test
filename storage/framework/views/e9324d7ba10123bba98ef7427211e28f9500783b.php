	<?php
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";

	$class_dir_text_pad_lr = ($dir_rtl == "true") ? 'lg:pr-12 text-right' : 'lg:pl-12 text-left';
	?>

	<div class="w-full float-left" <?php echo e($class_dir); ?>>

		<?php
		$get_meta = App\Models\TblOtherpage::get_meta('favourite-ads');
		$meta_title = (!empty($get_meta->meta_title) ? $get_meta->meta_title : "");
		$meta_keywords = (!empty($get_meta->meta_key) ? $get_meta->meta_key : "");
		$meta_description = (!empty($get_meta->meta_description) ? $get_meta->meta_description : "");

		?>

		<?php if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description)): ?>
		<?php $__env->startSection('meta_title', $meta_title); ?>
		<?php $__env->startSection('meta_keywords', $meta_keywords); ?>
		<?php $__env->startSection('meta_description', $meta_description); ?>
		<?php endif; ?>

		<?php if($message = Session::get('message')): ?>
		<div class="text-white px-6 py-4 border-0 rounded relative mb-4 bg-yellow-500 z-50">
			<span class="text-xl inline-block mr-5 align-middle"><i class="fas fa-bell"></i></span>
			<span class="inline-block align-middle mr-8"><b class="capitalize"></b> <?php echo e($message); ?></span>
			<button class="absolute bg-transparent text-2xl font-semibold leading-none right-0 top-0 mt-4 mr-6 outline-none focus:outline-none" onclick="closeAlert(event)"><span>×</span></button>
		</div>
		<?php endif; ?>

		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<h1 class="text-xl md:text-2xl font-bold text-black my-4 sm:my-6 lg:my-8 uppercase"><?php echo e(__('p_favourite.favourite ads')); ?></h1>
			</div>
		</div>

		<div class="w-full bg-gray-100 h-48 float-left">

		</div>

		<div class="w-full float-left">
			<div class="container mx-auto px-4">
				<div class="p-5 w-full bg-white shadow-lg float-left mb-8 sm:mb-12 md:mb-16 lg:mb-20 lg:p-12 relative -mt-36">

					<!-- search and delete check box -->

					<?php if($dir_rtl =="false"): ?>
					<div class="text-sm font-bold w-full items-center md:flex sm:px-4 mb-4 md:mb-8 lg:mb-12">
						<?php if(!empty($data[0])): ?>
						<input class="w-4 h-4 inline-block align-middle" type="checkbox" id="master" />
						<label for="master" class="text-sm md:text-lg lg:text-xl text-gray-800 font-semibold inline-block align-middle">&nbsp; <?php echo e(__('p_favourite.select all')); ?> |</label>
						<button class="multiple_del items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-sm lg:text-lg text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 ml-5 float-right"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i><?php echo e(__('p_favourite.delete')); ?></button>
						<?php endif; ?>
						<!-- search -->
						<div class="md:flex flex-row flex-wrap items-center md:ml-auto mt-4 md:mt-0">
							<div class="relative">
								<div class="absolute top-3 left-3"> <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
								<input type="text" wire:model="search" class="px-10 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-200 rounded-md h-11" placeholder="<?php echo e(__('p_favourite.search here')); ?>">
							</div>
						</div>
					</div>
					<?php else: ?>
					<div class="text-sm font-bold w-full items-center md:flex sm:px-4 mb-4 md:mb-8 lg:mb-12">
						<?php if(!empty($data[0])): ?>
						<input class="w-4 h-4 inline-block align-middle" type="checkbox" id="master" />
						<label for="master" class="text-sm md:text-lg lg:text-xl text-gray-800 font-semibold inline-block align-middle">&nbsp; <?php echo e(__('p_favourite.select all')); ?> |</label>
						<button class="multiple_del items-center px-2 py-1 bg-red-500 border border-transparent rounded-md text-sm lg:text-lg text-white tracking-widest hover:bg-red-700 active:bg-red-900 focus:outline-none focus:border-red-900 focus:shadow-outline-red disabled:opacity-25 transition ease-in-out duration-150 mr-5 float-left"><i class="fa fa-trash-o mr-1" aria-hidden="true"></i><?php echo e(__('p_favourite.delete')); ?></button>
						<?php endif; ?>
						<!-- search -->
						<div class="md:flex flex-row flex-wrap items-center md:mr-auto mt-4 md:mt-0">
							<div class="relative">
								<div class="absolute top-3 left-3"> <i class="fa fa-search text-gray-400 z-20 hover:text-gray-500"></i> </div>
								<input type="text" wire:model="search" class="px-10 py-2 placeholder-gray-400 text-gray-500 rounded text-sm md:text-lg shadow outline-none focus:outline-none focus:shadow-outline w-full border border-gray-200 rounded-md h-11" placeholder="<?php echo e(__('p_favourite.search here')); ?>">
							</div>
						</div>
					</div>
					<?php endif; ?>

					<!-- end -->

					<div class="w-full overflow-auto m-0 p-0 bg-white">
						<?php if(!empty($data[0])): ?>
						<table class="pflex-auto w-full">
							<thead>
								<tr>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold "></th>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold  "><?php echo e(__('p_favourite.photo')); ?></th>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold <?php echo e($class_dir_text_pad_lr); ?>"><?php echo e(__('p_favourite.ads details')); ?></th>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold "><?php echo e(__('p_favourite.posted on')); ?></th>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold "><?php echo e(__('p_favourite.price')); ?></th>
									<th class="p-2 border border-gray-300 md:border-0 text-sm md:text-lg lg:text-2xl text-gray-800 font-semibold "></th>
								</tr>
							</thead>

							<tbody>
								<?php $i = 0; ?>
								<?php $__currentLoopData = $data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $row): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
								<?php
								$i++;
								$imgUrlfinal = App\Models\TblChat::getPostImgForList($row['id']);
								$posted_on = date('d M Y', strtotime($row['created_at']));
								$viewcount = App\Models\TblPostInsight::views_count($row['id']);
								$currency_symbol = App\Models\TblPost::get_post_currency($row['currency_id']);
								// get locality & city 
								$final_city_name = !empty($row["locality"]) ? $row["locality"] : $row["city_name"];
								$slug = App\Models\TblPost::get_post_slug($row["slug"]);

								?>
								<tr>
									<td class="p-2 py-7 border border-gray-300 md:border-0 text-center text-xs md:text-sm text-gray-500 font-medium">
										<div class="sm:px-2">
											<input type="checkbox" class="del_check w-4 h-4" id="del_chk_<?php echo e($i); ?>" id="del_chk" data-id="<?php echo e($row['fav_id']); ?>" data-delete-row-id="<?php echo e($i); ?>" />
										</div>
									</td>

									<td class="py-2 lg:py-7 border border-gray-300 md:border-0 " align="center">
										<a href="<?php echo e($slug); ?>" target="_blank">
											<img class="w-16 h-16 lg:w-36 lg:h-36 object-contain " src="<?php echo e($imgUrlfinal); ?>">
										</a>
									</td>

									<td class="border border-gray-300 md:border-0 py-2 lg:py-7 p-2 <?php echo e($class_dir_text_pad_lr); ?>">
										<p><strong class=" inline-block pb-1 text-sm md:text-lg font-semibold"><a class="text-gray-900" href="<?php echo e($slug); ?>" target="_blank"><?php echo e($row["title"]); ?></a></strong></p>
										<p><strong class="inline-block pb-1 text-xs md:text-sm text-gray-500 font-medium"><span class="text-green-500"><i class="fa fa-eye" aria-hidden="true"></i></span> <?php echo e($viewcount); ?> <?php echo e(__('p_myads.views')); ?> </strong></p>
										<p><strong class="inline-block pb-1 text-xs md:text-sm text-gray-500 font-medium"><span class="text-green-500"><i class="fa fa-map-marker" aria-hidden="true"></i> </span><?php echo e($final_city_name); ?></strong></p>
									</td>

									<td class="py-2 lg:py-7 p-2 border border-gray-300 md:border-0 text-center text-xs md:text-sm text-gray-500 font-medium"><?php echo e($posted_on); ?></td>

									<td class="p-2 py-7 border border-gray-300 md:border-0 text-center text-xs md:text-sm text-gray-500 font-medium"><?php echo $currency_symbol[0]; ?><?php echo e($row["price"]); ?></td>

									<td class="p-2 py-7 border border-gray-300 md:border-0 text-center text-xs md:text-sm text-gray-500 font-medium">
										<button wire:click="destroy('<?php echo e($row['id']); ?>')" onclick="confirm('Are you sure you want to delete?') || event.stopImmediatePropagation()" class="items-center px-2 py-1  text-lg md:text-xl lg:text-2xl   transition ease-in-out duration-150 ml-0 lg:ml-5"><i class="fa fa-trash-o" aria-hidden="true"></i></button>
									</td>

								</tr>

								<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
							</tbody>
						</table>
						<?php else: ?>
						<div class="w-full text-center">
							<img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
							<p class="text-2xl pl-2 pb-1 font-bold mb-8 sm:mb-12"><?php echo e(__('p_favourite.no data found')); ?></p>
						</div>
						<?php endif; ?>
					</div>
					<?php echo e($data->links()); ?>

					<!--</div>-->
				</div>
			</div>
		</div>

		<script>
			$.ajaxSetup({
				headers: {
					'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
				}
			});
			$(document).ready(function() {
				// delete all record code.. 
				$('#master').on('click', function(e) {
					if ($(this).is(':checked', true)) {
						$(".del_check").prop('checked', true);
					} else {
						$(".del_check").prop('checked', false);
					}
				});
				$('.multiple_del').on('click', function(e) {
					var allVals = [];
					$(".del_check:checked").each(function() {
						allVals.push($(this).attr('data-id'));
					});
					if (allVals.length <= 0) {
						toastr.warning("Please select row.");
					} else {
						var check = confirm("You want to delete this row?");
						if (check == true) {
							var join_selected_values = allVals.join(",");
							$.ajax({
								type: 'POST',
								dataType: 'json',
								url: "<?php echo e(route('delete_fav')); ?>",
								data: {
									ids: join_selected_values
								},
								success: function(data) {
									alert(data.message);
									window.location.reload();
								}
							});
						}
					}
				});
			});
			// end delete all..
		</script>
	</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/front-favourite.blade.php ENDPATH**/ ?>