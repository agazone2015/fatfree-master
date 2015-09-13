<div id="mapContainer">
    <div id="map">

    </div>
    <div id="searchBox">
        <label>Route to Closest Gllow</label>
        <input type="text" id="address" placeholder="Your address" autofocus>
        <button id="search">Show</button>
        <div id="result">
            <i class="fa fa-car"></i>
            <span id="distance"></span>
            <span id="duration"></span>
        </div>
    </div>
    <div id="loadMask">
        <span><i class="fa fa-circle-o-notch fa-spin"></i> Loading...</span>
    </div>
</div>
<section id="about">
    <span id="aboutHeader">About Us</span>
    <p>
        <?php echo $aboutUs; ?>
    </p>
</section>
<script>
    /*
     * Ions used for Google map markers are from address below
     * Maps Icons Collection https://mapicons.mapsmarker.com
     */

    var map;
    var cafeRichmond = 'Gllow Taiwanese cafe 448 Bridge Road Richmond VIC 3121';
    var cafeMalvern  = 'Gllow Taiwanese cafe 29 Station Street Malvern VIC 3144';
    var richmondMobile = '9429 9937';
    var malvernMobile = '9500 9995';
    var markers      = [];

    var directionsDisplay;
    var directionsService;

    var startMarker;
    var endMarker;
    var startInfoWindow;
    var endInfoWindow;

    window.onload = initMap;

    function initMap() {
        document.getElementById('address').focus();
        map = new google.maps.Map(
            document.getElementById('map'),
            { center: {lat: -37.833,lng: 145.016}, zoom: 13, zoomConrol: false, panControl: false, scaleControl: false,
             streetViewControl: false, overviewMapControl: false, disableDefaultUI: true, suppressMarkers: true}
        );

        // setup 2 markers on map for Richmond and Malvern
        getGeolocation(cafeRichmond, '448 Bridge Road Richmond VIC 3121', richmondMobile);
        getGeolocation(cafeMalvern,  '29 Station Street Malvern VIC 3144', malvernMobile);

        directionsDisplay = new google.maps.DirectionsRenderer({ suppressMarkers: true});
        directionsDisplay.setMap(map);

        // Add enter key listener to input box
        $('#address')[0].addEventListener('keyup', function (event) {
            if (event.keyCode === 13) {
                if (this.value.trim() !== '') {
                    $('#search').click();
                }
            }
        })

        /*
        * Function call:
        * Get Element with id 'search'
        * Assign click event listener
        * Assign a function to event click
        */
        $('#search').click(function () {
            var inputValue = $('#address').val();
            if (inputValue.trim() !== '') {
                $('#loadMask').css('display', 'block');
                $('#address').blur();
                calculateDistance(inputValue);
            }
        });
    }


    // Sets the map on all markers in the array.
    // If set map null for marker = hide marker
    function setMapOnAll(map) { for (var i = 0; i < markers.length; i++) { markers[i].setMap(map); } }

    // Declare function getGeolocation to display marker on the map
    function getGeolocation (address, shortAddress, mobile) {
        var baseUrl ='https://maps.googleapis.com/maps/api/geocode/json?address=';
        var key = '&key=AIzaSyCpQ3Qicw8ZdQm6BSP0sOBFNCR25hSMpjk';

        var finalRequest = baseUrl + address + ', Melbourne Australia' + key;

        $.ajax(
            {
                url:        finalRequest,
                success:    function (result) {
                    // log result in the console to understand structure of result
                    // console.log(result);

                    // store latitude and longtitude in local variable for shorthand
                    var latLng = result.results[0].geometry.location;
                    var lat = latLng.lat;
                    var long = latLng.lng;

                    // create latitude and longtitude object to call function setCenter
                    var ggLatLngObject = new google.maps.LatLng(lat,long);

                    // call function setCenter to point the map to the address
                    //map.setCenter(ggLatLngObject);

                    var marker = new google.maps.Marker(
                        {
                            // specific latitude and longtitude to place the marker
                            position: ggLatLngObject,
                            // specific what map to put marker on
                            map: map,
                            // some content for marker
                            title: address
                        }
                    );

                    var infowindow = new google.maps.InfoWindow({
                        content:    '<div class="infoWindow">'+
                                        '<span><b>Gllow Taiwanese Cafe</b></span>' +
                                        '<span><i class="fa fa-home"></i> '+shortAddress+'</span>' +
                                        '<span><i class="fa fa-phone"></i> ' + mobile + '</span>' +
                                        '<span><i class="fa fa-clock-o"></i> <span>Lunch:</span> 11am - 3pm</span>' +
                                        '<span><i class="fa fa-clock-o"></i> <span>Dinner:</span> 5pm - 10pm</span>' +
                                    '</div>'
                    });
                    infowindow.open(map, marker);

                    markers.push(marker);
                },
            }
        );
    }

    //'https://maps.googleapis.com/maps/api/distancematrix/json?
    //origins=Vancouver+BC|Seattle&destinations=San+Francisco|Victoria+BC

    function calculateDistance (address) {
        var baseUrl ='https://maps.googleapis.com/maps/api/distancematrix/json?';
        var key = '&key=AIzaSyCpQ3Qicw8ZdQm6BSP0sOBFNCR25hSMpjk';
        var mode = '&mode=car';

        var compareRichmond = 'origins=' + address.replace(/ /g, '+') + '&destinations=' + cafeRichmond.replace(/ /g, '+');
        var compareMalvern  = 'origins=' + address.replace(/ /g, '+') + '&destinations=' + cafeMalvern.replace(/ /g, '+');

        var RichmondRequest = baseUrl + compareRichmond + mode + key;
        var MalvernRequest  = baseUrl + compareMalvern  + mode + key;

        $.ajax(
            {
                url: 'cafes/find/'+address,
                method: 'post',
                data: {address: address},
                success: function (result) {
                    console.log(result);
                    // we can name 'result' any name, to indicate response from server

                    var toRichmond = result[0].rows[0].elements[0];
                    var distanceToRichmond = toRichmond.distance.value;
                    var durationToRichmond = toRichmond.duration.value;

                    var toMalvern = result[1].rows[0].elements[0];
                    var distanceToMalvern = toMalvern.distance.value;
                    var durationToMalvern = toMalvern.duration.value;

                    // Now we compare
                    var closestCafe = '';
                    // Store the full - right origin address from GG
                    var origin = '';
                    var time = '';
                    var distance = '';
                    if (distanceToMalvern > distanceToRichmond) {
                        // this means richmond is closer
                        closestCafe = cafeRichmond;
                        origin      = result[0].origin_addresses[0];
                        time        = toRichmond.duration.text;
                        distance    = toRichmond.distance.text;
                    } else {
                        // this means malvern is closer
                        closestCafe = cafeMalvern;
                        origin      = result[1].origin_addresses[0];
                        time        = toMalvern.duration.text;
                        distance    = toMalvern.distance.text;
                    }

                    $('#duration').html('Duration: ' + time);
                    $('#distance').html('Distance: ' + distance);

                    // Hide away initial markers
                    setMapOnAll(null);

                    // now we calculate and display on map
                    displayRoute(origin, closestCafe);
                }
            }
        );

    }

    function displayRoute(originAddress, destinationCafe) {
        directionsService = new google.maps.DirectionsService();
        var request = {
            origin: originAddress,
            destination: destinationCafe,
            travelMode: google.maps.TravelMode.DRIVING,
            waypoints: [],
            optimizeWaypoints: false,
            provideRouteAlternatives: true,
            avoidHighways: true,
        };
        directionsService.route(request, function(result, status) {
            console.log('result:', result);
            console.log('status:', status);
            if (status == google.maps.DirectionsStatus.OK) {
                directionsDisplay.setDirections(result);

                // first clean old markers
                if (startMarker) { startMarker.setMap(null);}
                if (endMarker) { endMarker.setMap(null);}
                // result.routes[0].legs[0].end_location | start_location
                // setup custom marker
                startMarker = new google.maps.Marker({
                    // specific latitude and longtitude to place the marker
                    position: result.routes[0].legs[0].start_location,
                    // specific what map to put marker on
                    map: map,
                    // some content for marker
                    title: result.routes[0].legs[0].start_address,
                });
                var mobile;
                if (result.routes[0].legs[0].end_address.toLowerCase().indexOf('malvern') != -1) {
                    mobile = malvernMobile;
                } else { mobile = richmondMobile; }

                endMarker = new google.maps.Marker({
                    position: result.routes[0].legs[0].end_location,
                    map: map,
                    title: result.routes[0].legs[0].end_address,
                    icon: 'ui/img/markers/restaurant_korean.png'
                });
                endInfoWindow = new google.maps.InfoWindow({
                    content:    '<div class="infoWindow">'+
                                    '<span><b>Gllow Taiwanese Cafe</b></span>' +
                                    '<span><i class="fa fa-home"></i> '+result.routes[0].legs[0].end_address+'</span>' +
                                    '<span><i class="fa fa-phone"></i> ' + mobile + '</span>' +
                                    '<span><i class="fa fa-clock-o"></i> <span>Lunch:</span> 11am - 3pm</span>' +
                                    '<span><i class="fa fa-clock-o"></i> <span>Dinner:</span> 5pm - 10pm</span>' +
                                '</div>'
                });
                endInfoWindow.open(map, endMarker);
                $('#loadMask').css('display', 'none');
                $('#searchBox').addClass('done');
                setTimeout(function () {
                    $('#result').css('display', 'block');
                }, 500);
                $('#address').focus();
            }
        });
    }
</script>
<script src="https://maps.googleapis.com/maps/api/js"></script>
