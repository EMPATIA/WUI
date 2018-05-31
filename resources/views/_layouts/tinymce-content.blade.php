<div class="box-header">
    <h4 class="box-title">{{ trans('pages.type') }}</h4>
    <select id="type_select" class="select" onchange="javascript:reloadTable()">
        @foreach($types as $value => $display)
            <option value="{{$value}}">{{ trans($display) }}</option>
        @endforeach
    </select>
</div>

<div>
    {!! ONE::fileUploadBox("drop-zone", trans('files.drop-zone'), trans('files.files'), 'select-files', 'files-list', 'files') !!}
</div>



<script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>
<script language="javascript">
    function addFileBrowserLink(link) {
        top.tinymce.activeEditor.windowManager.getParams().oninsert(link);
        top.tinymce.activeEditor.windowManager.close();
    }


    var uploader = new plupload.Uploader({
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },

        browse_button: 'select-files',
        drop_element: document.getElementById('drop-zone'),

        // General settings
        runtimes: 'html5,flash,silverlight,html4',
        url: "{{ action('FilesController@upload') }}",

        chunk_size: '1mb',

        multi_selection: false,

        // Flash settings
        flash_swf_url: "{{ asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.swf') }}",

        // Silverlight settings
        silverlight_xap_url: "{{ asset('vendor/jildertmiedema/laravel-plupload/js/Moxie.xap') }}",

        init: {
            PostInit: function () {
            },

            FilesAdded: function (up, files) {
                plupload.each(files, function (file) {
                    console.log(file.size);
                    $("#file-list").append("<div id='" + file.id + "' class='row'><div class='col-xs-6'>" + file.name + " (" + plupload.formatSize(file.size) + ")</div><div class='col-xs-6'><div class='progress' style='margin-bottom: 0px'><div class='progress-bar progress-bar-success' role='progressbar' aria-valuenow='0' aria-valuemin='0' aria-valuemax='100' style='width:0%'><span>0%</span></div></div></div></div>");
                });
                uploader.start();
                $("#file-list").slideDown();
            },

            UploadProgress: function (up, file) {
                var div = $("#file-list #" + file.id);
                div.find("span").html(file.percent + "%");
                div.find(".progress-bar").width(file.percent + "%");
                div.find(".progress-bar").attr("aria-valuenow", file.percent);
            },

            FileUploaded: function (up, file, obj) {
                try {
                    var response = JSON.parse(obj.response);
                    addFileBrowserLink(response.link);
                } catch (e) {
                }
            },

            Error: function (up, err) {
                var div = $("#file-list #" + err.file.id);
                div.find("span").html("{{ trans('files.error') }}: " + err.message);
                div.find(".progress-bar").width("100%");
                div.find(".progress-bar").attr("aria-valuenow", 100);
                div.find(".progress-bar").removeClass("progress-bar-success").addClass("progress-bar-danger");

                console.log("ERROR: " + err.code + ": " + err.message);
                console.log(up);
                console.log(err);
            }
        }
    });

    uploader.init();
</script>