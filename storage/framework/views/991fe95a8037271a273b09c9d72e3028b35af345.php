<?php
$dir_rtl =  App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
$settings = App\Models\Setting::get_logos();
$post_methods = App\Models\TblPostMethod::get_active_post_methods();
$get_country = session::get('GetCountry');
if (!empty($get_country)) {
	$country = App\Models\TblCountry::where('name', $get_country)->first();
	if(!empty($country)){
		$states = App\Models\TblState::where('country_id', $country->id)->get();
		foreach ($states as $state) {
			$getcity = App\Models\TblCity::where('state_id', $state->id)->first();
			if (!empty($getcity)) {
				$cities[] = $getcity->id;
				$post = App\Models\TblPost::where('city', $getcity->id)->exists();
				if ($post == true) {
					$postids = App\Models\TblPost::where('city', $getcity->id)->orderby('city', 'desc')->get();
					$count[$getcity->name] = count($postids);
				} else {
					$count = [];
				}
			}
		}
		$poplocationsget = (arsort($count));
		$pop_locations = array_slice($count, 0, 5);
	}else{
		$pop_locations = App\Models\TblPost::get_populor_loc();
	}
} else {
	$pop_locations = App\Models\TblPost::get_populor_loc();
}
if (!empty($get_country)) {
	$trending_locations = App\Models\TblPost::get_trending_loc();
} else {
	$trending_locations = App\Models\TblPost::get_trending_loc_default();
}
?>
<div class="w-full forest-bg-green flex flex-col" <?php echo e($class_dir); ?>>
	<div class="container px-4 mx-auto md:flex md:flex-row">
		<?php if ($dir_rtl == "false") { ?>
			<!--Begin EN Alignment-->
			<div class="py-2 pt-6 md:pt-10 md:w-4/12">
				<div class="footer_logo_section w-full lg:w-3/4">
					<div class="footer_logo text-center md:text-left">
						<img class="inline-block w-48 lg:w-min h-16 object-contain p-2" src="<?php echo e(URL::to('storage/'.$settings['logo'])); ?>" alt="logo">
					</div>
					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<a class="banner_adv text-white px-2  focus:outline-none rounded py-2 btn-gradiant  rounded-sm mt-8 w-full  ease-linear transition-all duration-500" href="<?php echo e(URL::to('/pages/banner-ads')); ?>">
								<?php echo e(__('messages.banner advertise')); ?>

							</a>
					<?php }
					} ?>
					<ul class="w-full mt-8 text-center md:text-left">
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" aria-label="Facebook" class="text-base font-semibold text-white"></a><a href="https://www.facebook.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/fb.webp')); ?>" alt="facebook"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" aria-label="Instagram" class="text-base font-semibold text-white"></a><a href="https://www.instagram.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/insta.webp')); ?>" alt="instagram"></a></li>
						<li class="inline-block px-3 lg:pr-10 lg:pl-0"><a href="#" aria-label="Twitter" class="text-base font-semibold text-white"></a><a href="https://www.twitter.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/twitter.webp')); ?>" alt="twitter"></a></li>
					</ul>
				</div>
			</div>
			<!--End EN Alignment-->
		<?php } else { ?>
			<!--Begin AR Alignment-->
			<div class="py-2 pt-6 md:pt-10 md:w-4/12">
				<div class="footer_logo_section w-full lg:w-3/4">
					<div class="footer_logo text-center md:text-right">
						<img class="inline-block w-48 lg:w-min" src="<?php echo e(URL::to('storage/'.$settings['logo'])); ?>" alt="logo">
					</div>
					<?php
					if (!empty($post_methods)) {
						$check_banner_ads = $post_methods->pluck('name')->toArray();
						if (in_array("bannerads", $check_banner_ads)) { ?>
							<a href="<?php echo e(URL::to('/pages/banner-ads')); ?>" class="block w-full">
								<button class="text-white px-2 focus:outline-none rounded py-2 btn-gradiant rounded-sm mt-8 w-full ease-linear transition-all duration-500">
									<?php echo e(__('messages.banner advertise')); ?>

								</button>
							</a>
					<?php }
					} ?>
					<ul class="w-full mt-8 text-center md:text-right">
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" aria-label="Facebook" class="text-base font-semibold text-white"></a><a href="https://www.facebook.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/fb.png')); ?>" alt="facebook"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" aria-label="Instagram" class="text-base font-semibold text-white"></a><a href="https://www.instagram.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/insta.png')); ?>" alt="instagram"></a></li>
						<li class="inline-block px-3 lg:pl-10 lg:pr-0"><a href="#" aria-label="Twitter" class="text-base font-semibold text-white"></a><a href="https://www.twitter.com/" target="_blank"><img src="<?php echo e(URL::to('images/frontend/twitter.png')); ?>" alt="twitter"></a></li>
					</ul>
				</div>
			</div>
			<!--END AR Alignment-->
		<?php } ?>
		<div class="py-2 pt-6 md:pt-10 md:w-3/12">
			<p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 "><?php echo e(__('messages.popular locations')); ?></p>
			<?php if(!empty($get_country)): ?>
			<ul>
				<?php $__currentLoopData = $pop_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$pop_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
				$highest = min($pop_locations);
				if ($pop_location > $highest) {
					$highest = $pop_location;
				}
				?>
				<li class="pb-1.5"><a href="" class="text-base font-semibold text-white"><?php echo e($key); ?></a></li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
			<?php else: ?>
			<ul>
				<?php $__currentLoopData = $pop_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pop_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
				$popular_loc = URL::to('/' . str_replace(' ', '_', strtolower($pop_location)) . "?loc=" . $pop_location . "&country=&state=" . $pop_location . "&city=");
				?>
				<li class="pb-1.5"><a href="<?php echo e($popular_loc); ?>" class="text-base font-semibold text-white"><?php echo e($pop_location); ?></a></li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
			<?php endif; ?>
		</div>
		<div class="py-2 pt-6 md:pt-10 md:w-3/12">
			<p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 "><?php echo e(__('messages.trending locations')); ?></p>
			<?php if(!empty($get_country)): ?>
			<ul>
				<?php $__currentLoopData = $trending_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trending_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
				$trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($get_country)) . "?loc=" . $get_country . "&country=&state=" . $get_country . "&city=" . $trending_location['city_name'] . "");
				?>
				<li class="pb-1.5"><a href="<?php echo e($trending_loc); ?>" class="text-base font-semibold text-white"><?php echo e($trending_location['city_name']); ?></a></li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
			<?php else: ?>
			<ul>
				<?php $__currentLoopData = $trending_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trending_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
				<?php
				$trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($get_country)) . "?loc=" . $get_country . "&country=&state=" . $get_country . "&city=" . $trending_location . "");
				?>
				<li class="pb-1.5"><a href="<?php echo e($trending_loc); ?>" class="text-base font-semibold text-white"><?php echo e($trending_location); ?></a></li>
				<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
			</ul>
			<?php endif; ?>
		</div>
		<div class="py-2 pt-6 md:pt-10 md:w-3/12">
			<p class="text-xl poppins-600 text-white pb-1 uppercase inline-block border mb-6 border-b-2 border-t-0 border-r-0 border-l-0 "><?php echo e(__('messages.about us')); ?></p>
			<ul>
				<li class="pb-1.5"><a href="<?php echo e(URL::to('/contact-us')); ?>" class="text-base font-semibold text-white"><?php echo e(__('messages.contact us')); ?></a></li>
				<li class="pb-1.5"><a href="<?php echo e(URL::to('/pages/help')); ?>" class="text-base font-semibold text-white"><?php echo e(__('messages.help')); ?></a></li>
				<!-- <li class="pb-1.5"><a href="<?php echo e(URL::to('/packages')); ?>" class="text-base font-semibold text-white"><?php echo e(__('messages.packages')); ?></a></li> -->
				<li class="pb-1.5"><a href="<?php echo e(URL::to('/pages/terms-conditions')); ?>" class="text-base font-semibold text-white"><?php echo e(__('messages.terms & conditions')); ?></a></li>
				<li class="pb-1.5"><a href="<?php echo e(URL::to('/pages/privacy-policy')); ?>" class="text-base font-semibold text-white"><?php echo e(__('messages.privacy policy')); ?></a></li>
			</ul>
		</div>
	</div>
	<div class="text-center">
		<div class="container mx-auto border border-t-2 border-b-0 border-l-0 border-r-0 mt-4 lg:mt-8">
			<p class="text-base py-6 text-white capitalize"> <?php echo e(__('messages.copywrite')); ?> © <span id="get-current-year"></span> <a href="#">Justreuesd</a></p>
		</div>
	</div>
