<div class="flex justify-center" wire:ignore><div id="banner_map"></div></div>

<?php
$myip = $_SERVER['REMOTE_ADDR'];
$json_data = file_get_contents("http://ip-api.com/json/$myip");
$data = json_decode($json_data, true);
$contry = "Singapore";                 //$data['country'];
$state =  "Singapore";                                //$data['regionName'];
$curr_lat = " 1.290270";                        //$data['lat'];
$curr_lon = "103.851959";                          //$data['lon'];
$settings = App\Models\Setting::where('key', 'home_banner_map')->get()->toArray();
$json_data = json_decode($settings[0]['value']);
$cover_distance = $json_data->cover_max_distance_km;
?>

<script>
/*$(document).ready(function() {
	setTimeout(function() {
	initMap();
	}, 2000);
});*/
//begin - get current location lat, lang
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});


let map;
function initMap() {

var styledMapType = new google.maps.StyledMapType(
[
    {
        "featureType": "all",
        "elementType": "labels.text",
        "stylers": [
            {
                "color": "#878787"
            }
        ]
    },
    {
        "featureType": "all",
        "elementType": "labels.text.stroke",
        "stylers": [
            {
                "visibility": "off"
            }
        ]
    },
    {
        "featureType": "landscape",
        "elementType": "all",
        "stylers": [
            {
                "color": "#f9f5ed"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "all",
        "stylers": [
            {
                "color": "#f5f5f5"
            }
        ]
    },
    {
        "featureType": "road.highway",
        "elementType": "geometry.stroke",
        "stylers": [
            {
                "color": "#c9c9c9"
            }
        ]
    },
    {
        "featureType": "water",
        "elementType": "all",
        "stylers": [
            {
                "color": "#aee0f4"
            }
        ]
    }, 
	{
		"elementType": "labels.icon",
		"stylers": [
		  {
			"visibility": "off"
		  }
		]
    }
],
      {name: 'Styled Map'});
	  
  
//style end (new added)

    $("#banner_map").css('display', 'block');
    //$("#banner_map").css('height', '500px');
    $("#banner_map").prop('class', 'w-full h-96');
    var curr_lat = jQuery("#latitude").val();//"9.9202912";
    var curr_lon = jQuery("#longitude").val();//"78.1294314";

    if(jQuery("#latitude").val() !=""){
        var curr_lat = jQuery("#latitude").val();
    }else{
        var curr_lat = '<?php echo $curr_lat;?>';
    }

    if(jQuery("#longitude").val() !=""){
        var curr_lon = jQuery("#longitude").val();
    }else{
        var curr_lon = '<?php echo $curr_lon;?>';
    }	
	
    if(jQuery("#countryName").val() !=""){
        var current_country = jQuery("#countryName").val();
    }else{
        var current_country = '<?php echo $contry;?>';
    }
    
    if(jQuery("#stateName").val() != ""){
        var current_state = jQuery("#stateName").val();
    }else{
        var current_state = '<?php echo $state;?>';
    }
    
    var current_cityname = jQuery("#cityName").val();
    var dist = '<?php echo $cover_distance; ?>';

    // //end - get current location lat, lang
    
    var center = new google.maps.LatLng(parseFloat(curr_lat), parseFloat(curr_lon));
    // MAP ATTRIBUTES.
    var mapAttr = {center: center, zoom: 12, zoomControl: false, streetViewControl: false, mapTypeControl: false};
    //THE MAP TO DISPLAY.
    var map = new google.maps.Map(document.getElementById("banner_map"), mapAttr);
	
  	//style give here
	  map.mapTypes.set('styled_map', styledMapType);
	  map.setMapTypeId('styled_map');
  
  
        //begin - circle shown for current city with specific km distance
        var circle = new google.maps.Circle({
            center: center,
            map: map,
			animation: google.maps.Animation.DROP,
            radius: parseFloat(dist) * 1000, //convert 1000 meter = 1 km
            fillColor: '#e5e5ff', //fillOpacity: 0.1,
            strokeColor: "green", //circle color
            strokeWeight: 1 //circle stroke
        });
        //end - circle shown for current city with specific km distance
		
//ajax
        var post_url = "<?php echo URL::to('km_ads_from_cur_dist'); ?>";
        $.ajax({
            type: 'POST',
            dataType: 'json',
            url: post_url,
            data: {lat: curr_lat, lon: curr_lon, dist: dist, current_country: current_country,current_state : current_state,current_cityname: current_cityname},
            success: function (data)
            {
                if (data.result == "success")
                {
                    if(data.data !=""){
                        var center = new google.maps.LatLng(parseFloat(data.data[0].latitude), parseFloat(data.data[0].logitude));
                    }else{
                        var center = new google.maps.LatLng(parseFloat(curr_lat), parseFloat(curr_lon));
                    }
                
                // MAP ATTRIBUTES.
                var mapAttr = {center: center, zoom: 12, zoomControl: false, streetViewControl: false, mapTypeControl: false };

                //THE MAP TO DISPLAY.
                var map = new google.maps.Map(document.getElementById("banner_map"), mapAttr);
				
					//style give here
					  map.mapTypes.set('styled_map', styledMapType);
					  map.setMapTypeId('styled_map');
					  
                //begin - circle shown for current city with specific km distance
                var circle = new google.maps.Circle({
                    center: center,
                    map: map,
					animation: google.maps.Animation.DROP,
                    radius: parseFloat(dist) * 1000, //convert 1000 meter = 1 km
                    fillColor: '#e5e5ff', //fillOpacity: 0.1,
                    strokeColor: "green", //circle color
                    strokeWeight: 1 //circle stroke
                });
                    var infoWindow = new google.maps.InfoWindow();
                    var arrayData = data.data;
                    for (var i = 0; i < arrayData.length; i++)
                    {
                        console.log(arrayData[i].icon);
                        var description = arrayData[i].description;
                        var lat = parseFloat(arrayData[i].latitude);
                        var lon = parseFloat(arrayData[i].logitude);
                        var iconUrl = arrayData[i].icon;

                        //map begin
                        const marker = new google.maps.Marker({
                            position: new google.maps.LatLng(lat, lon), //features[i].position,
                            icon: iconUrl,
                            map: map,
							animation: google.maps.Animation.DROP,
                            description: description
                        });
                        //create hyper links
                        // google.maps.event.addListener(marker, 'click', function() {
                        // window.open(this.url,'_blank');
                        // });

                        //Attach click event to the marker.
                        (function (marker, data) {
                            google.maps.event.addListener(marker, "click", function (e) {
                                //Wrap the content inside an HTML DIV in order to set height and width of InfoWindow.
                                infoWindow.setContent("<div style = 'width:200px;min-height:40px'>" + this.description + "</div>");
                                infoWindow.open(map, marker);
                            });
                        })(marker, data);

                        //map end

                    }
                }
            }
        });
//ajax




  
}
</script>
<!--Map end-->