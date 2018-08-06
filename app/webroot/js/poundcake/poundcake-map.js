/*
 * This JQuery file is for interfacing with the jquery-ui-map.js library
 * @see http://code.google.com/p/jquery-ui-map/
 */

// See:
// http://code.google.com/p/jquery-ui-map/wiki/jquery_ui_map_v_3_sample_code
// 
// More Maps API documentation, see:
// https://developers.google.com/maps/documentation/javascript/reference#LatLng
// 
// Removing Polylines, see:
// https://groups.google.com/forum/?fromgroups=#!topic/jquery-ui-map-discuss/ZRAYtyD3HxI

$(document).ready(function () {
    if ($("#map_canvas").is('*')) {
        
        //var yourStartLatLng = new google.maps.LatLng(59.3426606750, 18.0736160278);
        var default_lat = $('#google_mapDefaultLat').val();
        var default_lon = $('#google_mapDefaultLon').val();
        // parseInt to convert the string into an integer
        var fit_bounds = parseInt( $('#google_mapFitBounds').val() );
        //console.log( "Fit bounds 1: " + fit_bounds );
        if ( fit_bounds == 0 )
            fit_bounds = false;
        else
            fit_bounds = true;
        //console.log( "Fit bounds 2: " + fit_bounds );
        
        // we're going to have to track our marker locations manually
        var marker_locations = [];
        
        // this array is for our poly lines
        var lines = [];
        
        /*
        Testing poly lines:
        
        var line1 = [ // INV to San Bruno
            new google.maps.LatLng(37.78171, -122.40840),
            new google.maps.LatLng( 37.67805, -122.40130)
        ];
        
        var line2 = [ // INV to Bruce
            new google.maps.LatLng(37.78171, -122.40840),
            new google.maps.LatLng(37.88951, -122.25270)
        ];
        
        var lines = [
            line1,
            line2
        ];
        */
       
        // make an array of site states
        var siteStates = [];
        $('input[id^=google_mapSitestate]').each(function() {
             siteStates.push( $(this).val() );
             //console.log( $(this).val() );
        });
        
        $('#map_canvas').gmap({'center': new google.maps.LatLng( default_lat, default_lon ) });
        
        // zoom will be ignored if 'bounds' to true inside the marker-creation,
        // that sets the boundry of the map to include all placemarkers
        // set bounds to false and set the center of the map to the marker-position
        // instead
        // get and set the zoom
        //var zoom = $('#map_canvas').gmap('option', 'zoom');
        //console.log(default_zoom);
        if ( !fit_bounds ) {
            //console.log( "Setting zoom to: " + default_zoom );
            $('#map_canvas').gmap('option', 'zoom', 13);
        }
        
        // JavaScript Polyline documentation:
        // @see https://developers.google.com/maps/documentation/javascript/overlays#PolylineOptions        
        
        $('#map_canvas').gmap().bind('init', function(ev, map) {
            $('#map_canvas').gmap('addControl', 'control', google.maps.ControlPosition.LEFT_TOP);  
            
            // adds placemarkers, data from json_encoded data in view
            $("[data-gmapping]").each(function(i,el) {
                var data = $(el).data('gmapping');
                //console.log(data.zoom);
//                console.log(data.icon);
                $('#map_canvas').gmap('addMarker', {'id': data.id, 'icon':data.icon, 'tags':data.tags, 'position': new google.maps.LatLng(data.latlng.lat, data.latlng.lng), 'bounds': fit_bounds }, function(map,marker) {
                    // console.log( data.latlng.lat );
                    // keep track of that marker!                    
                    marker_locations.push( marker );
                    $(el).click(function() {
                        $(marker).triggerEvent('click');
                    });
                    
                }).click(function() {
                    $('#map_canvas').gmap('openInfoWindow', { 'content': $(el).find('.info-box').html() }, this); // was: ('.info-box').text()()
                });
                
            });
            
            $("[data-gmaplines]").each(function(i,el) {
                var data = $(el).data('gmaplines');
//                console.log( data.src_lat );
//                console.log( data.src_lon );
//                console.log( data.dest_lat );
//                console.log( data.dest_lon );                
                var line = [
                    new google.maps.LatLng( data.src_lat, data.src_lon ),
                    new google.maps.LatLng( data.dest_lat, data.dest_lon )
                ];
                lines.push( line );
            });
            drawLines( lines, marker_locations );            
        });


        // setup the map's key -- extra styling needed for the checkboxes themselves
        $('#map_canvas').gmap('addControl', 'radios', google.maps.ControlPosition.RIGHT_BOTTOM);
        $.each(siteStates, function(i, tag) {
            //console.log(tag);
            var str = '<label style="margin-right:5px;display:block;"><input type="checkbox" style="margin-right:3px" value="';
            str += tag;
            str += '" checked/>'+tag+'</label>';
            $('#radios').append( str );
        });

        // toggels place marks if un-checked
        $('input:checkbox').click(function() {
            $('#map_canvas').gmap('closeInfoWindow');
            if ( !fit_bounds ) {
                $('#map_canvas').gmap('set', 'bounds', fit_bounds );
            } else {
                $('#map_canvas').gmap('set', 'bounds', null );
            }
            
            // get an array with the values of all the checked boxes
            var filters = [];
            $('input:checkbox:checked').each(function(i, checkbox) {
                filters.push($(checkbox).val());
            });

            // console.log("Length"+filters.length);

            if ( filters.length >= 0 ) {
                // console.log(filters);
                $('#map_canvas').gmap('find', 'markers', { 'property': 'tags', 'value': filters, 'operator': 'OR' }, function(marker, isFound) {
                    // console.log(isFound);
                    if ( isFound ) {
                        marker.setVisible(true);
                        if ( fit_bounds ) {
                            $('#map_canvas').gmap('addBounds', marker.position);
                        }
                    } else {
                        // hide the place marker itself
                        marker.setVisible(false);
                        // hide any lines that begin or end at the same location
                        // as the place marker
                        hidePolyLinesAtMarker( marker, lines );                     
                    }
                });
                
                // clear and re-draw all the lines
                $("#map_canvas").gmap('clear', 'overlays');
                drawLines( lines, marker_locations );
            }
        });
    }
});

