<!-- jQuery Library -->
<script src="https://code.jquery.com/jquery-3.6.4.min.js"></script>

<!-- jQuery UI Library -->
<script src="https://code.jquery.com/ui/1.13.2/jquery-ui.min.js"></script>

<!-- jQuery UI CSS -->
<link rel="stylesheet" href="https://code.jquery.com/ui/1.13.2/themes/base/jquery-ui.css">
	
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

	<!--Begin - ENGLISH Alignment -->
	<?php 
	$dir_rtl =  App\Models\Setting::is_dir_rtl();
	if($dir_rtl =="false"){  ?>
	<div class="search_prod_loc search-field-open absolute top-full z-10 lg:relative lg:top-auto bg-white w-full lg:w-2/5 xl:w-1/2 float-left hidden lg:block p-3 sm:p-5 lg:p-0 shadow lg:shadow-none" style="{{ request()->is('/') ? 'display:none;' : '' }}">
		
		<div class="w-full flex lg:block flex-col float-left xl:px-6">
				<div class="relative flex items-center float-left sm:w-2/4 w-full order-2">
				<div class=" w-full flex bg-gray-100 border border-gray-200 h-12 lg:h-14 rounded-full  my-1 lg:my-0">
				<label for="location" class="sr-only">{{ __('messages.location') }}</label>
					<!--<span class="z-20 h-full leading-snug font-normal absolute text-center text-green-700 absolute bg-transparent rounded text-base items-center justify-center w-6 pl-2 py-3">
						<img class="mt-1" src="{{ URL::to('images/frontend/iconfinder_search_126577.png') }}"></span>-->
					<input type="text" id="term" placeholder="{{__('messages.search product/category')}}" value="<?php echo (!empty(request()->s) ? request()->s : "") ?>" class=" px-6 rounded-full py-3 relative bg-gray-100 rounded text-sm outline-none focus:outline-none w-full lg:mr-0 h-12 lg:h-14 ui-autocomplete-input">
					<div class="absolute top-full bg-white left-0 right-0 z-40"><ul class="z-20 top-full left-0 bg-white shadow-md" id="filtered_search"></ul></div>
					<div class="relative text-center flex">
					<button class="pr-6 " aria-label="search" id="search"><i class="fa fa-search"></i></button>
					</div>
				</div>
				
			</div>
			<div class="relative sm:flex sm:flex-wrap sm:items-stretch float-left sm:w-2/4 w-full mb-2 sm:mb-0 lg:px-3 my-1 lg:my-0">
				<span class="absolute right-8 text-xl z-10 top-3 lg:top-4">
					<i class="fa fa-map-marker" aria-hidden="true"></i></span>
				<input type="text" id="location" placeholder="" value="<?php echo $lc; ?>" class="border border-gray-200 px-6 rounded-full py-3 relative bg-gray-100 rounded text-sm outline-none focus:outline-none w-full lg:mr-0 h-12  lg:h-14 pr-10">
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
		</div>
	
	</div>
		<!--End - EN Alignment -->
		<?php } else { ?>
		<!--Begin - AR Alignment -->

		<div class="search_prod_loc search-field-open absolute top-full z-10 lg:relative lg:top-auto bg-white w-full lg:w-2/5 xl:w-1/2 float-left lg:block p-3 sm:p-5 lg:p-0 shadow lg:shadow-none"
		     style="{{ request()->is('/') ? 'display:none;' : '' }}">





		<div class="w-full float-left xl:px-6">
			
			<div class="relative sm:flex sm:flex-wrap sm:items-stretch float-left sm:w-2/4 w-full mb-2 sm:mb-0">
			<label for="location" class="sr-only">{{ __('messages.location') }}</label>
				<span class="z-20 h-full leading-snug font-normal absolute text-center text-green-700 absolute bg-transparent rounded text-base items-center justify-center w-7 pr-2 py-3 pb-2">
					<img src="{{ URL::to('images/frontend/Group111.png') }}"></span>
				<input type="text" id="location" placeholder="{{__('messages.location')}}" value="<?php echo $lc; ?>" class="w-full  border border-green-500 lg:border-0 px-3 py-3 relative bg-white rounded text-sm shadow outline-none focus:outline-none  w-full pr-9 sm:ml-3 h-11">
			</div>
			
			<div class="relative flex items-center float-left sm:w-2/4 w-full">
				<div class=" w-full flex">
					<span class="z-20 h-full leading-snug font-normal absolute text-center text-green-700 absolute bg-transparent rounded text-base items-center justify-center w-6 pr-2 py-3">
						<img class="mt-1" src="{{ URL::to('images/frontend/iconfinder_search_126577.png') }}"></span>
					<input type="text" id="term" placeholder="{{__('messages.search product/category')}}" value="<?php echo (!empty(request()->s) ? request()->s : "") ?>" class="border border-green-500 lg:border-0 px-3 py-3 relative  bg-white rounded text-sm shadow outline-none focus:outline-none w-full pr-9 ml-3 lg:ml-0 xl:ml-3 h-11">
					<div class="absolute top-full bg-white left-0 right-0 z-40"><ul class="z-20 top-full left-0 bg-white shadow-md" id="filtered_search"></ul></div>

				</div>
				<div class="relative text-center flex">
					<button class="px-3 sm:px-4 xl:px-6 py-2 text-white hover:text-green-500 border-2 border-green-500 hover:bg-white bg-green-500 text-sm font-bold uppercase rounded shadow hover:shadow-md outline-none focus:outline-none ease-linear transition-all duration-500 text-base font-bold h-11" id="search"><i class="fa fa-search"></i></button>
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
		</div>
		
		
	</div>
	<?php } ?>
