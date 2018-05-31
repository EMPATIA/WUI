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
                PostInit: function () {
                },
                FilesAdded: function (up, files) {
                    <?php if ($fileCountLimit!=false && $replaceFile!=false) { ?>
                    oldFiles = JSON.parse($("#<?php echo $objName ?>").val());
                    if (oldFiles.length>=<?php echo $fileCountLimit;?>) {
                        removeUploadedFile_<?php echo $objName; ?>(oldFiles.shift()["id"]);
                        showFileUploadError_<?php echo $objName; ?>('<?php echo ONE::transSite('upload_file_limit_reached_removed_oldest'); ?>');
                    }
                    <?php } else if ($fileCountLimit!=false && $replaceFile==false) { ?>
                    if ($("#<?php echo $objName; ?>").val()=="null" || $("#<?php echo $objName; ?>").val()=="" || JSON.parse($("#<?php echo $objName; ?>").val()).length<<?php echo $fileCountLimit;?>) {
                        <?php } ?>
                        plupload.each(files,
                            function (file) {
                                var html = "<div id='file_<?php echo $objName; ?>_" + file.id + "' class='row'><div class='col-xs-8 col-sm-8 col-8'><i style='cursor:pointer' class='text-danger' onclick=removeUploadedFile_<?php echo $objName; ?>('" + file.id + "') class='fa fa-times' aria-hidden='true'></i> " + file.name + " (" + plupload.formatSize(file.size) + ")</div><div id='file_progress_bar_<?php echo $objName; ?>_" + file.id + "' class='col-xs-4 col-sm-4 col-4 text-right' ><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span></div></div></div></div>"
                                $(".<?php echo $wrapper; ?>").append(html);
                            });
                        fileUploader_<?php echo $objName; ?>.start();
                        $("#files-list-<?php echo $objName; ?>").slideDown();
                        <?php if ($fileCountLimit!=false && $replaceFile==false) { ?>
                    } else {
                        // Adicionar toastr aqui
                        // toastr.error('<?php echo ONE::transSite('upload_file_limit_reached');?>');
                        showFileUploadError_<?php echo $objName; ?>('<?php echo ONE::transSite('upload_file_limit_reached');?>');

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
                    console.log("objecto", <?php echo $objName; ?>);
                    if($("#<?php echo $objName; ?>").val() == "null" || $("#<?php echo $objName; ?>").val() =="" ){
                        filesStr = "[]";
                    } else {
                        filesStr = $("#<?php echo $objName; ?>").val();
                    }

                    var jsonArray = JSON.parse( filesStr );
                    console.log("jebhseruifvbsuifbsufdu", jsonArray);
                    var jsonStringFile = '{"id": '+objJson.result.id+', "code": "'+objJson.result.code+'", "name": "'+file.name+'" , "type": "'+file.type+'" , "size": '+file.size+', "description": ""  }';
                    jsonArray.push( JSON.parse(jsonStringFile) );

                    var jsonString = JSON.stringify(jsonArray);

                    $("#<?php echo $objName; ?>").val(jsonString);

                    var jsonObj = JSON.parse(obj.response);
                    $("#"+file.id).attr("file_id",jsonObj.result.id);

                    var html = "<div class=''";
                    var html = "<div class='fu-file-wrapper'><div id='file_<?php echo $objName; ?>_" +objJson.result.id + "' file_id='" + objJson.result.id + "' class='files-row'>" +
                        '<div class="files-col-name"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_'+objJson.result.id+'">' + file.name + '</span></div> ' +
                        '<div class="files-col-buttons" style="text-align: right;"> ' +
                        <?php if($isEdit){ ?>
                        '<a class="files-btn white" href="javascript:getFileDetails_<?php echo $objName; ?>(' + objJson.result.id  + ')"><i class="fa fa-gear"></i></a> ' +
                        <?php } ?>
                        '<a class="files-btn white" href="<?php echo $downloadPath; ?>?id=' + objJson.result.id  + '&code=' + objJson.result.code  +'" target="_blank" ><i class="fa fa-download"></i></a> ' +
                        <?php if($isEdit){ ?>
                        '<a class="files-btn white" href="javascript:removeUploadedFile_<?php echo $objName; ?>('+objJson.result.id +')"><i class="fa fa-trash"></i></a> ' +
                        <?php } ?>
                        '</div> ' +
                        '</div> ' +
                        '</div> ';
                    $(".<?php echo $wrapper; ?>").append(html);
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
            '<h4 class="modal-title"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_edit_file")); ?></h4>'+
            '<button type="button" class="close" data-dismiss="modal" aria-hidden="true">&times;</button>'+
            '</div>'+
            '<div class="modal-body">'+
            '<div class="form-group "><label for="name"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_editFile")); ?></label><input class="form-control" id="name_'+random+'_'+id+'" name="name" type="text" value=""></div>'+
            '<div class="form-group "><label for="description"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_description")); ?></label><input class="form-control" id="description_'+random+'_'+id+'" name="description" type="text" value=""></div>'+
            '</div>'+
            '<style>.modal-backdrop{z-index:1050;}</style>'+
            '<div class="modal-footer">'+
            '<button type="button" class="btn btn-default file-modal-close" data-dismiss="modal"><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_cancel")); ?></button>'+
            '<button type="button" class="btn btn-success file-modal-success" id="confirm" onclick=saveFileDetails_<?php echo $objName; ?>('+id+',"'+random+'") ><?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_save")); ?></button>'+
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
            for (var i = 0; i < jsonArray.length; i++) {
                var file = jsonArray[i].name + " (" + plupload.formatSize(jsonArray[i].size) + ")";

                var html = "<div class='fu-file-wrapper'><div id='file_<?php echo $objName; ?>_" + jsonArray[i].id + "' file_id='" + jsonArray[i].id + "' class='row'>" +
                    '<div class="col-sm-6 col-12"><span class="fu-file-name" id="file_title_<?php echo $objName; ?>_' + jsonArray[i].id + '" >' + jsonArray[i].name + '</span></div> ' +
                    '<div class="col-sm-6 col-12" style="text-align: right;"> ' +
                    <?php if($isEdit){ ?>
                    '<a class="btn btn-sm file-btn-info" href="javascript:getFileDetails_<?php echo $objName; ?>(' + jsonArray[i].id + ')"><i class="fa fa-gear"></i></a> ' +
                    <?php } ?>
                    '<a class="btn btn-sm file-btn-success" href="<?php echo $downloadPath; ?>?id=' + jsonArray[i].id + '&code=' + jsonArray[i].code + '" target="_blank" ><i class="fa fa-download"></i></a> ' +
                    <?php if($isEdit){ ?>
                    '<a class="btn btn-sm file-btn-danger" href="javascript:removeUploadedFile_<?php echo $objName; ?>(' + jsonArray[i].id + ')"><i class="fa fa-trash"></i></a> ' +
                    <?php } ?>
                    '</div> ' +
                    '</div> ' +
                    '</div> ';

                $(".<?php echo $wrapper; ?>").append(html);
            }
        $("#files-list-<?php echo $objName; ?>").slideDown();
    }

    function showFileUploadError_<?php echo $objName; ?>(err_message){
        var random = Math.floor(Math.random() * 99999999999999);
        <!-- Modal Dialog -->
        var data = '<div class="modal-dialog">' +
            '<div class="modal-content">'+
            '<div class="modal-header">'+
            '<h4 class="modal-title"><i class="fa fa-exclamation-triangle" aria-hidden="true"></i> <?php echo preg_replace( "/\r|\n/", "", ONE::transSite("upload_error")); ?></h4>'+
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