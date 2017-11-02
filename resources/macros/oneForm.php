<?php

/**
 * oneText - Displays input object to use in oneForm.
 *
 * Example:
 *   Form::oneText('description', trans('privateCbs.description'), isset($cb) ? $cb->contents : null, ['class' => 'form-control', 'id' => 'description'])
 *
 * Example:
 *   $label = array("name"=>trans('privateCbs.title'),"description"=>trans('privateCbs.helpTitle'));
 *   Form::oneText('title', $label, isset($cb) ? $cb->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required'])
 *
 * @param String $name
 * @param String $label
 * @param String $value
 * @param Array $options
 * @param Boolean $datepicker
 * @return html
 */
Form::macro('oneText', function($name, $label, $value = null, $options = array(), $datepicker = false) {
    $html = "";
    // inicializa a opcao id e classe se nao existirem.
    $options = ONE::initOptions($name,$options);

    // metodo original da laravel, se o valor for null é feito o databind do valor a input.
    $field = Form::text($name, $value, $options);

    // verifica se esta form está em modo edit ou view
    if(ONE::isEdit()) {
        $html = Form::oneFieldEdit($name, $label, $field, $options, $datepicker);
    } else {
        // Se nao for especificado, vai buscar o valor ao modelo
        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }
        //metodo para fazer o render do html
        if ($value != "")
            $html = Form::oneFieldShow($name, $label, $value, $options);
    }
    return $html;
});


/**
 * oneNumber - Displays input object to use in oneForm.
 *
 * Example:
 *   Form::oneNumber('numeric_value', trans('privateCbs.number'), isset($cb) ? $cb->contents : null, ['class' => 'form-control', 'id' => 'number'])
 *
 * Example:
 *   $label = array("name"=>trans('privateCbs.number'),"description"=>trans('privateCbs.helpTitle'));
 *   Form::oneNumber('numeric_value', $label, isset($cb) ? $cb->title : null, ['class' => 'form-control', 'id' => 'title', 'required' => 'required'])
 *
 * @param String $name
 * @param String $label
 * @param String $value
 * @param Array $options
 * @return html
 */
Form::macro('oneNumber', function($name, $label, $value = null, $options = array()) {
    $html = "";
    // inicializa a opcao id e classe se nao existirem.
    $options = ONE::initOptions($name,$options);

    // metodo original da laravel, se o valor for null é feito o databind do valor a input.
    $field = Form::number($name, $value, $options);

    // verifica se esta form está em modo edit ou view
    if(ONE::isEdit()) {
        $html = Form::oneFieldEdit($name, $label, $field, $options);
    } else {
        // Se nao for especificado, vai buscar o valor ao modelo
        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }
        //metodo para fazer o render do html
        if ($value != "")
            $html = Form::oneFieldShow($name, $label, $value, $options);
    }
    return $html;
});



/**
 * oneTextArea - Displays a textarea object to use in oneForm.
 *
 * Example:
 *   Form::oneTextArea('description', trans('eventSchedule.description'), isset($eventSchedule) ? $eventSchedule->description : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3])
 *
 * Example:
 *   $label = array("name"=>trans('privateCbs.title'),"description"=>trans('privateCbs.helpDescription'));
 *   Form::oneTextArea('description', $label, isset($eventSchedule) ? $eventSchedule->description : null, ['class' => 'form-control', 'id' => 'description', 'rows' =>3])
 *
 * @param String $name
 * @param String $label
 * @param String $value
 * @param Array $options
 * @param Boolean $datepicker
 * @return html
 */
Form::macro('oneTextArea', function($name, $label, $value = null, $options = array()) {
    $html = "";
    // inicializa a opcao id e classe se nao existirem.
    $options = ONE::initOptions($name,$options);

    // metodo original da laravel, se o valor for null é feito o databind do valor a input.
    $field = Form::textarea($name, $value, $options);

    // verifica se esta form está em modo edit ou view
    if(ONE::isEdit()) {
        $html = Form::oneFieldEdit($name, $label, $field, $options);
    } else {
        // Se nao for especificado, vai buscar o valor ao modelo
        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }
        //metodo para fazer o render do html
        if ($value != "")
            $html = Form::oneFieldShow($name, $label, $value, $options);

    }
    return $html;
});

Form::macro('oneColor', function($name, $label, $value = null, $options = array()) {
    $html = "";
    // inicializa a opcao id e classe se nao existirem.
    $options = ONE::initOptions($name,$options);

    // metodo original da laravel, se o valor for null é feito o databind do valor a input.
    if(!ONE::isEdit()) {
        $options[] = 'disabled';
    }

    $field = Form::color($name, $value, $options);

    // verifica se esta form está em modo edit ou view
    if(ONE::isEdit()) {
        $html = Form::oneFieldEdit($name, $label, $field, $options);

    } else {
        // Se nao for especificado, vai buscar o valor ao modelo
        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }

        //metodo para fazer o render do html
        if ($value != "")
            $html = Form::oneFieldEdit($name, $label, $field, $options);


    }
    return $html;
});

Form::macro('oneCheckbox', function($name,$label, $value = null, $checked = null, $options = array()) {
    $html = "";

    $field = Form::checkbox($name, ($value == null ? 1 : $value), $checked, $options);
    
    $labelFor = $name;
    if(!empty( $options["id"] )){
        $labelFor = $options["id"];
    }

    $options = ONE::initOptions($name,$options);
    if(ONE::isEdit()) {
        //$html = Form::oneFieldEdit($name, $label, $field, $options);
        $html = Form::hidden($name,'0');
        $html .= '<div class="form-group">';
        $html .= $field."  <label for='$labelFor' class='label-checkbox'>".$label."</label>";
        $html .= '</div>';
    } else {
        $html = isset($checked) ? Form::oneFieldShow($name, $label, $checked == 1 ? "Yes" : "No", $options) : "";
    }
    return $html;
});

Form::macro('oneTabs', function($contents) {

    return view('_layouts.oneTabs', compact('contents'));
});

Form::macro('oneGroup', function($contents) {

    return view('_layouts.oneGroup',$contents);
});

Form::macro('oneSelect', function($name,$label, $list = array(), $selected = null, $value, $options = array()) {

    $html = "";
    $options = ONE::initOptions($name,$options);

    if(ONE::isEdit()) {
        $arrList = array(""=>trans('form.select_value'));
        if (is_array($list)) {
            $arrList += $list;
        }

        $input = Form::select($name, $arrList, $selected,$options);
        $html = Form::oneFieldEdit($name, $label, $input, $options);
    } else {

        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }

        $value = ($value == "0" ? "" : $value);

        if ($value != "") {
            $html = Form::oneFieldShow($name, $label,$value, $options);
        } else if (!empty($list[$selected])) {
            $html = Form::oneFieldShow($name, $label, $list[$selected], $options);
        }
    }

    return $html;
});

Form::macro('oneDate', function($name, $label, $value = null, $options = array()) {

    // Html
    if (!isset($options['class'])) {
        $options['class'] = 'form-control oneDatePicker';
    }

    if (!isset($options['placeholder'])) {
        $options['placeholder'] = 'yyyy-mm-dd';
    }

    if (!isset($options['data-date-format'])) {
        $options['data-date-format'] = 'yyyy-mm-dd';
    }

    $html = '';

    $html .= Form::oneText($name, $label, $value, $options, 'date');

    return $html;
});

Form::macro('oneTime', function($name, $label, $value = null, $options = array()) {

    // Html
    if (!isset($options['class'])) {
        $options['class'] = 'form-control oneTimePicker';
    }

    $html = '';
    $html .= Form::oneText($name, $label, $value, $options, 'time');

    return $html;
});


Form::macro('oneDateRange', function($start,$end,$label, $startValue = null, $endValue = null, $options = array()) {

    if (!isset($options['class'])) {
        $options['class'] = 'input-sm form-control';
    }

    // Html for Date Rage picker
    $html = '';
    $html .= '<div class="form-group "><label>'.$label.'</label>';
    $html .= '<div class="input-daterange input-group">';
    $html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>';
    if(ONE::isEdit()) {
        $html .= '<input class="input-sm form-control" name="'.$start.'" value="'.$startValue.'" />';
    }else{
        $html .= $startValue;
    }
    $html .= '<span class="input-group-addon">'.trans("form.to").'</span>';
    if(ONE::isEdit()) {
        $html .= '<input class="input-sm form-control" name="'.$end.'" value="'.$endValue.'" />';
    }else{
        $html .= $endValue;
    }
    $html .= '</div>';
    $html .= '</div>';

    return $html;
});


/**
 * Displays the Google Maps Interface.
 *
 * Example:
 *   $options = array("defaultLocation" => "49.9853106,14.6259324", "enableSearch" => true, "markerIcon" => asset("images/default/pins/construction.png");
 *   Form::oneMaps('maps',"Maps","39.557191,-7.8536599", $options )
 *
 * Example:
 *   $options = array("defaultLocation" => "49.9853106,14.6259324", "enableSearch => true, "markerIcon" => asset("images/default/pins/construction.png", readOnly=> true);
 *   $geometrySelection = array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('CZ')");
 *   Form::oneMaps('maps',"Maps","39.557191,-7.8536599", $options, $geometrySelection)
 *
 * This macro needs the following header:
 *   <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
 *      OR
 *   <scrip src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY" type="text/javascript"></script>
 *
 * If you use "enableSearch" => true in options, you will need to add «libraries=places» to Google Maps URL script:
 *   <script src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY&libraries=places"></script>
 *
 *
 * If you require a API key please visit:
 *   https://developers.google.com/maps/documentation/javascript/get-api-key
 *
 *
 * Geometry Selection:
 *    World Geometry Selection uses Fusion Tables https://developers.google.com/maps/documentation/javascript/fusiontableslayer
 *    Example:
 *       $geometrySelection = array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('US', 'GB', 'DE')");
 *
 *    If you look at the Fusion Table you'll see there are columns for Name and ISO_2DIGIT. We can filter on these by passing a
 *    where condition to the FusionTablesLayer, e.g: https://www.google.com/fusiontables/data?docid=1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk&pli=1#rows%3aid=1
 *
 *
 * @param String $name
 * @param String $label
 * @param String $value
 * @param Array $options
 * @param Array $geometrySelection
 * @return html
 */
