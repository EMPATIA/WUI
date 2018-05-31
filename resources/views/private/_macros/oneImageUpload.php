<label><?php echo $label; ?></label><br>
<div class="<?php echo  $wrapperClass ?>">
    <?php if(is_array($image) && array_key_exists('id', $image) && array_key_exists('code', $image) && $image["id"] > 0) { ?>
        <img class="<?php echo $imageClass; ?>" src="<?php echo URL::action('FilesController@download', ['id' => $image["id"], 'code' => $image["code"], 1] ); ?>" alt="" id="<?php echo $idDropZone ?>" style="max-height: 200px">
    <?php } else { ?>
        <img class="<?php echo $imageClass; ?>" src="<?php echo asset($imagedefault); ?>" alt="" id="<?php echo $idDropZone; ?>" style="max-height: 200px">
    <?php }  ?>
    <input id="<?php echo $objName ?>" name="<?php echo $name ?>" value="<?php if(is_array($image) && array_key_exists('id', $image)) { echo $image["id"]; } ?>" type="hidden" >
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