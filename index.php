<?php
    $endpoint = 'https://placestostay.alexpotter.me/apotter/index.php/api/';
    $apiKey = '9dfb184560a245a';
?>

<!html>
<html>
<head>
    <title>Visit Colorado</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- jQuery -->
    <script src="dist/js/jquery.min.js"></script>
    <!-- Bootstrap -->
    <link rel="stylesheet" href="bootstrap/css/bootstrap.css">
    <script type="text/javascript" src="bootstrap/js/bootstrap.js"></script>
    <!-- Custom scripts -->
    <link rel="stylesheet" href="dist/css/calendar.css">
    <link rel="stylesheet" href="dist/css/style.css">
    <!-- Date Stuff -->
    <link rel="stylesheet" href="//code.jquery.com/ui/1.11.4/themes/smoothness/jquery-ui.css">
    <script src="//code.jquery.com/ui/1.11.4/jquery-ui.js"></script>
    <script src="dist/js/calendar.js"></script>
    <script>
        $(function() {
            localStorage.clear();
            $( "#dateFrom" ).datepicker();
            $( "#dateTo" ).datepicker();
        });
    </script>
</head>
<body>
<div id="splash-index">
    <div class="overlay"></div>
    <div class="splash-text-container-outer">
        <div class="splash-text-container">
            <p class="main-message">
                Visit Colorado
            </p>
            <p class="sub-message">
                <a class="start-search" data-scroll href="#searchContainer">Start Search</a>
            </p>
        </div>
    </div>
</div>
<div id="searchContainer">
    <div class="row">
        <div class="col-xs-12">
            <div class="row">
                <div class="col-sm-10 col-sm-offset-1" id="error" style="display: none;">
    
                </div>
            </div>
            <form class="form-horizontal" id="searchPlace" method="post" action="<?php echo $endpoint; ?>search">
                <div class="col-sm-8 col-sm-offset-2">
                    <div class="form-group">
                        <div class="col-xs-12">
                            <select class="form-control" style="font-size: 22pt; height: 60px;" id="location_type">
                                <option value="0">Select location type</option>
                                <option value="Hotel">Hotel</option>
                                <option value="Hostel">Hostel</option>
                                <option value="Bed and Breakfast">Bed and Breakfast</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="col-md-6">
                            <input name="available_to" class="form-control" type="text" id="dateFrom" style="font-size: 22pt; height: 60px;" placeholder="Date To">
                        </div>
                        <div class="col-md-6">
                            <input name="available_from" type="text" class="form-control" id="dateTo" style="font-size: 22pt; height: 60px;" placeholder="Date From">
                        </div>
                    </div>
                    <div class="form-group">
                        <button type="submit" class="btn search">Search</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<div id="resultsContainer">
    <div class="row">
        <div class="col-xs-12">
            <div id="map">
    
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-xs-12">
            <div id="results">
    
            </div>
        </div>
    </div>
</div>
<!-- Button trigger modal -->
<button type="button" class="btn btn-primary btn-lg" data-toggle="modal" id="displayAvailability" data-target="#roomAvailability" style="display: none;">
    Availability
</button>
<!-- Modal -->
<div class="modal fade modal-lg" id="roomAvailability" tabindex="-1" role="dialog" aria-labelledby="availabilityModalLabel" style="margin: auto;">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h4 class="modal-title" id="myModalLabel">Room Availability</h4>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-xs-12" id="roomDateSelector">

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-danger btn-lg" data-dismiss="modal" onclick="location.reload();">Close</button>
            </div>
        </div>
    </div>
