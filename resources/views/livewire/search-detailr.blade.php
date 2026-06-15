<div class="root-element-div">

    <link href="{{ URL::to('css/rangeslider.css') }}" rel="stylesheet">
    <!--begin body-->
    <?php $searchbased = Session::get("searchBased"); ?>
    <?php
    $city = (!empty(request()->city) ? request()->city : "");
    $state = (!empty(request()->state) ? request()->state : "");
    $country = (!empty(request()->country) ? request()->country : "");
   

    $latitude = (!empty(request()->lat) ? request()->lat : "");
    $longitude = (!empty(request()->lng) ? request()->lng : "");
    $distance = (!empty(request()->d) ? request()->d : "");
    $category_ids = (!empty(request()->c) ? request()->c : "");
    $sort_by = (!empty(request()->sort) ? request()->sort : "");

    $get_cat_metas = App\Models\TblCategory::where('slug', $category_ids)->first();
    $meta_title = (!empty($get_cat_metas->meta_title) ? $get_cat_metas->meta_title : "");
    $meta_keywords = (!empty($get_cat_metas->meta_key) ? $get_cat_metas->meta_key : "");
    $meta_description = (!empty($get_cat_metas->meta_description) ? $get_cat_metas->meta_description : "");

    ?>


    @if(!empty($meta_title) && !empty($meta_keywords) && !empty($meta_description))
    @section('meta_title', $meta_title)
    @section('meta_keywords', $meta_keywords)
    @section('meta_description', $meta_description)
    @endif

    <!--begin banner -->

    <!--end banner-->
    <?php $dir_rtl =  App\Models\Setting::is_dir_rtl();
    $class_dir = ($dir_rtl == "true") ? 'dir=rtl' : '';
    $class_dir_float = ($dir_rtl == "true") ? 'float-right' : 'float-left';
    $class_dir_filter = ($dir_rtl == "true") ? 'float-left' : 'float-right';
    $class_dir_padd = ($dir_rtl == "true") ? 'pr-0 lg:pr-6' : 'pl-0 lg:pl-6';
    $class_dir_txt_right = ($dir_rtl == "true") ? 'text-right' : '';
    $class_dir_btn_filters = ($dir_rtl == "true") ? 'flex flex-row-reverse justify-end' : 'text-left';
    $class_sort_by_txt = ($dir_rtl == "false") ? "float-right" : "";
    $class_dir_add_cls = ($dir_rtl == "true") ? 'ar' : "";
    ?>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.css" rel="stylesheet">
