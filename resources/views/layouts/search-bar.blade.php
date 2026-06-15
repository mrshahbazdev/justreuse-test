<div class="search_prod_loc w-full relative bg-white lg:bg-transparent lg:max-w-screen-md ml-auto mr-auto lg:absolute right-0 left-0 pt-4 pb-4 lg:pt-0 lg:pb-0 inset-y-1/3 flex items-center h-auto">
    <?php
    if (!empty(request()->loc)) {
        $lc = request()->loc;
    } else if (Session::has("GetAddress")) {
        $lc = Session::get("GetAddress");
    } else {
        $lc = Session::get("GetCountry");
    }
    ?>
    <?php
    if (!empty(request()->loc)) {
        $ci = !empty(request()->city) ? request()->city : Session::get("GetCity");
    } else if (Session::has("GetCity")) {
        $ci = Session::get("GetCity");
    } else {
        $ci = "";
    }

    if (!empty(request()->loc)) {
        $locality = !empty(request()->locality) ? request()->locality : Session::get("GetLocality");
    } else if (Session::has("GetLocality")) {
        $locality = Session::get("GetLocality");
    } else {
        $locality = "";
    }
    ?>
    <?php
    if (!empty(request()->loc)) {
        $lat = !empty(request()->lat) ? request()->lat : Session::get("Getlat");
    } else if (Session::has("Getlat")) {
        $lat = Session::get("Getlat");
    } else {
        $lat = "";
    }
    ?>
    <?php
    if (!empty(request()->loc)) {
        $lng = !empty(request()->lng) ? request()->lng : Session::get("Getlng");
    } else if (Session::has("Getlng")) {
        $lng = Session::get("Getlng");
    } else {
        $lng = "";
    }
    ?>
    <?php
    if (!empty(request()->loc)) {
        $st = !empty(request()->state) ? request()->state : Session::get("GetState");
    } else if (Session::has("GetState")) {
        $st = Session::get("GetState");
    } else {
        $st = "";
    }
    ?>
    <?php
    if (!empty(request()->loc)) {
        $cut = !empty(request()->country) ? request()->country : Session::get("GetCountry");
    } else if (Session::has("GetCountry")) {
        $cut = Session::get("GetCountry");
    } else {
        $cut = "";
    }
    ?>
    <?php
    if (Session::has("GetSearched")) {
        $cconty = Session::get("GetSearched");
    } else if (!empty(request()->country)) {
        $cconty = str_replace(' ', '_', strtolower(request()->country));
    } else {
        $cconty = str_replace(' ', '_', strtolower(Session::get("GetCountry")));
    }
    ?>
    <div class="container px-4 mx-auto">
        <div class="w-full relative flex flex-wrap items-stretch lg:w-2/4 mb-4 float-left">
            <span class="z-20 h-full leading-snug font-normal absolute text-center text-green-700 absolute bg-transparent rounded text-base items-center justify-center w-12 pl-7 py-4">
                <img src="{{ URL::to('images/frontend/Group111.png') }}"></span>
            <input type="text" id="location" placeholder="{{__('messages.location')}}" value="<?php echo $lc; ?>" class="w-2/4 border-0 px-3 py-4  relative bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-14">
        </div>
        <div class="w-full relative flex flex-wrap items-stretch lg:w-2/4 mb-4 float-left ">
            <div class=" w-full ml-0 lg:ml-7 relative">
                <span class="z-20 h-full leading-snug font-normal absolute text-center text-green-700 absolute bg-transparent rounded text-base items-center justify-center w-12 pl-7 py-4">
                    <img src="{{ URL::to('images/frontend/iconfinder_search_126577.png') }}"></span>
                <input type="text" id="term" placeholder="{{__('messages.search product/category')}}" value="<?php echo (!empty(request()->s) ? request()->s : "") ?>" class="border-0 px-3 py-4  relative  bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full pl-14">

                <div class="absolute top-full bg-white left-0 right-0 z-30"><ul class="z-20 top-full left-0 bg-white shadow-md" id="filtered_search"></ul></div>

            </div>
        </div>
        <div>
            <input id="category" type="hidden" value="<?php echo (!empty(request()->c) ? request()->c : "") ?>">
            <input type="hidden" id="cityName" value="<?php echo $ci; ?>">
            <input type="hidden" id="locality" value="<?php echo $locality; ?>">
            <input type="hidden" id="countryName" value="<?php echo $cut ?>">
            <input type="hidden" id="stateName" value="<?php echo $st; ?>">
            <input type="hidden" id="currentCountry" value="<?php echo $cconty; ?>">
            <input type="hidden" id="currentLocation" value="<?php echo Session::get("GetCountry"); ?>">
            <input type="hidden" id="latitude" value="<?php echo $lat; ?>" />
            <input type="hidden" id="longitude" value="<?php echo $lng; ?>" />
            <input type="hidden" id="is_location_allowed" value="0" />
            <input type="hidden" id="subcategory" value="">
            <input type="hidden" id="is_search" value="<?php echo Session::get("IsSearch"); ?>">
        </div>
        <!-- <div class="w-full lg:w-1/4  float-right">
            <select name="cars" id="cars" class="border-0 px-3 text-left py-4 placeholder-blueGray-300 text-blueGray-600 relative bg-white bg-white rounded text-sm shadow outline-none focus:outline-none focus:ring w-full">
                <option value="volvo" class="text-blueGray-600 text-left">Categories</option>
            </select>
            <br><br>
        </div> -->
        <div class="relative w-full text-center float-left">
            <button class="px-6 sm:px-10 py-3 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-150" id="search">{{__('messages.find')}}</button>
        </div>
    </div>
    {!! config('app.google_map_script') !!}
    <script>
        jQuery(function($) {
            var searchText='';
            $("#term").on("keyup",function(e){
                searchText = this.value;
                if(searchText.length<4) {
                    $("#filtered_search").html('');
                }
            });

            $("#term").autocomplete({
                    minLength: 3,
                    source: function(request, response) {
                        jQuery.ajax({
                            url: "{{ URL::to('/search') }}",
                            dataType: "json",
                            data: {
                                q: request.term,
                            },
                            success: function(data) {


                                //old method
                                // response(jQuery.map(data.data, function(item) {
                                //     return {
                                //         label: item.value,
                                //         value: item.value,
                                //         id: item.id,
                                //         type: item.type,
                                //         category_id: item.category_id,
                                //     };
                                // }));
                                var outputAr = data.data;
                                //console.log("dddd--"+JSON.stringify(data)+"--- data len");
                                var li = '';
                                $.each(outputAr, function (currentIndex, currentElem) {
                                    var title='', type='';
                                    //console.log("---"+currentElem.value);
                                    title = currentElem.value;
                                    type = (currentElem.type=="")?"":"  <span class='text-xs'>("+currentElem.type+")</span>";
                                    li += '<li class="border-b border-gray-200"><span class="text-opacity-100 block px-4 py-3 hover:text-green-500 text-sm lg:text-base xl:text-lg cursor-pointer click_from_search" data-search-term="'+title+'" data-search-category="'+currentElem.category_id+'">'+title+type+'</span></li>';
                                });
                                $("#filtered_search").html('');
                                $("#filtered_search").append(li);

                                
                                
                            }
                        });
                    }
                })
                .data("ui-autocomplete")._renderItem = function(ul, item) {
                    return $("<li>")
                        .append("<p>" + item.label + "</p>")
                        .append("<p class=cname>" + item.type + "</p>")
                        .appendTo(ul);
                };
        });
        $('#term').on('autocompleteselect', function(e, ui) {
            $('#subcategory').val(ui.item.category_id);
        });

        $("body").delegate(".click_from_search","click",function(){
        //$(".click_from_search").click(function(e){
            var term = $(this).attr('data-search-term');
            var category = $(this).attr('data-search-category');
            if(category.length>0 && term.length>0){
                $("#category").val(category);
                $("#term").val('');
            }
            
            if(category.length==0 && term.length>0)
            {
                $("#category").val('');
                $("#term").val(term);
                
            }
            $("#search").trigger('click');
            
            //document.getElementById("category").value

        });

        jQuery(function($) {
            //for location search map
            var input = document.getElementById('location');
            var autocomplete = new google.maps.places.Autocomplete(input);
            google.maps.event.addDomListener(input, 'keydown', function(event) {
                if (event.keyCode === 13) {
                    event.preventDefault();
                }
            });
            google.maps.event.addListener(autocomplete, 'place_changed', function() {
                $('#cityName').val('');
                $('#countryName').val('');
                $('#stateName').val('');
                $('#locality').val('');
                var place = autocomplete.getPlace();
                var address = place.address_components;
                var city, state;
                address.forEach(function(component) {
                    var types = component.types;
                    if (types.indexOf('administrative_area_level_2') > -1) {
                        city = component.long_name;
                    }
                    if (types.indexOf('administrative_area_level_1') > -1) {
                        state = component.long_name;
                    }
                });
                console.log(address);
                const country = place.address_components.find(item => item.types.includes('country'));
                $('#locality').val(place.name);
                $('#cityName').val(city);
                $('#countryName').val(country.long_name);
                $('#currentCountry').val(country.long_name);
                $('#stateName').val(state);
                $('#latitude').val(place.geometry.location.lat());
                $('#longitude').val(place.geometry.location.lng());
            });
        });
        document.getElementById("search").addEventListener("click", function() {
            var path = "{{ URL::to('/') }}";
            if (document.getElementById("subcategory").value != "") {
                var c = document.getElementById("subcategory").value;
            } else if (document.getElementById("category").value != "") {
                var c = document.getElementById("category").value;
            } else {
                var c = "";
            }

            if (document.getElementById("subcategory").value != "") {
                var term = "";
            } else if (document.getElementById("term").value != "") {
                var term = document.getElementById("term").value;
            } else {
                var term = "";
            }

            if ((document.getElementById("location").value) != "") {
                if (document.getElementById("locality").value != "") {
                    var current_address = removeSpace(document.getElementById("locality").value);
                } else if (document.getElementById("cityName").value != "") {
                    var current_address = removeSpace(document.getElementById("cityName").value);
                } else if (document.getElementById("stateName").value != "") {
                    var current_address = removeSpace(document.getElementById("stateName").value);
                } else {
                    var current_address = removeSpace(document.getElementById("currentCountry").value);
                }

                if (document.getElementById("cityName").value != "") {
                    var locality = document.getElementById("locality").value;
                } else {
                    var locality = "";
                }

                if (document.getElementById("cityName").value != "") {
                    var city = document.getElementById("cityName").value;
                } else {
                    var city = "";
                }
                var state = document.getElementById("stateName").value;
                var country = document.getElementById("countryName").value;
                var latitude = document.getElementById("latitude").value;
                var longitude = document.getElementById("longitude").value;
                var location = document.getElementById("location").value;
            } else {
                var city = "";
                var country = document.getElementById("currentLocation").value;
                var state = "";
                var location = document.getElementById("currentLocation").value;
                var current_address = document.getElementById("currentCountry").value;
            }
            result = path + "/" + current_address + "?c=" + c + "&s=" + term + "&locality=" + locality + "&city=" + city + "&state=" + state + "&country=" + country + "&lat=" + latitude + "&lng=" + longitude + "&loc=" + location + "&d=5";
            //alert(result);
            window.location = result;
        });

        function removeSpace(value) {
            var a = value.toLowerCase();
            // var b = a.replaceAll(' ', '_');
            var b = a.split(' ').join('_');
            return b;
        }
    </script>
</div>