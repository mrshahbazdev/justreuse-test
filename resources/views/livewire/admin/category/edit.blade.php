<div class="relative md:pt-28 pb-32 pt-12">
    <div class="px-4 md:px-10 mx-auto w-full">
        <?php $currency_symbol = App\Models\Setting::get_admin_default_currency(); ?>
        <div class="bg-white p-2 mb-2 rounded-md">
            @if($ancestors!="")
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
            @endif
        </div>
        <div class="mb-4 border-b-2 border-gray-800 bg-white rounded-md p-4">
            <form enctype="multipart/form-data" method="POST" wire:submit.prevent="update">
                <div>
                    <div class="inline-block mr-2">Parent</div>
                    <div class="inline-block w-2/4">
                        <select name="parent_id" wire:model.defer="parent_id" id="id_catparent" class="form-select mt-1 block w-full">
                            <option value="0" <?php
                                                if ($data->parent_id == null) {
                                                    echo "selected='selected'";
                                                }
                                                ?>>Root</option>
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
                            $traverse($categories, '', $data->parent_id);
                            ?>
                        </select>
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-6">Title</div>
                    <div class="inline-block w-2/4">
                        <input type="text" name="title" wire:model.defer="title" id="id_cattitle" value="<?php echo $data->title; ?>" class="form-input rounded-md shadow-sm block mt-1 w-full">
                        <input type="hidden" name="html" wire:model.defer="html" id="id_cathtml" />
                    </div>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-2">Web Image</div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" name="image" wire:model.defer="image" id="id_catimg" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <?php if ($data->image != "") { ?>
                        <img class="inline-block w-16 h-16 rounded-full" src="<?php echo URL::to('storage') . '/' . $data->image; ?>" />
                    <?php } ?>
                    <!-- <p class="text-red-500 pl-24 text-sm">Image size : 300px x 300px</p> -->
                    <p class="text-red-500 pl-24 text-sm">Image size : 50px x 50px</p>
                </div>
                <div class="mb-3">
                    <div class="inline-block mr-2">App Image </div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" name="app_image" wire:model.defer="app_image" id="id_app_image" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <?php if ($data->app_image != "") { ?>
                        <img class="inline-block w-16 h-16 rounded-full" src="<?php echo URL::to('storage') . '/' . $data->app_image; ?>" />
                    <?php } ?>
                    <p class="text-red-500 pl-24 text-sm">Image size : 150px x 150px</p>
                </div>
                <?php
                $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                if (!empty($post_methods)) {
                    $check_post_methods = $post_methods->pluck('name')->toArray();
                    if (in_array("bannerads", $check_post_methods)) { ?>
                        <div class="mb-3">
                            <div class="inline-block">Paid Banner Price/Per day</div>
                            <div class="inline-block w-2/4">
                                <div class="flex flex-row">
                                    <span class="text-center flex items-center bg-grey-lighter rounded rounded-r-none pr-3 text-lg"><?php echo !empty($currency_symbol) ? $currency_symbol['currency_hex'] : ""; ?></span>
                                    <input type="text" wire:model.defer="paid_banner_price" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline" placeholder="Enter Banner Price/Per day" value="0.00">
                                </div>
                            </div>
                        </div>
                <?php
                    }
                }
                ?>
                <div class="mb-3">
                    <div class="inline-block mr-2">Banner</div>
                    <div class="inline-block w-2/4">
                        <input type="file" accept="image/*" name="banner" wire:model.defer="banner" id="id_catbanner" class="form-input rounded-md shadow-sm block mt-1 w-full" />
                    </div>
                    <input type="hidden" name="cat_id" wire:model.defer="cat_id" id="id_catid" class="form-input rounded-md shadow-sm block mt-1 w-full">
                    <div class="inline-block">
                        <?php if ($data->banner != "") { ?>
                            <div class="">
                                <img class="object-cover inline-block w-16 h-16 rounded-full" src="<?php echo URL::to('storage') . '/categories/' . $data->banner; ?>" />
                            </div>
                        <?php } ?>
                    </div>
                    <p class="text-red-500 pl-24 text-sm">Image size : 1200px x 400px</p>
                </div>


                <div class="mb-3">
                    <div class="inline-block mr-2">Show Product Conditions?</div>
                    <div class="inline-block w-2/4">
                        <input type="checkbox" wire:model.defer="product_condition" name="product_condition" id="product_condition" <?php echo ($data->product_condition == 1) ? "checked" : ""; ?> value="1" />
                        <span class="text-xs">(like new / slightly used / heavely used)</span>
                    </div>
                </div>

                <div class="mb-3">
                    <div class="inline-block mr-6">Meta Title</div>
                    <div class="inline-block w-2/4">
                        <input type="text" name="meta_title" wire:model.defer="meta_title" id="meta_title" class="form-input rounded-md shadow-sm block mt-1 w-full">
                    </div>
                </div>

                <div class="mb-3">
                    <label class="inline-block mr-6 align-middle relative bottom-16">Meta Key</label>
                    <div class="inline-block w-2/4">
                        <textarea id="editor" placeholder="Meta key" wire:model.defer="meta_key" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="inline-block mr-6 align-middle relative bottom-16">Meta Description</label>
                    <div class="inline-block w-2/4">
                        <textarea id="editor" placeholder="Meta Description" wire:model.defer="meta_description" rows="6" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline"></textarea>
                    </div>
                </div>

                <div class="inline-block">
                    <button id="post_json_insert" type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 mt-4">Update</button>
                    <div id="post_cancel" class="cursor-pointer inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:shadow-outline-gray disabled:opacity-25 transition ease-in-out duration-150 ml-4">Cancel</div>
                </div>

            </form>
        </div>
        @include('livewire.admin.category.js_css_category')
        <script>
            $(document).ready(function() {
                $("#post_json_insert").click(function() {
                    var title = $('#id_cattitle').val();
                    if (title == "") {
                        $('#id_cattitle').focus();
                        return false;
                    }
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
                        if (parseFloat(imgwidth1) != 150) {
                            // alert("Improper image size uploaded.\nWeb Image Size (300px X 300px)");
                            alert("Improper image size uploaded.\nWeb Image Size (50px X 50px)");
                            $("#id_catimg").val("");
                            return false;
                        } else if (parseFloat(imgheight1) != 150) {
                            // alert("Improper image size uploaded.\nWeb Image Size (300px X 300px)");
                            alert("Improper image size uploaded.\nWeb Image Size (50px X 50px)");
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
</div>