</div>
<script>
    // Note: This example requires that you consent to location sharing when
    // prompted by your browser. If you see the error "The Geolocation service
    // failed.", it means you probably did not give permission for the browser to
    // locate you.

    function initMap() {
        var map = new google.maps.Map(document.getElementById('map'), {
            center: {lat: 39.550051, lng: -105.782067},
            zoom: 8
        });
        var infoWindow = new google.maps.InfoWindow({map: map});
        var geocoder = new google.maps.Geocoder;

        // Try HTML5 geolocation.
        if (navigator.geolocation) {
            navigator.geolocation.getCurrentPosition(function(position) {
                var pos = {
                    lat: position.coords.latitude,
                    lng: position.coords.longitude
                };

                infoWindow.setPosition(pos);
                infoWindow.setContent('Your location');
                map.setCenter(pos);
                map.setZoom(10);
            }, function() {
                handleLocationError(true, infoWindow, map.getCenter());
            });
        } else {
            // Browser doesn't support Geolocation
            handleLocationError(false, infoWindow, map.getCenter());
        }

        $('#searchPlace').submit(function(e) {
            e.preventDefault();

            infoWindow.close();

            var dateFrom = $('#dateFrom').val().split('/');
            localStorage.setItem('dateFrom', $('#dateFrom').val());
            dateFrom = dateFrom[2] + '-' + dateFrom[0] + '-' + dateFrom[1];

            var dateTo = $('#dateTo').val().split('/');
            localStorage.setItem('dateTo', $('#dateTo').val());
            dateTo = dateTo[2] + '-' + dateTo[0] + '-' + dateTo[1];

            var url = $( this ).prop( 'action' ) + '/colorado/' + dateFrom + '/'
                + dateTo + '/' + $('#location_type').val() + '?api_key=<?php echo $apiKey; ?>';

            $.get(url).done(function (rooms) {
                    $('#error').hide().html();

                    var html = '\
                    <table class="table" style="margin-top: 20px;">\
                        <tr>\
                            <th>Location</th>\
                            <th>Type</th>\
                            <th>Room Description</th>\
                            <th>Beds</th>\
                            <th>Price per night</th>\
                            <th>View on map</th>\
                            <th>View dates and book</th>\
                        <tr/>\
                    ';

                    var count = 0;
                    $.each(rooms, function (keys, params) {
                        $.each(params, function(key, param) {

                            var name = param.name;
                            var type = param.location_type;

                            localStorage.setItem("location" + param.ID, JSON.stringify(param));

                            // Drop pins
                            var location = {lat: parseFloat(param.lat), lng: parseFloat(param.lng)};

                            var contentString = '\
                            <div id="content">\
                                <div id="siteNotice">\
                                </div>\
                                <h1 id="firstHeading" class="firstHeading">' + param.name + '</h1>\
                                <div id="bodyContent">\
                                    <div class="row">';

                            if (count == 0)
                            {
                                map.setCenter(location);
                                map.setZoom(14);
                            }

                            $.each(param.rooms, function(key1, param1) {
                                html += '\
                                    <tr>\
                                        <td>' + name + '</td>\
                                        <td>' + type + '</td>\
                                        <td>' + param1.room_description + '</td>\
                                        <td>' + param1.number_of_beds + '</td>\
                                        <td> £ ' + parseFloat(param1.room_price / 100).toFixed(2) + '</td>\
                                        <td><a href="#" class="viewOnMap" data-value="' + param.ID + '">View on map</a></td>\
                                        <td><a href="#" class="viewAndBook" data-value="' + param1.ID + '">View and Book</a></td>\
                                    </tr>\
                                ';

                                contentString += '\
                                <div class="col-md-4">\
                                    <p>\
                                        '+ param1.room_description + '\
                                    </p>\
                                    <p>\
                                        Number of beds: ' + param1.number_of_beds +'\
                                        <br>\
                                        Price per night: £ ' + parseFloat(param1.room_price / 100).toFixed(2) + '\
                                    <p>\
                                        <button class="btn btn-success viewAndBook" data-value="' + param1.ID + '">Book now</button>\
                                    </p>\
                                </div>';

                                localStorage.setItem("room" + param1.ID, JSON.stringify(param1));
                            });

                            contentString += '\
                                    </div>\
                                </div>\
                            </div>';

                            var infoWindow = new google.maps.InfoWindow({
                                content: contentString
                            });
                            var marker = new google.maps.Marker({
                                position: location,
                                map: map,
                                title: 'Location'
                            });
                            marker.addListener('click', function() {
                                infoWindow.open(map, marker);
                            });

                            count ++;
                        });
                    });

                    html += '</table>';

                    $('#results').html(html);
                })
                .fail(function(jqXHR, status, thrownError) {
                    var responseText = jQuery.parseJSON(jqXHR.responseText);
                    $('#error').show().html('\
                        <div class="alert alert-danger">' + responseText.message + '<br>' + responseText.error + '</div>\
                    ');
                });
        });

        // View on map
        $('#results').on('click', '.viewOnMap', function (e) {
            e.preventDefault();
            var location = JSON.parse(localStorage.getItem("location" + this.getAttribute('data-value')));
            geocodePlaceId(geocoder, map, infoWindow, location.google_id);
        });

        $('#results').on('click', '.viewAndBook', function (e) {
            e.preventDefault();

            var room = JSON.parse(localStorage.getItem("room" + this.getAttribute('data-value')));
            displayBookings(room);
        });

        $('#map').on('click', '.viewAndBook', function (e) {
            e.preventDefault();

            var room = JSON.parse(localStorage.getItem("room" + this.getAttribute('data-value')));
            displayBookings(room);
        });
    }

    function handleLocationError(browserHasGeolocation, infoWindow, pos) {
        infoWindow.setPosition(pos);
        infoWindow.setContent(browserHasGeolocation ?
            'Error: The Geolocation service failed.' :
            'Error: Your browser doesn\'t support geolocation.');
    }

    function geocodePlaceId(geocoder, map, infowindow, placeId) {
        var placeId = placeId;
        geocoder.geocode({'placeId': placeId}, function(results, status) {
            if (status === google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    map.setZoom(18);
                    map.setCenter(results[0].geometry.location);
                    var marker = new google.maps.Marker({
                        map: map,
                        position: results[0].geometry.location
                    });
                    infowindow.setContent(results[0].formatted_address);
                    infowindow.open(map, marker);
                } else {
                    window.alert('No results found');
                }
            } else {
                window.alert('Geocoder failed due to: ' + status);
            }
        });
    }

    function displayBookings(room) {
        roomCalander.initialize($('#roomDateSelector'), room, '<?php echo $endpoint; ?>book', '<?php echo $apiKey; ?>');
        $('#displayAvailability').click();
    }
</script>
<script async defer
        src="https://maps.googleapis.com/maps/api/js?key=AIzaSyAuK5rdDsSZpXyi5VBjW7g8N1IJUtAXZwA&callback=initMap">
</script>
<script src="dist/js/smooth-scroll.js"></script>
<!-- Initiate smooth scroll -->
<script>
    smoothScroll.init();
</script>
</body>
</html>