/*
 * Draw lines between visible placemarks
 */
function drawLines( lines, marker_locations ) {    
    $.each( lines, function( key, value ) {
//        var start_lat = value[0].lat();
//        var start_lng = value[0].lng();
//        var end_lat = value[1].lat();
//        var end_lng = value[1].lng();
        if ( isMarkerVisible( value[0], marker_locations ) && isMarkerVisible( value[1], marker_locations ) ) {
            $('#map_canvas').gmap('addShape', 'Polyline', { 'path': value, 'strokeColor': "#006BFF", 'strokeOpacity': 1.0, 'strokeWeight': 2 } );
        }
    });
}


/*
 * Returns true or false if there's a marker at a given point and it's visible
 */
function isMarkerVisible( latlng, marker_locations ) {
    // run through the array of markers,  find the marker that matches the
    // one we're looking for and check its visibility
    
    // lat/lng of the marker we're searching for
    var tlat = latlng.lat();
    var tlng = latlng.lng();
        
    for (var i = 0; i < marker_locations.length; i++) {
        var mlat = marker_locations[i].getPosition().lat();
        var mlng = marker_locations[i].getPosition().lng();
        var mvis = marker_locations[i].getVisible();

        if ( ( mlat == tlat ) && ( mlng == tlng)  && ( mvis == true ) ) {
//            console.log(  "  The marker at " + mlat + "," + mlng + " is visible");
            return true;
        }
  }
//  console.log(  "  The marker at " + mlat + "," + mlng + " is NOT visible");
  return false;
}

/*
function showPolyLinesAtMarker( marker, lines, marker_locations, map ) {
    
    if ( marker.getVisible() ) {    
        // get the lat/lng
        var marker_lat = marker.getPosition().lat();
        var marker_lng = marker.getPosition().lng();
        
        // run through our array of lines and if any of them start or end
        // at our marker, then we need to hide them
        $.each( lines, function( key, value ) {
            var start_lat = value[0].lat();
            var start_lng = value[0].lng();
            var end_lat = value[1].lat();
            var end_lng = value[1].lng();
            
            // if we have a line that starts or ends at our current marker
            if (( marker_lat == start_lat && marker_lng == start_lng ) || ( marker_lat == end_lat && marker_lng == end_lng )) {
                console.log( "Comparing " + start_lat + "," + start_lng + " to " + end_lat + ", " + end_lng );
                var s = isMarkerVisible( marker_locations, new google.maps.LatLng(start_lat, start_lng) );
                var e = isMarkerVisible( marker_locations, new google.maps.LatLng(end_lat, end_lng) );
                if ( s && e ) {
                    console.log( "  Both markers visible!");
                    var line = [
                        new google.maps.LatLng( start_lat, start_lng ),
                        new google.maps.LatLng( end_lat, end_lng )
                    ];
                    $('#map_canvas').gmap('addShape', 'Polyline', { 'path': line, 'strokeColor': "#9205f7", 'strokeOpacity': 1.0, 'strokeWeight': 2 } );
                }
            }
        });
    }    
}
*/
function hidePolyLinesAtMarker( marker, lines ) {
    // get the lat/lng
    var marker_lat = marker.getPosition().lat();
    var marker_lng = marker.getPosition().lng();
    // console.log( "Marker: " + marker_lat + ', ' + marker_lng );

//    console.log( "Length of lines array: " + lines.length );
    // run through our array of lines and if any of them start or end
    // at our marker, then we need to hide them
    $.each( lines, function( key, value ) {
        var start_lat = value[0].lat();
        var start_lng = value[0].lng();
        var end_lat = value[1].lat();
        var end_lng = value[1].lng();        
        if (( marker_lat == start_lat && marker_lng == start_lng ) || ( marker_lat == end_lat && marker_lng == end_lng )) {
            // this really isn't hiding a line, it's removing it from the
            // array of Polylines -- it will need to be re-drawn
//            console.log( "  Key: " + key );
//            console.log( $('#map_canvas').gmap('get', 'overlays > Polyline') );
            var myOverlay = $('#map_canvas').gmap('get', 'overlays > Polyline');
            if ( myOverlay != null ) {
                if ( myOverlay[ key ] != null ) {
                    myOverlay[ key ].setMap( null ); 
                }
            }
        }        
    }); 
}

// traverse array:
//        $.each( lines, function( key, value ) {
//            console.log( key + ": " + value );
//          });


// get a reference to the current map:  $('#map_canvas').gmap('get', 'map')

/*
console.log( "start lat: " + marker_lat + " vs." + start_lat );
console.log( "start lng: " + marker_lng + " vs." + start_lng );
console.log( "end lat: " + marker_lat + " vs." + end_lat );
console.log( "end lng: " + marker_lng + " vs." + end_lng );

var clearLine = false;
if ( ( marker_lat == start_lat ) && ( marker_lng == start_lng )) {
    console.log( " Line starts where marker is!" );
    clearLine = true;
}
if ( ( marker_lat == end_lat ) && ( marker_lng == end_lng )) {
    console.log( " Line ends where marker is!" );
    clearLine = true;
}

if ( clearLine ) {
    console.log( "Clearing line #" + key);
    $('#map_canvas').gmap('get', 'overlays > Polyline')[ key ].setMap( mapDest );
}

console.log( " " );
*/