<div>
    <div class="py-4 px-4">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-5">
            <div class="w-full  bg-gray-100  pl-2 pr-2 mb-2 rounded-sm">
                <button wire:click="back()" class="bg-green-500 hover:bg-orange-500 text-white  py-2 px-4 rounded-sm my-3">Back</button>
            </div>
            <div class="bg-gray-50 px-4 pt-5 pb-4 sm:p-6 sm:pb-4 text-left border rounded shadow mt-4">
                <h1 class="text-2xl font-medium text-gray-900">Edit Post</h1>
                <form action="{{ url('/admin/post-update') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="post-id" value="{{$this->post_id}}" />
                    <div class="flex flex-wrap mt-3">
                        <div class="w-full lg:w-12/12 px-4 mb-3">
                            <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">Add Title *</label>
                            <input type="text" class="check_blacklist_title border px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $this->title }}" name="title" id="text-title-sst" required="required">
                        </div>
                        <div class="w-full lg:w-12/12 px-4 mb-3">
                            <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">Price *</label>
                            <input type="number" class="border px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $this->price }}" name="price" id="number-price-sst" min="0" required="required">
                        </div>
                        <div class="w-full lg:w-12/12 px-4 mb-3">
                            <label class="block  text-gray-700 text-xs font-bold mb-2">Description *</label>
                            <textarea type="text" class="border check_blacklist_detail px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="4" name="description" id="textarea-desc-sst" required="required">{{ $this->description }}</textarea>
                        </div>
                        <!-- <div class="w-full lg:w-12/12 px-4 mb-3">
                            <label class="block text-gray-700 text-xs font-bold mb-2">Image</label> <input type="file" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" name="images[]" id="predefined_images" multiple accept='image/*'/>
                        </div> -->
                        <div class="w-full lg:w-12/12 px-4 mb-3">
                            <label class="block  text-gray-700 text-xs font-bold mb-2">Images *</label>
                            <div class="input-images"></div>
                            <input type="hidden" class="border img_index" name="selected-img-index" />
                        </div>
                        <?php
                        $imgs = !empty($info_post[0]->images) ? explode(',', $info_post[0]->images) : array();
                        if (count($imgs) > 0 && $info_post[0]->images != "") {
                            $i = 1;
                            foreach ($imgs as $img) {
                                if ($i == 1) {
                                    $default = 'defaultImg';
                                } else {
                                    $default = '';
                                }
                                $imageUrl = URL::to('storage/' . $img);
                        ?>
                                <div class="w-1/2 md:w-1/3 lg:w-1/4 float-left">
                                    <div class="remove-img-div m-4 text-center existImg" data-index="{{$i}}">
                                        <img class="w-full object-cover object-center h-24 w-24 mx-auto sm:h-20 lg:h-24 {{$default}}" src="{{$imageUrl}}" alt="img-pre" />
                                        @if($i==1)
                                        <p class="editimage-default">Default</p>
                                        @endif
                                        <input type="hidden" name="old_images[]" value="{{$img}}">
                                        <button class="text-red-700 remove-img">
                                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                                        </button>
                                    </div>
                                </div>
                        <?php
                                $i++;
                            }
                        }
                        ?>
                        <?php
                        $img_limit = App\Models\Setting::get_image_size_settings();
                        ?>
                        <input type="hidden" class="upload_img_count" value="<?php echo count($imgs); ?>" />
                        <input type="hidden" class="max_img_upload_count" value="<?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 10; ?>" />
                        <input type="hidden" class="upload_page" value="Edit" />
                        <div class="inline-flex items-center px-4 mb-3">
                            <?php
                            $imgs = explode(',', $this->images);
                            if (count($imgs) > 0 && $this->images != "") {
                                $i = 1;
                                foreach ($imgs as $img) {
                                    if ($i == 1) {
                                        $default = 'defaultImg';
                                    } else {
                                        $default = '';
                                    }
                                    $imageUrl = URL::to('storage/' . $img);
                            ?>
                                    <div class="remove-img-div mr-2 text-center existImg" data-index="{{$i}}">
                                        <img class="w-full object-cover object-center h-24 w-24 mx-auto sm:h-20 lg:h-24 {{$default}}" src="{{$imageUrl}}" alt="img-pre" />
                                        @if($i==1)
                                        <p class="editimage-default">Default</p>
                                        @endif
                                        <input type="hidden" name="old_images[]" value="{{$img}}">
                                        <button class="text-red-700 remove-img">
                                            <i class="fa fa-times-circle" aria-hidden="true"></i>
                                        </button>
                                        <!--                                                     <img src="{{$imageUrl}}" width="70" height="70" alt="img-pre"/>
                                                    <input type="hidden" name="old_images[]" value="{{$img}}">
                                                    <button class="text-red-700 remove-img">
                                                      <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                    </button> -->
                                    </div>
                            <?php
                                    $i++;
                                }
                            } ?>
                            <input type="hidden" class="upload_img_count" value="<?php echo count($imgs); ?>" />
                            <input type="hidden" class="upload_page" value="Edit" />
                        </div>
                        <div class="w-full lg:w-12/12 px-4 mb-3">{!! $custJson !!}</div>
                    </div>
                    <div class="rounded-t mt-3 px-6 py-2">
                        <div class="text-right">
                            <button id="post_json_insert" class="bg-green-500 text-white active:bg-green-500 font-bold  text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">Save</button>
                            <button class="bg-green-500 text-white active:bg-green-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><a href="{{URL::to('admin/posts')}}">Cancel</a></button>
                        </div>
                    </div>
                </form>
                <?php
                $blacklist_words = App\Models\TblPost::get_blacklist();
                ?>
            </div>
        </div>
    </div>
    <script>
       $('.input-images').imageUploader({
            extensions: ['.jpg', '.jpeg', '.png', '.webp'], 
            mimes: ['image/jpeg', 'image/png', 'image/webp'],
            maxFiles: 5,
        });

        //Remove the old image 
        $('.remove-img').on('click', function() {
            $(this).closest(".remove-img-div").remove();
        });
        // remove blacklist words
        $(document).ready(function() {
            $(".check_blacklist_title").on('keyup', function(e) {
                var blacklist = <?php echo $blacklist_words; ?>;
                var words = $(".check_blacklist_title").val();
                var str = words.trim().split(" ");
                var lastWord = str[str.length - 1];
                var lowerword = lastWord.toLowerCase();
                var array_index = jQuery.inArray(lowerword, blacklist);
                if (array_index >= 0) {
                    $(".check_blacklist_title").val($(".check_blacklist_title").val().replace(lastWord, ''));
                }
            });
        });
        // remove blacklist words
        $(document).ready(function() {
            $(".check_blacklist_detail").on('keyup', function(e) {
                var blacklist = <?php echo $blacklist_words; ?>;
                var words = $(".check_blacklist_detail").val();
                var str = words.trim().split(" ");
                var lastWord = str[str.length - 1];
                var lowerword = lastWord.toLowerCase();
                var array_index = jQuery.inArray(lowerword, blacklist);
                if (array_index >= 0) {
                    $(".check_blacklist_detail").val($(".check_blacklist_detail").val().replace(lastWord, ''));
                }
            });
        });
        $(document).ready(function() {
            $(".brands-select").change(function(e) {
                var brands = $(this).val();
                if ((brands != "") && (brands != "undefined") && (brands != null)) {
                    get_custom_models(brands);
                }
            });
            function get_custom_models(brands) {
                $.ajax({
                    type: 'POST',
                    dataType: 'json',
                    url: "{{ route('get_custom_models') }}",
                    data: {
                        id: brands,
                    },
                    success: function(data) {
                        $(".models-select").html(data.data);
                    }
                });
            }
        });
        $(document).ready(function() {
            // Add click event handler for images
            $('.existImg img').click(function() {
                // Remove any previously added class
                $('.existImg img').removeClass('defaultImg');
                $('.editimage-default').remove();
                // Add the class to the clicked image
                $(this).addClass('defaultImg');
                $(this).after('<p class="editimage-default">Default</p>');
                // Get the index of the clicked image
                var index = $(this).closest('.existImg').index();
                // Get the value of old_images array
                var oldImages = $('input[name="old_images[]"]').map(function() {
                    return $(this).val();
                }).get();
                // Remove the clicked image from its current position
                var removed = oldImages.splice(index, 1);
                // Re-add the removed image at the beginning of the array
                oldImages.unshift(removed[0]);
                // Update the input values with the reordered array
                $('input[name="old_images[]"]').each(function(i) {
                    $(this).val(oldImages[i]);
                });
            });
        });
    </script>