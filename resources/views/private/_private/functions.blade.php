<script>
    function updateClickListener() {

        $('#save_banner').click(function () {

            var $image = $("#banner_img");
            var $this = $(this);
            var data = $this.data();
            var $target;
            var result;
            if ($this.prop('disabled') || $this.hasClass('disabled')) {
                return;
            }
            if ($image.data('cropper') && data.method) {
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
                                        formData.append('file', blob, $("#banner_img").attr("title"));

                                        var file = new Blob([blob], { type: "image/jpeg" });

                                        var up = this[$("#banner_img").attr("uploader")];
                                        up.addFile(file);

                                        //console.log( this[$("#banner_img").attr("uploader")]);
                                       // var xhr = new XMLHttpRequest();
                                    // Add any event handlers here...
                                        //xhr.open('POST', '/upload/', true);
                                        //xhr.send(formData);

                                        $("#banner_img").attr("src", "");
                                        /* ... */
                                    },
                                    'image/jpeg'
                            );
                        }


    /*
                        if (result) {
                            result.toBlob(function (blob) {
                                var file = new File([blob], $("#banner_img").attr("title"));
                                var up = this[$("#banner_img").attr("uploader")];
                                up.addFile(file);
                                $("#banner_img").attr("src", "");
                            });

                        } */
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




    function imageFileUploaded(id, response, div, upload, type) {


        $.post('{{ URL::action('PublicUsersController@addPhoto')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    file_code: response.result.code
                },
                function (data) {

                })
                .done(function (result) {

                    if(result != 'false'){
                        $(div).attr('src' , result);
                        location.reload();

                    }
                })
                .fail(function (xhr, textStatus, errorThrown) {

                    //alert(xhr.responseText);
                })
                .always(function () {
                });
         }


    function userFileUploaded(id, response, div, upload, type) {


        $.post('{{ URL::action('PublicUsersController@fileUpload')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    file_code: response.result.code
                },
                function (data) {

                })
                .done(function (result) {
                    $('#file_id').val(result["id"]);
                    $('#file_name_uploaded').html("");
                    $('#file_name_uploaded').append(result['name']);
                })
                .fail(function (xhr, textStatus, errorThrown) {

                    //alert(xhr.responseText);
                })
                .always(function () {
                });
    }


    function getFilePostDetails(id) {

        $.post('{{ URL::action('PublicPostController@getFileDetails') }}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: id,
                    post_key: "{{ isset($post) ? $post->post_key : 0 }}",
                },
                function (data) {
                })
                .done(function (data) {
                    $('<div id="fileDetails-modal" class="modal fade">' + data + '</div>').modal();

                })
                .fail(function () {
                })
                .always(function () {
                });
 
    }


    function ideaFileUploaded(id, response, div, upload, type) {

        $.post('{{ URL::action('PublicPostController@addFile') }}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    file_code: response.result.code,
                    post_key: "{{ isset($post) ? $post->post_key : 0 }}",
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {

                })
                .done(function (result) {

                    updateFilesPostList(div, type);

                    $("#" + id).remove();

                    if (($("#"+upload).html()).length <= 0)
                        $("#"+upload).slideUp();

			@if (isset($post))
			$(div).trigger('files-updated',[response.result.id, '{{(isset($post) ? $post->post_key : 0) }}']);
			@endif
                })
                .fail(function (xhr, textStatus, errorThrown) {

                    console.log(xhr.responseText);
                });
    }



    function contentFileUploaded(id, response, div, upload, type) {


        $.post('{{ URL::action('ContentsController@addFile')}}',
            {
                _token: "{{ csrf_token() }}",
                file_id: response.result.id,
                content_key: "{{ isset($content) ? $content->content_key : 0 }}",
                name: response.result.name,
                type_id: type,
            },
            function (data) {
            })
            .done(function (result) {
                updateContentList(div, type);

                $("#" + id).remove();

                if (($("#"+upload).html()).length <= 0)
                    $("#"+upload).slideUp();
            })
            .fail(function (fail) {
            })
            .always(function () {
            });
    }

    function imageMapUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('CbsParametersController@addImageMap')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
