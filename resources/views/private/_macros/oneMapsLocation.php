<!-- Map Locations -->
<div class="map-statistics">
    <?php
    if(!empty($label)){
        ?>
        <label class="maps_location_label"><?php echo $label; ?></label>
        <?php
    }
    ?>
    <div class="">
        <div class="<?php echo $mapColumns["map"] ?>">
            <div id="<?php echo $objId ?>" <?php echo $attributes; ?>></div>
        </div>
        <!-- <div class="<?php echo $mapColumns["legend"] ?> g-map-label-group">
            <div class="row map-statistics-row">
                <!-- Number of topics with geo-reference -->
            <div class="">
                <?php
                $j = 0;
                foreach($categoriesHelp as $categoryHelp){
                    if(($categoryHelp[1] != "" )){
                        $j++;
                        $image = $categoryHelp[1][0];
                        ?>
                            <div class="row">
                                <div class="col-lg-3 col-md-4 col-xs-3 col-3"><img src="<?php echo  URL::action('FilesController@download', ['id' => $image->id, 'code' => $image->code, 1] ); ?>" style="height:4em;" /></div>
                                <div class="col-lg-9 col-md-8 col-xs-9 col-9" style="padding-top: 1em"><?php echo ucfirst($categoryHelp[0]); ?></div>
                            </div>
                        <?php
                    }
                }
                ?>
            </div>
        </div>
    </div>
</div>