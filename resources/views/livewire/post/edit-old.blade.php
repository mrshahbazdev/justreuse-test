<div class="relative bg-gray-100">
    <!-- Header -->
    <link href="{{ URL::to('css/post.css') }}" rel="stylesheet">
    <div class="relative md:pt-5 pb-2 pt-12">
        <div class="flex flex-wrap">
            <div class="w-full lg:w-8/12 px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-gray-200 border-0">
                    <div class="rounded-t bg-white mb-0 px-6 py-3">
                        <div class="text-center flex justify-between">
                            <h6 class="text-gray-800 text-xl font-bold">{{__('p_myads.post free ad')}}</h6>
                        </div>
                    </div>
                    <div class="flex-auto px-4 lg:px-10 py-10 pt-0">
                        <div class="w-full lg:w-12/12 px-4">
                            <div class="inline-block mt-2 block text-gray-700 text-xs">
                                @if($ancestors!="")
                                <?php
                                echo '<ul class="flex text-indigo-600 text-sm lg:text-base">';
                                echo "<li>Category <i class='fa fa-angle-double-right'></i></li>";
                                $i = 1;
                                foreach ($ancestors as $r) {
                                    $totcount = $ancestors->count();
                                    $catname = "&nbsp;" . $r->title;
                                    if ($i != $totcount) {
                                        echo '<li>' . $catname . ' <i class="fa fa-angle-double-right"></i></li>';
                                    } else {
                                        echo '<li class="text-black">' . $catname . '</li>';
                                    }
                                    $i++;
                                }
                                echo "</ul>";
                                ?>
                                @endif
                            </div>
                        </div>
                        <form action="{{ URL::to('post-add-save') }}" method="POST" name="post-add" onsubmit="return validateForm()" enctype="multipart/form-data">
                            @csrf
                            <input type="hidden" name="post-id" value="{{ $info_post[0]->id }}" />
                            <div class="flex flex-wrap mt-3">
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">{{__('p_myads.ad title')}}<span class="text-red-800">*</span></label>
                                    <input type="text" class="check_blacklist_title px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $info_post[0]->title }}" name="text-title-sst" id="text-title-sst" required="required">
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">{{__('p_myads.price')}}<span class="text-red-800">*</span></label>
                                    <div class="flex flex-row">
                                        <span class="text-center flex items-center bg-grey-lighter rounded rounded-r-none pr-3 text-xs font-bold w-2/12">
                                            <select class="text-center text-sm px-3 py-3 bg-white text-black rounded shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" name="currency_id">
                                                <?php
                                                $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?php echo $currency->id ?>" <?php echo ($currency->id == $info_post[0]->currency_id) ? "selected" : ""; ?>><?php echo $currency->short_code . " (" . $currency->currency_hex . ")" ?></option>
                                                <?php } ?>
                                            </select>
                                        </span>
                                        <input type="number" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $info_post[0]->price }}" name="number-price-sst" id="number-price-sst" min="0" required="required">
                                    </div>
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block  text-gray-700 text-xs font-bold mb-2">{{__('p_myads.description')}}<span class="text-red-800">*</span></label>
                                    <textarea type="text" class="check_blacklist_detail px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" rows="4" name="textarea-desc-sst" id="textarea-desc-sst" required="required">{{ $info_post[0]->description }}</textarea>
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2">Image</label>
                                    @include('livewire.common.multiple_image')

                                    <div class="inline-flex items-center mb-3 mt-4">
                                        <?php

                                        $imgs = !empty($info_post[0]->images) ? explode(',', $info_post[0]->images) : array();

                                        if (count($imgs) > 0 && $info_post[0]->images != "") {
                                            foreach ($imgs as $img) {
                                                $imageUrl = URL::to('storage/' . $img);
                                        ?>
                                                <div class="remove-img-div mr-2 text-center">
                                                    <img src="{{$imageUrl}}" width="70" height="70" alt="img-pre" />
                                                    <input type="hidden" name="old_images[]" value="{{$img}}">
                                                    <button class="text-red-700 remove-img">
                                                        <i class="fa fa-times-circle" aria-hidden="true"></i>
                                                    </button>
                                                </div>
                                        <?php
                                            }
                                        }
                                        ?>
                                        <?php
                                        $img_limit = App\Models\Setting::get_image_size_settings();
                                        ?>
                                        <input type="hidden" class="upload_img_count" value="<?php echo count($imgs); ?>" />
                                        <input type="hidden" class="max_img_upload_count" value="<?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 5; ?>" />
                                        <input type="hidden" class="upload_page" value="Edit" />
                                    </div>
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3 mt-6">
                                    <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.city')}}<span class="text-red-800">*</span></label>
                                    <input type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ !empty($info_post[0]->locality) ? $info_post[0]->locality : $info_location[0]->name }}" id="searchTextField" name="text-city-sst" required="required">
                                    <input type="hidden" id="cName" name="city_name" size="50" value="{{ $info_location[0]->locality }}" />
                                    <input type="hidden" id="cityMain" name="main_city_name" size="50" value="{{ $info_location[0]->name }}" />
                                    <input type="hidden" id="cityLat" name="city_lat" size="50" value="{{ $info_location[0]->latitude }}" />
                                    <input type="hidden" id="cityLag" name="city_lag" size="50" value="{{ $info_location[0]->logitude }}" />
                                    <input type="hidden" id="country_long" name="country_long" size="50" value="{{ $info_location[0]->country_long }}" />
                                    <input type="hidden" id="country_short" name="country_short" size="50" value="{{ $info_location[0]->country_short }}" />
                                    <input type="hidden" id="state_long" name="state_long" size="50" value="{{ $info_location[0]->state_long }}" />
                                    <input type="hidden" id="state_short" name="state_short" size="50" value="{{ $info_location[0]->state_short }}" />
                                    <input type="hidden" name="post-catid-sst" id="id_post_catid" value="{{ $info_post[0]->category_id }}" />
                                </div>
                            </div>
                            <div class="w-full lg:w-12/12 px-4 mb-3">
                                <label class="block text-gray-700 text-md font-bold mb-4"><b>{{__('p_myads.what is your expectation and details')}}?</b></label>
                                <div id="pc_html"></div>
                                <br>
                                <?php
                                $post_methods = App\Models\TblPostMethod::get_active_post_methods();
                                ?>
                                <ul class="mt-4 inline-flex w-full">
                                    @foreach($post_methods as $post_method)
                                    @if($post_method->name == "exchange")
                                    <li>
                                        <div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                                            <span class="block text-gray-700 font-bold mr-10 text-xs mb-2">{{__('post_detail.exchange to buy')}}</span>
                                            <input id="Products_exchangeToBuy" class="cmn-toggle cmn-toggle-round" type="checkbox" name="exchangeToBuy" value="1" <?php echo ($info_post[0]->exchange_to_buy == 1) ? "checked" : ""; ?>>
                                            <label for="Products_exchangeToBuy"></label>
                                        </div>
                                    </li>
                                    @endif
                                    @if($post_method->name == "buynow")
                                    <li>
                                        <div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                                            <span class="block text-gray-700 font-bold mr-10 text-xs mb-2">{{__('p_myads.instant buy')}}</span>
                                            <input id="Products_InstantBuy" class="cmn-toggle cmn-toggle-round" type="checkbox" name="InstantBuy" value="1" <?php echo ($info_post[0]->instant_buy == 1) ? "checked" : ""; ?>>
                                            <label for="Products_InstantBuy"></label>
                                        </div>
                                    </li>
                                    @endif
                                    @endforeach
                                    <li>
                                        <div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                                            <span class="block text-gray-700 font-bold mr-10 text-xs mb-2">{{__('p_myads.fixed price')}}</span>
                                            <input id="Products_FixedPrice" class="cmn-toggle cmn-toggle-round" type="checkbox" name="FixedPrice" value="1" <?php echo ($info_post[0]->fixed_price == 1) ? "checked" : ""; ?>>
                                            <label for="Products_FixedPrice"></label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="w-full lg:w-12/12 px-4 mb-3 shipping_fee">
                                <label class="block text-gray-700 text-md font-bold mb-4">{{__('p_myads.quick buying information')}}</label>
                                <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.shipping cost')}}</label>
                                <input type="text" placeholder="Shipping Cost" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $info_post[0]->shipping_rate }}" name="text-shipping-fee" id="text-shipping-fee">
                            </div>
                            <div class="flex flex-wrap mt-3" id="fb-render">{!! $custJson !!}</div>
                            <div class="w-full lg:w-12/12 px-4 mb-3">
                                <label class="block text-gray-700 text-md font-bold mb-4">{{__('p_myads.youtube video embed url')}}</label>
                                <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.product video')}}</label>
                                <input type="text" placeholder="https://www.youtube.com/embed/9xwazD5SyVg" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="{{ $info_post[0]->video_url }}" name="text-video-sst" id="text-video-sst">
                            </div>
                            <div class="rounded-t mt-3 px-6 py-2">
                                <div class="text-right">
                                    <button id="post_json_insert" class="bg-indigo-500 text-white active:bg-indigo-500 font-bold  text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">{{__('p_myads.save')}}</button>
                                    <button class="bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
            <div class="w-full lg:w-4/12 px-4 text-sm">
                <div class="relative flex flex-col min-w-0 break-words bg-white w-full mb-6 shadow-xl rounded-lg">
                    <div class="px-6">
                        <div class="flex flex-wrap justify-center">
                            <div class="py-10 text-center">
                                <div class="flex flex-wrap justify-center">
                                    <div class="w-full lg:w-9/12 px-4">
                                        <p class="mb-4 leading-relaxed text-gray-800">
                                            {{__('p_myads.post free ad')}}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<?php
