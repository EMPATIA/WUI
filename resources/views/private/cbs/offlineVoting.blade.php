@extends('private._private.index')

@section('content')
    @if(isset($parameter))
        @if(count($parameter->parameter_user_options)> 0)
            <div class="form-group">

                <label>{{trans("private.selectMunicipality")}}</label>
                <select class="form-control" id="municipality" name="{{$parameter->parameter_user_type_key}}" @if($parameter->mandatory) required @endif>
                    <option value="" selected>{{trans("private.selectOption")}}</option>
                    @foreach($parameter->parameter_user_options as $option)
                        <option value="{{$option->parameter_user_option_key}}">{{$option->name}}</option>
                    @endforeach
                </select>
                <a id="save-municipality" class="btn empatia" style="margin-top: 15px;">{{trans("private.ok")}}</a>
            </div>
        @endif
    @else
        <a id="set-for-voting" class="btn empatia">{{trans("private.set_for_voting")}}</a>
    @endif




@endsection

@section('scripts')
    <script>
        $(document).on('click', '#save-municipality', function () {
            $.ajax({
                method: 'GET', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@publicUserVotingRegistration',[$type,$cbKey,$voteKey])}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'municipality': $("#municipality").val()
                }, success: function () {
                    location.reload();
                }
            });
        });
        $(document).on('click', '#set-for-voting', function () {
            $.ajax({
                method: 'GET', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@publicUserVotingRegistration',[$type,$cbKey,$voteKey])}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    'setForVoting': true,
                }, success: function () {
                    location.reload();
                }
            });
        });

    </script>
@endsection