<div class="relative md:pt-28 pb-32 pt-12">


	<div class="px-4 md:px-10 mx-auto w-full">


		<div class="bg-white p-2 mb-2 rounded-md">
			<?php if($ancestors!=""): ?>
			<?php
			echo '<ul class="flex text-orange-400 text-sm lg:text-base">';
			echo "<li><a href='" . URL::to('admin/category/') . "'>Category <i class='fa fa-angle-double-right'></i></a></li>";
			$i = 1;
			//echo $de = $ancestors->depth(request()->segment(2));
			foreach ($ancestors as $r) {
				$totcount = $ancestors->count();
				$catname = "&nbsp;" . $r->title;

				if ($i != $totcount) {
					$url = URL::to('admin/category/' . $r->uuid . '/subcategories');
					echo '<li><a href="' . $url . '">' . $catname . ' <i class="fa fa-angle-double-right"></i> </a></li>';
				} else {
					echo '<li class="text-black">' . $catname . '</li>';
				}
				$i++;
			}
			echo "</ul>";
			?>
			<?php endif; ?>
		</div>

		<div class="mb-4 p-4 border-b-2 border-gray-800 bg-white rounded-md">
			<form wire:submit.prevent="update(Object.fromEntries(new FormData($event.target)))">

				<div>
					<div class="inline-block">Category </div>
					<div class="inline-block w-2/4">
						<input type="text" name="cattitle" readonly="readonly" id="id_cattitle" value="<?php echo e($data['cat_title']); ?>" class="form-input rounded-md shadow-sm block mt-1 w-full">
						<input type="hidden" name="catid" id="id_catid" value="<?php echo e($data['cat_id']); ?>" class="form-input rounded-md shadow-sm block mt-1 w-full">
						<input type="hidden" name="cathtml" id="id_cathtml" value="<?php echo e($data['html']); ?>" />
						<input type="hidden" name="catfieldcount" id="id_catfieldcount" value="<?php echo e($data['field_count']); ?>" />
					</div>
					<div class="inline-block"><button id="post_json_insert" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Update</button>
						<div id="post_cancel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Cancel</div>
					</div>
				</div>
			</form>
		</div>
		<p class="text-red-600 text-sm">Note: Don't add same label name in custom fields *</p>
		<div class="demo-header" style="display:none"><label for="toggleBootstrap">Toggle Bootstrap <input type="checkbox" id="toggleBootstrap" /></label>
			<div>Language: <select id="setLanguage">
					<option value="en-US">English (US)</option>
				</select></div>
		</div>
		<div class="content">
			<div id="build-wrap"></div>
		</div>
		<?php echo $__env->make('livewire.admin.customfields.js_css', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
	</div>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/customfields/edit.blade.php ENDPATH**/ ?>