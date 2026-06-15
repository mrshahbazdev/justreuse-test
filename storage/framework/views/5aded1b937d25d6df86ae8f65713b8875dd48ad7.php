<?php
$dir_rtl = App\Models\Setting::is_dir_rtl();
$class_dir = ($dir_rtl == "true") ? 'dir=rtl' : "";
$settings = App\Models\Setting::get_logos();
$post_methods = App\Models\TblPostMethod::get_active_post_methods();
$get_country = session::get('GetCountry');

if (!empty($get_country)) {
	$country = App\Models\TblCountry::where('name', $get_country)->first();
	if (!empty($country)) {
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
	} else {
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

<footer class="text-black" style="background:#fffbfa !important;">
  <div class="max-w-7xl mx-auto px-6 py-12">
    <!-- 4 Equal Columns -->
    <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-4 gap-8" >

      <!-- Popular Categories / Locations -->
      <div>
        <h4 class="font-semibold mb-4"><?php echo e(__('messages.popular locations')); ?></h4>
        <ul class="space-y-2 text-sm">
          <?php if(!empty($get_country)): ?>
            <?php $__currentLoopData = $pop_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key=>$pop_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><a href="#" class="hover:text-primary transition"><?php echo e($key); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            <?php $__currentLoopData = $pop_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $pop_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
              $popular_loc = URL::to('/' . str_replace(' ', '_', strtolower($pop_location)) . "?loc=" . $pop_location . "&country=&state=" . $pop_location . "&city=");
              ?>
              <li><a href="<?php echo e($popular_loc); ?>" class="hover:text-primary transition"><?php echo e($pop_location); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        </ul>
      </div>

      <!-- Trending Searches -->
      <div>
        <h4 class="font-semibold mb-4"><?php echo e(__('messages.trending locations')); ?></h4>
        <ul class="space-y-2 text-sm">
          <?php if(!empty($get_country)): ?>
            <?php $__currentLoopData = $trending_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trending_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <?php
              $trending_loc = URL::to('/' . str_replace(' ', '_', strtolower($get_country)) . "?loc=" . $get_country . "&country=&state=" . $get_country . "&city=" . $trending_location['city_name']);
              ?>
              <li><a href="<?php echo e($trending_loc); ?>" class="hover:text-primary transition"><?php echo e($trending_location['city_name']); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php else: ?>
            <?php $__currentLoopData = $trending_locations; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trending_location): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
              <li><a href="#" class="hover:text-primary transition"><?php echo e($trending_location); ?></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
          <?php endif; ?>
        </ul>
      </div>

      <!-- About Us -->
      <div>
        <h4 class="font-semibold mb-4"><?php echo e(__('messages.about us')); ?></h4>
        <ul class="space-y-2 text-sm">
          <li><a href="<?php echo e(URL::to('/contact-us')); ?>" class="hover:text-primary transition"><?php echo e(__('messages.contact us')); ?></a></li>
          <li><a href="<?php echo e(URL::to('/pages/help')); ?>" class="hover:text-primary transition"><?php echo e(__('messages.help')); ?></a></li>
          <li><a href="<?php echo e(URL::to('/pages/terms-conditions')); ?>" class="hover:text-primary transition"><?php echo e(__('messages.terms & conditions')); ?></a></li>
          <li><a href="<?php echo e(URL::to('/pages/privacy-policy')); ?>" class="hover:text-primary transition"><?php echo e(__('messages.privacy policy')); ?></a></li>
        </ul>
      </div>

      <!-- Social + Back to top -->
      <div class="flex flex-col items-start">
        <h4 class="font-semibold mb-4"><?php echo e(__('messages.follow us')); ?></h4>
        <div class="flex gap-3 mb-6">
          <a href="#" class="w-10 h-10 flex items-center justify-center border rounded-full hover:bg-blue-200 transition">
            <i class="fab fa-facebook-f"></i>
          </a>
          <a href="#" class="w-10 h-10 flex items-center justify-center border rounded-full hover:bg-pink-200 transition">
            <i class="fab fa-instagram"></i>
          </a>
          <a href="#" class="w-10 h-10 flex items-center justify-center border rounded-full hover:bg-blue-200 transition">
            <i class="fab fa-linkedin-in"></i>
          </a>
        </div>
      </div>

    </div>
  </div>

  <!-- Bottom Bar -->
  <div class="text-gray-200 py-4 text-center text-sm" style="background:#024d02;">
    © <?php echo date('Y'); ?> <span class="font-semibold">JustReused</span>. All rights reserved.
  </div>
</footer>

<?php echo $__env->make('livewire.common.seo', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('livewire.common.location_default', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/layouts/footernew.blade.php ENDPATH**/ ?>