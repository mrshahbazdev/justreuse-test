<div>
    <div class="shell">
        <header class="flex flex-col justify-between mb-6">
            <div class="steps-wrap" style="margin-top:14px;">
                <div class="step active" id="step1-title">Step 1 – Basic Info</div>
                <div class="step" id="step3-title">Step 2 – Price/Details</div>
            </div>
        </header>

        <form id="multi-step-form" action="{{ URL::to('update_post_info') }}" method="POST" enctype="multipart/form-data">
            @csrf

            <!-- STEP 1 -->
            <div id="step-1">
                <h2>Choose Main Category</h2>
                <br>
                <select wire:model="selectedParentCategory" class="form-select" required>
                    <option value="">Select a Category</option>
                    @foreach($parentCategories as $category)
                        <option value="{{ $category->id }}">{{ $category->title }}</option>
                    @endforeach
                </select>

                @if(!empty($childCategories))
                <h2>What are you selling in {{ $selectedParentCategoryTitle }} ?</h2>
                <div class="categories mt-4" data-group="vehicle-type">
                    @foreach($childCategories as $child)
                    <button class="seg @if($selectedChildCategory == $child->id) active @endif" type="button" wire:click="selectChildCategory({{ $child->id }})">
                        {{ $child->title }}
                    </button>
                    @endforeach
                </div>

                <div id="fb-render"></div>
                <div id="pc_html"></div>

                <input type="hidden" id="id_post_catid" name="post-catid-sst">
                <input type="hidden" id="selected_category" name="selected_category">

                <div class="actions">
                    <button class="btn" type="button" onclick="goToStep(2)">Continue</button>
                </div>
                @endif
            </div>

            <!-- STEP 2 -->
            <div id="step-2" style="display:none;">
                <h1>How much do you want for it?</h1>
                <!--row 2-->
                                <div class="sm:flex sm:space-x-2  lg:space-x-6  sm:mb-6 md:mb-8 lg:mb-10 w-full">


                                    <div class="w-full sm:w-1/2 mb-4 sm:mb-0">
                                        <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500" htmlfor="grid-password">{{__('p_myads.price')}} <span class="text-red-800">*</span></label>
                                        <div class="flex flex-row">
                                            <span class="sub-field text-center bg-grey-lighter rounded rounded-r-none  text-xs font-bold w-4/6 xl:w-4/12">
                                                <select id="currency_id" class="w-full h-14  px-2 py-2 md:px-4 md:py-4 text-sm sm:text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black text-center ">
                                                <?php
                                                $currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                $settings = App\Models\Setting::get_logos();
                                                $default_curr = $settings['default_currency'];

                                                // $loop = 0;
                                                foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?php echo $currency->id ?>" <?php echo ($default_curr == $currency->id) ? "selected" : ""; ?>><?php echo $currency->short_code . " (" . $currency->currency_hex . ")" ?></option>
                                                <?php //$loop++;
                                                } ?>
                                                </select>
                                            </span>
                                            <input type="hidden" name="currency_id" value="{{$default_curr}}">
                                            <input type="number" class="w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" name="number-price-sst" id="number-price-sst" min="0" required="required">
                                            @error('number-price-sst')
                                            <div class="alert alert-danger"><p class="text-red-500">This field is required</p></div>
                                        @enderror
                                        </div>
                                    </div>

                                    <div class="w-full sm:w-1/2 mb-4 sm:mb-0">
                                        <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.city')}} <span class="text-red-800">*</span></label>
                                        <input type="text" class="w-full h-14  px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" placeholder="Shehar ka naam likhein" name="text-city-sst" required="required">
                                        <input type="hidden" name="post-catid-sst" id="id_post_catid" />
                                    </div>


                                </div>
                 <!--row 3-->
                                <div class="w-full block mb-4 sm:mb-6 md:mb-8 lg:mb-10">
                                    <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.description')}} <span class="text-red-800">*</span></label>
                                    <textarea class="check_blacklist_detail w-full h-36  px-6 py-4 text-base text-black border-l-4 border-green-800 bg-gray-100 focus:outline-none  placeholder-black" value="" name="textarea-desc-sst" id="textarea-desc-sst" required="required"></textarea>
                                    @error('textarea-desc-sst')
                                            <div class="alert alert-danger text-red-500"><p class="text-red-500">This field is required</p></div>
                                        @enderror
                                </div>
                 <!--row 5-->
                                <div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10">
                                    <h2 class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2 poppins-600 text-green-500">{{__('p_myads.what is your expectation and details')}}?</h2>
                                    <div id="pc_html"></div>
                                </div>


                                <?php $post_methods = App\Models\TblPostMethod::get_active_post_methods(); ?>


                                 <!--row 6-->
                                <div class="w-full mb-4 sm:mb-8 md:mb-10 lg:b-12">
                                    <ul class="sm:inline-flex w-full">
                                    @foreach($post_methods as $post_method)
                                    @if($post_method->name == "exchange")
                                        <li class="float-left w-full sm:w-auto mb-4 sm:mb-0">
                                            <!-- Toggle Exchange to Buy -->
                                            <label for="Products_exchangeToBuy" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold ">{{__('post_detail.exchange to buy')}}
                                                <!-- toggle -->
                                                <div class="relative mt-0 sm:mt-3 sm:float-none">
                                                <!-- input -->
                                                <input type="checkbox" id="Products_exchangeToBuy" class="sr-only" name="exchangeToBuy" value="1">
                                                <!-- line -->
                                                <div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
                                                <!-- dot -->
                                                <div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
                                                </div>
                                                <!-- label -->
                                            </label>
                                        </li>
                                        @endif
                                        @if($post_method->name == "buynow")
                                        <li class="float-left w-full sm:w-auto mb-4 sm:mb-0">
                                            <label for="Products_InstantBuy" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold ">{{__('p_myads.instant buy')}}
                                            <!-- toggle -->
                                                <div class="relative mt-0 sm:mt-3 sm:float-none">
                                                <!-- input -->
                                                <input type="checkbox" id="Products_InstantBuy" class="sr-only" name="InstantBuy" value="1">
                                                <!-- line -->
                                                <div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
                                                <!-- dot -->
                                                <div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
                                                </div>
                                                <!-- label -->
                                            </label>
                                        </li>

                                        @endif
                                        @endforeach
                                        <li class="float-left w-full sm:w-auto">
                                            <label for="Products_FixedPrice" class="w-full sm:w-auto float-left cursor-pointer block text-base text-black font-semibold ">{{__('p_myads.fixed price')}}
                                                <!-- toggle -->
                                                <div class="relative mt-0 sm:mt-3  sm:float-none">
                                                <!-- input -->
                                                <input type="checkbox" id="Products_FixedPrice" class="sr-only" name="FixedPrice" value="1">
                                                <!-- line -->
                                                <div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
                                                <!-- dot -->
                                                <div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
                                                </div>
                                                <!-- label -->
                                            </label>
                                        </li>
                                    </ul>
                                </div>

                                 <!--row 7 -- hidden -->
                                <div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10 shipping_fee">
                                    <label class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2">{{__('p_myads.quick buying information')}}</label>
                                    <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.shipping cost')}}</label>
                                    <input type="text" placeholder="Shipping Cost" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none placeholder-black pac-target-input" name="text-shipping-fee" id="text-shipping-fee">
                                </div>
                                <!--row 7.1-->
                                <!-- <div class="flex flex-wrap mt-3 w-full" id="fb-render"></div> -->
                                <div class="mt-3 w-full" id="fb-render"></div>
                                 <!--row 8-->

                                <div class="w-full block mb-2 sm:mb-6 md:mb-8 lg:mb-10">
                                    <h2 class="text-black text-lg md:text-xl lg:text-2xl xl:text-3xl font-bold sm:mt-0 mt-2 poppins-600 text-green-500">{{__('p_myads.youtube video embed url')}}</h2>
                                </div>

                                 <!--row 9-->
                                <div class="w-full mb-4 sm:mb-8 md:mb-10 lg:mb-12">
                                    <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.product video')}}</label>
                                    <input type="text" placeholder="Example: https://www.youtube.com/embed/9xwazD5SyVg" class="w-full h-14 rounded-lg px-6 py-4 text-base text-black border-l-2 border-green-800 bg-gray-100 focus:outline-none  placeholder-gray-400" value="" name="text-video-sst" id="text-video-sst">
                                </div>



                                <!--row 11-->
                                <div class="w-full flex flex-wrap mb-4 sm:mb-8 md:mb-10">


                                    <div class="w-full lg:w-3/5 mb-4 lg:mb-0 ">
                                        <div class="relative w-full float-left  mt-2 mb-4 sm:m-0">
                                            <div class="w-full float-left">
                                                <div class="w-full rounded-lg bg-gray-100 float-left">
                                                    <div class="w-full p-5 float-left">
                                                        <div class="w-full float-left py-2 post_add_img_upload" wire:ignore="">
                                                                <div class="input-images border-4 border-solid rounded-3xl hover:bg-gray-100 hover:border-gray-300 cursor-pointer relative h-52 sm:h-64 md:h-72 lg:h-80 pt-10 pb-6">
                                                                    <div class="absolute flex flex-col items-center top-0 bottom-0 left-0 right-0 justify-center text-center">
                                                                        <div class="text-4xl font-normal text-gray-200 group-hover:text-gray-600">
                                                                            <i class="fa fa-plus" aria-hidden="true"></i>
                                                                        </div>
                                                                        <p class="text-xl text-gray-500 font-semibold group-hover:text-gray-600">Upload Photos</p>
                                                                    </div>
                                                                </div>
                                                                @include('livewire.common.multiple_image')

                                                                <?php
                                                                    $img_limit = App\Models\Setting::get_image_size_settings();
                                                                ?>
                                                                <input type="hidden" class="upload_page" value="Add" />
                                                                <input type="hidden" class="max_img_upload_count" value="<?php echo !empty($img_limit['max_image_limit']) ? $img_limit['max_image_limit'] : 5; ?>" />
                                                        </div>

                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>



                                    <div class="w-full lg:w-2/5 ">
                                        <label class="block text-base sm:text-md md:text-lg lg:text-xl text-black font-semibold mb-2 sm:mb-4">{{__('p_myads.package')}}</label>
                                        <div class="mb-4 md:mb-8">
                                            <p class="text-base  font-semibold leading-relaxed leading-relaxed">{{__('p_myads.package_description')}}</p>
                                        </div>

                                            <?php
                                        $packagesList = App\Models\Package::get_active_packages();
                                        if (!empty($packagesList) && !empty($payment_methods)) { ?>
                                        <div class="w-full px-4 md:px-5 lg:px-7 py-4 bg-gray-50 float-left">

                                            <?php
                                                $pk_i = 0;
                                                foreach ($packagesList as $packagesList) {
                                                if ($packagesList->name == 'Free') {
                                                    $checked = "checked";
                                                } else {
                                                    $checked = "";
                                                }
                                            ?>

                                            <label for="free-ads_{{$pk_i}}" class="w-full cursor-pointer block text-lg md:text-xl lg:text-2xl text-black font-semibold float-left mb-2 sm:mb-4 md:mb-7">{{$packagesList->name}}

                                            <?php if ($packagesList->lft == 1) { ?>
                                            <small class="has-tooltip bg-yellow-500 p-1 text-xs rounded-md inline-block align-middle text-gray-800 "> Free <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : " . $packagesList->single_pack_limit . " Ad" ?></small></small>
                                            <?php } else { ?>
                                                <small class="text-white has-tooltip bg-green-500 p-1 text-xs rounded-md inline-block align-middle"> Upgrade <small class="tooltip"><?php echo "Duration : " . $packagesList->duration . " days, Limit : 1 Ad" ?></small></small>
                                                <?php } ?>

                                            <div class="relative ">
                                                <input type="radio" name="package_type" id="free-ads_{{$pk_i}}" class="sr-only package_type" data-amount="{{$packagesList->price}}" data-payement="{{$packagesList->lft}}" value="<?php echo $packagesList->id ?>" <?php echo $checked; ?>>
                                                <div class="block bg-gray-200 w-16 lg:w-20 h-9 lg:h-11 rounded-full"></div>
                                                <div class="dot absolute left-1 top-1 bg-gray-400 w-7 lg:w-9 h-7 lg:h-9 rounded-full transition"></div>
                                                </div>

                                                <span class="block text-base font-normal">
                                                <!--<select class="bg-green-500 bg-opacity-0 appearance-none" name="currency_id" disabled="">
                                                <?//php
                                                //$settings = App\Models\Setting::get_logos();
                                                //$currency = App\Models\TblCurrency::where('active', '0')->orderBy('id', 'desc')->get();
                                                //foreach ($currency as $currency) {
                                                ?>
                                                    <option value="<?//php echo $currency->id ?>" <?//php echo ($currency->id == $settings['default_currency']) ? "selected" : ""; ?>><?//php echo $currency->currency_hex; ?></option>
                                                <?//php } ?>
                                                </select> -->
                                            <?php $pack_curreny = App\Models\Setting::get_admin_default_currency(); ?>
                                                <?php echo $pack_curreny['currency_hex'];?> {{$packagesList->price}}</span>
                                            </label>
                                            <?php $pk_i++; } ?>

                                        </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            <!--row 12-->
                            <div class="w-full mb-4 sm:mb-8 md:mb-10 lg:mb-12 payment_methods">
                                <label class="block text-base text-black  mb-2 sm:mb-4 poppins-500">{{__('p_myads.choose payment method')}} <span class="text-red-800">*</span></label>
                                <ul class="bg-green-50 p-4">
                                    <?php
                                    $i = 0;
                                    foreach ($payment_methods as $p) {
                                        $checkString = ($i == 0) ? "checked=checked" : "";
                                        $i++;
                                    ?>
                                        <li class="border-b-2 border-gray-200 p-3 pl-0">
                                            <label class="inline-flex items-center mb-2">
                                                <input type="radio" class="payment_type form-radio h-6 w-6 text-green-500" name="payment_type" value="{{$p['name']}}" {{$checkString}}>
                                                <span class="mx-2">{{$p['display_name']}}</span>
                                            </label>
                                        </li>
                                    <?php } ?>
                                </ul>
                                <input type="hidden" id="final_total_amount" />
                                <input type="hidden" name="paid_id" id="paid_id" />
                            </div>

                            <div class="relative flex flex-col min-w-0 break-words w-full mt-0">
                                <div class="rounded-t mb-0 px-0 py-3 border-0">
                                    <div class="block w-full items-center">
                                        <div class="relative max-w-full flex-grow flex-1">
                                            <div class="font-semibold text-base text-gray-800">
                                                <div class="text-left" wire:ignore>
                                                    <?php
                                                     $recaptcha_sitekey = getenv("GOOGLE_RECAPTCHA_SITEKEY");
                                                     $recaptcha_secret = getenv("GOOGLE_RECAPTCHA_SECRETKEY");

                                                    ?>
                                                    <div class="g-recaptcha w-full"  data-tabindex=0 data-sitekey="{{$recaptcha_sitekey}}"></div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="w-full block float-left text-center mt-4 mb-2 form_save_btn">
                                            <button id="post_json_insert" class="bg-green-500 py-3 px-14 pb-3 inline-block rounded-md text-white text-base  outline-none focus:outline-none hover:text-green-500  poppins-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full mb-4">{{__('p_myads.post')}}</button>
                                            <button id="post_cancel" class="bg-orange py-3 px-14 pb-3 inline-block rounded-md text-white text-base  outline-none focus:outline-none hover:text-green-500  poppins-500 hover:bg-white transition ease-in-out duration-700 sm:w-auto w-full mb-4"><a href="{{URL::to('/post')}}">{{__('p_myads.cancel')}}</a></button>
                                        </div>
                                    </div>
                                </div>
                            </div>
               <!--  <div class="grid">
                    <div>
                        <label>Price</label>
                        <div class="field">
                            <span class="ico">€</span>
                            <input class="input with-icon" type="number" name="number-price-sst" required>
                        </div>
                    </div>
                    <div style="display:flex; flex-direction:column; justify-content:flex-end;">
                        <label>You are selling</label>
                        <p style="font-weight: 700; color: var(--ink);">Toyota Matenge</p>
                    </div>
                    <div style="grid-column: 1 / span 2;">
                        <label>Listing Title</label>
                        <input class="input" type="text" name="text-title-sst" required>
                    </div>
                    <div style="grid-column: 1 / span 2;">
                        <label>Description</label>
                        <textarea class="input" rows="4" name="textarea-desc-sst" required></textarea>
                    </div>
                </div> -->

                <div class="actions" style="display:flex; gap:16px;">
                    <button class="btn btn-secondary" type="button" onclick="goToStep(1)">Back</button>
                    <button class="btn" type="submit">Submit</button>
                </div>
            </div>
        </form>

        <!-- COMPLETED PAGE -->
        <div id="completed-content" style="display:none;">
            <div class="completed-page">
                <h3>Completed</h3>
                <div class="icon">
                    <svg viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M20 6 9 17l-5-5"/></svg>
                </div>
                <h2>Congratulations!</h2>
                <p>Your ad should be live in no time!</p>
                <button class="my-ads-btn">My ads</button>
            </div>
        </div>
    </div>
</div>


   <script>
    $(".shipping_fee").hide();
    $(".payment_methods").hide();
    $(".paymet_save_btn").hide();

    function validateForm() {
        var city_name = document.forms["post-add"]["city_name"].value;
        var country_name = document.forms["post-add"]["country_long"].value;
        var state_name = document.forms["post-add"]["state_long"].value;
        var video = document.forms["post-add"]["text-video-sst"].value;
        var adTitle = document.forms["post-add"]["text-title-sst"].value;
        var setPrice = document.forms["post-add"]["number-price-sst"].value;
        var descr = document.forms["post-add"]["textarea-desc-sst"].value;
        var cat = $('#selected_category option').filter(':selected').val();

        if(cat.length == 0)
        {
            toastr.warning("Select Category!");
            $("#selected_category").focus();
            return false;
        }
        else if(adTitle=="")
        {
            toastr.warning("Enter title!");
            $(".check_blacklist_title")[0].focus();
            return false;
        }
        else if(setPrice=="")
        {
            toastr.warning("Enter price!");
            $("#number-price-sst").focus();
            return false;
        }

        else if (city_name == null || city_name == "") {
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

        else if(descr=="")
        {
            toastr.warning("Enter description!");
            $("#textarea-desc-sst").focus();
            return false;
        }
        else{
            return true;
        }


    }
document.addEventListener('DOMContentLoaded', () => {
    const steps = ['step-1', 'step-2']; // step IDs
    const stepTitles = ['step1-title', 'step3-title']; // step title IDs
    let currentStep = 0;

    const updateStep = () => {
        // Show/hide step containers
        steps.forEach((stepId, index) => {
            const stepEl = document.getElementById(stepId);
            if (stepEl) stepEl.style.display = index === currentStep ? 'block' : 'none';
        });

        // Update step title active class
        stepTitles.forEach((titleId, index) => {
            const titleEl = document.getElementById(titleId);
            if (titleEl) titleEl.classList.toggle('active', index === currentStep);
        });

        // Show completed page
        const completedPage = document.getElementById('completed-content');
        const formContainer = document.getElementById('form-container');
        if (currentStep >= steps.length) {
            completedPage.style.display = 'block';
            if (formContainer) {
                Object.assign(formContainer.style, {
                    display: 'flex',
                    justifyContent: 'center',
                    alignItems: 'center',
                    padding: '48px 24px'
                });
            }
        } else {
            completedPage.style.display = 'none';
            if (formContainer) {
                Object.assign(formContainer.style, {
                    display: 'block',
                    justifyContent: 'flex-start',
                    alignItems: 'flex-start',
                    padding: '24px'
                });
            }
        }

        // Required attribute control
        document.querySelectorAll('#multi-step-form [required]').forEach(el => el.removeAttribute('required'));
        document.querySelectorAll(`#${steps[currentStep]} [name]`).forEach(el => {
            if (el.tagName !== 'INPUT' || el.type !== 'hidden') {
                el.setAttribute('required', true);
            }
        });
    };

    // Navigation functions
    window.goToStep = (step) => {
        currentStep = step - 1;
        updateStep();
    };

    // Brand/Color/etc single-select
    const setupSingleSelect = (groupSelector) => {
        const group = document.querySelector(groupSelector);
        if (!group) return;
        group.querySelectorAll('.seg, .chip').forEach(btn => {
            btn.addEventListener('click', () => {
                group.querySelectorAll('.seg, .chip').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
            });
        });
    };
    setupSingleSelect('[data-group="vehicle-type"]');
    setupSingleSelect('[data-group="doors"]');
    setupSingleSelect('[data-group="color"]');
    setupSingleSelect('[data-group="listing-type"]');

    // Livewire event listener
    window.addEventListener('contentChanged', event => {
        $("#fb-render").html(event.detail.custJson);
        document.getElementById('id_post_catid').value = event.detail.catId;
        $("#pc_html").html(event.detail.product_condition_html);
        $('#selected_category').val(event.detail.catId);

        if (event.detail.catId) getBrandIcon(event.detail.catId);

        function getBrandIcon(catId) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('get_brand_icon') }}",
                data: { cat_id: catId },
                success: function(response) {
                    $(".brands-select li").each(function() {
                        var brandId = $(this).data('label');
                        var iconPath = response.icons[brandId];
                        var fullPath = iconPath && iconPath !== "noimage50.png" ?
                            '{{ asset("storage/customfields/filters") }}/' + iconPath :
                            '{{ asset("storage/noimage50.png") }}';
                        $(this).find("#brand_icon").attr("src", fullPath).show();
                        $(this).attr("data-img", fullPath);
                    });
                },
                error: function() {
                    $(".brands-select li #brand_icon").hide();
                }
            });
        }

        // Brand select dropdown
        const $selectedBrand = $('.selected-brand');
        const $dropdownList = $('.brands-select');
        const $hiddenInput = $('#selected_brand');
        $selectedBrand.on('click', () => $dropdownList.toggleClass('hidden'));
        $dropdownList.on('click', '.dropdown-item', function () {
            const brandKey = $(this).data('label');
            const fullPath = $(this).data('img');
            $selectedBrand.empty()
                .append('<img src="' + fullPath + '" alt="brand-image" class="w-8 h-8 mr-4">')
                .append('<span>' + brandKey + '</span>');
            $hiddenInput.val($(this).data('id'));
            $dropdownList.addClass('hidden');
        });
        $(document).on('click', (e) => {
            if (!$selectedBrand.is(e.target) && !$dropdownList.is(e.target) && $dropdownList.has(e.target).length === 0) {
                $dropdownList.addClass('hidden');
            }
        });
    });

    // Brand change → load models
    $(document).on('change', '#brand-select', function() {
        var brandId = $(this).val();
        if (brandId) {
            $.ajax({
                type: 'POST',
                dataType: 'json',
                url: "{{ route('get_custom_models') }}",
                data: { id: brandId },
                success: function(data) {
                    $(".models-select").html(data.data);
                }
            });
        } else {
            $(".models-select").html('<option value="" disabled selected>Select brand first</option>');
        }
    });

    // Init
    updateStep();
});
</script>

</div>