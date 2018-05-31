@extends('public.empatia._layouts.index')

@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/empatia/cbs.css')}}">
@endsection

@section('content')


    <div class="container" style="padding-bottom: 50px">
        <div class="row menus-row">
            <div class="menus-line col-sm-6 col-sm-offset-3 mainTitleCb"><i class="fa fa-commenting"></i> {{$cb->title}}</div>
            <div style="clear:both;height:10px;"></div>
        </div>
        <div class="row">
            <div class="col-xs-12">
                <div class='cbContents'>{{$cb->contents}}</div>
            </div>
        </div>

        <div style="background-color: white;padding:10px 20px 10px 20px">

            @if($isModerator == 1 || (ONE::checkCBsOption($configurations, 'CREATE-TOPIC') && ONE::isAuth()) || ONE::checkCBsOption($configurations, 'CREATE-TOPICS-ANONYMOUS'))
                <div class="row">
                    <div class="col-xs-12">
                        <div style="margin:40px 0 50px 0;font-family: Open Sans">
                            <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}"
                               class="createCBBtn">{{ trans('PublicCbs.create') }}</a>
                        </div>
                    </div>
                </div>
            @endif

            @if(count($topics) > 0)
            <!-- Proposal Filter -->
                {{--<div style="padding-bottom: 20px;margin-left: 15px;display:none;">--}}
                {{--
                 @if(isset($parameters['category']))
                     <div class="btn-group" role="group">
                         <div class="dropdown">
                             <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                 <span id="dropdown-categories">Categories</span>
                                 <span class="caret"></span></button>
                             <ul class="dropdown-menu">
                                 <li><a href="javascript:showOptions('0', 'Categories', 'category')">Remove Filter</a></li>
                                 <li role="separator" class="divider"></li>
                                 @foreach($parameters['category']['options'] as $options)
                                     <li>
                                         <a href="javascript:showOptions('{{$options['id']}}', '{{$options['name']}}', 'category')">{{$options['name']}}</a>
                                     </li>
                                 @endforeach
                             </ul>
                         </div>
                     </div>
                 @endif

                 @if(isset($parameters['budget']))
                     <div class="btn-group" role="group">
                         <div class="dropdown">
                             <button class="btn btn-default dropdown-toggle" type="button" data-toggle="dropdown">
                                 <span id="dropdown-budget">Budget</span>
                                 <span class="caret"></span></button>
                             <ul class="dropdown-menu">
                                 <li><a href="javascript:showOptions('0', 'Budget', 'budget')">Remove Filter</a></li>
                                 <li role="separator" class="divider"></li>
                                 @foreach($parameters['budget']['options'] as $options)
                                     <li>
                                         <a href="javascript:showOptions('{{$options['id']}}', '{{$options['name']}}', 'budget')">{{$options['name']}}</a>
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
                 --}}

            <!--
                            <div class="col-xs-4 col-sm-3 col-md-2 pull-right hidden-xs">
                                <div class="btn-group">
                                    <button type='button' onclick="showMode('default')" id='show-default' class="btn btn-default active"><span class="glyphicon glyphicon-th"></span></button>
                                    <button type='button' onclick="showMode('list')" id='show-list' class="btn btn-default"><span class="glyphicon glyphicon-align-justify"></span></button>
                                </div>
                            </div>
                            -->

                {{--</div>--}}
            <!-- End Proposal Filter -->
                <!-- Proposals -->

                @if(!empty($voteType))
                    @foreach($voteType as $vt)
                        @if($vt['remainingVotes'])
                            {!! Html::oneVoteInfo($vt['remainingVotes'])!!}
                        @endif
                    @endforeach
                @endif

                {{--@if($existVotes == 1)--}}
                {{--<div class="col-xd-12" style="padding: 15px; margin: 20px 15px; background-color: #66A2D8; color: #FFF">--}}
                {{--Remaining total votes: <span id="info-total-votes">{{$remainingVotes->total}}</span>.<br/>--}}
                {{--You can use <span id="info-negative-votes">{{$remainingVotes->negative}}</span> negative votes.--}}
                {{--</div>--}}
                {{--@endif--}}





                <div class='clearfix'></div>
                <div class="row">
                    <?php $i = 1 ?>
                    @foreach ($topics as $topic)
                        <div class="col-xs-12 col-sm-6 col-md-4 idea" id="proposal_{{$topic->id}}">

                            <div class='topicsList'>
                            <!--
                                    <div style="padding: 5px; color: #adadad; font-size: 12px">{{ trans("PublicCbs.proposal") }} {{$i++}} {{ trans("PublicCbs.of") }} {{count($topics)}}</div>
                                    -->
                                <div class="row">
                                    <div class="col-xs-12">
                                        <div class="idea-title">
                                            <div style="margin-top:7px;font-size:20px;font-weight: 600;font-size: 16px; color:#62a351;text-transform: uppercase; padding-bottom: 5px;border-bottom: solid 1px #e9e4e4">
                                                <a class="titleCb" href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topic->topic_key, 'type' => $type] )  !!}">{{$topic->title}}</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-xs-12">
                                        <div class="idea-desc">
                                            {{$topic->contents}}
                                        </div>
                                    </div>
                                </div>
                            <!--
                                    <div style="padding: 10px; font-size: 10px ">
                                        <div class="row">
                                            @if(count($topic->parameters) > 1)
                                @foreach($topic->parameters as $parameter)
                                    {{--{{dd($topic->parameters)}}--}}

                                    @if($parameter->code != "image_map")
                                        <div class=" col-xs-offset-1  col-xs-10 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10 idea-location">
                                            <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">{{$parameter->parameter}}</div>
                                                            <span style="color: #62a351;font-weight:bold;">&#62;</span> {{str_replace(array('<br/>', '&', '"'), ' ', isset($categoriesNameById[$parameter->pivot->value]) ? $categoriesNameById[$parameter->pivot->value] : ''.$parameter->pivot->value)}}
                                                </div>
                                            @else
                                        <div class=" col-xs-offset-1  col-xs-10 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10 idea-location">
                                            <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">{{ trans('PublicCbs.location') }}</div>
                                                            <span style="color: #62a351;font-weight:bold;">&#62;</span> {{ONE::verifyEmpavilleGeoArea($parameter->pivot->value)}}
                                                </div>
                                            @endif
                                @endforeach
                            @else
                                @for($count = 0; $count < $parametersMaxCount; $count++)
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">&nbsp;</div>
                                                        <span style="color: #62a351;font-weight:bold;"></span>&nbsp;
                                                    </div>
                                                @endfor
                            @endif
                                    </div>
                                </div>
                                -->

                                <div style="margin-top:10px;padding: 5px 10px">
                                    <div class="row">
                                        <div class="col-xs-6 col-md-6" style="margin-bottom: 15px">
                                            <div class='textEllipsis'>
                                                <i class="demo-icon icon-clock-icon"></i>
                                                {{ substr($topic->created_at,0,10) }}
                                            </div>
                                            <div class='textEllipsis'>
                                                <i class="demo-icon  icon-author-icon"></i>
                                                {{ !empty($usersNames->{$topic->created_by}) ? $usersNames->{$topic->created_by}->name : $topic->created_by  }}
                                            </div>
                                            @if($topic->statistics->posts_counter > 1)
                                                <div class='textEllipsis' style="margin-top: 10px">
                                                    <i class="fa fa-comments-o" aria-hidden="true" style="margin-left: 2px; margin-right: 2px"></i>
                                                    {{ $topic->statistics->posts_counter }} {{ trans("PublicCbs.comments") }}
                                                </div>
                                            @else
                                                <div class='textEllipsis' style="margin-top: 10px"> <i class="fa fa-comments-o" aria-hidden="true" style="margin-left: 2px; margin-right: 2px"></i> {{ trans("PublicCbs.without_comments") }}
                                                </div>
                                            @endif
                                        </div>
                                        {{--Votes--}}
                                        <div class="@if(count($voteType)==1)col-md-6 col-sm-6 @elseif(count($voteType)>1) col-xs-12 @endif" style="margin-bottom: 15px">
                                            <div class="row">
                                                @if(!empty($voteType))
                                                    @foreach($voteType as $vt)
                                                        @if( isset($vt["genericConfigurations"]) && array_key_exists("vote_in_list", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["vote_in_list"] == 1 && $vt['existVotes'])
                                                            <div class="col-xs-12">
                                                                @if( $vt["method"] == "VOTE_METHOD_NEGATIVE")
                                                                    {!! Html::oneNegativeVoting($topic->topic_key,
                                                                                                $cb->cb_key,$vt["key"],
                                                                                                (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                                (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                                                !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                                $vt["configurations"]) !!}
                                                                @elseif( $vt["method"] == "VOTE_METHOD_MULTI" )
                                                                    {!! Html::oneMultiVoting($topic->topic_key,
                                                                                             $cb->cb_key,
                                                                                             $vt["key"],
                                                                                             (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                             !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null ,
                                                                                             $vt["configurations"]) !!}
                                                                @elseif( $vt["method"] == "VOTE_METHOD_LIKE" )
                                                                    {!! Html::oneLikes($topic->topic_key,
                                                                                       $cb->cb_key,
                                                                                       $vt["key"],
                                                                                       (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes",$vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["positive"] : '0' ): "",
                                                                                       (isset($vt["genericConfigurations"]) && array_key_exists("show_total_votes", $vt["genericConfigurations"]) && $vt["genericConfigurations"]["show_total_votes"] == 1) ? (isset($vt["totalVotes"][$topic->topic_key])? $vt["totalVotes"][$topic->topic_key]["negative"] : '0' ): "",
                                                                                       !empty($vt["allReadyVoted"][$topic->topic_key]) ? $vt["allReadyVoted"][$topic->topic_key] : null,
                                                                                       $vt["configurations"]) !!}
                                                                @endif
                                                            </div>
                                                        @endif
                                                    @endforeach
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <div class="row">
                                    <div class="col-xs-12 col-sm-12 col-md-12">
                                        <div class="readMoreTopicBox">
                                            <a href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topic->topic_key, 'type' => $type] )  !!}"
                                               class='readMoreTopic'>
                                                {{ trans("cbs.readMore") }}
                                            </a>
                                        </div>
                                    </div>
                                </div>

                            </div>
                        </div>


                @endforeach
                <!-- End Proposals -->
                </div>

            @else
                <div class='row'>
                    <div class="col-sm-8 col-md-12">
                        <div class="alert alert-warning">
                            <h4><i class="icon fa fa-warning"></i> {{ trans("PublicCbs.alert") }}!</h4>

                            <p>{{ trans("PublicCbs.noTopicsToDisplay") }}...</p>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>

@endsection


@section('scripts')

    <script>

        var categorySelect = 0;
        var budgetSelect = 0;
        var locationSelect = 0;

        function showOptions(id, selectedOption, typeDropDown) {

            var stringMenu = '{!! json_encode([]) !!}';
            var stringLocation = '{!! json_encode([]) !!}';


            var menus = JSON.parse(stringMenu);
            var location = JSON.parse(stringLocation);

            var showIds = [];



            $('.idea').hide();
            if(typeDropDown == 'category'){
                $("#dropdown-categories").html(selectedOption);
                categorySelect = id;
            }else if(typeDropDown == 'budget'){
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


    <!-- Dot Dot Dot -->
    <script>
        $.each([$(".idea-desc"), $(".idea-location"),$(".idea-title")], function( index, value ) {
            $(document).ready(function () {
                value.dotdotdot({
                    ellipsis: '... ',
                    wrap: 'word',
                    aft: null,
                });
            });
        });
    </script>

@endsection


