<!-- HTML for fileupload -->
<div id="attachments-container-<?php echo $objName; ?>" class="form-group attachments-container" >
    <div id="drop-zone-<?php echo $objName; ?>" class="box files-drop-zone">
<!--        <div class="row no-gutters">-->
<!--            <div class="col-sm-12">-->
<!--                <div class="box-title">-->
<!--                    <i class="fa fa-file-o"></i> --><?php //echo $label; ?>
<!--                </div>-->
<!--            </div>-->
<!--            </div>-->
        <div class="row no-gutters">
            <div class="col-sm-12 box-tools files-box-tools">
                <?php if($isEdit){ ?>
                    <a id="select-files-<?php echo $objName; ?>" class="btn btn-flat empatia btn-xs file-upload-button"><i class="fa fa-upload"></i> <?php echo  $translation; ?></a>
                <?php } ?>
            </div>
        </div>
<!--        <div id="files---><?php //echo $objName; ?><!--" class="files"></div>-->
<!--        <div id="files-list---><?php //echo $objName; ?><!--" class="files-list box-footer" style="display: none"></div>-->
    </div>
    <!-- Hidden input to store files in JSON format -->
    <input id="<?php echo $objName ?>" name="<?php echo !empty($name) ? $name : $objName; ?>" value='<?php echo (!empty($files) ? $files : ""); ?>'  <?php echo $attributes; ?> type="hidden" >
</div>

<style>
    #select-files-files1:hover{
        color: black!important;
    }

    .fu-file-wrapper{
        padding: 0 10px;
    }

    #drop-zone-files1, .button, .button a{
        min-height: 0px;
        height: auto;
    }

    .button, .button a{
        padding: 0!important;
    }

    #attachments-container-<?php echo $objName; ?>, #drop-zone-<?php echo $objName; ?>{
        min-height: 0px;
        height: auto;
    }
    .primary-color{
        background: <?php echo ONE::getSiteConfiguration("color_primary")?>!important;
    }

    .files-row{
        display:flex;
        flex-direction: row;
    }

    .files-col-name{
        flex:0;
        flex-basis: 155px;
    }

    .files-col-buttons{
        flex:1;
        display: flex;
        justify-content: flex-end;
        align-items: center;
    }

    .files-btn{
        padding: 0 3px;
    }

    .files-btn.white{
        color: #fff;
    }


    .files-btn.white:hover{
        color: <?php echo ONE::getSiteConfiguration("color_primary")?>;
        background-color: #fff;
    }


</style>