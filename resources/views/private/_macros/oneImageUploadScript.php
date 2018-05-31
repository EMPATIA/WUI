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
                                    var file = new Blob([blob], { type: blob.type });
                                    var up = window[$("#<?php echo $imageId; ?>").attr("uploader")];
                                    up.addFile(file);

                                    $("#<?php echo $imageId; ?>").attr("src", "");
                                    /* ... */
                                }
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