Form::macro('oneMaps', function($name, $label, $value = null, $options = array(), $geometrySelection = array()) {
    // Initial values
    $html = "";
    $latitude = "";
    $longitude = "";
    $noStyle = true;

    // Geo Location
    if(!empty($value)){
        $geoLocation = explode(",",$value);
        if(is_array($geoLocation) && count($geoLocation) == 2){
            $latitude = $geoLocation[0];
            $longitude = $geoLocation[1];
        }
    }

    // If default location not set, $defaultLocation = Coimbra
    $defaultLocation = "39.557191,-7.8536599";
    $enableSearch = false;
    $mapTypeId = "google.maps.MapTypeId.ROADMAP";  //  google.maps.MapTypeId.ROADMAP|| google.maps.MapTypeId.SATELLITE
    $markerIcon = "";
    $height = "300px";
    $width = "100%";
    $center = "center";
    $staticMap = false;
    $size = "600x300";
    $marker = "color:blue";
    $shaddow = "false";
    $draggable = "false";
    $removeOption = false;
    $noPinOnSearch = false;
    $help_has_tooltip = false;

    if( ONE::isEdit() ) {
        $zoom = 13;
    } else {
        $zoom = 16;
    }

    $attributes = "";

    foreach ($options as $option => $tmp){
        if(strtolower($option) !="id"){
            $attributes .= $option.'="'.$tmp.'" ';
        }
        if(strtolower($option) == "defaultlocation" ){
            $defaultLocation = $tmp;
        }
        if(strtolower($option) == "enablesearch" ){
            $enableSearch = $tmp;
        }
        if(strtolower($option) == "maptypeid" ){
            $mapTypeId = $tmp;
        }
        if(strtolower($option) == "zoom" ){
            $zoom = $tmp;
        }
        if(strtolower($option) == "markericon" ){
            $markerIcon = $tmp;
        }
        if(strtolower($option) == "height" ){
            $height = $tmp;
        }
        if(strtolower($option) == "width" ){
            $width = $tmp;
        }
        if(strtolower($option) == "readonly" ){
            $readOnly = $tmp;
        }
        if(strtolower($option) == "scrollwheel" ){
            $scrollWheel = $tmp;
        }
        if(strtolower($option) == "center" ){
            $center = $tmp;
        }
        if(strtolower($option) == "staticmap" ){
            $staticMap = $tmp;
        }
        if(strtolower($option) == "size" ){
            $size = $tmp;
        }
        if(strtolower($option) == "size" ){
            $marker = $tmp;
        }
        if(strtolower($option) == "draggable" ){
            $draggable = $tmp;
        }
        if(strtolower($option) == "removeoption" ){
            $removeOption = $tmp;
        }
        if(strtolower($option) == "description" ){
            $description = $tmp;
        }
        if(strtolower($option) == "nopinonsearch" && $tmp){
            $noPinOnSearch = true;
        }
        if(strtolower($option) == "categoryicon"){
            $markerIcon = $tmp;
        }
        if(strtolower($option) == "nostyle"){
            //if false shows colored map
            $noStyle = $tmp;
        }
        if(strtolower($option) == "help_has_tooltip" ){
            $help_has_tooltip = $tmp;
        }
    }

    // Default Location
    $defaults = explode(",",$defaultLocation);

    ob_start();

    if($staticMap == true){
        ?>
        <!-- custom marker URL (max 64x64) -->
        <img style="height:<?php echo $height;?>;width:<?php echo $width;?>;" src="https://maps.googleapis.com/maps/api/staticmap?center=<?php echo$latitude; ?>,<?php echo $longitude ?>&zoom=<?php echo $zoom; ?>&size=<?php echo $size; ?>&maptype=<?php echo $mapTypeId; ?>&markers=<?php echo !empty($markerIcon) ? "icon:".$markerIcon : "color:blue"; ?>%7Cshadow:<?php echo $shaddow;?>%7Clabel:%7C<?php echo $latitude;?>,<?php echo $longitude;?>&key=AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" />
        <?php
    } else {
        ?>

        <?php
        if($enableSearch && (isset($readOnly) && $readOnly === false) || (!isset($readOnly)) && ONE::isEdit()){
            ?>
            <style>
                .controls-<?php echo $name;?> {
                    margin-top: 10px;
                    border: 1px solid transparent;
                    border-radius: 2px 0 0 2px;
                    box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    height: 32px;
                    outline: none;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                }
                .pac-input-<?php echo $name;?> {
                    background-color: #fff;
                    font-size: 15px;
                    font-weight: 300;
                    margin-left: 12px;
                    padding: 0 11px 0 13px;
                    text-overflow: ellipsis;
                    width: 300px;
                }
                .pac-input-<?php echo $name;?>:focus {
                    border-color: #4d90fe;
                }
                #floating-panel-<?php echo $name;?> {
                    position: absolute;
                    top: 10px;
                    right: 10px;
                    background-color: #fff;
                    padding: 5px;
                    border: 1px solid #999;
                    text-align: center;
                    font-family: 'Roboto','sans-serif';
                    line-height: 30px;
                    padding-left: 10px;
                }
                .google-maps-delete-panel{
                    margin-top: 0px;
                    border: 1px solid transparent!important;
                    box-sizing: border-box;
                    -moz-box-sizing: border-box;
                    height: 34px;
                    outline: none;
                    box-shadow: 0 2px 6px rgba(0, 0, 0, 0.3);
                    border-radius: 0;
                    padding: 2px 15px!important;
                }
                .google-maps-delete-button{
                    background: transparent;
                    box-shadow: none;
                    border: none;
                    padding: 0;
                    font-size: 14px;
                    height: 28px;
                    color: #e6225e;
                    font-weight: 100;
                    cursor:pointer;
                }
                .google-maps-delete-panel a i {
                    color: #e6225e;
                }
                .google-maps-delete-panel a i:hover {
                    color: #e6225e;
                }
                .google-maps-delete-panel a:hover {
                    color: #e6225e;
                }
            </style>
            <?php
        }
        ?>
        <div class="row">
            <div class="col-xs-12 col-12">

                <!-- OneMaps -->
                <div class="oneGoogleMapsGroup">
                    <?php if(!empty($label)){?>
                        <label><?php echo $label; ?></label>
                    <?php }?>
                    <?php if(!empty($description)){
                                if($help_has_tooltip){?>
                                    <i style='margin-left: 10px; color: #02686f' data-toggle='tooltip' title='". <?php echo $description; ?> ."' class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>"
                                <?php}else{
                        ?>

                        <span class="help-block oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;"><?php echo $description; ?></span>
                    <?php }
                    } ?>

                    <div id="oneMap<?php echo $name;?>" class="oneGoogleMap" style="height:<?php echo $height;?>;" ></div>
                    <input id="<?php echo $name;?>" name="<?php echo $name;?>" <?php echo $attributes; ?> value="<?php echo $value; ?>" type="hidden" />
                </div>

            </div>
        </div>

        <script>
            var markers = [];
            var map = "";
            function initializeMap_<?php echo $name;?>(latitude, longitude, title, placeMarker) {
                var center = new google.maps.LatLng(latitude,longitude);

                placeMarker  = typeof(placeMarker) != 'undefined' ? placeMarker : false;

                var myOptions = {
                    scrollwheel: true,
                    zoom: <?php echo $zoom; ?>,
                    center: <?php echo $center; ?>,
                    disableDefaultUI: <?php echo (((isset($readOnly) && $readOnly === true) || (!isset($readOnly)) && !ONE::isEdit() )  ? "true" : "false"); ?>,
                    panControl: true,
                    streetViewControl: false,
                    zoomControl: true,
                    zoomControlOptions: {
                        style: google.maps.ZoomControlStyle.LARGE
                    },
                    mapTypeId: <?php echo $mapTypeId; ?>,
                    mapTypeControlOptions: {
                        style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR,
                        position: google.maps.ControlPosition.TOP_CENTER
                    },
                    tilt:45,
                    <?php
                    if(((isset($readOnly) && $readOnly === true) || (!isset($readOnly)) && !ONE::isEdit() ) && $noStyle) {
                    ?>
                    // https://snazzymaps.com/explore?tag=light
                    styles: [{"featureType":"landscape","stylers":[{"saturation":-100},{"lightness":65},{"visibility":"on"}]},{"featureType":"poi","stylers":[{"saturation":-100},{"lightness":51},{"visibility":"simplified"}]},{"featureType":"road.highway","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"road.arterial","stylers":[{"saturation":-100},{"lightness":30},{"visibility":"on"}]},{"featureType":"road.local","stylers":[{"saturation":-100},{"lightness":40},{"visibility":"on"}]},{"featureType":"transit","stylers":[{"saturation":-100},{"visibility":"simplified"}]},{"featureType":"administrative.province","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"labels","stylers":[{"visibility":"on"},{"lightness":-25},{"saturation":-100}]},{"featureType":"water","elementType":"geometry","stylers":[{"hue":"#ffff00"},{"lightness":-25},{"saturation":-97}]}]
                    <?php
                    }
                    ?>
                };

                map = new google.maps.Map(document.getElementById("oneMap<?php echo $name;?>"), myOptions);
                var myLatLng = center;


                <?php
                if((isset($readOnly) && $readOnly === true) || (!isset($readOnly)) && !ONE::isEdit() ) {
                ?>
                map.setOptions({draggable: <?php echo $draggable; ?>, scrollwheel: false, disableDoubleClickZoom: true, });
                <?php
                }
                ?>


                <?php
                if(!isset($readOnly) && ONE::isEdit() || (isset($readOnly) && $readOnly === false)){
                ?>
                google.maps.event.addListener(map, 'click', function(event) {
                    clearMakers();
                    var myMarker = placeMarker_<?php echo $name;?>(event.latLng, map, null);
                    updatePosition_<?php echo $name;?>(myMarker);
                    //initializeMap_<?php echo $name;?>(event.latLng.lat(), event.latLng.lng(), title, true);
                });
                <?php
                }
                ?>


                if( placeMarker  ) {
                    placeMarker_<?php echo $name;?>(myLatLng, map, title);
                }

                <?php
                if($enableSearch && (isset($readOnly) && $readOnly === false) || (!isset($readOnly)) && ONE::isEdit()){
                ?>
                // Add search input map
                $("#oneMap<?php echo $name; ?>").append('<input id="pac-input-<?php echo $name; ?>" class="controls-<?php echo $name; ?> pac-input-<?php echo $name; ?>" placeholder="<?php echo trans("googleMaps.search_box"); ?>" autocomplete="off" type="text">');

                <?php if($removeOption) { ?>

                // Add option to remove map
                $("#oneMap<?php echo $name; ?>").append('<div class="google-maps-delete-panel" id="floating-panel-<?php echo $name; ?>"><a class="google-maps-delete-button google-maps-remove-marker" onclick="deleteMarker_<?php echo $name; ?>();"><i class="fa fa-times google-maps-times" aria-hidden="true"></i> <?php echo  trim(preg_replace('/\s+/', ' ',trans("googleMaps.delete_marker"))); ?></a></div>');
                <?php } ?>

                $('form #pac-input-<?php echo $name;?>').on('keypress', function(e) {
                    return e.which !== 13;
                });

                // Create the search box and link it to the UI element.
                var input = document.getElementById('pac-input-<?php echo $name;?>');
                var searchBox = new google.maps.places.SearchBox(input);
                map.controls[google.maps.ControlPosition.TOP_LEFT].push(input);

                // Bias the SearchBox results towards current map's viewport.
                map.addListener('bounds_changed', function() {
                    searchBox.setBounds(map.getBounds());
                });

                searchBox.addListener('places_changed', function() {
                    var places = searchBox.getPlaces();

                    if (places.length == 0) {
                        return;
                    }

                    // For each place, get the icon, name and location.
                    var bounds = new google.maps.LatLngBounds();
                    places.forEach(function(place) {
                        if (!place.geometry) {
                            console.log("Returned place contains no geometry");
                            return;
                        }
                        var icon = {
                            url: place.icon,
                            size: new google.maps.Size(71, 71),
                            origin: new google.maps.Point(0, 0),
                            anchor: new google.maps.Point(17, 34),
                            scaledSize: new google.maps.Size(25, 25)
                        };


                        if (place.geometry.viewport) {
                            // Only geocodes have viewport.
                            bounds.union(place.geometry.viewport);
                        } else {
                            bounds.extend(place.geometry.location);
                        }

                        <?php if (!$noPinOnSearch) {?>
                        myLatLng = new google.maps.LatLng(place.geometry.location.lat(),place.geometry.location.lng());
                        deleteMarker_<?php echo $name;?>();
                        placeMarker_<?php echo $name;?>(myLatLng, map, title);
                        <?php } ?>
                    });
                    map.fitBounds(bounds);
                });
                <?php
                }
                ?>

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
            }

            function placeMarker_<?php echo $name;?>(myLatLng, map, title) {
                var myMarker = new google.maps.Marker({
                    position: myLatLng,
                    map: map,
                    <?php
                    if((isset($readOnly) && $readOnly === true)) {
                    ?>
                    draggable: false,
                    <?php
                    } else if(ONE::isEdit()){
                    ?>
                    draggable: true,
                    <?php
                    }else if(!ONE::isEdit()){
                    ?>
                    draggable: false,
                    <?php
                    }
                    ?>
                    title: title,
                    icon: "<?php echo $markerIcon; ?>"
                });

                <?php
                if(!isset($readOnly) && ONE::isEdit() || (isset($readOnly) && $readOnly === false)){
                ?>
                google.maps.event.addListener(myMarker, 'dragend', function (event) {
                    updatePosition_<?php echo $name;?>(myMarker);
                });
                <?php
                }
                ?>
                document.getElementById('<?php echo $name;?>').value = myLatLng.lat()+","+myLatLng.lng();
                markers.push(myMarker);
                return myMarker;
            }

            function updatePosition_<?php echo $name;?>(marker) {
                document.getElementById('<?php echo $name;?>').value = marker.getPosition().lat()+","+marker.getPosition().lng();
            }

            function deleteMarker_<?php echo $name;?>() {
                clearMakers();
                document.getElementById('<?php echo $name;?>').value = "";
            }

            function clearMakers() {
                console.log(markers.length);
                for (var i = 0; i < markers.length; i++ ) {
                    markers[i].setMap(null);
                }
                markers.length = 0;
            }

            $(document).ready( function() {
                var latitude = "<?php echo !empty($latitude) ? $latitude : (Session::get("SITE-CONFIGURATION.maps_default_latitude") ?? $defaults[0]); ?>";
                var longitude = "<?php echo !empty($longitude) ? $longitude : (Session::get("SITE-CONFIGURATION.maps_default_longitude") ?? $defaults[1]);?>";


                var title = "Test";
                initializeMap_<?php echo $name;?>(latitude,longitude,title,<?php echo !empty($value) ? 'true' : 'false'?> );
                <?php
                if($enableSearch && (isset($readOnly) && $readOnly === false) || (!isset($readOnly)) && ONE::isEdit()){
                ?>
                $('form #pac-input-<?php echo $name;?>').on('keypress', function(e) {
                    return e.which !== 13;
                });
                <?php
                }
                ?>
            });

            function refreshMap() {
                setTimeout(function(){
                    var latitude = "<?php echo !empty($latitude) ? $latitude : (Session::get("SITE-CONFIGURATION.maps_default_latitude") ?? $defaults[0]);?>";
                    var longitude = "<?php echo !empty($longitude) ? $longitude : (Session::get("SITE-CONFIGURATION.maps_default_longitude") ?? $defaults[1]);?>";
                    var title = "Test";
                    initializeMap_<?php echo $name;?>(latitude,longitude,title,<?php echo !empty($value) ? 'true' : 'false'?> );
                    <?php
                    if($enableSearch && (isset($readOnly) && $readOnly === false) || (!isset($readOnly)) && ONE::isEdit()){
                    ?>
                    $('form #pac-input-<?php echo $name;?>').on('keypress', function(e) {
                        return e.which !== 13;
                    });
                    <?php
                    }
                    ?>
                }, 250);

            }

        </script>
        <?php
    }
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});