<!--End - AR Alignment -->



	 {!! config('app.google_map_script') !!} 
		<script>
			jQuery(function($){
		    if (window.location.pathname === "/" || window.location.pathname === "") {
		        $(".search_prod_loc").css("display", "none");
		    }
		});

			jQuery(function ($) {
		    var searchText = '';

		    // Handle keyup for both inputs: #term & #termd
		    $("#term, #termd").on("keyup", function (e) {
		        searchText = this.value;
		        if (searchText.length < 4) {
		            $("#filtered_search").html('');
		        }
		    });

		    // Apply autocomplete on both inputs
		    $("#term, #termd").each(function () {
		        $(this).autocomplete({
		            minLength: 3,
		            source: function (request, response) {
		                jQuery.ajax({
		                    url: "{{ URL::to('/search') }}",
		                    dataType: "json",
		                    data: {
		                        q: request.term,
		                    },
		                    success: function (data) {
		                        var outputAr = data.data;
		                        var li = '';

		                        $.each(outputAr, function (currentIndex, currentElem) {
		                            var title = currentElem.value;
		                            var type = (currentElem.type == "") ? "" : "  <span class='text-xs'>(" + currentElem.type + ")</span>";
		                            li += '<li class="border-b border-gray-200">' +
		                                '<span class="text-opacity-100 block px-4 py-3 hover:text-green-500 text-sm lg:text-base xl:text-lg cursor-pointer click_from_search" data-search-term="' + title + '" data-search-category="' + currentElem.category_id + '">' +
		                                title + type +
		                                '</span></li>';
		                        });

		                        $("#filtered_search").html('');
		                        $("#filtered_search").append(li);
		                    }
		                });
		            }
		        })
		        .data("ui-autocomplete")._renderItem = function (ul, item) {
		            return $("<li>")
		                .append("<p>" + item.label + "</p>")
		                .append("<p class=cname>" + item.type + "</p>")
		                .appendTo(ul);
		        };
		    });

		    // Autocomplete select handler for both inputs
		    $('#term, #termd').on('autocompleteselect', function (e, ui) {
		        $('#subcategory').val(ui.item.category_id);
		    });

		    // Click event for search results (from autocomplete list)
		    $("body").delegate(".click_from_search", "click", function () {
		        var term = $(this).attr('data-search-term');
		        var category = $(this).attr('data-search-category');

		        if (category.length > 0 && term.length > 0) {
		            $("#category").val(category);
		            $("#term, #termd").val('');
		        }

		        if (category.length == 0 && term.length > 0) {
		            $("#category").val('');
		            $("#term, #termd").val(term);
		        }

		        $("#search").trigger('click');
		    });
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
			["search", "searched"].forEach(function (id) {
			    document.getElementById(id).addEventListener("click", function () {
			       

			        var path = "{{ URL::to('/') }}";

			        // Category & Subcategory Handling
			        var c = "";
			        if (document.getElementById("subcategory").value != "") {
			            c = document.getElementById("subcategory").value;
			        } else if (document.getElementById("category").value != "") {
			            c = document.getElementById("category").value;
			        }

			        // Term Handling
			        var term = "";
			        if (
					    document.getElementById("subcategory").value == "" &&
					    document.getElementById("term").value != ""
					) {
					    term = document.getElementById("term").value;

					// Agar #term empty hai lekin #termd me value hai to use karo
					} else if (
					    document.getElementById("subcategory").value == "" &&
					    document.getElementById("termd").value != ""
					) {
					    term = document.getElementById("termd").value;
					}

			        // Location Handling
			        var result = "";
			        if (document.getElementById("location").value != "") {
			            var current_address = "";
			            if (document.getElementById("locality").value != "") {
			                current_address = removeSpace(document.getElementById("locality").value);
			            } else if (document.getElementById("cityName").value != "") {
			                current_address = removeSpace(document.getElementById("cityName").value);
			            } else if (document.getElementById("stateName").value != "") {
			                current_address = removeSpace(document.getElementById("stateName").value);
			            } else {
			                current_address = removeSpace(document.getElementById("currentCountry").value);
			            }

			            var locality = document.getElementById("cityName").value != "" ? document.getElementById("locality").value : "";
			            var city = document.getElementById("cityName").value || "";
			            var state = document.getElementById("stateName").value;
			            var country = document.getElementById("countryName").value;
			            var latitude = document.getElementById("latitude").value;
			            var longitude = document.getElementById("longitude").value;
			            var location = document.getElementById("location").value;

			            result =
			                path +
			                "/" +
			                current_address +
			                "?c=" +
			                c +
			                "&s=" +
			                term +
			                "&locality=" +
			                locality +
			                "&city=" +
			                city +
			                "&state=" +
			                state +
			                "&country=" +
			                country +
			                "&lat=" +
			                latitude +
			                "&lng=" +
			                longitude +
			                "&loc=" +
			                location +
			                "&d=5";
			        } else {
			            var country = document.getElementById("currentLocation").value;
			            var location = document.getElementById("currentLocation").value;
			            var current_address = document.getElementById("currentCountry").value;

			            result =
			                path +
			                "/" +
			                current_address +
			                "?country=" +
			                country +
			                "&loc=" +
			                location +
			                "&d=5";
			        }

			        // Final Redirect
			        window.location = result;
			    });
			});


			function removeSpace(value) {
				var a = value.toLowerCase();
				// var b = a.replaceAll(' ', '_');
				var b = a.split(' ').join('_');
				return b;
			}
		</script>
	