</div>
<?php
// Bottom position tracking code
$advertising = App\Models\TblAdvertising::where('position', 'bottom')->where('active', '1')->first();
if (!empty($advertising)) {
	echo $advertising->tracking_code;
}
?>
<!-- Footer end -->
<!-- fixed header -->
<script>
	$(window).scroll(function() {
		var bodyheight = $(document).scrollTop() + 100;
		if (bodyheight >= 250) {
			$(".navbar-expand-lg").addClass("fixed");
		} else {
			$(".navbar-expand-lg").removeClass("fixed");
		}
	});
</script>
<?php
if (!empty(auth()->user())) {
	$auth_id = auth()->user()->id;
} else {
	$auth_id = "";
}
?>
<script type="text/javascript">
	// notification count update area
	setInterval(function() {
		var authId = "<?php echo e($auth_id); ?>";
		if (authId != "" || authId != false) {
			// console.log("no id");
			$.ajax({
				type: 'get',
				dataType: 'json',
				url: "<?php echo e(URL::to('get-notification-count')); ?>",
				success: function(data) {
					$(".notification_count").html(data.count);
					$(".mbl_notification_count").html("(" + data.count + ")");
					console.log("count: " + data.count);
				},
				error: function(data, error) {
					$(".notification_count").html(0);
					$(".mbl_notification_count").html("(0)");
				}
			});
			$.ajax({
				type: 'get',
				dataType: 'json',
				url: "<?php echo e(URL::to('get-unread-chat-count')); ?>",
				success: function(data) {
					$(".chat_count").html(data.count);
					$(".mbl_chat_count").html("(" + data.count + ")");
					// console.log("chat-count: "+data.count);
				},
				error: function(data, error) {
					$(".chat_count").html(0);
					$(".mbl_chat_count").html("(0)");
				}
			});
		}
	}, 15000);
	// notification count update area
	// Favourate product process begin
	$.ajaxSetup({
		headers: {
			'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
		}
	});
	$("body").on("click", ".save_favourate", function(e) {
		var val = $(this).attr("data-fav-post-id");
		$.ajax({
			type: 'POST',
			dataType: 'json',
			url: "<?php echo e(route('savepost')); ?>",
			data: {
				post_id: val
			},
			success: function(data) {
				if (data.result == "failed" && data.flag == "0") {
					toastr.warning(data.message);
				} else if (data.result == "success" && data.flag == "1") {
					$("#favourate_post_id_" + val + " i").removeClass("fa fa-heart-o");
					$("#favourate_post_id_" + val + " i").addClass("fa fa-heart");
					toastr.success(data.message);
				} else {
					$("#favourate_post_id_" + val + " i").removeClass("fa fa-heart");
					$("#favourate_post_id_" + val + " i").addClass("fa fa-heart-o");
					toastr.success(data.message);
				}
			}
		});
	});