/**
 * Displays Reverse geocoding - this macro is for converting geographic coordinates into a human-readable address.
 *
 * Example:
 *   Form::oneReverseGeocoding("streetReverseGeocoding","Maps","39.557191,-7.8536599")
 *
 * This macro needs the following header:
 * <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY" type="text/javascript"></script>
 *
 * And you will have to require a key:
 * https://developers.google.com/maps/documentation/javascript/get-api-key
 *
 * Example online:
 * https://developers.google.com/maps/documentation/javascript/examples/geocoding-reverse
 *
 * @param String $name
 * @param String $label
 * @param String $value
 * @return html
 */
Form::macro('oneReverseGeocoding', function($name, $label, $value = "", $icon = false) {
    // Initial values
    $html = "";
    $latitude = "";
    $longitude = "";

    // Geo Location
    if(!empty($value)){
        $geoLocation = explode(",",$value);
        if(is_array($geoLocation) && count($geoLocation) == 2){
            $latitude = $geoLocation[0];
            $longitude = $geoLocation[1];
        }
    }

    ob_start();
    ?>
    <?php
    if(!empty($label)){
        ?>
        <div class="row">
            <div class="col-xs-12 col-12">
                <label class="reverse_geocoding_label"><?php echo $label; ?></label>
            </div>
            <div class="col-xs-12 col-12">
                <span id="reverse_geocoding_formatted_address_<?php echo $name;?>" class="reverse_geocoding_values">---</span>
            </div>
        </div>
        <?php
    } else{
        if($icon) {
            ?>
            <i class="fa fa-map-marker fa-3x" aria-hidden="true"></i>
            <?php
        }
        ?>
        <span id="reverse_geocoding_formatted_address_<?php echo $name;?>" class="reverse_geocoding_values">---</span>
        <?php
    }
    ?>

    <script>
        function showGeolocation_<?php echo $name;?>(latitude,longitude) {
            // Geocoding - Geolocation
            var geocoder = new google.maps.Geocoder;

            var latlng = {lat: parseFloat(latitude), lng: parseFloat(longitude)};
            geocoder.geocode({'location': latlng}, function(results, status) {
                if (status === 'OK') {
                    if (results[1]) {
                        $("#reverse_geocoding_formatted_address_<?php echo $name;?>").html(results[1].formatted_address);
                    } else {
                        $("#reverse_geocoding_formatted_address_<?php echo $name;?>").html("---");
                        console.log('No results found');
                    }
                } else {
                    $("#reverse_geocoding_formatted_address_<?php echo $name;?>").html("---");
                    console.log('Geocoder failed due to: ' + status);
                }
            });
        }

        <?php
        if(!empty($value)) {
        ?>
        $(document).ready( function() {
            var latitude = "<?php echo $latitude;?>";
            var longitude = "<?php echo $longitude;?>";
            showGeolocation_<?php echo $name;?>(latitude,longitude);
        });
        <?php
        }
        ?>

    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});