//                    window.location = result;
                    $('#imageMapFile').attr("src","/files/"+response.result.id+"/"+response.result.code);
                    $('#file_id').val(result["id"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');
                })
                .fail(function () {
                })
                .always(function () {
                });
    }


    function imageQuestionOptionUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('QuestionOptionsController@addOptionImage')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    content_id: "{{ isset($questionoption) ? $questionoption->id : 0 }}",
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
                    result = JSON.parse(result);
                    $('#question_file_id').val(result["id"]);
                    $('#question_file_code').val(result["code"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');

                })
                .fail(function () {
                })
                .always(function () {
                });
    }



    function imageSponsorUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('ConferenceEventSponsorsController@addImageSponsor')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    content_id: "{{ isset($sponsor) ? $sponsor->sponsor_key : 0 }}",
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
                    $('#file_id').val(result["id"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');

                })
                .fail(function () {
                })
                .always(function () {
                });
    }


    function questIconUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('QuestIconsController@addIconImage')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    content_id: "{{ isset($icon) ? $icon->icon_key : 0 }}",
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
                    result = JSON.parse(result);
                    $('#file_id').val(result["id"]);
                    $('#file_code').val(result["code"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');

                })
                .fail(function () {
                })
                .always(function () {
                });
    }
    function homeConfigurationImageUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('HomePageConfigurationsController@addImage')}}',
                {
                    _token: "{{ csrf_token() }}",
                    file_id: response.result.id,
                    content_id: 0,
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
                    $('#imageLink').val(result["link"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');
                })
                .fail(function () {
                })
                .always(function () {
                });
    }




    function imageEventUploaded(id, response, div, upload, type) {
        $.post('{{ URL::action('ConferenceEventsController@addEventImage')}}',
                {
                    _token: "{{ csrf_token() }}",
                    fileId: response.result.id,
                    content_id: "{{ isset($sponsor) ? $sponsor->sponsor_key : 0 }}",
                    name: response.result.name,
                    type_id: type,
                },
                function (data) {
                })
                .done(function (result) {
                    $('#fileId').val(result["id"]);
                    $('#files_banner').html("");
                    $('#files_banner').append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');

                })
                .fail(function () {
                })
                .always(function () {
                });
    }


    function cbDeleteFile(id, div, type) {
        $.post('{{ URL::action('PublicPostController@deleteFile')}}',
            {
                _token: "{{ csrf_token() }}",
                file_id: id,
                post_key: "{{(isset($post) ? $post->post_key : 0) }}",
            },
            function (data) {
            })
            .done(function (result) {
		    updateFilesPostList(div,type);
		    $(div).trigger('files-updated',[id, '{{(isset($post) ? $post->post_key : 0) }}']);
            })
            .fail(function () {
            })
            .always(function () {
            });
    }

    function contentDeleteFile(id, div, type) {
        $.post('{{ URL::action('ContentsController@deleteFile')}}',
            {
                _token: "{{ csrf_token() }}",
                file_id: id,
                content_key: "{{ isset($content) ? $content->content_key : 0 }}",
            },
            function (data) {
            })
            .done(function (result) {

                updateContentList(div, type);
            })
            .fail(function () {
            })
            .always(function () {
            });
    }

    function getFileDetails(id) {
        window.location = '{{ URL::action('ContentsController@getFileDetails', isset($content) ? $content->content_key : 0) }}' + '/' + id;
    }

    function orderFile(id, movement, div, type) {
        $.post('{{ URL::action('ContentsController@orderFile')}}',
            {
                _token: "{{ csrf_token() }}",
                file_id: id,
                content_key: "{{ isset($content) ? $content->content_key : 0 }}",
                movement: movement,
                type_id: type,
            },
            function (data) {
            })
            .done(function (result) {

                updateContentList(div, type);
            })
            .fail(function () {
            })
            .always(function () {
            });
    }
    
   

    function updateContentList(divName, type) {
        $.get('{{ URL::action('ContentsController@getFiles', (isset($content) ? $content->content_key : 0))}}' + '/' + type)
                .done(function (result) {
                    var div = $(divName);
                    div.html("");
                    for (var i = 0; i < result.length; i++) {
                        div.append('<div class="row" style="padding-bottom: 10px; table-layout:fixed;"> ' +
                                '<div class="col-2" style="text-align: left; display:none"> ' +
                                '<a class="btn btn-flat btn-warning btn-sm' + (i == 0 ? " disabled" : "") + '" href="javascript:orderFile(' + result[i].id + ', 1, \'' + divName + '\', ' + type + ')"><i class="fa fa-arrow-up"></i></a> ' +
                                '<a class="btn btn-flat btn-warning btn-sm' + (i == result.length - 1 ? " disabled" : "") + '" href="javascript:orderFile(' + result[i].id + ', -1, \'' + divName + '\', ' + type + ')"><i class="fa fa-arrow-down"></i></a> ' +
                                '</div> ' +
                                //'<div class="col-10 col-sm-5">' + result[i].name + ' (' + plupload.formatSize(result[i].size) + ')' + '</div> ' +
                                '<div class="col-8 col-sm-8" style="white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">' + result[i].name + ' (' + plupload.formatSize(result[i].size) + ')' + '</div> ' +
                                //'<div class="col-6 col-sm-2">' + result[i].type + '</div> ' +
                                '<div class="col-4 col-sm-4" style="text-align: right;"> ' +
                                '<a class="btn btn-flat btn-info btn-sm" href="javascript:getFileDetails(' + result[i].id + ')"><i class="fa fa-eye"></i></a> ' +
                              //  '<a class="btn btn-flat btn-success btn-sm" href="' + "{{ env('DOWNLOAD_API', 'https://empatia-test.onesource.pt:5005/file/download/') }}" + result[i].id + '/' + result[i].code + ' "><i class="fa fa-download"></i></a> ' +
                                '<a class="btn btn-flat btn-danger btn-sm" href="javascript:contentDeleteFile(' + result[i].id + ', \'' + divName + '\', ' + type + ')"><i class="fa fa-trash"></i></a> ' +
                                '</div> ' +
                                '</div> ');
                    }
                })
                .fail(function () {
                })
                .always(function () {
                });
    }

    function updateFileSponsorList(divName) {
        $.get('{{ URL::action('ConferenceEventSponsorsController@getFile', (isset($sponsor) ? $sponsor->file_id : 0))}}')
                .done(function (result) {
                    var div = $(divName);
                    div.html("");
                    div.append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');
                })
                .fail(function () {
                })
                .always(function () {
                });
    }
    
    function updateFileEventList(divName) {
        $.get('{{ URL::action('ConferenceEventsController@getEventFile', (isset($event) ? $event->file_id : 0))}}')
                .done(function (result) {
                    var div = $(divName);
                    div.html("");
                    div.append('<div class="row"> ' +
                            '<div class="col-10 col-sm-5">' + result["name"] + ' (' + plupload.formatSize(result["size"]) + ')' + '</div> ' +
                            '<div class="col-6 col-sm-2">' + result["type"] + '</div> ' +
                            '<div class="col-6 col-sm-3" style="text-align: right;"> ' +
                            '</div> ' +
                            '</div> ');
                })
                .fail(function () {
                })
                .always(function () {
                });
    }


    function updateFilesPostList(divName, type) {
        $.get('{{ URL::action('PublicPostController@getFiles', (isset($post) ? $post->post_key : 0)) }}?type=' + type)
                .done(function (result) {

                    var div = $(divName);
                    div.html("");

                    for (var i = 0; i < result.length; i++) {
                        div.append('<div class="row" style="margin-bottom:5px;"> ' +
                                /*
                                '<div class="col-2" style="text-align: left;"> ' +
                                '<a class="btn btn-flat btn-warning btn-sm' + (i == 0 ? " disabled" : "") + '" href="javascript:orderFilePosts(' + result[i].file_id + ', 1, \'' + divName + '\', ' + type + ')"><i class="fa fa-arrow-up"></i></a> ' +
                                '<a class="btn btn-flat btn-warning btn-sm' + (i == result.length - 1 ? " disabled" : "") + '" href="javascript:orderFilePosts(' + result[i].file_id + ', -1, \'' + divName + '\', ' + type + ')"><i class="fa fa-arrow-down"></i></a> ' +
                                '</div>' +
                                */
                            '<div class="col-10 col-lg-8 col-md-7 col-xs-10">' + result[i].name + '</div> ' +
                            '<div class="col-2 col-lg-4 col-md-5 col-xs-2" style="text-align: right;"> ' +
                            '<a class="btn btn-flat btn-info btn-sm file-btn-info" href="javascript:getFilePostDetails(' + result[i].file_id + ')"><i class="fa fa-gear"></i></a> ' +
                            '<a class="btn btn-flat btn-success btn-sm file-btn-success" href="' + "{{ env('DOWNLOAD_API', '/files/') }}" + result[i].file_id + '/' + result[i].file_code + ' "><i class="fa fa-download"></i></a> ' +
                            '<a class="btn btn-flat btn-danger btn-sm file-btn-danger" href="javascript:cbDeleteFile(' + result[i].file_id + ', \'' + divName + '\', ' + type + ')"><i class="fa fa-trash"></i></a> ' +
                            '</div> ' +
                            '</div> ');
                    }
                })
                .fail(function (xhr, textStatus, errorThrown) {
                    console.log(xhr.responseText);
                })
                .always(function () {
                });
    }

    function orderFilePosts(id, movement, div, type) {
        $.post('{{ URL::action('PublicPostController@orderFile')}}',
            {
                _token: "{{ csrf_token() }}",
                file_id: id,
                post_key: "{{ isset($post) ? $post->post_key : 0 }}",
                movement: movement,
                type_id: type,
            },
            function (data) {
            })
            .done(function (result) {
                console.log(result);
                updateFilesPostList(div,type);
            })
            .fail(function () {
            })
            .always(function () {
            });
    }
</script>
