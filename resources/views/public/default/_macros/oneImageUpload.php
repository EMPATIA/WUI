<script>
    $( document ).ready(function() {
        setTimeout(function(){ $('#add_image').modal('hide'); }, 400);
        setTimeout(function(){ $('#add_image').css("opacity", "1"); }, 450);
//        setTimeout(function(){ $('#add_image').addClass('fade'); }, 500);
    });
</script>

<a data-toggle="modal" data-target="#add_image_<?php echo $objName; ?>" class="btn btn-flat empatia" style="margin-top:0;">
    <em class="fa fa-picture-o" aria-hidden="true"></em> 
    <?php 
        if( !empty($cb) || !empty($cbKey) )
            echo ONE::transCb('upload_image', !empty($cb) ? $cb->cb_key : $cbKey);
        else    
            echo ONE::transSite('upload_image');
    ?>
</a>

<div id="add_image_<?php echo $objName; ?>" class="modal show">
    <div class="modal-dialog" role="document">
        <div class="modal-content" >
            <div class="modal-header">
                <h5 class="modal-title">
                    <?php
                    if( !empty($cb) || !empty($cbKey) )
                        echo ONE::transCb('upload_image', !empty($cb) ? $cb->cb_key : $cbKey);
                    else
                        echo ONE::transSite('upload_image');
                    ?>
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-12">
                        <div class="form-group parameter-drop-zone">
                            <div class="help-text-upload">
                                <?php
                                if( !empty($cb) || !empty($cbKey) )
                                    echo ONE::transCb('upload_add_image_help', !empty($cb) ? $cb->cb_key : $cbKey);
                                else
                                    echo ONE::transSite('upload_add_image_help');
                                ?>
                                <!--  Adicione uma imagem que represente a sua proposta. Esta imagem será a imagem principal da sua
                                proposta e será utlizada na listagem das propostas e na partilha no Facebook. A dimensão desta
                                imagem será ajustada automaticamente, mas recomendamos imagems com a dimensão XXXXX.-->
                            </div>
                            <div style="clear:both"></div>

                            <label><?php echo $label; ?></label><br>
                            <div class="<?php echo  $wrapperClass ?>">
                                <?php if(is_array($image) && array_key_exists('id', $image) && array_key_exists('code', $image) && $image["id"] > 0) { ?>
                                    <img class="<?php echo $imageClass; ?>" src="<?php echo URL::action('FilesController@download', ['id' => $image["id"], 'code' => $image["code"], 1] ); ?>" alt="" id="<?php echo $idDropZone ?>" style="max-height: 200px">
                                <?php } else { ?>
                                    <center><img class="<?php echo $imageClass; ?>" src="<?php echo asset($imagedefault); ?>" alt="" id="<?php echo $idDropZone; ?>" style="max-height: 200px"></center>
                                <?php }  ?>
                                <input id="<?php echo $objName ?>" name="<?php echo $name ?>" value="<?php if(is_array($image) && array_key_exists('id', $image)) { echo $image["id"]; } ?>" type="hidden" >
                            </div>

                            <?php
                            if($isEdit) {
                                ?>
                                <!-- /.widget-user-image -->
                                <div class="<?php echo $wrapperButtons; ?>">
                                    <button id="<?php echo $idBrowseButton ?>" class="<?php echo $buttonClass; ?>">
                                        <i class="fa fa-upload"></i>&nbsp;
                                        <?php
                                        if( !empty($cb) || !empty($cbKey) )
                                            echo ONE::transCb('upload_change_profile_picture', !empty($cb) ? $cb->cb_key : $cbKey);
                                        else
                                            echo ONE::transSite('upload_change_profile_picture');
                                        ?>
                                    </button>
                                </div>
                                <?php
                            }
                            ?>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
if($isEdit) {
    ?>
    <!-- /.widget-user-image -->
    <div class="modal docs-cropped" id="<?php echo $idModal; ?>"
         aria-labelledby="<?php echo $idTitle; ?>" role="dialog" tabindex="-1" style="border:none;">
        <div class="modal-dialog" style="border:none;">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title" id="<?php echo $idTitle; ?>"><?php echo $title; ?></h4>
                    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>
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
                    <button type="button" class="btn btn-flat btn-default"
                            data-dismiss="modal" style="margin-top: 0; margin-bottom: 0">
                        <?php
                        if( !empty($cb) || !empty($cbKey) )
                            echo ONE::transCb('upload_image_close', !empty($cb) ? $cb->cb_key : $cbKey);
                        else
                            echo ONE::transSite('upload_image_close');
                        ?>
                      </button>
                    <a class="btn bt-success" id="<?php echo $cropperButton; ?>" data-method="getCroppedCanvas" style="margin-top: 0; margin-bottom: 0">
                        <?php
                        if( !empty($cb) || !empty($cbKey) )
                            echo ONE::transCb('upload_image_save', !empty($cb) ? $cb->cb_key : $cbKey);
                        else
                            echo ONE::transSite('upload_image_save');
                        ?>
                    </a>
                </div>
            </div>
        </div>
    </div>

    <?php
}
?>
<script>
    $( document ).ready(function() {
        $('#add_image').css("opacity", "0");
        $('#add_image').modal('show');
        $('.modal-backdrop').hide();
    });
</script>


