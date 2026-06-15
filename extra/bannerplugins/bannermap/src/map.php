
<script>
var markers = new Array();
function initMap() {
    var styledMapType = new google.maps.StyledMapType(
// [
//   {
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#f5f5f5"
//       }
//     ]
//   },
//   {
//     "elementType": "labels.icon",
//     "stylers": [
//       {
//         "visibility": "off"
//       }
//     ]
//   },
//   {
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#616161"
//       }
//     ]
//   },
//   {
//     "elementType": "labels.text.stroke",
//     "stylers": [
//       {
//         "color": "#f5f5f5"
//       }
//     ]
//   },
//   {
//     "featureType": "administrative.land_parcel",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#bdbdbd"
//       }
//     ]
//   },
//   {
//     "featureType": "poi",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#eeeeee"
//       }
//     ]
//   },
//   {
//     "featureType": "poi",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#757575"
//       }
//     ]
//   },
//   {
//     "featureType": "poi.park",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#e5e5e5"
//       }
//     ]
//   },
//   {
//     "featureType": "poi.park",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#9e9e9e"
//       }
//     ]
//   },
//   {
//     "featureType": "road",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#ffffff"
//       }
//     ]
//   },
//   {
//     "featureType": "road.arterial",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#757575"
//       }
//     ]
//   },
//   {
//     "featureType": "road.highway",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#dadada"
//       }
//     ]
//   },
//   {
//     "featureType": "road.highway",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#616161"
//       }
//     ]
//   },
//   {
//     "featureType": "road.local",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#9e9e9e"
//       }
//     ]
//   },
//   {
//     "featureType": "transit.line",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#e5e5e5"
//       }
//     ]
//   },
//   {
//     "featureType": "transit.station",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#eeeeee"
//       }
//     ]
//   },
//   {
//     "featureType": "water",
//     "elementType": "geometry",
//     "stylers": [
//       {
//         "color": "#c9c9c9"
//       }
//     ]
//   },
//   {
//     "featureType": "water",
//     "elementType": "labels.text.fill",
//     "stylers": [
//       {
//         "color": "#9e9e9e"
//       }
//     ]
//   }
// ],
      {name: 'Styled Map'});
      if($('#latitude').length > 0  && $('#logitude').length > 0 ){

        var curr_lat = jQuery("#latitude").val();  //"9.9202912";
    
      var curr_lon =   jQuery("#logitude").val();  // "78.1294314";

      }else{

        var curr_lat = "9.9202912";
    
       var curr_lon = "78.1294314";

      }
    
    // console.log(jQuery("#log").val());
  var map = new google.maps.Map(document.getElementById('map_views'), {
    zoom: 10,
    center: {lat: 9.9202912, lng: 78.1294314},
    mapTypeId: google.maps.MapTypeId.ROADMAP,
    
  })
var imageoption = {
  url:'https://www.goavito.com/storage/banners/loc-mark.png',
  size: new google.maps.Size(104, 139),
   origin: new google.maps.Point(0, 0),
  // anchor: new google.maps.Point(0, 32)

};
  var infowindow = new google.maps.InfoWindow({});
  var i = 0;
  document.querySelectorAll('#map_det').forEach(event => {
    console.log(i);
    // console.log(event.getAttribute("data-lat"));
    // console.log(event.getAttribute("data-lon"));
    marker = new google.maps.Marker({
      title: event.getAttribute("data-title"),
	 // address:event.getAttribute("data-address"),
      position: new google.maps.LatLng(event.getAttribute("data-lat"), event.getAttribute("data-lon")),
      icon: imageoption,
      map: map,
	    animation: google.maps.Animation.DROP,
    });
markers.push(marker);
    google.maps.event.addListener(marker, 'click', (function (marker, i) {
      return function () {
        // var dir = event.getAttribute("data-title") + '<br><a href ="https://www.google.com/maps/search/?api=1&query=' + event.getAttribute("data-lat") + ',' + event.getAttribute("data-lon") + '" target="_blank" >Get directions</a>';
		
		var dir = '<div class="get_directions_section"><h4>'+ event.getAttribute("data-title") +'</h4><a class="get_direct" href="https://www.google.com/maps/search/?api=1&query=' + event.getAttribute("data-lat") + ',' + event.getAttribute("data-lon") +'" target="_blank">Get Directions</a></div>';
        infowindow.setContent(dir)
        infowindow.open(map, marker)
        map.setZoom(10);
			  map.setCenter(marker.getPosition());
      }
    })(marker, i)

    )
    //  google.maps.event.addListener(marker, 'focus', (function (marker, i) {     
    //   return function () {
    //     alert("haii");
    //   }
    // })(marker, i)
    // )
    google.maps.event.addListener(marker, 'mouseover', function() {

     // alert('test');
    
     
  });


    google.maps.event.addListener(marker, 'mouseout', (function (marker, i) {
      
      return function () {
     
        $("#services_" + i).removeClass("highlighted");
      }
    })(marker, i)
    )
    i++
  });

  map.mapTypes.set('styled_map', styledMapType);
  map.setMapTypeId('styled_map');

}

function triggerClick(i) {
  
  google.maps.event.trigger(markers[i], 'click');
}

</script>