/**
 * Displays the Google Maps interface that shows a group of Locations in a Clustered way.
 *
 * Example:
 *   $locations =[['<a target="_blank" href="http://google.pt">Title A</a>', 3.180967, 101.715546 ],
 *                ['Title B', 3.200848, 101.616669, "construction"],
 *                ['Title C', 3.147372, 101.597443 ,"transportation"],
 *                ['Title D', 3.191251, 101.710052, "security"]]];
 *   $geometrySelection = array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('PT')");
 *   $options = array("folderIcons" => "/images/default/pins/", "zoom" => 7, "defaultLocation" => "49.9853106,14.6259324", "style" => "height:500px;width:100%;", "markerIcon" => asset('images/default/pins/construction.png'));
 *   Form::oneMapsLocations("mapId1", "Maps", $locations, $options, $geometrySelection);
 *
 * This macro needs the following header:
 *   <script type="text/javascript"  src="{{ asset('js/js-marker-clusterer-gh-pages/src/markerclusterer.js') }}"></script>
 *   <script src="https://maps.googleapis.com/maps/api/js?v=3.exp&sensor=false"></script>
 *        OR
 *   <script async defer src="https://maps.googleapis.com/maps/api/js?key=YOUR_API_KEY" type="text/javascript"></script>
 *
 *   If you require a API key please visit:
 *       https://developers.google.com/maps/documentation/javascript/get-api-key
 *
 *
 * Geometry Selection:
 *    World Geometry Selection uses Fusion Tables https://developers.google.com/maps/documentation/javascript/fusiontableslayer
 *    Example:
 *       $geometrySelection = array("select" => "geometry", "from" => "1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk", "where" => "ISO_2DIGIT IN ('US', 'GB', 'DE')");
 *
 *    If you look at the Fusion Table you'll see there are columns for Name and ISO_2DIGIT. We can filter on these by passing a
 *    where condition to the FusionTablesLayer, e.g: https://www.google.com/fusiontables/data?docid=1N2LBk4JHwWpOY4d9fobIn27lfnZ5MDy-NoqqRpk&pli=1#rows%3aid=1
 *
 *
 * @param String $objId
 * @param String $label
 * @param String $locations
 * @param Array $geometrySelection
 * @param Array $options
 * @return html
 */
Form::macro('oneMapsLocations', function($objId, $label, $locations = [], $options = array("style" => "min-height:300px;width:100%;") , $geometrySelection = [],  $totalNoMapTopics = null, $allCategoriesHelp = null){
    // Initial values
    $html = "";

    // If default location not set, $defaultLocation = Coimbra
    $defaultLocation = "39.557191,-7.8536599";
    $mapTypeId = "google.maps.MapTypeId.ROADMAP";  //  google.maps.MapTypeId.ROADMAP|| google.maps.MapTypeId.SATELLITE
    $zoom = 13;
    $folderIcons = "";
    $defaultIconsType = "png";
    $markerIcon = "";
    $pinTitle = trans("googleMaps.pin");
    $pinGroup =  trans("googleMaps.pinGroups");
    $attributes = "";
    $hasLegend = true;

    foreach ($options as $option => $tmp){
        if($option!="id"){
            $attributes .= $option.'="'.$tmp.'" ';
        }

        if(strtolower($option) == "defaultlocation" ){
            $defaultLocation = $tmp;
        }

        if(strtolower($option) == "zoom" ){
            $zoom = $tmp;
        }

        if(strtolower($option) == "maptypeid" ){
            $mapTypeId = $tmp;
        }

        if(strtolower($option) == "foldericons"){
            $folderIcons = $tmp;
        }

        if(strtolower($option) == "defaulticonstype"){
            $defaultIconsType = $tmp;
        }

        if(strtolower($option) == "markericon" ){
            if(Session::get("SITE-CONFIGURATION.file_marker_icon") && !empty(Session::get("SITE-CONFIGURATION.file_marker_icon"))) {
                $markerIcon = Session::get("SITE-CONFIGURATION.file_marker_icon");
            }else{
                $markerIcon = $tmp;
            }
        }

        if(strtolower($option) == "pintitle" ){
            $pinTitle = $tmp;
        }
        if(strtolower($option) == "pingroup" ){
            $pinGroup = $tmp;
        }
        if(strtolower($option) == "nolegend"){
            $hasLegend = false;
        }
    }

    /** Categories for help - show in map view*/
    if(empty($allCategoriesHelp)){
        $categoriesHelp = [];
        foreach($locations as $location){
            if(!empty($location[5]) ){
                $categoriesHelp[] = [$location[5],$location[4]];
            }
        }

        $categoriesHelp = collect($categoriesHelp);
        $categoriesHelp = $categoriesHelp->unique();
    }else{
        $categoriesHelp = [];
        foreach($allCategoriesHelp as $category){
            if(!empty($category['category']) && !empty($category['pin'])){
                $categoriesHelp[] = [$category['category'], $category['pin']];
            }
        }
        $categoriesHelp = collect($categoriesHelp);
        $categoriesHelp = $categoriesHelp->unique();
    }

    
        if ($hasLegend) {
            $mapColumns = array(
                "map" => "col-md-10 col-sm-12 col-12",
                "legend" => "col-md-2 col-sm-12 col-12",
            );
        } else {
            $mapColumns = array(
                "map" => "col-xs-12 col-12",
                "legend" => "hidden",
            );
        }
    
    ?>

    <!-- Map Locations -->
    <div class="container map-statistics">
        <?php
        if(!empty($label)){
            ?>
            <label class="maps_location_label"><?php echo $label; ?></label>
            <?php
        }
        ?>
        <div class="row">
            <div class="<?php echo $mapColumns["map"] ?>">
                <div id="<?php echo $objId ?>" <?php echo $attributes; ?>></div>
            </div>
            <div class="<?php echo $mapColumns["legend"] ?> g-map-label-group">
                <div class="row map-statistics-row">
                    <!-- Number of topics with geo-reference -->
                    <div class="col-md-6 col-xs-12 col-12">
                        <div class="row">
                            <?php if (isset($locations) && count($locations) > 0) { ?>
                                <div class="col-xs-12 col-12 label-info-map">
                                    <span class="bigger"><?php echo count($locations); ?></span> <?php echo trans("googleMaps.n_topics_with_geo_mapping"); ?>
                                </div>
                            <?php } else { ?>
                                <div class="col-xs-12 col-12"><?php echo trans("googleMaps.there_are_no_topics_with_geo_mapping"); ?></div>
                            <?php }?>

                            <!-- Number of topics without geo-reference -->
                            <?php if (isset($totalNoMapTopics)) {
                                if (!is_null($totalNoMapTopics) && !empty($totalNoMapTopics)) { ?>
                                    <?php if ($totalNoMapTopics > 0) {
                                        ?>
                                        <div class="col-xs-12 col-12 label-info-map">
                                            <span class="bigger"><?php echo $totalNoMapTopics; ?></span> <?php echo trans("googleMaps.n_topics_dont_have_geo_mapping"); ?>
                                        </div>
                                    <?php } else { ?>
                                        <div class="col-xs-12 col-12"><?php echo trans("googleMaps.there_are_no_topics_with_geo_mapping"); ?></div>
                                    <?php } ?>
                                <?php }
                            } ?>
                        </div>
                    </div>
                    <div class="col-md-6 col-xs-12 col-12">
                        <div class="row">
                            <div class="col-md-2 col-xs-3 col-3"><img src="<?php echo asset('images/pin_group.png'); ?>" style="height:4em;" class="map-icons-caption" alt="pin_group"/></div>
                            <div class="col-md-10 col-xs-9 col-9" style="padding-top: 1.5em;"><?php echo $pinGroup; ?></div>
                        </div>
                    </div>
                </div>

                <div class="row" style="margin-bottom:8px;">
                    <?php
                    $j = 0;
                    foreach($categoriesHelp as $categoryHelp){
                        if(($categoryHelp[1] != "" )){
                            $j++;
                            $image = $categoryHelp[1][0];
                            ?>
                            <div class="col-md-3 col-xs-12 col-12">
                                <div class="row">
                                    <div class="col-lg-3 col-md-4 col-xs-3 col-3"><img src="<?php echo  URL::action('FilesController@download', ['id' => $image->id, 'code' => $image->code, 1] ); ?>" style="height:4em;" /></div>
                                    <div class="col-lg-9 col-md-8 col-xs-9 col-9" style="padding-top: 1em"><?php echo ucfirst($categoryHelp[0]); ?></div>
                                </div>
                            </div>
                            <?php
                        }
                    }
                    ?>
                </div>

                <?php
                if($j == 0){
                    ?>
                    <div class="row map-statistics-row" style="margin-bottom:8px;">
                        <div class="col-md-4 col-xs-3 col-3"><img src="<?php echo !empty($markerIcon) ? $markerIcon : asset('images/pin.png'); ?>" style="height:32px;margin-right:8px;" class="map-icons-caption" alt="pin"/></div>
                        <div class="col-md-8 col-xs-9 col-9"><?php echo $pinTitle; ?></div>
                    </div>
                    <?php
                }
                ?>
            </div>
        </div>
    </div>

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
                    ['<?php echo str_replace("\n","",$latItem);  ?>', <?php echo $longItem; ?>, <?php echo $location[2]; ?>, '<?php echo $markerIcon; ?>'],
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
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;

});



Form::macro('onePassword', function($name, $label, $value = null, $options = array()) {
    $html = "";
    // inicializa a opcao id e classe se nao existirem.
    $options = ONE::initOptions($name,$options);

    // metodo original da laravel, se o valor for null é feito o databind do valor a input.
    $field = Form::password($name, $options);

    // verifica se esta form está em modo edit ou view
    if(ONE::isEdit()) {
        $html = Form::oneFieldEdit($name, $label, $field, $options);
    } else {
        // Se nao for especificado, vai buscar o valor ao modelo
        if ($value == null) {
            $value = Form::getValueAttribute($name);
        }
        //metodo para fazer o render do html
        if ($value != "")
            $html = Form::oneFieldShow($name, $label, $value, $options);
    }
    return $html;
});


