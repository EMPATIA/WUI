@extends('private._private.index')

@section('content')


    <div class="row">
        <div class="col-md-9">
            @php $form = ONE::form('vote')
                    ->settings(["model" => isset($voteEvent) ? $voteEvent : null,'id'=>isset($voteEvent) ? $voteEvent->voteKey : null])
                    ->show('IdeaVoteController@edit', 'IdeaVoteController@delete', ['cbId' => $cbId,'voteKey' => isset($voteEvent) ? $voteEvent->voteKey : null], 'IdeasController@show', ['id' => isset($cbId) ? $cbId : null])
                    ->create('IdeaVoteController@store', 'IdeasController@show', ['cbId' => $cbId, 'voteKey' => isset($voteEvent) ? $voteEvent->voteKey : null])
                    ->edit('IdeaVoteController@update', 'IdeaVoteController@show', ['cbId' => $cbId, 'voteKey' => isset($voteEvent) ? $voteEvent->voteKey : null])
                    ->open();
            @endphp



            @if((ONE::actionType('vote') == 'show') || (ONE::actionType('vote') == 'edit'))
                {!! Form::hidden('cbId', isset($cbId) ? $cbId : 0, ['id' => 'cbId']) !!}
                {!! Form::hidden('voteKey', isset($voteEvent) ? $voteEvent->voteKey : 0, ['id' => 'voteKey']) !!}
                {!! Form::oneText('voteKey', trans('form.voteKey'), isset($voteEvent) ? $voteEvent->voteKey : null, ['class' => 'form-control', 'id' => 'voteKey', 'required' => 'required','readonly'=>'readonly']) !!}
                {!! Form::oneText('method', trans('form.method'), isset($voteEvent) ? $voteEvent->methodName : null, ['class' => 'form-control', 'id' => 'method','readonly' => 'readonly']) !!}
                {!! Form::oneText('description', trans('form.description'), isset($voteEvent) ? $voteEvent->methodDescription : null, ['class' => 'form-control', 'id' => 'description','readonly'=>'readonly']) !!}
                {!! Form::oneDate('startDate', trans('form.startDate'), isset($voteEvent) ? substr($voteEvent->startDate, 0, 10) : null, ['id' => 'startDate', 'readonly' => isset($voteEvent)?($voteEvent->startDate < date('Y-m-d') ? 'readonly' : null):null]) !!}
                {!! Form::oneDate('endDate', trans('form.endDate'), isset($voteEvent) ? substr($voteEvent->endDate, 0, 10) : null, ['id' => 'endDate']) !!}



                {!! $html !!}

            @elseif(ONE::actionType('vote') == 'create')


                <div class="box-body btn-group-vertical">
                    <select class="form-control" id="methodGroupSelect" name="methodGroupSelect" onchange="showMethods()" required>
                        <option value="-1">{{ trans("privateVotes.select_vote_type")}}</option>
                        @foreach($methodGroup as $group)
                            <option value="{{$group->method_group_id}}">{{$group->name}}</option>
                        @endforeach
                    </select>

                    <div id="methodsDiv">

                    </div>
                    {!! Form::oneDate('startDate', trans('form.startDate'), isset($voteEvent) ? substr($voteEvent->startDate, 0, 10) : null, ['id' => 'startDate']) !!}
                    {!! Form::oneDate('endDate', trans('form.endDate'), isset($voteEvent) ? substr($voteEvent->endDate, 0, 10) : null, ['id' => 'endDate']) !!}


                    <div class="form-group" id="configurationsDiv" >

                    </div>
                </div>
            @endif

            {!! $form->make() !!}
        </div>

    </div>
@endsection
@if(ONE::actionType('vote') == 'create')
@section('scripts')

    <script>
        function showMethods() {
            var idMethodGroup = $('#methodGroupSelect').val();

            if (idMethodGroup == -1) {
                $('#methodsDiv').html("");
                $('#configurationsDiv').html("");
                return;
            }
            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '', // This is the url we gave in the route
                data: {postId: idMethodGroup, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#methodsDiv").html(response);

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });


        }

        function getMethodConfigurations(){

            var idMethod = $('#methodSelect').val();

            if (idMethod == -1) {
                $('#configurationsDiv').html("");
                return;
            }

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '{{action('IdeaVoteController@getMethodConfigurations')}}', // This is the url we gave in the route
                data: {postId: idMethod, _token: "{{ csrf_token() }}"}, // a JSON object to send back
                success: function (response) { // What to do if we succeed
                    console.log(response);
                    $("#configurationsDiv").html(response);

                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }

            });

        }
    </script>
@endsection
@endif