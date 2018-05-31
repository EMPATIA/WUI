<div class="dashboard-scrollable">
    {{--{{ dd($collection) }}--}}
    <div class='dashboard-item-wrapper'>
        <div class='row'>
            <div class='col-12 col-md-3 col-lg-3 ellipis'><b>{{ trans('private.topic') }}</b></div>
            <div class='col-12 col-md-3 col-lg-3 ellipis'><b>{{ trans('private.created_by') }}</b></div>
            <div class='col-12 col-md-3 col-lg-3 ellipis'><b>{{ trans('private.content') }}</b></div>
            <div class='col-12 col-md-3 col-lg-3 text-right'></div>
        </div>
    </div>
    @foreach(!empty($collection) ? $collection :[] as $post)
    <div class='dashboard-item-wrapper'>
        <div class="row">
            <div class='col-12 col-md-3 col-lg-3 ellipis'><div class='dashboard-text ellipis'><a href='{{ action('PublicTopicController@show', [$arguments['cbKey'], $post->topic->topic_key,'type' => $arguments['padType']]) }}'>{{ $post->topic->title }}</a></div></div>
            <div class='col-12 col-md-3 col-lg-3 ellipis'><div class='dashboard-text ellipis'> {{ $post->created_by != 'anonymous' ? $usersKeysNames[$post->created_by] : trans('privateUser.anonymous')}}</div></div>
            <div class='col-12 col-md-3 col-lg-3 ellipis'><div class='dashboard-text ellipis'><a href='{{ action('PublicTopicController@show', [$arguments['cbKey'], $post->topic->topic_key,'type' => $arguments['padType']]) }}'>{{ $post->contents }}</a></div></div>
            <div class='col-12 col-md-3 col-lg-3 text-right'>
                @if($post->blocked)
                    <a href='{{ action('PostController@active', [$arguments['padType'], $arguments['cbKey'], $post->topic->topic_key,$post->post_key, 1, 'home']) }}' class='button btn-sm btn-danger'><i class='fa fa-thumbs-o-down' aria-hidden=true'></i></a>
                @else
                    <a href='{{ action('PostController@blocked', [$arguments['padType'], $arguments['cbKey'], $post->topic->topic_key,$post->post_key, 1, 'home']) }}' class='button btn-sm btn-danger'><i class='fa fa-thumbs-o-down' aria-hidden=true'></i></a>
                    @if($commentsNeedAuthorization)
                        <a href='{{ action('PostController@active', [$arguments['padType'], $arguments['cbKey'], $post->topic->topic_key,$post->post_key, 1, 'home']) }}' class='button btn-sm btn-success'><i class='fa fa-thumbs-o-up' aria-hidden=true'></i></a>
                    @endif
                @endif
            </div>
        </div>
    </div>
    @endforeach


    @if($collection->count() == 0)
        @include("private.dashBoardElements.sections._emptyListMessage")
    @endif
</div>

<div class="view_full_list">
    <div class="row">
        <div class='col-12'>
            <a href="{{ action('CbsController@showCbComments', [$arguments['padType'], $arguments['cbKey'] ?? $arguments['cbKey']]) }}" class="btn-seemore pull-right">{{ trans('private.view_full_list') }}</a>
        </div>
    </div>
</div>


<script>
    $('#updateStatusModal').on('show.bs.modal', function (event) {
        $('#updateStatus').off();
        $('#updateStatus').on('click', function (evt) {
            var allVals = {};
            var isValid = true;

            //get inputs to update status
            allVals['topicKey'] = $('#topicKeyStatus').val();
            $('#updateStatusModal input:text').each(function () {
                if($(this).val().length > 0){
                    allVals[$(this).attr('name')] = $(this).val();
                }
            });
            $('#updateStatusModal textarea').each(function () {
                if($(this).val().length > 0){
                    allVals[$(this).attr('name')] = $(this).val();
                }
            });

            //all values ok to update
            if (isValid) {
                $('#updateStatusModal input:text').each(function () {
                    $(this).val('');
                });
                $('#updateStatusModal textarea').each(function () {
                    $(this).val('');
                });

                allVals.type =  $('#type_hidden').val();
                allVals.cbKey = $('#cb_key_hidden').val();

                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('TopicController@updateStatusTopic')}}", // This is the url we gave in the route
                    data: allVals, // a JSON object to send back
                    success: function (response) { // What to do if we succeed

                        if (response != 'false') {
                            window.location.reload();
                            toastr.success('{{ trans('private.update_topic_status_ok') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        }else{
                            toastr.error('{{ trans('private.error_updating_state_or_sending_email_to_user') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        }

                        $('#updateStatusModal').modal('hide');
                    },
                    error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        $('#updateStatusModal').modal('hide');
                    }
                });
            }
        });
</script>