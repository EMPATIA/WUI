<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta content='width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no' name='viewport'>
    <link href="{{ url('/').elixir("css/esenfc_private.css") }}" rel="stylesheet" type="text/css"/>
    <link href='https://fonts.googleapis.com/css?family=PT+Sans:400,400italic,700,700italic' rel='stylesheet'
          type='text/css'>

    <script src="{{ url('/').elixir("js/esenfc.js") }}"></script>
    <script>
        // DataTable defaults
        $.extend(true, $.fn.dataTable.defaults, {
            language: {
                search: "<span class='pull-right'><button class='btn btn-default btn-sm btn-flat' type='button'><i class='fa fa-search'></i></button></span>",
                searchPlaceholder: "{{ trans('table.search') }}",
            },
            paging: true,
            lengthChange: true,
            searching: true,
            ordering: true,
            info: true,
            autoWidth: false,
            responsive: true,
            stateSave: true,
        });
    </script>
</head>

<body>
    <div id="tinymce_filebrowser_main" style="padding: 10px;"></div>
</body>

<script>
    $(document).ready(function() {
        var args = top.tinymce.activeEditor.windowManager.getParams();

        $.ajax({
            url: "{{ URL::action('PagesController@getTinyMCEView') }}" + '/' + args.type,
            success: function(data) {
                $("#tinymce_filebrowser_main").html(data);
            }
        });
    })
</script>