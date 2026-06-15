<?php $searchbased = Session::get("searchBased"); ?>
<link href="{{ URL::to('css/rangeslider.css') }}" rel="stylesheet">
<section class="relative pt-4 py-20">
    <div class="container mx-auto">
        <div class="flex flex-wrap">
            <?php
            $city = (!empty(request()->city) ? request()->city : "");
            $state = (!empty(request()->state) ? request()->state : "");
            $country = (!empty(request()->country) ? request()->country : "");
            $latitude = (!empty(request()->lat) ? request()->lat : "");
            $longitude = (!empty(request()->lng) ? request()->lng : "");
            $distance = (!empty(request()->d) ? request()->d : "");
            $category_ids = (!empty(request()->c) ? request()->c : "");
            ?>
            <!--left portion-->
            <div x-data="{ mobileMenuOpen : false }" class="w-full lg:w-3/12 md:w-12/12 ml-auto mr-auto px-2">
                <!-- Column -->
                <button @click="mobileMenuOpen = !mobileMenuOpen" class="inline-block lg:hidden w-16 h-8 bg-gray-200 text-gray-600 p-1">
                    Filter
                </button>
                <div class="my-1 md:w-6/12 lg:overflow-y-hidden md:px-0 lg:relative md:overflow-y-scroll border md:rounded-lg md:border-indigo-500 px-1 md:bg-white z-40 md:absolute lg:w-full lg:block" :class="{ 'show' : mobileMenuOpen , 'hidden' : !mobileMenuOpen , 'h-screen w-9/12 px-0 overflow-y-scroll border rounded-lg border-indigo-500 bg-white z-40 absolute' : mobileMenuOpen}" @click.away="mobileMenuOpen = false">

                    <!-- category and subcategory list -->
                    <article class="overflow-hidden rounded-lg shadow-lg border-gray-300 border">
                        <h4 class="text-lg m-1 border-gray-300 border-b">
                            <div class="text-black p-3">
                                <?php
                                $all_cate_url = url()->current() . "?loc=" . request()->loc . "&locality=" . request()->locality . "&city=" . request()->city . "&state=" . request()->state . "&country=" . request()->country . "&lat=" . request()->lat . "&lng=" . request()->lng . "&d=" . request()->d;
                                ?>
                                <a href="<?php echo $all_cate_url; ?>">{{__('p_search.all categories')}}</a>
                            </div>
                        </h4>
                        @if(!empty($selectedcategory))
                        <div class="items-center justify-between leading-none p-2 md:p-4">
                            @foreach($selectedcategory as $selectedcat)
                            @if($selectedcat->parent_id =="")
                            <?php
                            // maincategory with subcategory porducts count                                 
                            $main_product_cnt = App\Models\TblPost::product_count(1, $latitude, $longitude, $distance, $selectedcat->id, $city, $state, $country);
                            ?>
                            <button data-id="{{ $selectedcat->slug }}" class="scat ml-0 text-left text-sm mb-2 p-2 w-full rounded focus:outline-none <?php echo ($selectedcat->slug == request()->c) ? 'bg-indigo-500 text-white' : ''; ?>">
                                <span class="font-bold ml-0 mr-2">
                                    <i class="fa fa-angle-double-down" aria-hidden="true"></i>
                                </span>
                                {{ $selectedcat->title}} <?php if ($main_product_cnt > 0) {
                                                                echo "(" . $main_product_cnt . ")";
                                                            } ?>
                            </button>
                            @else
                            <?php
                            // subcategory porducts count                                 
                            $sub_prd_cnt = App\Models\TblPost::product_count(0, $latitude, $longitude, $distance, $selectedcat->id, $city, $state, $country);
                            ?>
                            <button data-id="{{ $selectedcat->slug }}" class="scat focus:outline-none w-full text-left rounded ml-0 hover:bg-indigo-500 hover:text-white mb-2 p-2 text-sm <?php echo ($selectedcat->slug == request()->c) ? 'bg-indigo-500 text-white' : ''; ?>">
                                <span class="font-bold ml-3 mr-2">
                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </span>
                                {{ $selectedcat->title }} <?php if ($sub_prd_cnt > 0) {
                                                                echo "(" . $sub_prd_cnt . ")";
                                                            } ?>
                            </button>
                            @endif
                            @endforeach
                        </div>
                        @else
                        <div class="items-center justify-between leading-none p-2 md:p-4">
                            @foreach($category as $row)
                            <button data-id="{{ $row->slug }}" class="scat focus:outline-none ml-0 rounded text-left hover:bg-indigo-500 hover:text-white mb-2 p-2 text-sm w-full">
                                <span class="font-bold ml-3 mr-2">
                                    <i class="fa fa-angle-down" aria-hidden="true"></i>
                                </span>
                                {{ $row->title}}
                            </button>
                            @endforeach
                        </div>
                        @endif
                    </article>
                    <!-- end category -->
                    <!--- Custom field fillter --->
                    <article id="fb-render" class="overflow-hidden rounded-lg shadow-lg border-gray-300 border md:mt-5 mt-10">
                        <form method="GET" id="filters-form">
                            {{ csrf_field() }}
                            <?php if ($searchbased == "City") { ?>
                                <div class="pt-4 pb-4 pl-4 pr-4">
                                    <label class="w-full">{{__('messages.ads in')}} <b><?php echo !empty(request()->locality) ? request()->locality :  request()->city; ?></b> </label>
                                    <br>
                                    <label class="w-full"> around <span id="price"><?php echo request()->d; ?></span> km</label>
                                    <div id="distance-range"></div>
                                    <input type="hidden" value="<?php echo (!empty(request()->d) ? request()->d : 5); ?>" name="d" id="dist" />
                                </div>
                            <?php } ?>
                            <?php echo $custHtml; ?>
                        </form>
                    </article>
                    <!--- end custom field filter --->
                    <!-- start locations -->
                    @if(!empty($getstates[0]))
                    <article class="overflow-hidden rounded-lg shadow-lg border-gray-300 border md:mt-5 mt-10">
                        <h1 class="text-lg m-1   border-gray-300 border-b">
                            <div class="text-black p-3">{{__('p_search.location')}}</div>
                        </h1>
                        <div class="items-center justify-between leading-none p-2 md:p-4 loc-list">
                            <button data-country="{{ $getcountries->name}}" class="scntry rounded ml-2 text-left text-sm mb-0 p-2 w-full focus:outline-none <?php echo ($getcountries->name == request()->country) ? 'text-indigo-500' : ''; ?>">
                                <span class="font-bold ml-0 mr-2"><i class="fa fa-angle-double-down" aria-hidden="true"></i></span>
                                {{ $getcountries->name}}
                            </button>


                            @if(!empty($getstates))
                            @foreach($getstates as $staterow)
                            <button data-state="{{ $staterow->name }}" class="sstate rounded ml-0 text-left text-sm mb-2 p-2 w-full focus:outline-none <?php echo ($staterow->name == request()->state) ? 'text-indigo-500' : ''; ?>">
                                <span class="font-bold ml-3 mr-2"><i class="fa fa-thumb-tack" aria-hidden="true"></i></span>
                                {{ $staterow->name }}
                            </button>
                            <?php $getcities = App\Models\TblPost::getCities($staterow->id); ?>
                            @if(!empty($getcities))
                            @foreach($getcities as $row)
                            <?php $city_product_cnt = App\Models\TblPost::city_product_cnt($category_ids, $row->name); ?>
                            <?php if ($city_product_cnt > 0) { ?>
                                <button data-citystate="{{$staterow->name}}" data-city="{{ $row->name }}" data-latitude="{{ $row->latitude }}" data-longitude="{{ $row->logitude }}" class="rounded scity ml-3 text-left text-sm mb-1 p-2 w-full focus:outline-none {{ $row->id }} <?php echo ($row->name == request()->city) ? 'text-indigo-500' : ''; ?>">
                                    <span class="font-bold ml-3 mr-2"> -- </span>
                                    <?php echo mb_strimwidth($row->name, 0, 25, "..."); ?>
                                </button>
                            <?php } ?>
                            @endforeach
                            @endif
                            @endforeach
                            @endif

                            
                        </div>
                        <p class="readmore mb-1 underline"> {{__('messages.read more')}} </p>
                    </article>
                    @endif
                    <!-- end locations -->

                </div>
            </div>
            <!--right portion-->
            <div class="w-full lg:w-9/12 pl-6 md:w-12/12 ml-auto mr-auto">
                <div class="slider">
                    <?php if (!empty($category_ids)) { ?>
                        <?php
                        $category_banner = array();
                        $get_cat_banners = App\Models\TblCategory::get_cat_banners($category_ids, NULL);
                        if (!empty($get_cat_banners)) {
                            $category_banner = $get_cat_banners;
                        }

                        //paid banners for this selected category
                        $paid_category_banner = array();
                        $get_paid_cat_banners = App\Models\TblCategory::get_paid_cat_banners($category_ids, NULL, "web");
                        if (!empty($get_paid_cat_banners)) {
                            $paid_category_banner = $get_paid_cat_banners;
                        }
                        $visible_banners = array_merge($category_banner, $paid_category_banner);
                        ?>
                        <?php if (!empty($visible_banners)) { ?>
                            <ul class="category-banners">
                                <?php foreach ($visible_banners as $visible_banner) { ?>
                                    <a href="{{$visible_banner['url']}}" target="_blank">
                                        <img src="<?php echo $visible_banner['image']; ?>" class="rounded-md w-full" style="height:300px;" />
                                    </a>
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    <?php } ?>
                </div>
                @if(count($filtered_data)>0)
                <div class="flex flex-wrap mt-2 mb-2" id="filter-results">

                    <?php $i = 0; ?>
                    @foreach($filtered_data as $d)
                    <?php
                    $i++;
                    $imgUrlfinal = App\Models\TblChat::getPostImgForList($d['id']);
                    $url = App\Models\TblPost::get_post_slug($d["slug"]); // post slug url //
                    $fav_style = App\Models\TblSavedPosts::check_fav($d['id']); // check if post is fav post //
                    $adtype = App\Models\TblPost::getAddtype($d->id); // get post package type //
                    $postloc = App\Models\TblPost::getPostloc($d->city); // get post location name //                   
                    $settings = App\Models\Setting::get_logos(); // show the curreny symbol //
                    $slected_currency = !empty($d->currency_id) ? $d->currency_id : $settings['default_currency'];
                    $currency_symbol = App\Models\TblPost::get_post_currency($slected_currency);
                    $locality_name = !empty($d['locality']) ? $d['locality'] : $postloc;
                    ?>
                    <div class="border-0 pb-6 rounded w-full md:w-1/3 sm:w-2/4 lg:w-1/3 xl:w-1/4 items-center">
                        <div class="border rounded-xl bg-gray-300 m-1 items-center">
                            <div class="flex flex-wrap items-center">
                                <div class="relative w-full px-2 max-w-full flex-grow flex-1">
                                    <?php
                                    if (!empty($adtype)) {
                                    ?>
                                        <h3 class="text-xs text-black">
                                            <span class="bg-yellow-500 px-2 py-1 uppercase">
                                                <?php echo str_replace('_', ' ', strtoupper($adtype->ad_type)); ?>
                                            </span>
                                        </h3>
                                    <?php } ?>
                                </div>
                                <div class="relative w-full pr-1 px-4 max-w-full flex-grow flex-1 text-right">
                                    <button type="button" id="favourate_post_id_{{$d->id}}" data-fav-post-id="{{ $d->id }}" value="{{$d->title}}" class="text-white leading-8 w-8 h-8 save_favourate rounded-full py-0 px-1 focus:outline-none text-center border-0 {{$fav_style}} "><i class="fa fa-heart text-base inline-block" aria-hidden="true"></i></button>
                                </div>
                            </div>
                            <div class="text-left">
                                <a href="{{$url}}">
                                    <img style="max-width: 90%; margin: 15px auto;height:150px;" alt="{{$d['title']}}" src="{{ $imgUrlfinal }}">
                                    <div class="overflow-auto rounded-t-none border rounded-xl flex-wrap py-3 px-3 bg-white items-center">
                                        <p class="text-lg pl-2 float-left pb-1 font-bold w-full"><?php echo mb_strimwidth($d['title'], 0, 16, "..."); ?></p>
                                        <p class="text-sm pl-2 pb-1"><?php echo $currency_symbol[0]; ?>{{ $d['price'] }}</p>
                                        <p class="pl-1 text-xs clear-left float-left mb-1"><i class="fa fa-map-marker fa-xs">&nbsp;</i> <?php echo mb_strimwidth($locality_name, 0, 18, "..."); ?></p>
                                        <p class="pr-1 text-xs mb-1 text-right">&nbsp;<?php echo date("d M Y", strtotime($d["created_at"])); ?></p>
                                    </div>
                                </a>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                <input type="hidden" class="totalpages" value="<?php echo $filtered_data->lastPage(); ?>" />
                <?php if ($filtered_data->lastPage() > 1) { ?>
                    <div class="flex flex-wrap justify-center text-center mb-4">
                        <button class="seemore text-white loadbtn btn btn-dark btn-lg shadow-sm px-4 bg-red-200 border rounded-3xl py-2 bg-indigo-500 border focus:outline-none border-gray-600" data-page="2" data-link="<?php echo url()->full(); ?>&page=" data-div="#filter-results">{{__('messages.Load More')}}</button>
                    </div>
                <?php } ?>
                @else
                <div class="w-full text-center">
                    <img src="<?php echo URL::to('/images/nodata.jpg'); ?>" class="m-auto" />
                    <p class="text-2xl pl-2 pb-1 font-bold">No data found!</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</section>
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
                if((searchParams('field_modelswithbrand') == true) && (searchParams('field_brand*and*model') == true)){
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
        var row = $(this).parent(".range-slider").attr('data-rowclass');
        var min_value = $(this).parent(".range-slider").find(".minprice_" + row).val();
        var max_value = $(this).parent(".range-slider").find(".maxprice_" + row).val();
        var name = $(this).parent(".range-slider").find(".rangevalue_" + row).attr('name');
        var is_param = searchParams(name);
        if (is_param == true) {
            var alterUrl = removeParams(name);
            var path = alterUrl + "&" + name + "=" + min_value + "-" + max_value;
        } else {
            var path = window.location.href + "&" + name + "=" + min_value + "-" + max_value;
        }
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