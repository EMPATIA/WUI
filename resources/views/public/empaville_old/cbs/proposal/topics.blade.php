@extends('public.empaville._layouts.index')
<link rel="stylesheet" href="{{ asset('css/sweetalert.css')}}">


@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="box box-solid box-deflaut">
                <div class="box-header " style="color: white; background-color: #333333;">
                    <h3 style="padding-top: 5px;display: inline-block;margin: 0;line-height: 1;"><i class="fa fa-commenting"></i> {{$cb->title}}</h3>

                    @if($isModerator == 1)
                        <div style="float:right">
                            <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}"
                               class="btn btn-flat empatia">{{ trans('proposal.create') }}</a>
                        </div>
                    @endif
                </div>
                <div class="box-body" style="margin-top: 10px;background-color: white">
                @if(count($topics) > 0)
                    <!-- Proposal Filter -->

                        <div style="padding-bottom: 20px;margin-left: 15px;">
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

                            <div class="hidden-xs" style="float: right">
                                <div class="btn-group">
                                    <button type='button' onclick="showMode('default')" id='show-default' class="btn btn-default active"><span class="glyphicon glyphicon-th"></span></button>
                                    <button type='button' onclick="showMode('list')" id='show-list' class="btn btn-default"><span class="glyphicon glyphicon-align-justify"></span></button>
                                </div>
                            </div>
                        </div>
                        <!-- End Proposal Filter -->
                        <!-- Proposals -->


                        @if(!empty($voteType))
                            @foreach($voteType as $vt)
                                @if($vt['remainingVotes'])
                                    {!! Html::oneVoteInfo($vt['remainingVotes'])!!}
                                @endif
                            @endforeach
                        @endif
                        <?php $i = 1 ?>
                        @foreach ($topics as $topic)
                            <div class="col-sm-6 col-md-4 idea" id="proposal_{{$topic->id}}">

                                <div style="margin-bottom: 10px;border-left: 1px solid #f5f5f5; border-top: 3px solid #737373; box-shadow: 4px 4px 5px #d2d6de;">
                                    <div style="padding: 5px; color: #adadad; font-size: 12px">Proposal {{$i++}} of {{count($topics)}}</div>

                                    <div style="height: 26px; width: 100%; position: relative;">
                                        <h3 style="margin: 5px 0px 0px 10px;overflow: hidden; white-space: nowrap; text-overflow: ellipsis; width: 95%; color: #62a351;">
                                            <a style="font-weight: 600;font-size: 16px; color:#62a351; text-transform: uppercase" href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topic->topic_key, 'type' => $type] )  !!}">{{$topic->title}}</a>
                                        </h3>
                                    </div>
                                    <div style="padding: 0px 10px 10px 10px; margin-bottom: 10px; height: 40px; text-overflow: ellipsis;  word-wrap: break-word;  overflow: hidden;  ">
                                        <div style=" display: -webkit-box; -webkit-line-clamp: 2;-webkit-box-orient: vertical;  ">
                                            {{$topic->contents}}
                                        </div>
                                    </div>
                                    <div style="padding: 10px; font-size: 10px ">
                                        <div class="row">
                                            @if(count($topic->parameters) > 1)
                                                @foreach($topic->parameters as $parameter)
                                                    @if($parameter->code != "image_map")
                                                        <div class=" col-xs-offset-1  col-xs-10 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10">
                                                            <div style="text-transform: uppercase; color:#62a351; font-weight:bold; font-size: 12px">{{$parameter->parameter}}</div>
                                                            <span style="color: #62a351;font-weight:bold;">&#62;</span> {{str_replace(array('<br/>', '&', '"'), ' ',isset($categoriesNameById[$parameter->pivot->value]) ? $categoriesNameById[$parameter->pivot->value] : ''.$parameter->pivot->value)}}
                                                        </div>
                                                    @else
                                                        <div class=" col-xs-offset-1  col-xs-10 col-sm-offset-1 col-sm-10 col-md-offset-1 col-md-10">
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
                                    @if($existVotes == 1)
                                        <hr style="width: 80%; margin-top: 5px; margin-bottom: 15px">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12" style="margin-top: 0px; margin-bottom: 15px; text-align: center" >
                                                <a class="btn btn-flat" onclick="voteProposal('{{$topic->id}}', 1)" id="plus_button_{{$topic->id}}" style="<?php echo (array_key_exists($topic->id, $allReadyVoted)) ? ($allReadyVoted[$topic->id] == 1 ? 'background-color: #8cc542;' : 'border: 1px solid #8cc542') : 'border: 1px solid #8cc542;'?>"><i id="vote_plus_{{$topic->id}}" style="<?php echo (array_key_exists($topic->id, $allReadyVoted)) ? ($allReadyVoted[$topic->id] == 1 ? 'color:FFF' : 'color: #8cc542') : 'color: #8cc542'?>" class="fa fa-plus"></i></a>
                                                <a class="btn btn-flat" onclick="voteProposal('{{$topic->id}}', -1)" id="minus_button_{{$topic->id}}" style="<?php echo (array_key_exists($topic->id, $allReadyVoted)) ? ($allReadyVoted[$topic->id] == -1 ? 'background-color: #f74553;' : 'border: 1px solid #f74553') : 'border: 1px solid #f74553;'?>"><i id="vote_minus_{{$topic->id}}" style="<?php echo (array_key_exists($topic->id, $allReadyVoted)) ? ($allReadyVoted[$topic->id] == -1 ? 'color:FFF' : 'color: #f74553') : 'color: #f74553'?>" class="fa fa-minus"></i></a>
                                            </div>
                                        </div>
                                    @endif


                                    <div style="border-top: 0px solid #f4f4f4;padding: 10px; text-align: center; background-color: #f4f4f4">
                                        <div class="row">
                                            <div class="col-xs-12 col-sm-12 col-md-12" >
                                                <a  href="{!! action('PublicTopicController@show', ['cbKey' => $cbKey,'topicKey' => $topic->topic_key, 'type' => $type] )  !!}" style="font-weight: 600;font-size: 14px; text-transform: uppercase; cursor: pointer">Read MORE</a>
                                            </div>
                                        </div>
                                    </div>

                                </div>
                            </div>
                        @endforeach
                    <!-- End Proposals -->
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




        function voteProposal(topicId, value) {

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: '/public/ideas/message/post/vote', // This is the url we gave in the route
                data: {
                    id: topicId,
                    value: value,
                    voteKey: '{{$voteKey}}',
                    _token: '{{ csrf_token() }}'
                }, // a JSON object to send back
                success: function (responseVOte) { // What to do if we succeed

                    toastr.options = {
                        "closeButton": true,
                        "debug": false,
                        "newestOnTop": false,
                        "progressBar": false,
                        "positionClass": "toast-bottom-right",
                        "preventDuplicates": true,
                        "onclick": null,
                        "showDuration": "300",
                        "hideDuration": "1000",
                        "timeOut": "4000",
                        "extendedTimeOut": "1000",
                        "showEasing": "swing",
                        "hideEasing": "linear",
                        "showMethod": "fadeIn",
                        "hideMethod": "fadeOut"
                    };


                    if (responseVOte.indexOf("error") == -1) {
                        var response = JSON.parse(responseVOte);
                        var vote = response['vote'];


                        if (vote == '-1') {
                            $("#plus_button_"+topicId).css('border', '1px solid #8cc542');
                            $("#plus_button_"+topicId).css('background-color', '');
                            $("#vote_plus_"+topicId).css("color", "#8cc542");


                            $("#minus_button_"+topicId).css('background-color', '#f74553');
                            $("#vote_minus_"+topicId).css("color", "white");

                        } else if (vote == 1) {

                            $("#minus_button_"+topicId).css('border', '1px solid #f74553');
                            $("#minus_button_"+topicId).css('background-color', '');
                            $("#vote_minus").css("color", "#f74553");

                            $("#plus_button_"+topicId).css('background-color', '#8cc542');
                            $("#vote_plus_"+topicId).css("color", "white");

                        } else if (vote == '0') {
                            $("#plus_button_"+topicId).css('border', '1px solid #8cc542');
                            $("#plus_button_"+topicId).css('background-color', '');
                            $("#vote_plus_"+topicId).css("color", "#8cc542");

                            $("#minus_button_"+topicId).css('border', '1px solid #f74553');
                            $("#minus_button_"+topicId).css('background-color', '');

                            $("#vote_minus_"+topicId).css("color", "#f74553");
                        }



                        $("#info-total-votes").html(response['total']);
                        $("#info-negative-votes").html(response['negative']);
                        toastr.info("Remaining total votes: " + response['total'] + ". You can use " + response['negative'] + " negative votes.");
                    }else{
                        var response = JSON.parse(responseVOte);
                        var message = response['error'];

                        toastr.error(message);
                    }
                },
                error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail

                    console.log(JSON.stringify(jqXHR));
                    console.log("AJAX error: " + textStatus + ' : ' + errorThrown);
                }
            });

        }

    </script>

@endsection