/**
 * Displays an On/Off FlipSwitch.
 *
 * Example 1:
 *  Form::oneSwitch('flipSwitch1',"Mostrar total de votos",1)
 *  Form::oneSwitch("configuration_".$option->id,$option->title, in_array($option->id, (isset($cbConfigurations) ? $cbConfigurations : []) )  )
 *
 * Example 2:
 *  $options = array("value"=>17, "readonly"=>false,'id' => $module->module_key,'data-toggle' => 'collapse','data-target' => '#'.$module->name, 'aria-expanded' => 'true','aria-controls' => $module->name);
 *  Form::oneSwitch("modules[]",null, array_key_exists($module->module_key, (isset($entityModules) ? $entityModules : []), $options)
 *
 * Example 3:
 *  $options = array("groupClass"=>"row", "labelClass" => "col-sm-12 col-md-9", "switchClass" => "col-sm-12 col-md-3" );
 *  Form::oneSwitch("configuration_".$option->id,$option->title, in_array($option->id, (isset($cbConfigurations) ? $cbConfigurations : []) ) , $options)
 *
 * Example 4:
 *   Form::oneSwitch("configuration",
 *                   array("name" => "Title", "description" => "Lorem ipsum dolor sit amet, consectetur adipiscing elit."),
 *                   in_array($option->id, (isset($cbConfigurations) ? $cbConfigurations : []) ),
 *                   array("groupClass"=>"row", "labelClass" => "col-sm-12 col-md-9", "switchClass" => "col-sm-12 col-md-3" ) )
 *
 * The input $checked can be 1, 0, null, true or false
 *
 * @param String $name
 * @param String $label
 * @param Mixed $checked
 * @param Array $options
 * @return html
 */
Form::macro('oneSwitch', function($name, $label, $checked = false, $options = array()) {
    $html = "";
    $attributes = "";
    $id = $name;
    $value = 1;
    $groupClass = "";
    $labelClass = "";
    $switchClass = "";

    foreach ($options as $option => $tmp){
        if(strtolower($option) == "readonly" ){
            $readOnly = $tmp;
        } else if(strtolower($option) == "id" ){
            $id = $tmp;
        } else if(strtolower($option) == "value" ){
            $value = $tmp;
        } else if(strtolower($option) == "groupclass" ){
            $groupClass = $tmp;
        } else if(strtolower($option) == "labelclass" ){
            $labelClass = $tmp;
        } else if(strtolower($option) == "switchclass" ){
            $switchClass = $tmp;
        }else {
            $attributes .= $option.'="'.$tmp.'" ';
        }
    }

    // Readonly Check
    if(!isset($readOnly) && ONE::isEdit() || (isset($readOnly) && $readOnly === false)){
        $disabled = "";
        $pointerEvents = "";
        $classReadOnly = "";
    } else {
        $disabled = "disabled";
        $pointerEvents = "style=\"pointer-events: none\"";
        $classReadOnly = ($checked) ?  "readOnlySwitch-on" : "readOnlySwitch-off";
    }

    ob_start();

    if(!empty($label)) {
        ?>
        <div class="<?php echo $groupClass; ?>">
        <div class="<?php echo $labelClass; ?>">
            <label for="<?php echo $id; ?>"><?php echo is_array($label) ? $label["name"] : $label; ?></label>
            <?php
            if(is_array($label) && $label["description"] ){
                ?>
                <span class="help-block oneform-help-block" style="margin:-4px 0px 5px;font-size:10px;"><?php echo $label["description"]; ?></span>
                <?php
            }
            ?>
        </div>
        <div class="<?php echo $switchClass; ?>">
        <?php
    }
    ?>

    <div class="onoffswitch <?php echo $classReadOnly; ?>" <?php echo $attributes; ?> <?php echo $pointerEvents; ?> >
        <input id="<?php echo $id; ?>" name="<?php echo $name; ?>" type="checkbox" <?php echo !empty($checked) ?  'checked': ''; ?> class="onoffswitch-checkbox" value="<?php echo $value; ?>" <?php echo $disabled; ?>/>
        <label for="<?php echo $id; ?>" class="onoffswitch-label" <?php echo $disabled; ?> >
            <span class="onoffswitch-inner"></span>
            <span class="onoffswitch-switch"></span>
        </label>
    </div>
    <?php
    if(!empty($label)) {
        ?>
        </div>
        </div>
        <?php
    }

    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});



Form::macro('oneFieldEdit', function($name, $label, $input, $options = array(), $datepicker = false) {
    $e = "";

    if(Session::has('errors')) {
        $errors = Session::get('errors');
    }

    if(Session::has('errors') && $errors->has($name)) {
        $e = "has-error";
    }

    if(in_array("required", $options, true))
        $required = "required";
    else
        $required = "";

    $html = '<div class="form-group '.$e.' ' . $required . '">';

    if (!empty($label) && !is_array($label) ){
        $html .= Form::label($name, $label);
    } else  if(!empty($label) && is_array($label) ){
        $html .= Form::label($name, !empty($label["name"]) ? $label["name"] : "");
        if(isset($options['help_has_tooltip'])){
            $html .= "<i style='margin-left: 10px; color: #02686f' data-toggle='tooltip' title='". (!empty($label["description"]) ? $label["description"] : "") ."' class=\"fa fa-info-circle\" aria-hidden=\"true\"></i>";
        }else{
            $html .= "<span class=\"help-block oneform-help-block\" style=\"margin:-4px 0px 5px;font-size:10px;\">".(!empty($label["description"]) ? $label["description"] : "")."</span>";
        }


    }

    if ($datepicker === 'date') {
        $html .= '<div class="input-group date">';
        $html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>';
    } elseif ($datepicker === 'time') {
        $html .= '<div class="input-group time">';
        $html .= '<span class="input-group-addon"><i class="glyphicon glyphicon-time"></i></span>';
    }

    $html .= $input;

    if ($datepicker !== false) {
        $html .= '</div>';
    }

    if(Session::has('errors') && $errors->has($name)) {
        $html .= '<p class="help-block">'.$errors->first($name).'</p>';
    }

    $html .= '</div>';
    return $html;
});

Form::macro('oneFieldShow', function($name, $label, $value, $options = array()) {
    $html = "";
    if (!empty($label) && !is_array($label) ){
        $html = "<dt>".$label."</dt>";
    } else  if(!empty($label) && is_array($label) ){
        $html = "<dt>".(!empty($label["name"]) ? $label["name"] : "")."</dt>";
        $html .= "<span class=\"help-block oneform-help-block-show\" style=\"margin:1px 0px 5px;font-size:10px;\">".(!empty($label["description"]) ? $label["description"] : "")."</span>";
    }
    $html .= "<dd> ".nl2br(strip_tags($value))." </dd>";
    if (!isset($options["noTop"]) || $options["noTop"] === false) {
        $html = $html."<hr style='margin: 10px 0 10px 0'>" ;
    }

    return $html;
});



Form::macro('oneEmpavilleMap', function($name, $label, $imageMap, $edit ,$mandatory, $value = null ) {
    // Initial values
    $html = "";
    $latitude = "";
    $longitude = "";
    $mapId = 'map_'.uniqid();
    if($value === ',')
        $value =  null;

    // Geo Location
    if(!empty($value)){
        $geoLocation = explode(",",$value);
        if(is_array($geoLocation) && count($geoLocation) == 2){
            $latitude = $geoLocation[0];
            $longitude = $geoLocation[1];
        }
    }
    ob_start();
    ?>

    <div class="row">
        <div class="col-xs-12 col-12">
            <!-- EmpavilleMap -->
            <label><?php echo $label; ?></label>
            <div class="oneEmpavilleImageGroup">
                <div id="<?php echo $mapId; ?>" class="image_map"></div>
                <input id="xcoord_<?php echo $name;?>" name="xcoord_<?php echo $name;?>" value="<?php echo $value; ?>" type="hidden" />
                <input id="ycoord_<?php echo $name;?>" name="ycoord_<?php echo $name;?>" value="<?php echo $value; ?>" type="hidden" />
                <input id="coord_required_<?php echo $name;?>" name="coord_required_<?php echo $name;?>" value="<?php echo $mandatory; ?>" type="hidden"  />
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            <?php if($edit){ ?>
            $('#<?php echo $mapId; ?>').dropPin({
                fixedHeight:390,
                fixedWidth:307,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: 'pointer',
                hiddenXid: '#xcoord_<?php echo $name;?>',
                hiddenYid: '#ycoord_<?php echo $name;?>',
                pinclass: 'qtipinfo'
            });
            <?php if(!empty($value)){ ?>
            $('#<?php echo $mapId; ?>').dropPin('showPin' ,{
                fixedHeight:390,
                fixedWidth:307,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: 'pointer',
                hiddenXid: '#xcoord_<?php echo $name;?>',
                hiddenYid: '#ycoord_<?php echo $name;?>',
                pinclass: 'qtipinfo',
                pinX: <?php echo $latitude?>,
                pinY: <?php echo $longitude ?>
            });
            <?php } ?>
            <?php }else{ ?>

            $('#<?php echo $mapId; ?>').dropPin('showPin' ,{
                fixedHeight:390,
                fixedWidth:307,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: '',
                pinclass: 'qtipinfo',
                pinX: <?php echo $latitude?>,
                pinY: <?php echo $longitude ?>
            });
            <?php }?>
        });
    </script>


    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});