</script>
<!-- Favourate product process end -->
<script>
	// for taba modal alert slidover
	const application = Stimulus.Application.start();
	application.register('dropdown', TailwindcssStimulusComponents.Dropdown);
	application.register('modal', TailwindcssStimulusComponents.Modal);
	application.register('tabs', TailwindcssStimulusComponents.Tabs);
	application.register('popover', TailwindcssStimulusComponents.Popover);
	application.register('alert', TailwindcssStimulusComponents.Alert);
	application.register('slideover', TailwindcssStimulusComponents.Slideover);
</script>
<?php echo $__env->yieldPushContent('modals'); ?>
<script>
	/* Make dynamic date appear */
	(function() {
		if (document.getElementById("get-current-year")) {
			document.getElementById(
				"get-current-year"
			).innerHTML = new Date().getFullYear();
		}
	})();
	/* Function for opning navbar on mobile */
	function toggleNavbar(collapseID) {
		document.getElementById(collapseID).classList.toggle("hidden");
		document.getElementById(collapseID).classList.toggle("block");
	}
	/* Function for dropdowns */
	function openDropdown(event, dropdownID) {
		let element = event.target;
		while (element.nodeName !== "A") {
			element = element.parentNode;
		}
		Popper.createPopper(element, document.getElementById(dropdownID), {
			placement: "bottom-start",
		});
		document.getElementById(dropdownID).classList.toggle("hidden");
		document.getElementById(dropdownID).classList.toggle("block");
	}
