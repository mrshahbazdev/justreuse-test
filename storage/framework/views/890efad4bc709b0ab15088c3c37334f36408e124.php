<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
	 <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
        <?php $currency_symbol = App\Models\Setting::get_admin_default_currency(); ?>
        <?php if($ancestors!=""): ?>
        <div class="bg-white p-2 mb-2 rounded-md">
            <?php
            echo '<ul class="flex text-orange-400 text-sm lg:text-base">';
            echo "<li><a href='" . URL::to('admin/category/') . "'>Category <i class='fa fa-angle-double-right'></i></a></li>";
            $i = 1;
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
        </div>
        <?php endif; ?>
        <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
            <form enctype="multipart/form-data" method="POST" wire:submit.prevent="store">
                <?php echo csrf_field(); ?>
                <div>
                    <div class="inline-block mr-2">Parent</div>
                    <div class="inline-block w-2/4">
                        <select name="parent_id" wire:model="parent_id" id="id_catparent" class="form-select mt-1 block w-full">
                            <option value="0">Root</option>
                            <?php
                            $traverse = function ($categories, $prefix = '', $parent = '') use (&$traverse) {
                                foreach ($categories as $category) {
                                    $catname = $prefix . '| ' . $category->title;
                                    $catid = $category->id;
                                    $selected = ($parent == $catid) ? "selected='selected'" : "";
                                    echo '<option value="' . $catid . '" ' . $selected . '>' . $catname . '</option>';
                                    $traverse($category->children, $prefix . '--', $parent);
                                }
                            };
                            $traverse($categories, '', $current_parent);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-6">Title</div>
                    <div class="inline-block w-2/4">
                        <input type="text" name="title" wire:model="title" id="id_cattitle" class="form-input rounded-md shadow-sm block mt-1 w-full">
                        <input type="hidden" name="html" wire:model="html" id="id_cathtml" value="" />
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-2">Image</div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" wire:model="image" name="image" id="id_catimg" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <p class="text-red-500 pl-24 text-sm">Image size : 300px x 300px</p>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-2">App Image </div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" name="app_image" wire:model.defer="app_image" id="id_app_image" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <p class="text-red-500 pl-24 text-sm">Image size : 150px x 150px</p>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-2">Banner</div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" wire:model="banner" name="banner" id="id_catbanner" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <p class="text-red-500 pl-24 text-sm">Image size : 1200px x 400px</p>
                </div>
                <?php
                $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                if (!empty($post_methods)) {
                    $check_post_methods = $post_methods->pluck('name')->toArray();
                    if (in_array("bannerads", $check_post_methods)) { ?>
                        <div class="mb-3">
                            <div class="inline-block">Paid Banner Price/Per day</div>
                            <div class="inline-block">
                                <span class="text-center flex items-center bg-grey-lighter rounded rounded-r-none pr-2 text-lg font-bold"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?></span>
                            </div>
                            <div class="inline-block w-2/4">
                                <input type="text" wire:model="paid_banner_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Banner Price/Per day" value="0.00">
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
                <div class="mb-3">
                    <div class="inline-block mr-2">Show Product Conditions?</div>
                    <div class="inline-block w-2/4">
                        <input type="checkbox" id="product_condition" wire:model.defer="product_condition" name="product_condition" value="1" />
                        <span class="text-xs">(like new / slightly used / heavely used)</span>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-6">Meta Title</div>
                    <div class="inline-block w-2/4">
                        <input type="text" name="meta_title" wire:model="meta_title" id="meta_title" class="form-input rounded-md shadow-sm block mt-1 w-full">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="inline-block mr-6 align-middle relative bottom-16">Meta Key</label>
                    <div class="inline-block w-2/4">
                        <textarea id="editor" placeholder="Meta key" wire:model="meta_key" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="inline-block mr-6 align-middle relative bottom-16">Meta Description</label>
                    <div class="inline-block w-2/4">
                        <textarea id="editor" placeholder="Meta Description" wire:model="meta_description" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                </div>

                <div class="inline-block"><button id="post_json_insert" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mt-4">Save</button>
                    <div id="post_cancel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Cancel</div>
                </div>


            </form>
			 </div>
        </div>
        <?php echo $__env->make('livewire.admin.category.js_css_category', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
        <script>
            $(document).ready(function() {
                $("#post_json_insert").click(function() {
                    var title = $('#id_cattitle').val();
                    var image = $('#id_catimg').val();
                    if (title == "") {
                        $('#id_cattitle').focus();
                        return false;
                    }
                    // if (image == "") {
                    //     $('#id_catimg').focus();
                    //     return false;
                    // }
                });
                //cancel begin
                $("#post_cancel").click(function() {
                    window.location.href = document.referrer;
                });
                //cancel end


                var _URL = window.URL || window.webkitURL;

                $('#id_catimgs').change(function() {
                    var imgwidth1 = 0;
                    var imgheight1 = 0;
                    var wb_file = $(this)[0].files[0];
                    var x_img = new Image();
                    x_img.src = _URL.createObjectURL(wb_file);
                    x_img.onload = function() {
                        imgwidth1 = this.width;
                        imgheight1 = this.height;
                        if (parseFloat(imgwidth1) != 300) {
                            alert("Improper image size uploaded.\nWeb Image Size (300px X 300px)");
                            $("#id_catimg").val("");
                            return false;
                        } else if (parseFloat(imgheight1) != 300) {
                            alert("Improper image size uploaded.\nWeb Image Size (300px X 300px)");
                            $("#id_catimg").val("");
                            return false;
                        }
                    };
                });



                $('#id_app_images').change(function() {
                    var imgwidth1 = 0;
                    var imgheight1 = 0;
                    var wb_file = $(this)[0].files[0];
                    var x_img = new Image();
                    x_img.src = _URL.createObjectURL(wb_file);
                    x_img.onload = function() {
                        imgwidth1 = this.width;
                        imgheight1 = this.height;
                        if (parseFloat(imgwidth1) != 150) {
                            alert("Improper image size uploaded.\nApp Image Size (150px X 150px)");
                            $("#id_app_image").val("");
                            return false;
                        } else if (parseFloat(imgheight1) != 150) {
                            alert("Improper image size uploaded.\nApp Image Size (150px X 150px)");
                            $("#id_app_image").val("");
                            return false;
                        }
                    };
                });


                $('#id_catbanners').change(function() {
                    var imgwidth1 = 0;
                    var imgheight1 = 0;
                    var wb_file = $(this)[0].files[0];
                    var x_img = new Image();
                    x_img.src = _URL.createObjectURL(wb_file);
                    x_img.onload = function() {
                        imgwidth1 = this.width;
                        imgheight1 = this.height;
                        if (parseFloat(imgwidth1) != 1200) {
                            alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
                            $("#id_catbanner").val("");
                            return false;
                        } else if (parseFloat(imgheight1) != 400) {
                            alert("Improper image size uploaded.\nBanner Image For Website (1200px X 400px)");
                            $("#id_catbanner").val("");
                            return false;
                        }
                    };
                });


            });
        </script>
    </div>
</div><?php /**PATH /home/justreused/htdocs/www.justreused.com/resources/views/livewire/admin/category/add.blade.php ENDPATH**/ ?>