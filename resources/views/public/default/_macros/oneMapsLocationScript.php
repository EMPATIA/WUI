<script>
    function loadOneMapsLocationsScripts_<?php echo $objId ?>(){
        var locations = [
            <?php
            $i = 1;
            $lats = 0;
            $longs = 0;
            foreach($locations as $location){
            $latItem = !empty($location[0]) ? $location[0] : 0;
            $longItem = !empty($location[1]) ? $location[1] : 0;
            $markerIcon = !(empty($location[4][0]) ) ? action('FilesController@download',["id"=>$location[4][0]->id, "code" => $location[4][0]->code, 1]) : "";
            ?>
            ['<?php echo $latItem; ?>', <?php echo $longItem; ?>, <?php echo $location[2]; ?>, '<?php echo $markerIcon; ?>'],
            <?php
            $lats += $location[1];
            $longs += $location[2];
            $i++;
            }
            ?>
        ];

        <?php
        // center location
        if(!empty($defaultLocation) ){
            $defaults = explode(",", $defaultLocation);
            $centeredLat = $defaults[0];
            $centeredLong = $defaults[1];
        } else if($lats!=0 && $longs!=0){
            $centeredLat = $lats;
            $centeredLong = $longs;
        } else {
            $centeredLat = 39.557191;
            $centeredLong = -7.8536599;
        }

        if(Session::get("SITE-CONFIGURATION.maps_default_latitude") && !empty(Session::get("SITE-CONFIGURATION.maps_default_latitude"))){
            $centeredLat = Session::get("SITE-CONFIGURATION.maps_default_latitude");
        }
        if(Session::get("SITE-CONFIGURATION.maps_default_longitude") && !empty(Session::get("SITE-CONFIGURATION.maps_default_longitude"))){
            $centeredLong = Session::get("SITE-CONFIGURATION.maps_default_longitude");
        }

        ?>

        var map = new google.maps.Map(document.getElementById('<?php echo $objId ?>'), {
            zoom: <?php echo $zoom; ?>,
            center: new google.maps.LatLng(<?php echo $centeredLat ?>,<?php echo $centeredLong ?>),
            mapTypeId: <?php echo $mapTypeId; ?>
        });

        var infowindow = new google.maps.InfoWindow;
        var marker, i;
        var markers = [];

        for (i = 0; i < locations.length; i++) {
            marker = new google.maps.Marker({
                position: new google.maps.LatLng(locations[i][1], locations[i][2]),
                map: map,
                icon: locations[i][3],
            });

            google.maps.event.addListener(marker, 'click', (function(marker, i) {
                return function() {
                    infowindow.setContent(locations[i][0]);
                    infowindow.open(map, marker);
                }
            })(marker, i));

            markers.push(marker);
        }

        var options = {
            imagePath: '../../../js/js-marker-clusterer-gh-pages/images/m'
        };

        var markerCluster = new MarkerClusterer(map, markers, options);

        <?php
        // Geometry Selection based inFusion Tables Layer
        if( !empty($geometrySelection) && count($geometrySelection) > 0) {
        ?>
        var world_geometry = new google.maps.FusionTablesLayer({
            query: {
                select: '<?php echo $geometrySelection["select"]; ?>',
                from: '<?php echo $geometrySelection["from"]; ?>',
                where: "<?php echo $geometrySelection["where"]; ?>"
            },
            styles: [{
                polygonOptions: {
                    fillColor: '#000000',
                    fillOpacity: 0.000000001
                }
            }],
            map: map,
            suppressInfoWindows: true
        });
        <?php
        }
        ?>
        google.maps.event.addListener(infowindow, 'domready', function() {

            // Reference to the DIV that wraps the bottom of infowindow
            var iwOuter = $('.gm-style-iw');

            /* Since this div is in a position prior to .gm-div style-iw.
             * We use jQuery and create a iwBackground variable,
             * and took advantage of the existing reference .gm-style-iw for the previous div with .prev().
             */
            var iwBackground = iwOuter.prev();

            // Removes background shadow DIV
            iwBackground.children(':nth-child(2)').css({'display' : 'none'});

            // Removes white background DIV
            iwBackground.children(':nth-child(4)').css({'display' : 'none'});

            // Moves the infowindow 115px to the right.
            iwOuter.parent().parent().css({left: '115px'});

            // Moves the shadow of the arrow 76px to the left margin.
            iwBackground.children(':nth-child(1)').attr('style', function(i,s){ return s + 'left: 76px !important;'});

            // Moves the arrow 76px to the left margin.
            iwBackground.children(':nth-child(3)').attr('style', function(i,s){ return s + 'left: 45px !important;'});

            // Changes the desired tail shadow color.
            iwBackground.children(':nth-child(3)').find('div').children().css({'box-shadow': 'rgba(0,0,0,1) -1px 0px 10px', 'z-index' : '1'});

            // Reference to the div that groups the close button elements.
            var iwCloseBtn = iwOuter.next();

            // Apply the desired effect to the close button
            iwCloseBtn.css({opacity: '1', left: '-5px', top: '6px', width:'15px',height:'15px', 'border-radius': '50%', border: "1px solid #000", color:'<?php echo Session::get("SITE-CONFIGURATION.color_secondary") ?? '#000'?>'});

            // If the content of infowindow not exceed the set maximum height, then the gradient is removed.
            /*if($('.iw-content').height() < 140){
             $('.iw-bottom-gradient').css({display: 'none'});
             }*/

            // The API automatically applies 0.7 opacity to the button after the mouseout event. This function reverses this event to the desired value.
            iwCloseBtn.mouseout(function(){
                $(this).css({opacity: '1'});
            });
        });

    }

    // Load Maps
    $( document ).ready(function() {
        loadOneMapsLocationsScripts_<?php echo $objId ?>()
    });

</script>