<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AdminLayout::class, []); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
    <div class="relative md:pt-28 pb-32 pt-12">
        <div class="px-4 md:px-10 mx-auto w-full">
            <div class="bg-white p-5 rounded-md">
            <button class="bg-indigo-500 hover:bg-indigo-700 text-white font-bold py-2 px-4 rounded-sm my-3"><a href="<?php echo e(route('features-map-show')); ?>">Back</a></button>
                <!-- Form to add features mapping -->
                <form action="<?php echo e(route('features-map-store')); ?>" method="POST">
                    <?php echo csrf_field(); ?>

                    <!-- Category Dropdown -->
                    <label class="inline-block w-2/12">Category</label>
                    <select id="selected_category" name="cat_id" class="selectpicker w-2/4 h-14 px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black mb-3">
                        <option hidden disabled selected value=""><?php echo e(__('p_myads.please select')); ?></option>
                        <?php
                        $traverse = function ($categorylist, $prefix = '') use (&$traverse) {
                            foreach ($categorylist as $category) {
                                $isparent = (is_null($category->parent_id)) ? "disabled" : "";
                                $catname = $prefix . '| ' . $category->title;
                                $catid = $category->id;
                                echo '<option value="' . $catid . '" ' . $isparent . '>' . $catname . '</option>';
                                $traverse($category->children, $prefix . '--');
                            }
                        };
                        $traverse($categorylist);
                        ?>
                    </select>
                    <!-- Category validation error -->
                    <?php $__errorArgs = ['cat_id'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-500 text-sm mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- Feature Title and Items (Dynamic) -->
                    <div id="feature-rows">
                        <div class="feature-row">
                            <div class="mb-3">
                                <div class="w-2/12 inline-block">Feature Title</div>
                                <div class="inline-block w-2/4">
                                    <input type="text" name="feature_title[]" class="form-input rounded-md shadow-sm block mt-1 w-full">
                                </div>
                            </div>
                            <div class="mb-3">
                                <div class="inline-block w-2/12 align-top">Feature Items</div>
                                <div class="inline-block w-2/4">
                                    <textarea name="feature_items[]" class="form-input rounded-md shadow-sm block mt-1 w-full"></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Validation errors for dynamic fields -->
                    <?php $__errorArgs = ['feature_title.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-500 text-sm mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
                    <?php $__errorArgs = ['feature_items.*'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                        <div class="text-red-500 text-sm mt-2"><?php echo e($message); ?></div>
                    <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>

                    <!-- Button to add more feature rows -->
                    <div>
                        <button type="button" id="add-feature-row" class="bg-blue-500 text-white p-2 inline-flex items-center">
                            <span class="mr-2">+</span> Add More
                        </button>
                    </div>

                    <!-- Submit Button -->
                    <div class="text-center">
                        <button type="submit" class="bg-green-500 mt-2 text-white p-2">Create Map</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- JavaScript to handle dynamic rows -->
    <script>
        document.getElementById('add-feature-row').addEventListener('click', function () {
            const featureRows = document.getElementById('feature-rows');
            const newRow = document.createElement('div');
            newRow.classList.add('feature-row');
            newRow.innerHTML = `
                <div class="mb-3">
                    <div class="w-2/12 inline-block">Feature Title</div>
                    <div class="inline-block w-2/4">
                        <input type="text" name="feature_title[]" class="form-input rounded-md shadow-sm block mt-1 w-full">
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block w-2/12 align-top">Feature Items</div>
                    <div class="inline-block w-2/4">
                        <textarea name="feature_items[]" class="form-input rounded-md shadow-sm block mt-1 w-full"></textarea>
                    </div>
                </div>
            `;
            featureRows.appendChild(newRow);
        });
    </script>
 <?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/features/featuremap.blade.php ENDPATH**/ ?>