$blacklist_words = App\Models\TblPost::get_blacklist();
?>
<script>
    <?php if ($info_post[0]->instant_buy == 0) { ?>
        $(".shipping_fee").hide();
    <?php } ?>

    function validateForm() {
        var city_name = document.forms["post-add"]["city_name"].value;
        var country_name = document.forms["post-add"]["country_long"].value;
        var state_name = document.forms["post-add"]["state_long"].value;
        var video = document.forms["post-add"]["text-video-sst"].value;
        var buynow = document.forms["post-add"]["InstantBuy"].value;
        if (city_name == null || city_name == "") {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (city_name == country_name) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Please choose the locality address only!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (city_name == state_name) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Please choose the locality address only!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (country_name == "" || country_name == null) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Invalid address, please choose valid address!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (state_name == "" || state_name == null) {
            $("#searchTextField").focus();
            $("#searchTextField").val("");
            toastr.warning("Invalid address, please choose valid address!");
            $("#searchTextField").attr("required", true);
            return false;
        } else if (video != "") {
            if (video != undefined || video != '') {
                var regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|\&v=|\?v=)([^#\&\?]*).*/;
                var match = video.match(regExp);
                if (match && match[2].length == 11) {} else {
                    toastr.warning("Invalid youtube url!");
                    return false;
                }
            }
        } else if (buynow == 1) {
            var shipping_rate = document.forms["post-add"]["text-shipping-fee"].value;
            if (shipping_rate == "" || shipping_rate == null) {
                toastr.warning("Please include shipping rate!");
                $("#text-shipping-fee").attr("required", true);
                return false;
            }
        }
    }
    //Remove the old image 
    $('.remove-img').on('click', function(event) {
        if (confirm('Are you sure to delete the image?')) {
            if ($(".upload_img_count").val() > 0) {
                var imgcount = $(".upload_img_count").val();
                var sub = imgcount - 1;
                $(".upload_img_count").val(parseInt(sub));
            }
            $(this).closest(".remove-img-div").remove();
        } else {
            event.preventDefault();
        }
    });
    $(document).ready(function() {
        $("#Products_InstantBuy").on('change', function(e) {
            if (this.checked) {
                $(".shipping_fee").show();
                $("#text-shipping-fee").attr("required", true);
            } else {
                $(".shipping_fee").hide();
                $("#text-shipping-fee").attr("required", false);
            }
        });
    });
    //*Allow decimals only
    $("#text-shipping-fee").on("input", function(evt) {
        var self = $(this);
        self.val(self.val().replace(/[^0-9\.]/g, ''));
        if ((evt.which != 46 || self.val().indexOf('.') != -1) && (evt.which < 48 || evt.which > 57)) {
            evt.preventDefault();
        }
    });
    //*
    //prevent enter submit
    window.addEventListener('load', event => {
        if (event.keyCode == 13) {
            event.preventDefault();
            return false;
        }
    });
    //Select - On change filling process - begin
    window.addEventListener('contentChanged', event => {
        $("#fb-render").html('');
        $("#pc_html").html('');
        $("#fb-render .models-select").html('');
        $("#fb-render").html(event.detail.custJson1);
        $("#fb-render .models-select").html(event.detail.modelHtml1);
        $("#pc_html").html(event.detail.pc_html1);
        document.getElementById('id_post_catid').value = event.detail.catId;
    });
    //Select - On change filling process - end
    //Save process start  
    jQuery(function($) {
        var options = {
            types: ['(regions)']
        };
        //  componentRestrictions: {country: "us"} //restric country if need
        //for location search map
        var input = document.getElementById('searchTextField');
        var autocomplete = new google.maps.places.Autocomplete(input);
        google.maps.event.addDomListener(input, 'keydown', function(event) {
            if (event.keyCode === 13) {
                event.preventDefault();
            }
        });
        google.maps.event.addListener(autocomplete, 'place_changed', function() {
            $('#cName').val('');
            $('#cityMain').val('');
            $('#cityLat').val('');
            $('#cityLag').val('');
            $('#country_long').val('');
            $('#country_short').val('');
            $('#state_long').val('');
            $('#state_short').val('');
            var place = autocomplete.getPlace();
            const country = place.address_components.find(item => item.types.includes('country'));
            const state = place.address_components.find(item => item.types.includes('administrative_area_level_1'));
            const city = place.address_components.find(item => item.types.includes('administrative_area_level_2'));
            $('#cName').val(place.name);
            $('#cityMain').val(city.long_name);
            $('#cityLat').val(place.geometry.location.lat());
            $('#cityLag').val(place.geometry.location.lng());
            $('#country_long').val(country.long_name);
            $('#country_short').val(country.short_name);
            $('#state_long').val(state.long_name);
            $('#state_short').val(state.short_name);
        });
        //for location search map
        var getUserDataBtn = document.getElementById("post_json_insert");
        getUserDataBtn.addEventListener("click", () => {
            var $fileUpload = $("#predefined_images");
            if (parseInt($fileUpload.get(0).files.length) > 5) {
                alert("You are only allowed to upload a maximum of 5 files");
                return false;
            }
            if ($("#selected_category").val() == 0) {
                $("#selected_category").focus();
                return false;
            }
            if ($("#text-title-sst").val() == "") {
                $("#text-title-sst").focus();
                return false;
            }
            if ($("#number-price-sst").val() == "") {
                $("#number-price-sst").focus();
                return false;
            }
            if ($("#textarea-desc-sst").val() == "") {
                $("#textarea-desc-sst").focus();
                return false;
            }
            if ($("#product-condition-sst").val() == "") {
                $("#product-condition-sst").focus();
                return false;
            }
            if ($("#cName").val() == "") {
                $("#searchTextField").focus();
                return false;
            }
        }, false);
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

    /* Republish post */
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
</script>