<?php if (isset($component)) { $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040 = $component; } ?>
<?php $component = $__env->getContainer()->make(App\View\Components\AdminLayout::class, []); ?>
<?php $component->withName('admin-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php $component->withAttributes([]); ?>
<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <div class="bg-white p-6 rounded-lg shadow-lg">
            <?php if(session()->has('message')): ?>
                <div class="bg-teal-100 border-t-4 border-teal-500 rounded-b text-teal-900 px-4 py-3 shadow-md mb-6" role="alert">
                    <p class="text-sm"><?php echo e(session('message')); ?></p>
                </div>
            <?php endif; ?>

            <!-- Import and Export Buttons -->
            <form action="<?php echo e(route('features.store')); ?>" method="POST" enctype="multipart/form-data" class="space-y-6">
                <?php echo csrf_field(); ?>
                <!-- Category Selection -->
                <div class="space-y-2">
                    <label for="selected_category" class="inline-block w-2/12">Category</label>
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
                </div>

                <!-- Brand Selection (Initially Hidden) -->
                <div class="space-y-2">
                    <label for="brand_select" class="brand_select w-2/12 inline-block hidden">Brand</label>
                    <select name="brand_id" id="brand_select" class="inline-block w-2/4 hidden">
                        <option>Please select value</option>
                    </select>
                </div>

                <!-- File Input -->
                <div class="space-y-2">
                    <label for="file" class="w-2/12 inline-block">Import File</label>
                    <input type="file" name="file" id="file" class="inline-block w-2/4">
                </div>

                <!-- Submit Button -->
                <div class="text-center">
                    <button type="submit" class="bg-green-500 hover:bg-green-700 text-white font-semibold py-2 px-4 rounded-md transition duration-200 ease-in-out">Import Features</button>
                </div>
            </form>
        </div>
    </div>
</div>
<script>
    $('#selected_category').on('change', function() {
        let selectedValue = $(this).val();  // Get the selected value
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
        // Make an AJAX request to the get_brand URL
        $.ajax({
            url: '/get_brand', // Your endpoint to handle the request
            type: 'POST',       // You can use POST if needed
            data: { cat_id: selectedValue },  // Passing the selected cat_id
            success: function (response) {

                var result = response.data;

            // Check if result is empty
            if (result.length === 0) {
                // Hide the brand select dropdown if no brands are returned
                $('#brand_select').hide();
                $('.brand_select').hide();
            } else {
                // Show and populate the brand select dropdown if brands are present
                $('#brand_select').empty();
                $('#brand_select').append('<option value="" hidden>Please Select Brand</option>');

                // Loop through the response data and populate the options
                $.each(result, function (index, brand) {
                    $('#brand_select').append('<option value="' + brand.id + '">' + brand.key + '</option>');
                });

                // Show the dropdown if it was hidden
                $('#brand_select').show();
                $('.brand_select').show();
            }
            },
            error: function (xhr, status, error) {
                console.error("Error occurred: " + error);
            }
        });
    });
</script>
 <?php if (isset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040)): ?>
<?php $component = $__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040; ?>
<?php unset($__componentOriginalbacdc7ee2ae68d90ee6340a54a5e36f99d0a3040); ?>
<?php endif; ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/features/show.blade.php ENDPATH**/ ?>