Form::macro('oneEmpavilleParkMap', function($name, $label, $imageMap, $edit ,$mandatory, $value = null ) {
    // Initial values
    $html = "";
    $latitude = "";
    $longitude = "";
    $mapId = 'map_'.uniqid();
    // Geo Location

    if($value === ',')
        $value =  null;

    if(!empty($value)){
        $geoLocation = explode(",",$value);
        if(is_array($geoLocation) && count($geoLocation) == 2){
            $latitude = $geoLocation[0];
            $longitude = $geoLocation[1];
        }

    }
    ob_start();
    ?>

    <div class="row">
        <div class="col-xs-12 col-12">
            <!-- EmpavilleMap -->
            <label><?php echo $label; ?></label>
            <div class="oneEmpavilleParkImageGroup">
                <div id="<?php echo $mapId; ?>" class="image_map"></div>
                <input id="xcoord_<?php echo $name;?>" name="xcoord_<?php echo $name;?>" value="<?php echo $value; ?>" type="hidden"  />
                <input id="ycoord_<?php echo $name;?>" name="ycoord_<?php echo $name;?>" value="<?php echo $value; ?>" type="hidden"  />
                <input id="coord_required_<?php echo $name;?>" name="coord_required_<?php echo $name;?>" value="<?php echo $mandatory; ?>" type="hidden"  />
            </div>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            <?php if($edit){ ?>
            $('#<?php echo $mapId; ?>').dropPin({
                fixedHeight:209,
                fixedWidth:297,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: 'pointer',
                hiddenXid: '#xcoord_<?php echo $name;?>',
                hiddenYid: '#ycoord_<?php echo $name;?>',
                pinclass: 'qtipinfo'
            });
            <?php if(!empty($value)){ ?>
            $('#<?php echo $mapId; ?>').dropPin('showPin' ,{
                fixedHeight:209,
                fixedWidth:297,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: 'pointer',
                hiddenXid: '#xcoord_<?php echo $name;?>',
                hiddenYid: '#ycoord_<?php echo $name;?>',
                pinclass: 'qtipinfo',
                pinX: <?php echo $latitude?>,
                pinY: <?php echo $longitude ?>
            });
            <?php } ?>
            <?php }else{ ?>

            $('#<?php echo $mapId; ?>').dropPin('showPin' ,{
                fixedHeight:209,
                fixedWidth:297,
                backgroundImage: '<?php echo $imageMap;?>',
                pin: '<?php echo asset('images/empavilleSchools/map_pin.png');?>',
                cursor: '',
                pinclass: 'qtipinfo',
                pinX: <?php echo $latitude?>,
                pinY: <?php echo $longitude ?>
            });
            <?php }?>
        });
    </script>


    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});




/**
 * Displays the oneFileUpload interface.
 *
 * Example:
 *  Form::oneFileUpload("files", "Files", [], $uploadKey )
 *  Form::oneFileUpload("files", "Files", $files, $uploadKey, array("max_file_size"=>"50mb") )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("readonly"=> false) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1,'replaceFile'=>true) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> "images") )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> ["images","docs"]) )
 *
 * Files should be a JSON array:
 *   [{"id":865,"code":"sorSAm8HpAOde5DG4aRZ","name":"home.png","type":"image\/png","size":"1421378","description":"home"},
 *    {"id":867,"code":"ycQ46l8gLrjyCj0Vj7Xz","name":"home_no_logo.png","type":"image\/png","size":1375823,"description":"home no logo"}]
 *
 *
 * This macro needs the following header:
 *  <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
 *
 *
 * @param String $objName
 * @param String $label
 * @param JsonArray $files
 * @param String $uploadKey
 * @param Array $options
 * @return html
 */