<script src="https://cdnjs.cloudflare.com/ajax/libs/noUiSlider/14.6.3/nouislider.min.js"></script>
<div class="w-full inline-block search_detail_page" {{$class_dir}}>
<div class="container  px-4 mx-auto my-8">
    <div class="w-full inline-block">
        <!--Filter-->
        <div class="w-full {{$class_dir_float}} md:w-4/12 lg:w-3/12 relative">
            <div x-data="{ open: false }">
                <!--mobile-filter-->
                <div class="absolute block cursor-pointer inline-block md:hidden float-left filter_button px-2 rounded-md bg-green-500 py-2 text-white text-sm uppercase mb-4" @click="open = !open"><svg fill="#fff" viewBox="0 0 20 20" class="inline-block w-4 sm:w-6 h-4 sm:h-6">
                        <path x-show="!open" fill-rule="evenodd" d="M3 5a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM3 10a1 1 0 011-1h12a1 1 0 110 2H4a1 1 0 01-1-1zM9 15a1 1 0 011-1h6a1 1 0 110 2h-6a1 1 0 01-1-1z" clip-rule="evenodd"></path>
                        <path x-show="open" fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                    </svg><span class="ml-1 inline-block">{{__('messages.filters')}}<span></div>

                <!--end-->
                <div class="hidden w-full float-left filter_butto n px-6   bg-green-500 py-4 rounded-tl-lg rounded-tr-lg text-white text-2xl uppercase mb-4">{{__('messages.filters')}}</div>

                <div :class="{'block w-full shadow-xl p-4 border-t-4 border-green-500 sm:w-2/4 absolute top-12 left-0 scrollbar scrollbar-thumb-green-900 scrollbar-track-green-100 overflow-y-scroll overscroll-x-auto h-screen w-9/12 bg-white z-10': open, 'hidden': !open}" class="w-full float-left md:min-w-full hidden  -translate-x-1/2 transition md:block text-left left_sidebar z-30">
                    <!--Begin Left-->

                    <div class="{{$class_dir_add_cls}} filters w-full float-left {{$class_dir_txt_right}}">

                        <!--Begin - Categories-->                                
                        <div class="w-full float-left border border-gray-300  mb-4 rounded-md">
                            <h3 class="text-black font-bold poppins-600 text-xl mb-4 bg-orange px-4 py-4 rounded-md rounded-b-none ">
                               
                                <a class="poppins-600 text-white">Categories</a>
                            </h3>
							<h4 class="text-black  text-lg mb-1 px-4">
							<?php
                                $all_cate_url = url()->current() . "?loc=" . request()->loc . "&locality=" . request()->locality . "&city=" . request()->city . "&state=" . request()->state . "&country=" . request()->country . "&lat=" . request()->lat . "&lng=" . request()->lng . "&d=" . request()->d;
                                ?>
                                <a class="poppins-500" href="<?php echo $all_cate_url; ?>">{{__('p_search.all categories')}}</a>
							</h4>
                            @if(!empty($selectedcategory))
                            <div class="items-center justify-between leading-none p-4">
	
								
                                @foreach($selectedcategory as $selectedcat)
                                @if($selectedcat->parent_id =="")
                                <?php
                                // maincategory with subcategory porducts count       

                                $main_product_cnt = App\Models\TblPost::product_count(1, $latitude, $longitude, $distance, $selectedcat->id, $city, $state, $country);
                                ?>
                                <button data-id="{{ $selectedcat->slug }}" class="scat text-base font-semibold ml-0 {{$class_dir_btn_filters}} mb-2 p-2 w-full rounded focus:outline-none <?php echo ($selectedcat->slug == request()->c) ? 'bg-green-500 text-white' : ''; ?>">
                                    <span class="font-bold ml-0 mr-2">
                                        <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                    </span>
									<?php  $title = __($selectedcat->title); ?>
                                   {{__('catagories.' .$title)}}
                                    <?php if ($main_product_cnt > 0) {
                                        echo "(" . $main_product_cnt . ")";
                                    } ?>
                                </button>
                                @else
                                <?php
                                // subcategory porducts count                                 
                                $sub_prd_cnt = App\Models\TblPost::product_count(0, $latitude, $longitude, $distance, $selectedcat->id, $city, $state, $country);
                                ?>
                                <button data-id="{{ $selectedcat->slug }}" class="scat text-base font-semibold focus:outline-none w-full {{$class_dir_btn_filters}} rounded ml-0 hover:bg-green-500 hover:text-white mb-2 p-2 text-sm <?php echo ($selectedcat->slug == request()->c) ? 'bg-green-500 text-white' : ''; ?>">
                                    <span class="font-bold ml-3 mr-2">
                                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </span>
									<?php  $title = __($selectedcat->title); ?>
                                   
									{{__($title)}}
                                    <?php if ($sub_prd_cnt > 0) {
                                        echo "(" . $sub_prd_cnt . ")";
                                    } ?>
                                </button>
                                @endif
                                @endforeach
                            </div>
                            @else
                            <div class="items-center justify-between leading-none px-2">
                                @foreach($category as $row)
                                <button data-id="{{ $row->slug }}" class="scat text-base text-gray-600 font-semibold focus:outline-none ml-0 rounded {{$class_dir_btn_filters}} hover:bg-green-500 hover:text-white mb-2 p-2 w-full">
                                    <span class="font-bold ml-3 mr-2">
                                        <i class="fa fa-angle-down" aria-hidden="true"></i>
                                    </span>
                                    {{__('catagories.' .$row->title)}}
                                    
                                </button>
                                @endforeach
                            </div>
                            @endif
				
                        </div>
                        <!--End - Categories-->

                        {{-- BEGIN - Price Slider Section --}}
                    <?php echo $priceSliderHtml; ?>
                    {{-- END - Price Slider Section --}}


                        <!--Begin - Ads in -->
                        <div id="fb-render">
                            <?php if ($searchbased == "City") { ?>
                                <article class="w-full float-left border border-gray-300 p-4 mb-4 rounded-md">
                                    <form method="GET" id="filters-form">
                                        {{ csrf_field() }}

                                        <div class="pt-4 pb-4 pl-4 pr-4 search_page">
                                            <div class="w-full float-left mb-2">
                                                <label class="w-full">{{__('messages.ads in')}} <b><?php echo !empty(request()->locality) ? request()->locality :  request()->city; ?></b> </label>
                                                <label class="w-full"> around <span id="price"><?php echo request()->d; ?></span> km</label>

                                                <p class="my-3">
                                                <div id="distance-range"></div>
                                                </p>
                                            </div>
                                            <input type="hidden" value="<?php echo (!empty(request()->d) ? request()->d : 5); ?>" name="d" id="dist" />
                                        </div>


                                    </form>
                                </article>
                            <?php } ?>
                            <!-- End Ads in  -->
                            <?php echo $custHtml; ?>
                        </div>


                        <!--Begin - Location Division-->
                        @if(!empty($getstates[0]))
                        <div class="w-full float-left border border-gray-300 p-4 mt-2 mb-4 rounded-md">
                            <h3 class="text-black font-bold text-lg mb-8">{{__('p_search.location')}}</h3>
                            <ul class="pl-2">
                                <li>
                                    <button class="text-gray-400 font-thin font-bold scntry" data-country="{{ $getcountries->name}}">{{ $getcountries->name}}</button>
                                    @if(!empty($getstates))
                                    @foreach($getstates as $staterow)
                                    <ul class="pl-2">
                                        <li>
                                            <button class="text-gray-600 font-semibold mt-1 px-2 py-1 rounded-md sstate <?php echo ($staterow->name == request()->state) ? 'text-green-500' : ''; ?>" data-state="{{ $staterow->name }}">{{ $staterow->name }}</button>
                                            <ul class="pl-2">
                                                <?php $getcities = App\Models\TblPost::getCities($staterow->id); ?>
                                                @if(!empty($getcities))
                                                @foreach($getcities as $row)
                                                <?php $city_product_cnt = App\Models\TblPost::city_product_cnt($category_ids, $row->name); ?>
                                                <?php if ($city_product_cnt > 0) { ?>
                                                    <li><button class="text-gray-600 font-semibold scity py-1 px-2 my-1 rounded <?php echo ($row->name == request()->city) ? 'text-green-600' : ''; ?>" data-citystate="{{$staterow->name}}" data-city="{{ $row->name }}" data-latitude="{{ $row->latitude }}" data-longitude="{{ $row->logitude }}"><?php echo mb_strimwidth($row->name, 0, 25, "..."); ?></button></li>
                                                <?php } ?>
                                                @endforeach
                                                @endif
                                            </ul>
                                        </li>
                                    </ul>
                                    @endforeach
                                    @endif
                                </li>
                            </ul>
                        </div>
                        @endif
                        <!--End - Location Division-->

                    </div>
                    <!--End Left-->
                </div>
            </div>
        </div>
        <!--Products-->




        <div class="w-full lg:w-9/12 {{$class_dir_padd}} md:w-8/12 ml-auto mr-auto float-left ">
            <div class="float-left w-full">
                <div class="w-full flex float-right items-center md:h-auto justify-end mb-3">
					<div class="flex items-center hidden">
					<button type="button" class="mr-1 px-2 py-1  hover:bg-green-500" id="grid"><i class="fa fa-th"></i></button>
					<button type="button" class="mr-1 px-2 py-1  hover:bg-green-500" id="list"><i class="fa fa-th-list"></i></button>
					</div>
                    <div class="float-right   flex items-center">
                        <?php $sort_select = 'selected class="font-bold text-gray-900"'; ?>
                        <label class="text-black text-sm sm:text-base poppins-500 mr-2 inline-block">{{__('p_search.sort_by_colon')}} </label>
                        <select id="sort_by" class="focus:outline-none  py-2  px-3 pr-0 rounded-md text-sm sm:text-base capitalize  text-black poppins-500  {{$class_sort_by_txt}} ">
                            <option value="post-desc" <?php if ($sort_by == 'post-desc') {
                                                            echo $sort_select;
                                                        } ?>>{{__('p_search.recently_posted')}}</option>
                            <option value="price-asc" <?php if ($sort_by == 'price-asc') {
                                                            echo $sort_select;
                                                        } ?>>{{__('p_search.price_low_to_high')}}</option>
                            <option value="price-desc" <?php if ($sort_by == 'price-desc') {
                                                            echo $sort_select;
                                                        } ?>>{{__('p_search.price_high_to_low')}}</option>
                            <option value="most-viewed" <?php if ($sort_by == 'most-viewed') {
                                                            echo $sort_select;
                                                        } ?>>{{__('p_search.popular')}}</option>
                        </select>
                    </div>
					<a title="Map view" class="hidden lg:ml-0 rounded-md cursor-pointer hover:shadow hover:bg-white  py-2 px-3 show_map transition-all hover:transition-all ease-in-out float-right flex items-center gap-2 text-mygreen text-base poppins-500 border hover:bg-white hover:shadow " id="map">
					<span><img class="w-6 inline-block bg-white" src="https://www.bazari.az/images/frontend/map.png"></span></a>
                </div>

                <!--Products list-->
                <div id="filter-results">
				<div class="float-left w-full grid grid-cols-2 lg:grid-cols-3">
                    @if(count($filtered_data)>0)
                    @foreach($filtered_data as $d)
                  
                    <!--<div class=" w-1/2 sm:w-1/3 md:w-1/2 lg:w-1/3 xl:w-1/4 pb-3 lg:pb-6 {{$class_dir_float}}">-->
					<div class="w-full pb-3 lg:pb-6 {{$class_dir_float}}">
                        <?php echo $k = App\Models\Setting::htmlAdBlock($d['id']); ?>
                    </div>
                    @endforeach
					</div>
                </div>

                <input type="hidden" class="totalpages" value="<?php echo $filtered_data->lastPage(); ?>" />
                <?php
                if ($filtered_data->lastPage() > 1) { ?>
                    <div class="w-full float-left text-center my-8 lg:my-14">
                        <button class="seemore loadbtn cursor-pointer inline-block p-8 bg-green-500 py-2 pb-3 px-8 rounded text-white text-xl Capitalize items-center mx-auto transition duration-500 ease-in-out hover:bg-white hover:text-green-500 border border-green-500" data-page="2" data-link="<?php echo url()->full(); ?>&page=" data-div="#filter-results">{{__('messages.Load More')}}</button>
                    </div>
                <?php } ?>

                @else
                <div class="w-full text-center">
                    <img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
                    <p class="text-2xl pl-2 pb-1 font-bold mb-8 sm:mb-12">{{__('messages.no data found')}}</p>
                </div>
                @endif
            </div>
        </div>


    </div>
