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
    <input id="<?php echo $objName ?>" name="<?php echo !empty($name) ? $name : $objName; ?>" value='<?php echo (!empty($files) ? $files : ""); ?>'  <?php echo $attributes; ?> type="hidden" >
</div>
