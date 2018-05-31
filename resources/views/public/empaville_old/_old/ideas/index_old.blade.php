@extends('public._layouts.index')
<link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">


@section('content')
    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid box-deflaut">
                <div class="box-header " style="color: white; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-commenting"></i> {{$cbTitle}}</h3>

                    @if($isModerator == 1)
                        <div style="float:right">
                            <a href="{!! action('PublicIdeasController@create', $cbId) !!}"
                               class="btn btn-flat empatia">{{ trans('proposal.create') }}</a>
                        </div>
                    @endif
                </div>
                <div class="box-body" style="margin-top: 10px;background-color: white">
                    <div style="padding-bottom: 10px; text-align: center;">
                        @if(count($ideas) > 0)
                            @if(isset($parameters['Categories']))
                                <div class="btn-group" role="group">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span id="dropdown-categories">Categories</span>
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:showOptions('0', 'Categories', 'Categories')">Remove Filter</a></li>
                                            <li role="separator" class="divider"></li>
                                            @foreach($parameters['Categories']['options'] as $options)
                                                <li>
                                                    <a href="javascript:showOptions('{{$options['id']}}', '{{$options['name']}}', 'Categories')">{{$options['name']}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif

                            @if(isset($parameters['Budget']))
                                <div class="btn-group" role="group">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span id="dropdown-budget">Budget</span>
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:showOptions('0', 'Budget', 'Budget')">Remove Filter</a></li>
                                            <li role="separator" class="divider"></li>
                                            @foreach($parameters['Budget']['options'] as $options)
                                                <li>
                                                    <a href="javascript:showOptions('{{$options['id']}}', '{{$options['name']}}', 'Budget')">{{$options['name']}}</a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            @endif


                            @if(count($ideasLocation) > 0)
                                <div class="btn-group" role="group">
                                    <div class="dropdown">
                                        <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                            <span id="dropdown-location">Location</span>
                                            <span class="caret"></span></button>
                                        <ul class="dropdown-menu">
                                            <li><a href="javascript:showOptions('0', 'Location', 'Location')">Remove Filter</a></li>
                                            <li role="separator" class="divider"></li>
                                            <li><a href="javascript:showOptions('1', 'UpTown', 'Location')">UpTown</a></li>
                                            <li><a href="javascript:showOptions('2', 'MiddleTown', 'Location')">MiddleTown</a></li>
                                            <li><a href="javascript:showOptions('3', 'DownTown', 'Location')">DownTown</a></li>
                                        </ul>
                                    </div>
                                </div>
                            @endif
                            @if(count($ideas) > 0)
                                <div style="float: right">
                                    <div class="btn-group">
                                        <button type='button' onclick="showMode('default')" id='show-default' class="btn btn-default active"><span class="glyphicon glyphicon-th"></span></button>
                                        <button type='button' onclick="showMode('list')" id='show-list' class="btn btn-default"><span class="glyphicon glyphicon-align-justify"></span></button>
                                    </div>
                                </div>
                            @endif
                        </div>

                        @foreach ($ideas as $idea)

                            <div class="col-sm-4 col-md-4 idea" id="proposal_{{$idea->id}}">
                                @if($idea->created_by == Session::get('user')->user_key)
                                    <div class="box" style="margin-bottom: 10px;border-left: 1px solid #f5f5f5;border-right: 1px solid #f5f5f5; border-bottom: 1px solid #f5f5f5;border-top-color: red;">
                                @else
                                    <div class="box" style="margin-bottom: 10px;border-left: 1px solid #f5f5f5;border-right: 1px solid #f5f5f5; border-bottom: 1px solid #f5f5f5;border-top-color: #737373;">
                                @endif
                                        <div class="box-header" style="height: 35px; width: 100%; ">
                                            <h3 class="box-title" data-toggle="tooltip" data-placement="top" data-original-title="{{$idea->title}}" style="overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 100%; color: #62a351;">
                                                <a style="font-weight: 600;font-size: 15px; color:#62a351" href="{!! action('PublicIdeasMessageController@index', $idea->id ) !!}">{{$idea->title}}</a>
                                            </h3>
                                        </div>
                                        <div class="box-body no-padding" style="min-height: 100px; position: relative;">
                                            <div style="height: 100%; position: absolute; width: 100%; font-size: 12px;">
                                                <div style="position: absolute; height: 60%; text-overflow: ellipsis; overflow: hidden;padding: 10px;  width: 100%;">
                                                    {{$idea->contents}}
                                                </div>
                                                <div style="position: absolute; bottom: 0;padding-left: 10px; font-size: 11px;">
                                                    <i class="fa fa-clock-o margin-r-5" style="color: #999;"></i>{{substr($idea->created_at, 0, 10)}}
                                                        <br>Created by <i><a href="{{ action('PublicUsersController@show', $idea->created_by) }}">{{$usersNames[$idea->created_by]['name']}}</a></i>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="box-footer" style=" color:black; border-top: 0px; padding-bottom: 2px;padding-top: 15px;">

                                            @if(($idea->statistics->posts_counter - 1) == 0)
                                                <small style="font-size: 11px;"><i class="fa fa-comments-o margin-r-5" style="color: #999;"></i>Without
                                                    comments
                                                </small>
                                            @else
                                                <small style="font-size: 11px;"><i class="fa fa-comments-o margin-r-5" style="color: #999;"></i>Comments({{($idea->statistics->posts_counter - 1)}})
                                                </small>
                                            @endif
                                            <div class="col-md-12" style=" margin-top: 10px; height: 20px;">
                                                <div class="col-md-8">
                                                    <small>
                                                        @foreach($idea->parameters as $parameter)
                                                            @if($parameter->parameter == 'Categories')
                                                                <span class="badge badge-secondary" style="padding: 5px">Category : {{$categoriesNameById[$parameter->pivot->value]}}</span>
                                                            @endif
                                                                @if($parameter->parameter == 'Budget')
                                                                 <span class="badge badge-secondary" style="padding: 5px">Cost : {{$categoriesNameById[$parameter->pivot->value]}}</span>
                                                                @endif
                                                        @endforeach
                                                    </small>
                                                </div>
                                                @if($existVotes == 1)
                                                    <div class="col-md-4">
                                                        <div data-toggle="tooltip" data-placement="top" data-original-title="Remaining total votes: {{$remainingVotes->total}}. You can use {{$remainingVotes->negative}} negative votes.">
                                                            <i class="fa fa-plus" style="font-size: 1.2em; <?php echo (array_key_exists($idea->id, $allReadyVoted)) ? ($allReadyVoted[$idea->id] == 1 ? 'color:green' : 'color: #777') : 'color: #777;'?>"></i>&nbsp;&nbsp;<i class="fa fa-minus" style="font-size: 1.2em; <?php echo (array_key_exists($idea->id, $allReadyVoted)) ? ($allReadyVoted[$idea->id] == -1 ? 'color:red' : 'color: #777') : 'color: #777'?>"></i>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        @else
                            <div class="col-sm-8 col-md-12">
                                <div class="alert alert-warning">
                                    <h4><i class="icon fa fa-warning"></i> Alert!</h4>

                                    <p>No topics to display...</p>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection


@section('scripts')

<script>

    var categorySelect = 0;
    var budgetSelect = 0;
    var locationSelect = 0;


    function showOptions(id, selectedOption, typeDropDown) {

        var stringMenu = '{!! json_encode($ideasMenu) !!}';
        var stringLocation = '{!! json_encode($ideasLocation) !!}';


        var menus = JSON.parse(stringMenu);
        var location = JSON.parse(stringLocation);

        var showIds = [];



        $('.idea').hide();

        if(typeDropDown == 'Categories'){
            $("#dropdown-categories").html(selectedOption);
            categorySelect = id;
        }else if(typeDropDown == 'Budget'){
            $("#dropdown-budget").html(selectedOption);
            budgetSelect = id;
        }else if(typeDropDown == 'Location'){
            $("#dropdown-location").html(selectedOption);
            locationSelect = id;
        }



        if(categorySelect != 0){
            var idsProposals = menus[categorySelect].split(",");


            for(var i=0; i< idsProposals.length; i++){
                showIds.push(idsProposals[i]);
            }
        }

        if(budgetSelect != 0){
            var idsProposals = menus[budgetSelect].split(",");

            if(showIds.length == 0){

                for(var i=0; i< idsProposals.length; i++){
                    showIds.push(idsProposals[i]);
                }
            }
            else{
                var tempIds = showIds;
                showIds = [];

                for(var i=0; i< idsProposals.length; i++){
                    var tempId = idsProposals[i];
                    if(tempId in tempIds){
                        showIds.push(tempId);
                    }
                }
            }
        }




        if(locationSelect != 0){
            var locationSelectName = '';
            if(locationSelect == 1){
                locationSelectName = 'UpTown';
            }else if(locationSelect == 2){
                locationSelectName = 'MiddleTown';
            }else if(locationSelect == 3){
                locationSelectName = 'DownTown';
            }


            var idsProposals = location[locationSelectName].split(",");

            if(showIds.length == 0){

                for(var i=0; i< idsProposals.length; i++){
                    showIds.push(idsProposals[i]);
                }
            }
            else{
                var tempIds = showIds;
                showIds = [];

                for(var i=0; i< idsProposals.length; i++){
                    var tempId = idsProposals[i];
                    if(tempId in tempIds){
                        showIds.push(tempId);
                    }
                }
            }
        }

        if(showIds.length > 0){
            for (var i = 0; i < showIds.length; i++) {
                $('#proposal_' + showIds[i]).show();
            }
        }else{
            if(locationSelect == 0 && budgetSelect == 0 && categorySelect == 0){
                $('.idea').show();
            }
        }


    }

    function showMode(type){

        if(type == 'list'){
            $('#show-list').addClass('active');
            $('#show-default').removeClass('active');


            $('.idea').removeClass('col-sm-4');
            $('.idea').removeClass('col-md-4');
        }else{
            $('#show-default').addClass('active');
            $('#show-list').removeClass('active');

            $('.idea').addClass('col-sm-4');
            $('.idea').addClass('col-md-4');
        }
    }

</script>

@endsection