</script>
<!--for tab design, dropdown, tabs, popover-->
<!-- begin - force logout if user if user blocked -->
<?php
$isBlocked = "0";
if (!empty(auth()->user())) {
	$isBlocked = auth()->user()->is_blocked;
}
?>
<script>
	(function() {
		var blocked = '<?php echo e($isBlocked); ?>';
		if (blocked == "1") {
			document.getElementById('logout_trig').click();
		}
	})();
</script>
<!-- end - force logout if user if user blocked -->
<?php
//from paypal success(/)failure
$payment_nofy = Session('payment_nofy');
Session::forget('payment_nofy');
?>
<script type="text/javascript">
	var payment_nofy = "<?php echo e($payment_nofy); ?>";
	if (payment_nofy != "") {
		toastr.info(payment_nofy);
	}
</script>
<style>
	.pac-logo:after {
		display: none;
	}
</style>
<!-- <script src="<?php echo e(URL::to('js/allmin.js')); ?>"></script> -->
<!-- <script src="<?php echo e(URL::to('js/jquery-ui.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/tailwindcss-stimulus-components.umd.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/alpine.min.js')); ?>" defer></script>
<script src="<?php echo e(URL::to('js/toastr.min.js')); ?>"></script> -->
<script src="<?php echo e(URL::to('js/slick.min.js')); ?>"></script> 
<!-- <script>
	$(document).ready(function() {
		setTimeout(function() {
			$('footer').append('<script async src="<?php echo e(URL::to('js/slick.min.js')); ?>"><\/script>')
		}, 3000);
	});
</script> -->
<!-- <script src="<?php echo e(URL::to('js/stimulus.umd.js')); ?>"></script> -->
<!-- <script src="<?php echo e(URL::to('js/tailwind_popper.js')); ?>" defer></script> -->
<!-- <script src="<?php echo e(URL::to('js/lazysizes.min.js')); ?>"></script> -->
<script>
    setTimeout(function() {
        var script = document.createElement('script');
        script.src = "<?php echo e(URL::to('js/lazysizes.min.js')); ?>";
        document.body.appendChild(script);
    }, 3000); // 5000ms = 5 seconds
</script>
<script src="<?php echo e(URL::to('js/lazyload_js_css.js')); ?>"></script>
<script src="<?php echo e(URL::to('js/common.js')); ?>"></script>
<script>
	jQuery.event.special.touchstart = {
		setup: function(_, ns, handle) {
			this.addEventListener("touchstart", handle, {
				passive: !ns.includes("noPreventDefault")
			});
		}
	};
	jQuery.event.special.touchmove = {
		setup: function(_, ns, handle) {
			this.addEventListener("touchmove", handle, {
				passive: !ns.includes("noPreventDefault")
			});
		}
	};
	jQuery.event.special.wheel = {
		setup: function(_, ns, handle) {
			this.addEventListener("wheel", handle, {
				passive: true
			});
		}
	};
	jQuery.event.special.mousewheel = {
		setup: function(_, ns, handle) {
			this.addEventListener("mousewheel", handle, {
				passive: true
			});
		}
	};
</script>
<?php echo $__env->make('livewire.common.seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('livewire.common.location_default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/apm/frontend_footer.blade.php ENDPATH**/ ?>