Form::macro('oneFileUpload', function($objName, $label, $files = [], $uploadKey = "", $options = array()){
    // Initial values
    $html = "";
    $downloadPath = "/file/download/";
    $fileTypes = [
        "images"    => ["title" => "Imagens",   "extensions" => "jpg,gif,png,tif"],
        "docs"      => ["title" => "Docs",      "extensions" => "doc,docx,rtf,zip,rar,pdf,xls,ppt,xppt"],
        "videos"    => ["title" => "Videos",    "extensions" => "mpg,avi,asf,mov,qt,flv,swf,mp4,wmv,webm,vob,ogv,ogg,mpeg,3gp"],
    ];

    // Attributes
    $max_file_size = "25mb";
    $attributes = "";
    $fileCountLimit = false;
    $replaceFile = false;
    $acceptedTypes = "";
    foreach ($options as $option => $tmp){
        if(strtolower($option) == "max_file_size" ) {
            $max_file_size = $tmp;
        } else if(strtolower($option) == "readonly" ){
            $readOnly = $tmp;
        } else if(strtolower($option) == "downloadpath" ){
            $downloadPath = $tmp;
        } else if(strtolower($option) == "filescountlimit" && is_int($tmp)) {
            $fileCountLimit = $tmp;
        } else if(strtolower($option) == "replacefile") {
            $replaceFile = true;
        } else if(strtolower($option) == "acceptedtypes") {
            if (is_array($tmp)) {
                foreach ($tmp as $tmp2) {
                    $acceptedTypes .= "{title: '" . $fileTypes[$tmp2]["title"] . "', extensions: '" . $fileTypes[$tmp2]["extensions"] . "'},";
                }
            } else
                $acceptedTypes .= "{title: '" . $fileTypes[$tmp]["title"] . "', extensions: '" . $fileTypes[$tmp]["extensions"] . "'}";

            if ($acceptedTypes!=="")
                $acceptedTypes = "mime_types: [" . rtrim($acceptedTypes, '.') . "]";
        } else {
            $attributes .= $option.'="'.$tmp.'" ';
        }
    }

    // Readonly Check
    if(!isset($readOnly) && ONE::isEdit() || (isset($readOnly) && $readOnly === false)){
        $isEdit = true;
    } else {
        $isEdit = false;
    }

    $files = json_encode($files);

    ob_start();
    ?>
    <!-- HTML for fileupload -->
    <div id="attachments-container-<?php echo $objName; ?>" class="form-group attachments-container" >
        <div id="drop-zone-<?php echo $objName; ?>" class="box files-drop-zone">
            <div class="files-dragdrop-here">
                <?php if($isEdit){ ?>
                    <i class="fa fa-cloud-download"></i> <?php echo trans("files.drag_and_drop_files_to_here") ?>
                <?php } ?>
            </div>
            <div class="row no-gutters">
                <div class="col-sm-10"><h5><i class="fa fa-file-o"></i> <?php echo $label; ?></h5></div>
                <div class="col-sm-2 box-tools files-box-tools">
                    <?php if($isEdit){ ?>
                        <a id="select-files-<?php echo $objName; ?>" class="btn btn-flat empatia btn-xs pull-right file-upload-button"><i class="fa fa-upload"></i> <?php echo trans('files.upload'); ?></a>
                    <?php } ?>
                </div>
            </div>
            <div id="files-<?php echo $objName; ?>" class="files"></div>
            <div id="files-list-<?php echo $objName; ?>" class="files-list box-footer" style="display: none"></div>
        </div>
        <!-- Hidden input to store files in JSON format -->
        <input id="<?php echo $objName ?>" name="<?php echo $objName ?>" value='<?php echo (!empty($files) ? $files : ""); ?>'  <?php echo $attributes; ?> type="hidden" >
    </div>


    <!-- JavaScript for fileupload -->
    <script>
        var fileUploader_<?php echo $objName; ?> = new plupload.Uploader(
            {
                headers: {
                    'X-CSRF-TOKEN': "<?php echo  csrf_token(); ?>",
                    'X-UPLOAD-TOKEN': "<?php echo $uploadKey; ?>",
                    'X-AUTH-TOKEN': "<?php echo Session::get('X-AUTH-TOKEN', 'INVALID'); ?>" },
                browse_button: 'select-files-<?php echo $objName; ?>',
                drop_element: document.getElementById('drop-zone-<?php echo $objName; ?>'),
                runtimes: 'html5,flash,silverlight,html4',
                url: "<?php echo action('FilesController@upload'); ?>",
                chunk_size: '1mb',
                filters: {  max_file_size: '<?php echo $max_file_size; ?>',<?php echo $acceptedTypes;?>},
                flash_swf_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf');  ?>",
                silverlight_xap_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap'); ?>",
                init: {
                    PostInit: function () { },
                    FilesAdded: function (up, files) {
                        <?php if ($fileCountLimit!=false && $replaceFile!=false) { ?>
                        oldFiles = JSON.parse($("#<?php echo $objName ?>").val());
                        if (oldFiles.length>=<?php echo $fileCountLimit;?>) {
                            removeUploadedFile_<?php echo $objName; ?>(oldFiles.shift()["id"]);
                            toastr.error('<?php echo trans('files.file_limit_reached_removed_oldest'); ?>');
                        }
                        <?php } else if ($fileCountLimit!=false && $replaceFile==false) { ?>
                        if (JSON.parse($("#<?php echo $objName; ?>").val()).length<<?php echo $fileCountLimit;?>) {
                            <?php } ?>

                            plupload.each(files,
                                function (file) {
                                    var html = "<div id='file_<?php echo $objName; ?>_" + file.id + "' class='row'><div class='col-xs-8 col-sm-8 col-8'><i style='cursor:pointer' class='text-danger' onclick=removeUploadedFile_<?php echo $objName; ?>('" + file.id + "') class='fa fa-times' aria-hidden='true'></i> " + file.name + " (" + plupload.formatSize(file.size) + ")</div><div id='file_progress_bar_<?php echo $objName; ?>_" + file.id + "' class='col-xs-4 col-sm-4 col-4 text-right' ><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span></div></div></div></div>"
                                    $("#files-list-<?php echo $objName; ?>").append(html);
                                });
                            fileUploader_<?php echo $objName; ?>.start();
                            $("#files-list-<?php echo $objName; ?>").slideDown();

                            <?php if ($fileCountLimit!=false && $replaceFile==false) { ?>
                        } else {
                            // Adicionar toastr aqui
                            toastr.error('<?php echo trans('files.file_limit_reached'); ?>');
                            $.each(files, function (i, file) {
                                up.removeFile(file);
                            });
                        }
                        <?php }?>
                    },
                    UploadProgress: function (up, file) {
                        var div = $("#files-list-<?php echo $objName; ?> #file_<?php echo $objName; ?>_" + file.id);
                        div.find("span").html(file.percent + "%");
                        div.find(".progress-bar").width(file.percent + "%"); div.find(".progress-bar").attr("aria-valuenow", file.percent);
                    },

                    removeFile: function ( file) {
                        console.log("removeFile");
                    },

                    FileUploaded: function (up, file, obj) {
                        var filesStr = "";
                        var objJson =  JSON.parse(obj.response);

                        if($("#<?php echo $objName; ?>").val() == "null" || $("#<?php echo $objName; ?>").val() =="" ){
                            filesStr = "[]";
                        } else {
                            filesStr = $("#<?php echo $objName; ?>").val();
                        }

                        var jsonArray = JSON.parse( filesStr );

                        var jsonStringFile = '{"id": '+objJson.result.id+', "code": "'+objJson.result.code+'", "name": "'+file.name+'" , "type": "'+file.type+'" , "size": '+file.size+', "description": ""  }';
                        jsonArray.push( JSON.parse(jsonStringFile) );

                        var jsonString = JSON.stringify(jsonArray);

                        $("#<?php echo $objName; ?>").val(jsonString);

                        var jsonObj = JSON.parse(obj.response);
                        $("#"+file.id).attr("file_id",jsonObj.result.id);

                        var html = "<div class='fu-file-wrapper'><div id='file_<?php echo $objName; ?>_" +objJson.result.id + "' file_id='" + objJson.result.id + "' class='row'>" +
                            '<div class="col-xs-8 col-8"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_'+objJson.result.id+'">' + file.name + '</span></div> ' +
                            '<div class="col-xs-4 col-4" style="text-align: right;"> ' +
                            <?php if($isEdit){ ?>
                            '<a class="btn-flat btn-xs file-btn-info" href="javascript:getFileDetails_<?php echo $objName; ?>(' + objJson.result.id  + ')"><i class="fa fa-gear"></i></a> ' +
                            <?php } ?>
                            '<a class="btn-flat btn-xs file-btn-success" href="<?php echo $downloadPath; ?>?id=' + objJson.result.id  + '&code=' + objJson.result.code  +'" target="_blank" ><i class="fa fa-download"></i></a> ' +
                            <?php if($isEdit){ ?>
                            '<a class="btn-flat btn-xs file-btn-danger" href="javascript:removeUploadedFile_<?php echo $objName; ?>('+objJson.result.id +')"><i class="fa fa-trash"></i></a> ' +
                            <?php } ?>
                            '</div> ' +
                            '</div> ' +
                            '</div> ';
                        $("#files-<?php echo $objName; ?>").append(html);
                        $("#file_<?php echo $objName; ?>_" + file.id).fadeOut("slow");
                    },
                    Error: function (up, err) {
                        toastr["error"](err.message);
                        var div = $("#files-list-<?php echo $objName; ?> #" + err.file.id); div.find("span").html("Erro: " + err.message);
                        div.find(".progress-bar").width("100%");
                        div.find(".progress-bar").attr("aria-valuenow", 100);
                        div.find(".progress-bar").removeClass("progress-bar-success").addClass("progress-bar-danger");
                    }
                }
            });

        <?php if($isEdit){ ?>
        $( document ).ready(function() {
            fileUploader_<?php echo $objName; ?>.init();
        });
        <?php } ?>

        function removeUploadedFile_<?php echo $objName; ?>(id){
            var file_id = $("#file_<?php echo $objName; ?>_"+id).attr("file_id");
            $("#file_<?php echo $objName; ?>_"+id).remove();

            var jsonArray = JSON.parse( $("#<?php echo $objName; ?>").val() );
            for(var i = 0; i < jsonArray.length; i++){
                if(jsonArray[i].id == file_id) {
                    jsonArray.splice(i,1);
                    break;
                }
            }
            $("#<?php echo $objName; ?>").val(JSON.stringify(jsonArray));
        }

        function getFileDetails_<?php echo $objName; ?>(id) {
            var filesStr = $("#<?php echo $objName; ?>").val();
            var jsonArray = JSON.parse( filesStr );

            for(var k in jsonArray) {
                if(jsonArray[k].id == id){
                    var name = jsonArray[k].name;
                    var description = jsonArray[k].description;
                }
            }

            var random = Math.floor(Math.random() * 99999999999999);
            <!-- Modal Dialog -->
            var data = '<div class="modal-dialog">' +
                '<div class="modal-content">'+
                '<div class="modal-header">'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '<h4 class="modal-title"><?php echo preg_replace( "/\r|\n/", "", trans("files.editFile")); ?></h4>'+
                '</div>'+
                '<div class="modal-body">'+
                '<div class="form-group "><label for="name"><?php echo preg_replace( "/\r|\n/", "", trans("files.editFile")); ?></label><input class="form-control" id="name_'+random+'_'+id+'" name="name" type="text" value=""></div>'+
                '<div class="form-group "><label for="description"><?php echo preg_replace( "/\r|\n/", "", trans("files.description")); ?></label><input class="form-control" id="description_'+random+'_'+id+'" name="description" type="text" value=""></div>'+
                '</div>'+
                '<div class="modal-footer">'+
                '<button type="button" class="btn btn-default file-modal-close" data-dismiss="modal"><?php echo preg_replace( "/\r|\n/", "", trans("files.cancel")); ?></button>'+
                '<button type="button" class="btn btn-success file-modal-success" id="confirm" onclick=saveFileDetails_<?php echo $objName; ?>('+id+',"'+random+'") ><?php echo preg_replace( "/\r|\n/", "", trans("files.save")); ?></button>'+
                '</div>'+
                '</div>'+
                '</div>';

            $('<div id="fileDetails-modal_'+random+'_'+id+'" class="modal fade">' + data + '</div>').modal();

            setTimeout(function(){
                $('#name_'+random+'_'+id).val(name);
                $('#description_'+random+'_'+id).val(description);
            }, 300);

        }

        function saveFileDetails_<?php echo $objName; ?>(id,random){
            var filesStr = $("#<?php echo $objName; ?>").val();
            var jsonArray = JSON.parse( filesStr );

            for(var k in jsonArray) {
                if(jsonArray[k].id == id){
                    var name = $('#name_'+random+'_'+id).val();
                    var description = $('#description_'+random+'_'+id).val();
                    jsonArray[k].name = name;
                    jsonArray[k].description = description;
                    // Update in List
                    $("#file_title_<?php echo $objName; ?>_"+id).html(name);
                }
            }

            var jsonString = JSON.stringify(jsonArray);
            $("#<?php echo $objName; ?>").val(jsonString);

            $('#fileDetails-modal_'+random+'_'+id).modal('hide')
        }

        function initFileUploadList_<?php echo $objName; ?>(){
            var jsonArray = JSON.parse( $("#<?php echo $objName; ?>").val() );
            for(var i = 0; i < jsonArray.length; i++){
                var file = jsonArray[i].name + " (" + plupload.formatSize( jsonArray[i].size) + ")";

                var html = "<div class='fu-file-wrapper'><div id='file_<?php echo $objName; ?>_" + jsonArray[i].id + "' file_id='" + jsonArray[i].id + "' class='row'>" +
                    '<div class="col-xs-8 col-8"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_' + jsonArray[i].id + '" >' + jsonArray[i].name + '</span></div> ' +
                    '<div class="col-xs-4 col-4" style="text-align: right;"> ' +
                    <?php if($isEdit){ ?>
                    '<a class="btn-flat btn-xs file-btn-info" href="javascript:getFileDetails_<?php echo $objName; ?>(' + jsonArray[i].id + ')"><i class="fa fa-gear"></i></a> ' +
                    <?php } ?>
                    '<a class="btn-flat btn-xs file-btn-success" href="<?php echo $downloadPath; ?>?id=' + jsonArray[i].id + '&code=' + jsonArray[i].code + '" target="_blank" ><i class="fa fa-download"></i></a> ' +
                    <?php if($isEdit){ ?>
                    '<a class="btn-flat btn-xs file-btn-danger" href="javascript:removeUploadedFile_<?php echo $objName; ?>('+ jsonArray[i].id+')"><i class="fa fa-trash"></i></a> ' +
                    <?php } ?>
                    '</div> ' +
                    '</div> ' +
                    '</div> ';

                $("#files-<?php echo $objName; ?>").append(html);
            }
            $("#files-list-<?php echo $objName; ?>").slideDown();
        }

        initFileUploadList_<?php echo $objName; ?>();
    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});



/**
 * Displays the oneImageUpload interface.
 *
 * Example:
 *  Form::oneImageUpload("files", "Image", ["id" => 122, "code" => "GYtak716"], $uploadKey, ["maxfilesize"=>20,"mimetypes"=>"jpg,gif,png"])
 *
 * This macro needs the following header:
 *  <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
 *  <script src="{{ asset("js/cropper.min.js") }}"></script>
 *  <script src="{{ asset("js/canvas-to-blob.js") }}"></script>
 *
 *
 * @param String $objName
 * @param String $label
 * @param Array $image
 * @param String $uploadKey
 * @param Array $options
 * @return html
 */
