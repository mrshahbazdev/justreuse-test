<div class="relative bg-gray-100">
    <!-- Header -->
    <link href="{{ URL::to('css/post.css') }}" rel="stylesheet">
    <div class="relative md:pt-10 pb-2">
        <div class="flex flex-wrap">
            <div class="w-full lg:w-8/12 px-4">
                <div class="relative flex flex-col min-w-0 break-words w-full mb-6 shadow-lg rounded-lg bg-gray-200 border-0">
                    <div class="rounded-t bg-white mb-0 px-6 py-3">
                        <div class="text-center flex justify-between">
                            <h6 class="text-gray-800 text-xl font-bold">{{__('p_myads.post free ads')}}</h6>
                        </div>
                    </div>
                    <div class="flex-auto px-0 lg:px-10 py-10 pt-0">
                        <form action="{{ URL::to('post-add-save') }}" name="post-add" onsubmit="return validateForm()" method="POST" enctype="multipart/form-data">
                            @csrf
                            <div class="w-full lg:w-12/12 px-4 mt-3">
                                <div class="mr-0 block text-gray-700 text-xs font-bold">{{__('p_myads.category')}} <span class="text-red-800">*</span></div>
                                <div class="w-full">
                                    <select required id="selected_category" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150 mt-3" wire:change="onchanging($event.target.value)">
                                        <option hidden="" disabled="disabled" selected="selected" value="">{{__('p_myads.please select')}}</option>
                                        <?php
                                        $traverse = function ($categorylist, $prefix = '') use (&$traverse) {
                                            foreach ($categorylist as $category) {
                                                $catname = $prefix . '| ' . $category->title;
                                                $catid = $category->id;
                                                echo '<option value="' . $catid . '">' . $catname . '</option>';
                                                $traverse($category->children, $prefix . '--');
                                            }
                                        };
                                        $traverse($categorylist);
                                        ?>
                                    </select>
                                </div>
                            </div>
                            <input type="hidden" name="post-id" value="0" />
                            <div class="flex flex-wrap mt-3">
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password"> {{__('p_myads.ad title')}} <span class="text-red-800">*</span></label>
                                    <input type="text" class="check_blacklist_title px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="" name="text-title-sst" id="text-title-sst" required="required">
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2" htmlfor="grid-password">{{__('p_myads.price')}} <span class="text-red-800">*</span></label>
                                    <div class="flex flex-row">
                                        <span class="text-center flex items-center bg-grey-lighter rounded rounded-r-none pr-3 text-xs font-bold w-2/12">
                                            <select class="text-center text-sm px-3 py-3 bg-white text-black rounded shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" name="currency_id">
                                                <?php
                                                $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                $loop = 0;
                                                foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?php echo $currency->id ?>" <?php echo ($loop == 0) ? "selected" : ""; ?>><?php echo $currency->short_code . " (" . $currency->currency_hex . ")" ?></option>
                                                <?php $loop++;
                                                } ?>
                                            </select>
                                        </span>
                                        <input type="number" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="" name="number-price-sst" id="number-price-sst" min="0" required="required">
                                    </div>
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3">
                                    <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.description')}} <span class="text-red-800">*</span></label>
                                    <textarea type="text" class="check_blacklist_detail px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150 resize-none" rows="4" name="textarea-desc-sst" id="textarea-desc-sst" required="required"></textarea>
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3" wire:ignore>
                                    <label class="block text-gray-700 text-xs font-bold mb-2">Images</label>
                                    @include('livewire.common.multiple_image')
                                    <?php
                                    $img_limit = App\Models\Setting::get_image_size_settings();
                                    ?>
                                    <input type="hidden" class="upload_page" value="Add" />
                                    <input type="hidden" class="max_img_upload_count" value="<?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 5; ?>" />
                                </div>
                                <div class="w-full lg:w-12/12 px-4 mb-3 mt-4">
                                    <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.city')}} <span class="text-red-800">*</span></label>
                                    <input type="text" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" value="" id="searchTextField" name="text-city-sst" required="required">
                                    <input type="hidden" id="cName" name="city_name" size="50" />
                                    <input type="hidden" id="cityMain" name="main_city_name" size="50" />
                                    <input type="hidden" id="cityLat" name="city_lat" size="50" />
                                    <input type="hidden" id="cityLag" name="city_lag" size="50" />
                                    <input type="hidden" id="country_long" name="country_long" size="50" />
                                    <input type="hidden" id="country_short" name="country_short" size="50" />
                                    <input type="hidden" id="state_long" name="state_long" size="50" />
                                    <input type="hidden" id="state_short" name="state_short" size="50" />
                                    <input type="hidden" name="post-catid-sst" id="id_post_catid" />
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
                                            <input id="Products_exchangeToBuy" class="cmn-toggle cmn-toggle-round" type="checkbox" name="exchangeToBuy" value="1">
                                            <label for="Products_exchangeToBuy"></label>
                                        </div>
                                    </li>
                                    @endif
                                    @if($post_method->name == "buynow")
                                    <li>
                                        <div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                                            <span class="block text-gray-700 font-bold mr-10 text-xs mb-2">{{__('p_myads.instant buy')}}</span>
                                            <input id="Products_InstantBuy" class="cmn-toggle cmn-toggle-round" type="checkbox" name="InstantBuy" value="1">
                                            <label for="Products_InstantBuy"></label>
                                        </div>
                                    </li>
                                    @endif
                                    @endforeach
                                    <li>
                                        <div class="switch col-xs-12 col-sm-12 col-md-12 col-lg-12 no-hor-padding">
                                            <span class="block text-gray-700 font-bold mr-10 text-xs mb-2">{{__('p_myads.fixed price')}}</span>
                                            <input id="Products_FixedPrice" class="cmn-toggle cmn-toggle-round" type="checkbox" name="FixedPrice" value="1">
                                            <label for="Products_FixedPrice"></label>
                                        </div>
                                    </li>
                                </ul>
                            </div>
                            <div class="w-full lg:w-12/12 px-4 mb-3 shipping_fee">
                                <label class="block text-gray-700 text-md font-bold mb-4">{{__('p_myads.quick buying information')}}</label>
                                <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.shipping cost')}}</label>
                                <input type="text" placeholder="Shipping Cost" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" name="text-shipping-fee" id="text-shipping-fee">
                            </div>

                            <div class="flex flex-wrap mt-3" id="fb-render"></div>

                            <div class="w-full lg:w-12/12 px-4 mb-3">
                                <label class="block text-gray-700 text-md font-bold mb-4">{{__('p_myads.youtube video embed url')}}</label>
                                <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.product video')}}</label>
                                <input type="text" placeholder="https://www.youtube.com/embed/9xwazD5SyVg" class="px-3 py-3 placeholder-gray-400 text-gray-700 bg-white rounded text-sm shadow focus:outline-none focus:shadow-outline w-full ease-linear transition-all duration-150" name="text-video-sst" id="text-video-sst">
                            </div>
                            <!---- packages list start --->
                            <div class="px-4 mb-8 mt-4">
                                <?php
                                $packagesList = App\Models\Package::get_active_packages();
                                if (!empty($packagesList) && !empty($payment_methods)) { ?>
                                    <label class="block text-gray-700 text-xs font-bold mb-2">{{__('p_myads.package')}} <span class="text-red-800">*</span></label>

                                    <ul class="bg-gray-300 p-4">

                                        <p class="text-xs mt-2 mb-2">{{__('p_myads.package description')}}</p>
                                        <?php
                                        $pk_i = 0;
                                        foreach ($packagesList as $packagesList) {
                                            if ($pk_i == 0) {
                                                $checked = "checked";
                                            } else {
                                                $checked = "";
                                            }
                                        ?>
                                            <li class="border-b-2 border-gray-200 p-3 pl-0">
                                                <input type="radio" data-amount="{{$packagesList->price}}" data-payement="{{$packagesList->lft}}" class="mr-2 package_type" <?php echo $checked; ?> name="package_type" value="<?php echo $packagesList->id ?>" />{{$packagesList->name}}
                                                <span class="float-right">

                                                    <select class="text-center text-md bg-gray-300 appearance-none" name="currency_id" disabled="">
                                                        <?php
                                                        $settings = App\Models\Setting::get_logos();
                                                        $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                        foreach ($currency as $currency) {
                                                        ?>
                                                            <option value="<?php echo $currency->id ?>" <?php echo ($currency->id == $settings['default_currency']) ? "selected" : ""; ?>><?php echo $currency->currency_hex; ?></option>
                                                        <?php } ?>
                                                    </select> {{$packagesList->price}}

                                                </span>
                                                <?php if ($packagesList->lft == 1) { ?>
                                                    <small class="has-tooltip bg-yellow-500 p-1 text-xs rounded-md"> free
                                                        <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : " . $packagesList->single_pack_limit . " Ad" ?></small>
                                                    </small>
                                                <?php } else { ?>
                                                    <small class="has-tooltip bg-green-700 text-white p-1 text-xs rounded-md">upgrade
                                                        <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : 1 Ad" ?></small>
                                                    </small>
                                                <?php } ?>
                                            </li>
                                        <?php $pk_i++;
                                        } ?>
                                    </ul>
                                <?php } ?>
                            </div>
                            <!--- packages list end ---->
                            <div class="px-4 mb-8 mt-4 payment_methods">
                                <label class="block text-gray-700 text-xs font-bold mb-2">Choose payment method <span class="text-red-800">*</span></label>
                                <ul class="bg-gray-300 p-4">
                                    <?php
                                    $i = 0;
                                    foreach ($payment_methods as $p) {
                                        $checkString = ($i == 0) ? "checked=checked" : "";
                                        $i++;
                                    ?>
                                        <li class="border-b-2 border-gray-200 p-3 pl-0">
                                            <label class="inline-flex items-center mb-2">
                                                <input type="radio" class="form-radio text-indigo-600" name="payment_type" value="{{$p['name']}}" {{$checkString}}>
                                                <span class="ml-2">{{$p['display_name']}}</span>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <input type="hidden" id="final_total_amount" />
                                <input type="hidden" name="paid_id" id="paid_id" />
                            </div>
                            <div class="relative flex flex-col min-w-0 break-words w-full mb-6 mt-0">
                                <div class="rounded-t mb-0 px-0 py-3 border-0">
                                    <div class="block w-full items-center">
                                        <div class="relative px-4 max-w-full flex-grow flex-1">
                                            <div class="font-semibold text-base text-gray-800">
                                                <div class="text-left" wire:ignore>
                                                    <div class="g-recaptcha w-full" data-tabindex=0 data-sitekey="6LfIKnwaAAAAAG3YYAabIKFoBx4hb6fO-IbiAYOm"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="relative px-4 max-w-full flex-grow flex-1 mt-4 text-left form_save_btn">
                                            <button id="post_json_insert" class="bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">{{__('p_myads.save')}}</button>
                                            <button class="bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </form>
                        <div class="relative flex flex-col min-w-0 break-words w-full mb-6 mt-0 paymet_save_btn">
                            <div class="relative px-4 max-w-full flex-grow flex-1 mt-4 text-left">
                                <button id="pay_proceed" class="bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150">Proceed to pay</button>
                                <button class="bg-indigo-500 text-white active:bg-indigo-500 font-bold text-xs px-4 py-2 rounded shadow hover:shadow-md outline-none focus:outline-none mr-1 ease-linear transition-all duration-150"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
                            </div>
                        </div>
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
{!! config('app.google_map_script') !!}
<script src="https://www.google.com/recaptcha/api.js" async defer="defer"></script>
<script>
    $(".shipping_fee").hide();
    $(".payment_methods").hide();
    $(".paymet_save_btn").hide();

    function validateForm() {
        var city_name = document.forms["post-add"]["city_name"].value;
        var country_name = document.forms["post-add"]["country_long"].value;
        var state_name = document.forms["post-add"]["state_long"].value;
        var video = document.forms["post-add"]["text-video-sst"].value;
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
        }
    }

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
        $(".package_type").on('change', function(e) {
            var free = $(this).attr('data-payement');
            var value = $(this).attr('data-amount');
            if (free == 1) {
                $(".payment_methods").hide();
                $(".form_save_btn").show();
                $('.paymet_save_btn').hide();
            } else {
                $(".payment_methods").show();
                $("#final_total_amount").val(value);
                $(".form_save_btn").hide();
                $(".paymet_save_btn").show();
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
        $("#fb-render").html(event.detail.custJson);
        document.getElementById('id_post_catid').value = event.detail.catId;
        $("#pc_html").html('');
        $("#pc_html").html(event.detail.product_condition_html);
        //document.getElementById("post_json_insert").disabled = false;
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
        //getting
        var getUserDataBtn = document.getElementById("post_json_insert");
        getUserDataBtn.addEventListener("click", () => {
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
        }, false);
    });
    window.onload = function() {
        var $recaptcha = document.querySelector('#g-recaptcha-response');
        if ($recaptcha) {
            $recaptcha.setAttribute("required", "required");
        }
    };
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

    /* brands and models */
    $("body").delegate(".brands-select", "change", function() {
        var brands = $(this).val();
        if ((brands != "") && (brands != "undefined") && (brands != null)) {
            get_custom_models(brands);
        }
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
<?php
if (!empty($payment_methods)) {
    foreach ($payment_methods as $p) {
        $base_path = base_path() . '/extra/plugins/' . $p['name'] . '/src/add_post.php';
        if (is_file($base_path)) {
            include_once($base_path);
        }
    }
}
?>