</div>
</div>


    <!--end body-->

    <?php $distance_range = App\Models\TblPost::getMaxDistace(); ?>
    <style>
        ul.category-banners .slick-prev {
            display: none !important;
        }

        ul.category-banners .slick-next {
            display: none !important;
        }
    </style>
    <script src="{{ URL::to('js/slick.min.js') }}"></script>
    <script>
        /* Category based filter */
        $(".scat").on('click', function(event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            var path = "{{ URL::to('/'.request()->path()) }}";
            var c = $(this).data("id");
            result = path + "?c=" + c + "&s=" + "{{ request()->s}}" + "&locality=" + "{{ request()->locality}}" + "&city=" + "{{ request()->city}}" + "&state=" + "{{ request()->state}}" + "&country=" + "{{request()->country}}" + "&lat=" + "{{request()->lat}}" + "&lng=" + "{{request()->lng}}" + "&loc=" + "{{request()->loc}}" + "&d=20";
            window.location = result;
        });
        /* City based filter */
        $(".scity").on('click', function(event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            var path = "{{ URL::to('/')}}";
            var c = $(this).data("city");
            window.location = path + "/" + removeSpace(c) + "?c=" + "{{ request()->c}}" + "&s=" + "{{ request()->s}}" + "&city=" + c + "&state=" + $(this).data('citystate') + "&country=" + "{{request()->country}}" + "&loc=" + c + "&lat=" + $(this).data('latitude') + "&lng=" + $(this).data('longitude') + "&d=20";
        });
        /* State based filter */
        $(".sstate").on('click', function(event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            var path = "{{ URL::to('/')}}";
            var c = $(this).data("state");
            window.location = path + "/" + removeSpace(c) + "?c=" + "{{ request()->c}}" + "&s=" + "{{ request()->s}}" + "&city=" + "&state=" + c + "&country=" + "{{ request()->country }}" + "&loc=" + c + "&d=20";
        });
        /* Country based filter */
        $(".scntry").on('click', function(event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            var path = "{{ URL::to('/') }}";
            var c = $(this).data("country");
            localStorage.clear();
            window.location = path + "/" + removeSpace(c) + "?c=" + "{{ request()->c}}" + "&s=" + "{{ request()->s}}" + "&city=" + "&state=" + "&country=" + c + "&loc=" + c + "&d=20";
        });
        /* Custome field filter */
        $("#fb-render .filter").on('change', function(event) {
            event.stopPropagation();
            event.stopImmediatePropagation();
            var name = $(this).attr('name');
            var value = $(this).val();
            var type = $(this).data('type');
            var is_param = searchParams(name);
            if (type == "select") {
                if (is_param == true) {
                    var alterUrl = removeParams(name);
                    if ((searchParams('field_modelswithbrand') == true) && (searchParams('field_brand*and*model') == true)) {
                        var alterUrl = removeParams('field_modelswithbrand');
                    }
                    if (value != "") {
                        var path = alterUrl + "&" + name + "=" + value;
                    } else {
                        var path = alterUrl;
                    }
                } else {
                    var path = window.location.href + "&" + name + "=" + value;
                }
            } else if (type == "radio") {
                if (is_param == true) {
                    var alterUrl = removeParams(name);
                    if (value != "") {
                        var path = alterUrl + "&" + name + "=" + value;
                    } else {
                        var path = alterUrl;
                    }
                } else {
                    var path = window.location.href + "&" + name + "=" + value;
                }
            } else if (type = "checkbox") {
                if (is_param == true) {
                    var alterUrl = removeParams(name);
                    var path = alterUrl;
                } else {
                    var path = window.location.href + "&" + name + "=" + value;
                }
            } else if (type = "text") {
                if (is_param == true) {
                    var alterUrl = removeParams(name);
                    var path = alterUrl;
                } else {
                    var path = window.location.href + "&" + name + "=" + value;
                }
            } else {
                var path = window.location.href + "&" + name + "=" + value;
            }
            window.location = path;
        });
        /* search if param is available in the current page url*/
        function searchParams(sParam) {
            var field = sParam;
            var url = decodeURIComponent(window.location.href);
            if (url.indexOf('?' + field + '=') != -1)
                return true;
            else if (url.indexOf('&' + field + '=') != -1)
                return true;
            return false
        }
        /* check if field name is available in the current page url*/
        function checkName(sParam) {
            var url_string = window.location.href;
            var url = new URL(url_string);
            var c = url.searchParams.get("filter");
            if (c.indexOf(sParam) != -1) {
                return c.indexOf(sParam);
            } else {
                return -1;
            }
        }
        /* remove if field name is available in the current page url*/
        function removeParams(sParam) {
            var url = window.location.href.split('?')[0] + '?';
            var sPageURL = decodeURIComponent(window.location.search.substring(1)),
                sURLVariables = sPageURL.split('&'),
                sParameterName,
                i;
            for (i = 0; i < sURLVariables.length; i++) {
                sParameterName = sURLVariables[i].split('=');
                if (sParameterName[0] != sParam) {
                    url = url + sParameterName[0] + '=' + sParameterName[1] + '&'
                }
            }
            return url.substring(0, url.length - 1);
        }
        /* Price range slider script start*/
        function getVals() {
            // Get slider values
            var parent = this.parentNode;
            var slides = parent.getElementsByTagName("input");
            var slide1 = parseFloat(slides[0].value);
            var slide2 = parseFloat(slides[1].value);
            // Neither slider will clip the other, so make sure we determine which is larger
            if (slide1 > slide2) {
                var tmp = slide2;
                slide2 = slide1;
                slide1 = tmp;
            }
            var displayElement = parent.getElementsByClassName("rangeValues")[0];
            displayElement.innerHTML = slide1 + " - " + slide2;
        }
        window.onload = function() {
            // Initialize Sliders
            var sliderSections = document.getElementsByClassName("range-slider");
            for (var x = 0; x < sliderSections.length; x++) {
                var sliders = sliderSections[x].getElementsByTagName("input");
                for (var y = 0; y < sliders.length; y++) {
                    if (sliders[y].type === "range") {
                        sliders[y].oninput = getVals;
                        // Manually trigger event first time to display values
                        sliders[y].oninput();
                    }
                }
            }
        }
        $(document).on("click", ".rangeclick", function(event) {
            event.preventDefault();
            var row = $(this).parent(".range-sliders").attr('data-rowclass');
            
            // Read values directly from the slider inputs
            var min_value = $(this).parent(".range-sliders").find(".minprice_" + row).val();
            var max_value = $(this).parent(".range-sliders").find(".maxprice_" + row).val();
            
            // Get the base name (e.g., 'price')
            var name = $(this).parent(".range-sliders").find(".rangevalue_min_" + row).attr('name');
            var field_name = name.substring(0, name.indexOf('_min')); // Extracts 'field_price'

            var is_param_min = searchParams(field_name + '_min');
            var is_param_max = searchParams(field_name + '_max');

            // Start with the current URL without any price parameters
            var alterUrl = window.location.href;
            if (is_param_min) {
                alterUrl = removeParams(field_name + '_min');
            }
            if (is_param_max) {
                alterUrl = removeParams(field_name + '_max');
            }

            var path = alterUrl + "&" + field_name + "_min=" + min_value + "&" + field_name + "_max=" + max_value;
            
            window.location = path;
        });
        /* Price range slider script end*/
        
        /* Load more data script */
        $(".seemore").click(function() {
            $div = $($(this).data('div')); //div to append
            $link = $(this).data('link'); //current URL
            $page = $(this).data('page'); //get the next page #
            $href = $link + $page; //complete URL
            $.get($href, function(response) { //append data
                $html = $(response).find("#filter-results").html();
                $div.append($html);
            });
            $totalpages = $(".totalpages").val();
            $(this).data('page', (parseInt($page) + 1)); //update page # 
            if ($totalpages == $page) {
                $(".seemore").hide();
            }
        });
        /* distance filter */
        $(function() {
            $("#distance-range").slider({
                value: <?php echo (!empty(request()->d) ? request()->d : 5); ?>,
                max: <?php echo $distance_range; ?>,
                animate: "fast",
                orientation: "horizontal",
                slide: function(event) {
                    var range = $("#distance-range").slider("option", "value");
                    $("#price").text(range);
                    $("#dist").val(range);
                    var is_param = searchParams("d");
                    if (is_param == true) {
                        var alterUrl = removeParams("d");
                        var path = alterUrl + "&d=" + range;
                    } else {
                        var path = window.location.href + "&d=" + range;
                    }
                    window.location = path;
                }
            });
        });
        /* location list read more / read less*/

        //sort by filter
        $("#sort_by").change(function(e) {
            event.preventDefault();
            var selectedOption = $(this).val();
            console.log("val==> " + selectedOption);
            var name = 'sort';
            var is_param = searchParams(name);
            if (is_param == true) {
                var alterUrl = removeParams(name);
                var path = alterUrl + "&" + name + "=" + selectedOption;
            } else {
                var path = window.location.href + "&" + name + "=" + selectedOption;
            }
            window.location = path;
        });

        $(".readmore").click(function() {
            if ($(".loc-list").height() <= 196) {
                jQuery(".loc-list").animate({
                    height: "0px" /*or height of your choice*/
                }, {
                    duration: 0,
                    /*or speed of your choice*/
                    queue: false,
                    specialEasing: {
                        height: "easeInCirc"
                    },
                    complete: function() {
                        jQuery(".loc-list").css({
                            height: "auto"
                        });
                    }
                });
                $(".readmore").text("Read less");
            } else {
                $(".readmore").text("Read more");
                $(".loc-list").animate({
                    height: 190
                }, 1000);
            }
        });

        jQuery(document).ready(function() {
            jQuery(".category-banners").slick({
                dots: true,
                infinite: false,
                speed: 300,
                slidesToShow: 1,
                adaptiveHeight: true,
                autoplay: true,
                autoplaySpeed: 2000,
            });

        });
    </script>

</div>