Form::macro('oneImageUpload', function($objName, $label, $image, $uploadKey = "", $options = array()){
    // Initial values
    $html = "";
    $downloadPath = "/file/download/";
    $maxFileSize = 20;
    $idBrowseButton = 'browse-button-' . $objName;
    $idDropZone = 'image-drop-zone-' . $objName;
    $variable = "imageUploader" . $objName;
    $idModal = 'getCroppedCanvasModal';
    $aspectRatio = 1;
    $dragMode = "move";
    $idTitle = "getCroppedCanvasTitle";
    $title = trans('files.imageResize');
    $imageId = "fuImage_" . $objName;
    $cropperButton = "cropper_button_" . $objName;
    $imageContainer = "img-container" . $objName;
    $mimeTypes = "jpg,gif,png";
    $imageClass = "";
    $wrapperClass = "";
    $wrapperButtons = "";
    $buttonClass = "btn btn-outlined btn-block btn-file-upload";

    foreach ($options as $option => $tmp) {
        if (strtolower($option) == "maxfilesize") {
            $maxFileSize = $tmp;
        }
        if (strtolower($option) == "mimetypes") {
            $mimeTypes = $tmp;
        }
        if (strtolower($option) == "aspectratio") {
            $aspectRatio = $tmp;
        }
        if (strtolower($option) == "dragmode") {
            $dragMode = $tmp;
        }
        if (strtolower($option) == "imagedefault") {
            $imagedefault = $tmp;
        }
        if (strtolower($option) == "wrapperclass") {
            $wrapperClass = $tmp;
        }
        if (strtolower($option) == "wrapperbuttons") {
            $wrapperButtons = $tmp;
        }
        if (strtolower($option) == "buttonclass") {
            $buttonClass = $tmp;
        }
        if(strtolower($option) == "readonly" ) {
            $readOnly = $tmp;
        }
    }

    // Readonly Check
    if(!isset($readOnly) && ONE::isEdit() || (isset($readOnly) && $readOnly === false)){
        $isEdit = true;
    } else {
        $isEdit = false;
    }

    ob_start();
    ?>
    <label><?php echo $label; ?></label><br>
    <div class="<?php echo  $wrapperClass ?>">
        <?php if(is_array($image) && array_key_exists('id', $image) && array_key_exists('code', $image) && $image["id"] > 0) { ?>
            <img class="<?php echo $imageClass; ?>" src="<?php echo URL::action('FilesController@download', ['id' => $image["id"], 'code' => $image["code"], 1] ); ?>" alt="" id="<?php echo $idDropZone ?>" style="max-height: 200px">
        <?php } else { ?>
            <img class="<?php echo $imageClass; ?>" src="<?php echo asset($imagedefault); ?>" alt="" id="<?php echo $idDropZone; ?>" style="max-height: 200px">
        <?php }  ?>
        <input id="<?php echo $objName ?>" name="<?php echo $objName ?>" value="<?php if(is_array($image) && array_key_exists('id', $image)) { echo $image["id"]; } ?>" type="hidden" >
    </div>


    <?php
    if($isEdit) {
        ?>
        <!-- /.widget-user-image -->
        <div class="<?php echo $wrapperButtons; ?>">
            <button id="<?php echo $idBrowseButton ?>" class="<?php echo $buttonClass; ?>">
                <i class="fa fa-upload"></i>&nbsp;<?php echo trans('user.change_profile_picture') ?>
            </button>
            <div class="modal fade docs-cropped" id="<?php echo $idModal; ?>" aria-hidden="true"
                 aria-labelledby="<?php echo $idTitle; ?>" role="dialog" tabindex="-1">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
                            <h4 class="modal-title" id="<?php echo $idTitle; ?>"><?php echo $title; ?></h4>
                        </div>
                        <div class="modal-body">
                            <div class="docs-preview clearfix">
                                <div class="img-preview preview-lg"></div>
                            </div>
                            <div class="row">
                                <div class="col-md-12">
                                    <div id="<?php echo $imageContainer ?>" style="height:400px;width:400px;">
                                        <img id="<?php echo $imageId; ?>" src="" alt="">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-default"
                                    data-dismiss="modal"><?php echo trans('files.close'); ?></button>
                            <a class="btn btn-primary" id="<?php echo $cropperButton; ?>"
                               data-method="getCroppedCanvas"><?php echo trans('files.save'); ?></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }
    ?>

    <script>
        function updateClickListener<?php echo $objName; ?>() {
            $('#<?php echo $cropperButton; ?>').click(function () {
                var $image = $("#<?php echo $imageId; ?>");
                var $this = $(this);
                var data = $this.data();
                var $target;
                var result;
                if ($this.prop('disabled') || $this.hasClass('disabled')) {
                    return;
                }
                if($image.data('cropper') && data.method) {
                    data = $.extend({}, data); // Clone a new one
                    if (typeof data.target !== 'undefined') {
                        $target = $(data.target);
                        if (typeof data.option === 'undefined') {
                            try {
                                data.option = JSON.parse($target.val());
                            } catch (e) {
                                console.log(e.message);
                            }
                        }
                    }
                    result = $image.cropper(data.method, data.option, data.secondOption);

                    switch (data.method) {
                        case 'scaleX':
                        case 'scaleY':
                            $(this).data('option', -data.option);
                            break;
                        case 'getCroppedCanvas':
                            if (result) {
                                result.toBlob(
                                    function (blob) {
                                        // Do something with the blob object,
                                        // e.g. creating a multipart form for file uploads:
                                        var formData = new FormData();
                                        formData.append('file', blob, $("#<?php echo $imageId; ?>").attr("title"));
                                        var file = new Blob([blob], { type: "image/jpeg" });
                                        var up = this[$("#<?php echo $imageId; ?>").attr("uploader")];
                                        up.addFile(file);

                                        $("#<?php echo $imageId; ?>").attr("src", "");
                                        /* ... */
                                    },
                                    'image/jpeg'
                                );
                            }

                            break;
                    }
                    if ($.isPlainObject(result) && $target) {
                        try {
                            $target.val(JSON.stringify(result));
                        } catch (e) {
                            console.log(e.message);
                        }
                    }
                }
            });

        }

        // This is just a sample script. Paste your real code (javascript or HTML) here.
        var <?php echo $variable ?> = new plupload.Uploader({
            headers: {
                'X-CSRF-TOKEN': "<?php echo csrf_token(); ?>",
                'X-UPLOAD-TOKEN': "<?php echo $uploadKey; ?>",
                'X-AUTH-TOKEN': "<?php echo Session::get('X-AUTH-TOKEN', 'INVALID'); ?>",
            },
            browse_button: '<?php echo $idBrowseButton; ?>',
            drop_element: document.getElementById('<?php echo $idDropZone ?>'),
            runtimes: 'html5,flash,silverlight,html4',
            url: "<?php echo action('FilesController@upload'); ?>",
            chunk_size: '1mb',
            multi_selection: false,
            filters: {
                // Maximum file size
                <?php if(!empty($maxFileSize) && $maxFileSize!=0){ ?>
                max_file_size: '<?php echo $maxFileSize; ?>mb',
                <?php } ?>
                mime_types: [{
                    title: "images",
                    extensions: "<?php echo $mimeTypes; ?>"
                }, ]
            },
            flash_swf_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf'); ?>",
            silverlight_xap_url: "<?php asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap'); ?>",
            init: {
                PostInit: function() {},
                FilesAdded: function(up, files) {
                    originalData = {};
                    if ($('#<?php echo $idModal; ?>').hasClass('in')) {
                        $('#<?php echo $idModal; ?>').modal('hide');
                        <?php echo $variable ?>.start();
                        return;
                    }
                    $('#<?php echo $idModal ?>').modal();
                    if (files && files[0]) {
                        var reader = new FileReader();
                        reader.onload = function(e) {
                            $('#<?php echo $imageContainer; ?> > img').attr('src', e.target.result);
                        };
                        reader.readAsDataURL(files[0].getNative());
                        var $image = $("#<?php echo $imageContainer; ?> > img");
                        up.splice();
                        reader.onload = function(oFREvent) {
                            $image.cropper('destroy');
                            $image.attr('src', this.result);
                            $("#<?php echo $imageContainer; ?> > img").attr("title", files[0].name);
                            $("#<?php echo $imageContainer; ?> > img").attr("uploader", "<?php echo $variable ?>");
                            var options = {
                                aspectRatio: <?php echo $aspectRatio; ?>,
                                dragMode: '<?php echo $dragMode; ?>',
                                crop: function(e) {}
                            };
                            $image.on({}).cropper(options);
                        };
                    }
                },
                UploadProgress: function(up, file) {
                    /*
                     var div = $("#banner-list #" + file.id);
                     div.find("span").html(file.percent + "%");
                     div.find(".progress-bar").width(file.percent + "%");
                     div.find(".progress-bar").attr("aria-valuenow", file.percent);
                     */
                },
                FileUploaded: function(up, file, obj) {
                    try {
                        // console.log(obj);
                        var jsonObj = JSON.parse(obj.response);

                        $("#<?php echo $objName; ?>").val(jsonObj.result.id);
                        // $("#<?php echo $idDropZone; ?>").attr("src",jsonObj.result.link);
                        $("#<?php echo $idDropZone; ?>").attr("src",'<?php echo $downloadPath; ?>?id='+jsonObj.result.id+'&code='+jsonObj.result.code);
                    } catch (e) {
                        console.log(e);
                    }
                },
                Error: function(up, err) {
                    toastr["error"](err.message);
                    /*
                     var div = $("#banner-list #" + err.file.id);
                     div.find("span").html("Erro: " + err.message);
                     div.find(".progress-bar").width("100%");
                     div.find(".progress-bar").attr("aria-valuenow", 100);
                     div.find(".progress-bar").removeClass("progress-bar-success").addClass("progress-bar-danger");
                     */
                }
            }
        });
        <?php echo $variable ?>.init();
        updateClickListener<?php echo $objName; ?>();
    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});

