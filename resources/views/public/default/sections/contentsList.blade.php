<div id="contents">

</div>
<script>
    $(document).ready(function () {
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('PublicContentManagerController@getLastOf')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'type': "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','contentType')->first()->value ?? 'news'}}",
                'count' : "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','numberOfTopics')->first()->value ?? '3'}}"
            }, beforeSend: function () {
                $("#contents").append('<div class="col-md-12 col-xs-12 loader"><div class="text-center"><img src="{{ asset('images/default/bluePreLoader.gif') }}" alt="Loading"  style="width: 40px;"/></div></div>');
                $(".loader").show();
            }, success: function (response) { // What to do if we succeed
                $("#contents").html(response);
                $("#recent").find('.loader').remove();
            }
        });

    });

</script>
