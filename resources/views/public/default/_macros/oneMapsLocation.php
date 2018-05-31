<!-- Map Locations -->
<div class="map-statistics">
    <div class="">
        <div class="row">
            <div class="col-12">
                <?php
                if(!empty($label)){
                    ?>
                    <label class="maps_location_label"><?php echo $label; ?></label>
                    <?php
                }
                ?>
                <div id="<?php echo $objId ?>" <?php echo $attributes; ?>></div>
            </div>
            <div class="col-12">
                <div class="g-map-label-group">
                    <div class="row no-gutters">
                        <div class="col-12 col-md-4 label-info2-map" style="display:none;">
                            <?php if (isset($locations) && count($locations) > 0) { ?>
                                <span class="counter">
                                    <?php echo count($locations); ?>
                                </span>
                                <?php echo ONE::transSite("map_no_topics_with_geo_mapping"); ?>
                            <?php } else { ?>
                                <?php echo ONE::transSite("map_there_are_no_topics_with_geo_mapping"); ?>
                            <?php }?>
                            <br>
                            <?php if (isset($totalNoMapTopics)) {
                                if (!is_null($totalNoMapTopics) && !empty($totalNoMapTopics)) { ?>
                                    <?php if ($totalNoMapTopics > 0) { ?>
                                        <span class="counter">
                                                <?php echo $totalNoMapTopics; ?>
                                            </span>
                                        <?php echo ONE::transSite("map_no_topics_dont_have_geo_mapping"); ?>
                                    <?php } else { ?>
                                        <?php echo ONE::transSite("map_there_are_no_topics_with_geo_mapping"); ?>
                                    <?php } ?>
                                <?php }
                            } ?>
                        </div>
                        <!--
                        <div class="col-12 col-md-2 label-info-map">
                            <img src="<?php echo !empty($markerIcon) ? $markerIcon : asset('images/pin.png'); ?>" style="height:40px;padding-right:8px;" class="map-icons-caption" alt="pin"/>
                            <?php echo $pinTitle; ?>
                        </div>
                        <div class="col-12 col-md-3 label-info-map">
                            <img src="<?php /*echo asset('images/pin_group.png'); */?>" style="height:40px;" class="map-icons-caption" alt="pin_group"/>
                            <?php /*echo $pinGroup; */?>
                        </div>-->
                        <?php
                        $j = 0;
                        foreach($categoriesHelp as $categoryHelp){
                            if(($categoryHelp[1] != "" )){
                                $j++;
                                $image = $categoryHelp[1][0];
                                ?>
                                <div class="col-md-2 col-12">
                                    <div class="row no-gutters">
                                        <div class="col-lg-3 col-md-4 col-xs-3 col-3">
                                            <img src="<?php echo  URL::action('FilesController@download', ['id' => $image->id, 'code' => $image->code, 'inline' => 1, 'h' => 40, 'extension' => 'png', 'quality' => 55] ); ?>" />
                                        </div>
                                        <div class="col-lg-9 col-md-8 col-xs-9 col-9 label-info-map">
                                            <span class="help-category-maps"><?php echo ucfirst($categoryHelp[0]); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php
                            }
                        }
                        ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>