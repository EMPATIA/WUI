<?php

/**
 * Displays the oneSingleFileUpload interface.
 *
 * Example:
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey )
 *  Form::oneSingleFileUpload("files", "Files", $files, $uploadKey, array("max_file_size"=>"50mb") )
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey, array("readonly"=> false) )
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1) )
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1,'replaceFile'=>true) )
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> "images") )
 *  Form::oneSingleFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> ["images","docs"]) )
 *  Form::oneSingleFileUpload("files", "Files", [], isset($uploadKey) ? $uploadKey : "",["name" => "files[]"])
 *
 * Files should be a JSON array:
 *   [{"id":865,"code":"sorSAm8HpAOde5DG4aRZ","name":"home.png","type":"image\/png","size":"1421378","description":"home"}]
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
Form::macro('oneSingleFileUpload', function($objName, $label, $files = [], $uploadKey = "", $options = array()){
    // Initial values
    $html = "";
    $downloadPath = "/file/download/";

    $fileTypes = [
        "images"    => ["title" => "Imagens",   "extensions" => "jpg,gif,png"],
        "docs"      => ["title" => "Docs",      "extensions" => "pdf"],
        "videos"    => ["title" => "Videos",    "extensions" => "avi,mp4"],
    ];

    // Attributes
    $max_file_size = "25mb";
    $attributes = "";
    $fileCountLimit = false;
    $replaceFile = false;
    $acceptedTypes = "";
    $name = "";
    foreach ($options as $option => $tmp){
        if(strtolower($option) == "name" ) {
            $name = $tmp;
        }elseif(strtolower($option) == "max_file_size" ) {
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
            <div style="display:flex;justify-content:center;align-items:center;">
              <div class="files-dragdrop-here" >
                    <a id="select-files-<?php echo $objName; ?>" class="btn btn-flat empatia btn-xs pull-right file-upload-button" style="color:white;display:block;width:100%;margin:auto;padding:15px;background-color:#4472c4;border-radius: 8px;">
                        <div style="display:flex;justify-content:center;align-items:center;">
                          <div>
                                <div style="background-color:#4472c4;width:100%;border-radius: 8px;margin: auto;">
                                    <?php if($isEdit){ ?>
                                        <i class="fa fa-upload"></i> <?php echo ONE::transSite('upload_image'); ?>
                                        <br>
                                        <?php if($isEdit){ ?>
                                            <span style="margin-top:8px">(<?php echo ONE::transSite("drag_and_drop_files_to_here") ?>)</span>
                                        <?php } ?>
                                    <?php } ?>
                                </div>
                            </div>
                        </div>
                     </a>
                </div>
            </div>
        </div>
            <!--
            <div class="row no-gutters">
                <div class="col-sm-10"><h5><i class="fa fa-file-o"></i> <?php /*echo $label; */?></h5></div>
                <div class="col-sm-2 box-tools files-box-tools">

                </div>
            </div>
            -->
            <div id="files-<?php echo $objName; ?>" class="files"></div>
            <div id="files-list-<?php echo $objName; ?>" class="files-list box-footer" style="display: none"></div>


        <div id="image-preview-<?php echo $objName; ?>" style="display:none;"></div>

        <!-- Hidden input to store files in JSON format -->
        <input id="<?php echo $objName ?>" name="<?php echo !empty($name) ? $name : $objName; ?>" value='<?php echo (!empty($files) ? $files : ""); ?>'  <?php echo $attributes; ?> type="hidden" >
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
                filters: {  max_file_size: '<?php echo $max_file_size; ?>',
                            prevent_duplicates: true,
                            <?php echo $acceptedTypes;?>},
                flash_swf_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf');  ?>",
                silverlight_xap_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap'); ?>",
                multi_selection: false,
                init: {
                    PostInit: function () { },
                    FilesAdded: function (up, files) {

                        <?php if ($fileCountLimit!=false && $replaceFile!=false) { ?>
                        oldFiles = JSON.parse($("#<?php echo $objName ?>").val());
                        if (oldFiles.length>=<?php echo $fileCountLimit;?>) {
                            removeUploadedFile_<?php echo $objName; ?>(oldFiles.shift()["id"]);
                            // toastr.error('<?php echo trans('files.file_limit_reached_removed_oldest'); ?>');
                            showFileUploadError_<?php echo $objName; ?>('files.file_limit_reached_removed_oldest'); ?>');
                        }
                        <?php } else if ($fileCountLimit!=false && $replaceFile==false) { ?>
                        if (JSON.parse($("#<?php echo $objName; ?>").val()).length<<?php echo $fileCountLimit;?>) {
                            <?php } ?>
                            $("#drop-zone-<?php echo $objName; ?>").hide();
                            plupload.each(files,
                                function (file) {
                                    var html = '<div style="background-color:#4472c4;width:100%;border-radius: 8px;margin: auto;color:white;">';
                                    // html += "<div id='file_<?php echo $objName; ?>_" + file.id + "' class='row'><div class='col-xs-8 col-sm-8 col-8'><i style='cursor:pointer' class='text-danger' onclick=removeUploadedFile_<?php echo $objName; ?>('" + file.id + "') class='fa fa-times' aria-hidden='true'></i> " + file.name + " (" + plupload.formatSize(file.size) + ")</div><div id='file_progress_bar_<?php echo $objName; ?>_" + file.id + "' class='col-xs-4 col-sm-4 col-4 text-right' ><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span></div></div></div></div>"
                                    html += "<div id='file_<?php echo $objName; ?>_" + file.id + "'><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span>"
                                    html += "</div>";
                                    $("#files-list-<?php echo $objName; ?>").append(html);
                                });
                            fileUploader_<?php echo $objName; ?>.start();
                            $("#files-list-<?php echo $objName; ?>").slideDown();

                            <?php if ($fileCountLimit!=false && $replaceFile==false) { ?>
                        } else {
                            // Adicionar toastr aqui
                            // toastr.error('<?php echo trans('files.file_limit_reached'); ?>');
                            showFileUploadError_<?php echo $objName; ?>('<?php echo ONE::transSite('file_limit_reached'); ?>')
                            $.each(files, function (i, file) {
                                up.removeFile(file);
                            });

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
                        /*
                        if($("#<?php echo $objName; ?>").val() == "null" || $("#<?php echo $objName; ?>").val() =="" ){
                            filesStr = "[]";
                        } else {
                            filesStr = $("#<?php echo $objName; ?>").val();
                        }
                        */

                        filesStr = "[]";

                        var jsonArray = JSON.parse( filesStr );

                        var jsonStringFile = '{"id": '+objJson.result.id+', "code": "'+objJson.result.code+'", "name": "'+file.name+'" , "type": "'+file.type+'" , "size": '+file.size+', "description": ""  }';
                        jsonArray.push( JSON.parse(jsonStringFile) );

                        var jsonString = JSON.stringify(jsonArray);

                        $("#<?php echo $objName; ?>").val(jsonString);

                        var jsonObj = JSON.parse(obj.response);
                        $("#"+file.id).attr("file_id",jsonObj.result.id);

                        /*
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
                        */
                        // $("#file_<?php echo $objName; ?>_" + file.id).fadeOut("slow");
                        $("#file_<?php echo $objName; ?>_" + file.id).hide();


                        // Image preview
                        var imageHtml = '<div style="background-image:url(' + "<?php echo env('DOWNLOAD_API', '/files/'); ?>" + objJson.result.id + '/' + objJson.result.code + '/1);background-size: cover;background-repeat:no-repeat;width:100%;height:180px;margin-bottom:10px;"></div>';
                        imageHtml += '<center><a class="btn btn-flat btn-danger btn-sm file-btn-danger" href="javascript:removeUploadedFile_<?php echo $objName; ?>(' + objJson.result.id  + ')"><i class="fa fa-trash"></i> <?php echo ONE::transSite('remove_image'); ?></a></center>'
                        $("#image-preview-<?php echo $objName; ?>").html(imageHtml);
                        $("#image-preview-<?php echo $objName; ?>").show();
                        $("#drop-zone-<?php echo $objName; ?>").hide();
                    },
                    Error: function (up, err) {
                            console.log(err);
                        showFileUploadError_<?php echo $objName; ?>(err.message);
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

            $("#image-preview-<?php echo $objName; ?>").hide();
            $("#drop-zone-<?php echo $objName; ?>").show();
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
                '<h4 class="modal-title"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("editFile")); ?></h4>'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '</div>'+
                '<div class="modal-body">'+
                '<div class="form-group "><label for="name"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("editFile")); ?></label><input class="form-control" id="name_'+random+'_'+id+'" name="name" type="text" value=""></div>'+
                '<div class="form-group "><label for="description"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("description")); ?></label><input class="form-control" id="description_'+random+'_'+id+'" name="description" type="text" value=""></div>'+
                '</div>'+
                '<div class="modal-footer">'+
                '<button type="button" class="btn btn-default file-modal-close" data-dismiss="modal"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("cancel")); ?></button>'+
                '<button type="button" class="btn btn-success file-modal-success" id="confirm" onclick=saveFileDetails_<?php echo $objName; ?>('+id+',"'+random+'") ><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("save")); ?></button>'+
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

        function showFileUploadError_<?php echo $objName; ?>(err_message){
            var random = Math.floor(Math.random() * 99999999999999);
            <!-- Modal Dialog -->
            var data = '<div class="modal-dialog">' +
                '<div class="modal-content">'+
                '<div class="modal-header">'+
                '<h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo preg_replace( "/\r|\n/", "", ONE::transSite("Error")); ?></h4>'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '</div>'+
                '<div class="modal-body">'+
                err_message+
                '<style>.modal-backdrop{z-index:1050;}</style>'+
                '</div>'+
                '</div>'+
                '</div>';
            var errorWindow = $('<div id="error-modal_'+random+'" class="modal fade">' + data + '</div>').modal();
            errorWindow.on('hidden.bs.modal', function () {
                // do something…
                $('#error-modal_'+random).remove();
            });
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
 * Displays the oneSingleFileUpload interface.
 *
 * Example:
 *  Form::oneFileUpload("files", "Files", [], $uploadKey )
 *  Form::oneFileUpload("files", "Files", $files, $uploadKey, array("max_file_size"=>"50mb") )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("readonly"=> false) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("filesCountLimit"=> 1,'replaceFile'=>true) )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> "images") )
 *  Form::oneFileUpload("files", "Files", [], $uploadKey, array("acceptedtypes"=> ["images","docs"]) )
 *  Form::oneFileUpload("files", "Files", [], isset($uploadKey) ? $uploadKey : "",["name" => "files[]"])
 *
 * Files should be a JSON array:
 *   [{"id":865,"code":"sorSAm8HpAOde5DG4aRZ","name":"home.png","type":"image\/png","size":"1421378","description":"home"}]
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
        "images"    => ["title" => "Imagens",   "extensions" => "jpg,gif,png"],
        "docs"      => ["title" => "Docs",      "extensions" => "pdf"],
        "videos"    => ["title" => "Videos",    "extensions" => "avi,mp4"],
    ];

    // Attributes
    $max_file_size = "25mb";
    $attributes = "";
    $fileCountLimit = false;
    $replaceFile = false;
    $acceptedTypes = "";
    $name = "";
    $attributes = "";
    $maxNumberOfFiles = "";
    foreach ($options as $option => $tmp){
        if(strtolower($option) == "name" ) {
            $name = $tmp;
        }elseif(strtolower($option) == "max_file_size" ) {
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
        }else {
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
            <div class="row no-gutters">
                <div class="col-sm-10"><h5><i class="fa fa-file-o"></i> <?php echo $label; ?></h5></div>

            </div>
            <div id="files-<?php echo $objName; ?>" class="files" style="height:100px;overflow-y:auto;overflow-x:hidden;"></div>
            <div id="files-list-<?php echo $objName; ?>" class="files-list box-footer" style="display: none;height:18px;overflow-y:auto;overflow-x:hidden;margin-bottom:10px;"></div>

            <div class="box-tools files-box-tools" style="color:white;display:block;width:70%;margin:auto;padding:10px;background-color:#4472c4;border-radius: 8px;">
                <?php if($isEdit){ ?>
                    <a id="select-files-<?php echo $objName; ?>" class="btn btn-flat empatia file-upload-button">
                            <?php echo ONE::transSite('upload_file'); ?> <br>
                             <span style="margin-top:8px">(<?php echo ONE::transSite("drag_and_drop_files_to_here") ?>)</span>
                    </a>
                <?php } ?>
            </div>

        </div>
        <!-- Hidden input to store files in JSON format -->
        <input id="<?php echo $objName ?>" name="<?php echo !empty($name) ? $name : $objName; ?>" value='<?php echo (!empty($files) ? $files : ""); ?>'  <?php echo $attributes; ?> type="hidden" >
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
                filters: {  max_file_size: '<?php echo $max_file_size; ?>',
                            prevent_duplicates: true,
                            <?php echo $acceptedTypes;?>},
                flash_swf_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf');  ?>",
                silverlight_xap_url: "<?php echo asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap'); ?>",
                init: {
                    PostInit: function () { },
                    FilesAdded: function (up, files) {
                        <?php if ($fileCountLimit!=false && $replaceFile!=false) { ?>
                        oldFiles = JSON.parse($("#<?php echo $objName ?>").val());
                        if (oldFiles.length>=<?php echo $fileCountLimit;?>) {
                            removeUploadedFile_<?php echo $objName; ?>(oldFiles.shift()["id"]);
                            showFileUploadError_<?php echo $objName; ?>('<?php echo ONE::transSite('file_limit_reached_removed_oldest'); ?>');
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
                            // toastr.error('<?php echo trans('files.file_limit_reached'); ?>');
                            showFileUploadError_<?php echo $objName; ?>('files.file_limit_reached'); ?>');

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
                            '<div class="col-sm-10 col-12"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_'+objJson.result.id+'">' + file.name + '</span></div> ' +
                            '<div class="col-sm-2 col-12" style="text-align: right;"> ' +
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
                        // toastr["error"](err.message);
                        showFileUploadError_<?php echo $objName; ?>(err.message);
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
                '<h4 class="modal-title"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("editFile")); ?></h4>'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '</div>'+
                '<div class="modal-body">'+
                '<div class="form-group "><label for="name"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("editFile")); ?></label><input class="form-control" id="name_'+random+'_'+id+'" name="name" type="text" value=""></div>'+
                '<div class="form-group "><label for="description"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("description")); ?></label><input class="form-control" id="description_'+random+'_'+id+'" name="description" type="text" value=""></div>'+
                '</div>'+
                '<style>.modal-backdrop{z-index:1050;}</style>'+
                '<div class="modal-footer">'+
                '<button type="button" class="btn btn-default file-modal-close" data-dismiss="modal"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("files.cancel")); ?></button>'+
                '<button type="button" class="btn btn-success file-modal-success" id="confirm" onclick=saveFileDetails_<?php echo $objName; ?>('+id+',"'+random+'") ><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("files.save")); ?></button>'+
                '</div>'+
                '</div>'+
                '</div>';

            var editWindow = $('<div id="fileDetails-modal_'+random+'_'+id+'" class="modal fade">' + data + '</div>').modal();
            editWindow.on('hidden.bs.modal', function () {
                // do something…
                $('#fileDetails-modal_'+random+'_'+id).remove();
            });

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
                    '<div class="col-sm-10 col-12"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_' + jsonArray[i].id + '" >' + jsonArray[i].name + '</span></div> ' +
                    '<div class="col-sm-2 col-12" style="text-align: right;"> ' +
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

        function showFileUploadError_<?php echo $objName; ?>(err_message){
            var random = Math.floor(Math.random() * 99999999999999);
            <!-- Modal Dialog -->
            var data = '<div class="modal-dialog">' +
                '<div class="modal-content">'+
                '<div class="modal-header">'+
                '<h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo preg_replace( "/\r|\n/", "", ONE::transSite("error")); ?></h4>'+
                '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
                '</div>'+
                '<div class="modal-body">'+
                err_message+
                '<style>.modal-backdrop{z-index:1050;}</style>'+
                '</div>'+
                '</div>'+
                '</div>';
            var errorWindow = $('<div id="error-modal_'+random+'" class="modal fade">' + data + '</div>').modal();
            errorWindow.on('hidden.bs.modal', function () {
                // do something…
                $('#error-modal_'+random).remove();
            });
        }

        initFileUploadList_<?php echo $objName; ?>();
    </script>
    <?php
    $html .= ob_get_contents();
    ob_end_clean();

    return $html;
});