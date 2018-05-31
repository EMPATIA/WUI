<div class="row pad-2">
    <div class="col-12" id="topics">

    </div>

</div>
<script>
    $(document).ready(function () {
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('PublicCbsController@getCbTopicsList')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                'cb_key': "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','cbKey')->first()->value ?? Session::get("SITE-CONFIGURATION.ideation_key","")}}",
                'type': "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','cbType')->first()->value ?? 'idea'}}",
                'sort_order': "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','topicsSortOrder')->first()->value ?? 'order_by_recent'}}",
                'topics_to_show': "{{ collect($section->section_parameters)->where('section_type_parameter.code','=','numberOfTopics')->first()->value ?? '6'}}",
                'no_loop': false

            }, beforeSend: function () {
                $("#topics").append('<div class="col-md-12 col-xs-12 loader"><div class="text-center"><img src="{{ asset('images/bipart/bluePreLoader.gif') }}" alt="Loading"  style="width: 40px;"/></div></div>');
            }, success: function (response) { // What to do if we succeed
                $("#topics").html(response);
            }
        });

    });
</script>