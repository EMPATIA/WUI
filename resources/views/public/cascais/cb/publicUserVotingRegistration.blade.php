<?php
$homeContentSections = \App\Http\Controllers\PublicContentManagerController::getSections("inVotePerson");

$background = collect($homeContentSections)->where('code','=','backgroundImage')->first();
    if (!empty($background) && count($background->section_parameters)>0){
        $backgroundImage = collect($background->section_parameters)->where("section_type_parameter.code","=","imagesSingleSection")->first()->value ?? "";
        $backgroundImage = json_decode($backgroundImage);
    }

?>

<style>
    /* body{
        overflow:hidden;
    } */
    .jscroll-inner{
        display: flex;
        flex-wrap: wrap;
    }

    /* width */
    ::-webkit-scrollbar {
        width: 10px;
    }

    /* Track */
    ::-webkit-scrollbar-track {
        background: #f1f1f1; 
    }
    
    /* Handle */
    ::-webkit-scrollbar-thumb {
        background: #888; 
    }

    /* Handle on hover */
    ::-webkit-scrollbar-thumb:hover {
        background: #555; 
    }
    

</style>

@extends('public.default._layouts.indexInPersonVoting')

@section('content')                                 


    <div class="row inperson-row">
        <div class="col-12 col-sm-11 col-md-10 col-lg-9">
            <div class="row">
                <div class="col-12 col-sm-6 inperson-title">
                    {{ONE::transCb('cb_in_person_vote_registration_title', !empty($cb) ? $cb->cb_key : $cbKey)}}
                </div>
                
                <input type="hidden" id="current-user-key" value="">
                <input type="hidden" id="max-votes-allowed" value="{{ $totalVotesAllowed }}">
                <div class="col-12 inperson-box">
                    <div class="row" id="number-votes" style="display: none;">
                        <div class="offset-lg-6 col-lg-6 remaining-votes">
                            <span>{{ONE::transCb('cb_in_person_vote_available_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}: </span>
                            <span class="number">{{ $totalVotesAllowed }}</span>
                            <div class="between-bar"></div>
                            <span>
                                {{ONE::transCb('cb_in_person_vote_votes_used', !empty($cb) ? $cb->cb_key : $cbKey)}}: 
                            </span>
                            <span class="number" id="remaining-votes"></span>
                        </div>
                    </div>
                    <div class="content" id="registration-msg">
                        <div class="row">
                            <div class="col-8 mx-auto">
                                <div class="title">
                                    {{ONE::transCb('cb_in_person_vote_registration_message', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                                <div class="description">
                                    {{ONE::transCb('cb_in_person_vote_registration_subtitle', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="voting-msg" style="display: none;">
                        <div class="row">
                            <div class="col-12">
                                <div class="title">
                                    {{ONE::transCb('cb_in_person_vote_voting_message', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                                <div class="description">
                                    {{ONE::transCb('cb_in_person_vote_voting_subtitle', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content" id="submit-msg" style="display: none;">
                        <div class="row">
                            <div class="col-8 mx-auto">
                                <div class="title">
                                    {{ONE::transCb('cb_in_person_vote_submit_message', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                                <div class="description">
                                    {{ONE::transCb('cb_in_person_vote_submit_subtitle', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="content">
                        <div class="row" id="registration">
                            <div class="col-8 mx-auto">
                                <div class="row">
                                    <input type="hidden" id="current-user-key" value="">
                                    <div class="form-group" hidden>
                                        <label for="name">{{ONE::transCb('cb_vote_in_person_name', !empty($cb) ? $cb->cb_key : $cbKey)}}</label>
                                        <input type="text" id="name" name="name" class="form-control" autocomplete="off">
                                    </div>
                                    @foreach($parameters as $parameter)
                                        <?php if(isset($parameter->code)){
                                                $id = $parameter->code;
                                            }else{
                                                $id = '';
                                            } ?>
                                        <div class="col-12 idea">
                                            @if($parameter->parameter_type->code == 'numeric' && $parameter->vote_in_person == 1)
                                                <div class="form-group {!! ($parameter->mandatory == true) ? 'required' : '' !!}">
                                                    {!! Form::label($parameter->name, $parameter->name, array('class' => "color-secundary " . ($parameter->mandatory == true ? '':null))) !!}
                                                    {!! Form::number('parameter_'.$parameter->parameter_user_type_key, null,  array('class'=>'form-control', 'autocomplete'=>"off", 'id' => $id, 'required' => ($parameter->mandatory == true ? '' : null))) !!}
                                                </div>
                                            @elseif($parameter->parameter_type->code == 'dropdown' && $parameter->vote_in_person == 1)
                                                <div class="form-group {!! ($parameter->mandatory == true) ? 'required' : '' !!}">
                                                    <label for="{{$parameter->parameter_user_type_key}}">{{ $parameter->name }}</label>
                    
                                                    <select class="form-control" id="parameter_{{$parameter->parameter_user_type_key}}" name="parameter_{{$parameter->parameter_user_type_key}}" id = "{{$id}}" @if($parameter->mandatory) required @endif>
                                                        <option value="" selected>{{trans("PublicUser.selectOption")}}</option>
                                                        @foreach($parameter->parameter_user_options as $option)
                    
                                                            <option value="{{$option->parameter_user_option_key}}">{{$option->name}}</option>
                                                        @endforeach
                                                    </select>
                                                </div>
                                            @elseif($parameter->parameter_type->code == 'check_box' && $parameter->vote_in_person == 1)
                                                <input type="hidden" class="form-control" id="parameter_{{$parameter->parameter_user_type_key}}" name="parameter_{{$parameter->parameter_user_type_key}}" id = "{{$id}}" value="{{ $parameterValue }}">
                                            @else
                                                @if($parameter->vote_in_person == 1)
                                                    <div class="form-group {!! ($parameter->mandatory == true) ? 'required' : '' !!}">
                    
                                                        {!! Form::label($parameter->name, $parameter->name, array('class' => "color-secundary " . ($parameter->mandatory == true ? 'required':null))) !!}
                                                        {!! Form::text('parameter_'.$parameter->parameter_user_type_key, null,  array('class'=>'form-control', 'autocomplete'=>"off", 'id' => $id,'required' => ($parameter->mandatory == true ? 'required' : null))) !!}
                    
                                                    </div>
                                                @endif
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                        
                                <div class="row footer-buttons">
                                    <div class="offset-sm-6 col-sm-6 col-12 login-btn">
                                        <input type="button" id="submit-registration" value="{{ONE::transCb('cb_in_person_vote_begin', !empty($cb) ? $cb->cb_key : $cbKey)}}">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="" style="display: none;" id="voting">
                            <div class="row topics" id="infinite-scroll">
                                @foreach($topics as $topic)
                                    <div class="col-6 idea topic-description">
                                        <a class="topic-key a-wrapper" data-topic-title="{{ $topic->title }}" data-topic-key="{{ $topic->topic_key }}">
                                            <div class="img" style="background-image:url('@if(!empty($filesByType) && count($filesByType) >0 && isset($filesByType[$topic->topic_key]) && !empty(reset($filesByType[$topic->topic_key])) ){{ action('FilesController@download', [$filesByType[$topic->topic_key]->file_id, $filesByType[$topic->topic_key]->file_code, 'inline' => 1, 'h' => 150, 'extension' => 'jpeg', 'quality' => 65])}} @else {{ONE::getSiteConfiguration("file_logo_first","/images/demo/LogoEmpatia-l-02.png")}}@endif');background-position:center;"></div>
                                            <div class="idea-title">
                                                <p>{{ $topic->title }}</p>
                                            </div>
                                        </a>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        

                        <div class="row footer-buttons">
                                <div class="col-sm-6 col-12 back-btn">
                                    <input type="button" id="edit-votes" value="{{ONE::transCb('cb_in_person_vote_back', !empty($cb) ? $cb->cb_key : $cbKey)}}" style="display: none;">
                                </div>
                                <div class="col-sm-6 col-12 login-btn">
                                    <input type="button" id="confirm-votes" value="{{ONE::transCb('cb_in_person_vote_confirm_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}" style="display: none;">
                                    <input type="button" id="submit-votes" value="{{ONE::transCb('cb_in_person_submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}" style="display: none;">
                                </div>
                            </div>
                        <div class="row margin-0">
                            <div class="col-xs-offset-2 col-xs-8">
                                <div id="alert"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

            
        {{--  <div class="col-md-6 col-xs-12 pull-right">
            <input type="hidden" id="current-user-key" value="">

            <div id="voting" class="row margin-0 margin-top-bottom-35" style="display: none;">
                    <div class="min-height">
                        <div class="preparing-user">
                            <div class="col-xs-offset-2 col-xs-8 info-div">
                                <span class="pull-left label-ajax-info-register">{!! trans('defaultPublicUserVotingRegistration.preparingUser') !!}</span>
                                <img src="{{ asset('images/bipart/bluePreLoader.gif') }}" alt="Loading"
                                     class="label-ajax-info-register-loader pull-left"
                                     style="width: 20px; padding-top:2px;"/>
                                <i class="fa fa-check label-ajax-info-register-success" style="display: none; color:#77b341!important;"></i>
                            </div>
                        </div>


                    </div>
                </div>

        </div>  --}}

    <div id="myModalWarning" class="modal fade my-modal-error" role="dialog">
        <div class="modal-dialog in-person-modal">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ONE::transCb('cb_in_person_modal_already_voted_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><i class="fa fa-exclamation-triangle color-red" aria-hidden="true"></i>{{ONE::transCb('cb_in_person_modal_already_voted_description', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalError" class="modal fade my-modal-error" role="dialog">
        <div class="modal-dialog in-person-modal">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ONE::transCb('cb_in_person_modal_error_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><i class="fa fa-exclamation-triangle color-red" aria-hidden="true"></i> {{ONE::transCb('cb_in_person_modal_error_description', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalNif" class="modal fade my-modal-error" role="dialog">
        <div class="modal-dialog in-person-modal">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ONE::transCb('cb_in_person_modal_error_nif_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><i class="fa fa-exclamation-triangle color-red" aria-hidden="true"></i> {{ONE::transCb('cb_in_person_modal_error_nif_description', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalVotes" class="modal fade my-modal-error" role="dialog" aria-hidden="true">
        <div class="modal-dialog in-person-modal">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ONE::transCb('cb_in_person_modal_vote_limit_reached_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p><i class="fa fa-exclamation-triangle color-red" aria-hidden="true"></i> {{ONE::transCb('cb_in_person_modal_vote_limit_reached_description', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                </div>
            </div>
        </div>
    </div>
    <div id="myModalSuccess" class="modal fade my-modal-success" role="dialog">
        <div class="modal-dialog in-person-modal">
            <!-- Modal content-->
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">{{ONE::transCb('cb_in_person_modal_success_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</h4>
                </div>
                <div class="modal-body">
                    <p>{{ONE::transCb('cb_in_person_modal_success_description', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                </div>
            </div>
        </div>
    </div>
    
@endsection

@section('scripts')
    <script>

        var remainingVotes = Number("{{$totalVotesAllowed}}");
        var vote = 0;
        $("#remaining-votes").empty();
        $("#remaining-votes").append(vote);


        $(document).on('click', '#confirm-votes', function () {
            var votedTopics = $(document).find('.active-topic');
            if(votedTopics.length == remainingVotes){
                $(document).find('.topic-description').hide();
                $(document).find('.active-topic').parents('.topic-description').show();
                $(document).find('.active-topic').parents('.topic-description').removeClass('col-6');
                $(document).find('.active-topic').parents('.topic-description').addClass('col-8 mx-auto votesConfirmed');
                $(document).find('.idea-title').addClass('alignVertical');
                $("#confirm-votes").hide();
                $("#voting-msg").hide();    
                $("#submit-votes").show();
                $("#edit-votes").show();
                $("#submit-msg").show();
                $(".active-topic").css({"pointer-events": "none"});  
                $(document).find("#submit-votes").removeClass('in-submit-btn');
            }
            else{
                $(document).find('.topic-description').hide();
                $(document).find('.active-topic').parents('.topic-description').show();
                $(document).find('.active-topic').parents('.topic-description').removeClass('col-6');
                $(document).find('.active-topic').parents('.topic-description').addClass('col-8 mx-auto votesConfirmed');
                $(document).find('.idea-title').addClass('alignVertical');
                $("#confirm-votes").hide();
                $("#voting-msg").hide();    
                $("#submit-votes").show();
                $("#edit-votes").show();
                $("#submit-msg").show();
                $(".active-topic").css({"pointer-events": "none"});   
                $(document).find("#submit-votes").addClass('in-submit-btn');
            }        });

        $(document).on('click', '#edit-votes', function () {
            $(document).find('.topic-description').removeClass('col-8 mx-auto votesConfirmed');
            $(document).find('.alignVertical').removeClass('alignVertical');
            $(document).find('.topic-description').addClass('col-6');
            $(document).find('.topic-description').show();
            $(".active-topic").css({"pointer-events": ""});
            $("#submit-votes").hide();
            $("#edit-votes").hide();
            $("#submit-msg").hide();
            $("#confirm-votes").show();
            $("#voting-msg").show();
        });

        $(document).on('click', '.topic-key', function () {
            console.log($(this));
            var element = $(this);
            if (element.hasClass('active-topic')) {
                vote = vote -1;
                if(vote < 0){
                    vote = 0; 
                }
                $("#remaining-votes").empty();
                $("#remaining-votes").append(vote);
                console.log("vote -1: "+vote);
                element.removeClass('active-topic');
            } else {
                var numberOfVotedTopics = $(document).find('.active-topic');
                vote = vote +1;
                if(vote > remainingVotes){
                    vote = remainingVotes;
                }
                $("#remaining-votes").empty();
                $("#remaining-votes").append(vote);
                console.log("vote +1: "+vote);
                if (numberOfVotedTopics.length >= $('#max-votes-allowed').val()) {
                    $("#myModalVotes").modal('show');
                } else {
                    element.addClass('active-topic');
                }


            }

        });

        $(document).on('click', '#submit-votes', function () {
            $("#submit-msg").hide();

            var votedTopics = $(document).find('.active-topic');
            var votedTopicsKey = [];
            for (var i = 0; i < votedTopics.length; i++) {
                votedTopicsKey[i] = $(votedTopics[i]).attr('data-topic-key');
            }

            console.log(votedTopicsKey);

            $.ajax({
                method: 'POST', // Type of response and matches what we said in the route
                url: "{{action('PublicCbsController@publicUserVotingRegistrationStoreVotes')}}", // This is the url we gave in the route
                data: {
                    "_token": "{{ csrf_token() }}",
                    "votes": votedTopicsKey,
                    "vote_event_key": "{{ $voteKey }}",
                    "user_key": $("#current-user-key").val()
                }, success: function (response) { // What to do if we succeed
                    if (response.error) {
                        console.log(response);
                    } else {
                        $(document).find('.topic-description').removeClass('col-8 mx-auto votesConfirmed');
                        $(document).find('.alignVertical').removeClass('alignVertical');
                        $(document).find('.topic-description').addClass('col-6');
                        $("#myModalSuccess").modal('show');
                        $("#edit-votes").hide();
                        $("#confirm-votes").hide();
                        $("#number-votes").hide();
                        $("#registration").show();
                        $("#registration-msg").show();
                        $("#registration").find('input[type="text"],input[type="number"]').val('');
                        $('select').each( function() {
                            $(this).val( $(this).find("option[selected]").val() );
                        });
                        $("#voting").hide();
                        $("#current-user-key").val('');
                        setTimeout(function(d) {
                            $("#myModalSuccess").modal('hide');
                        }, 10000);
                        $(".label-ajax-info-register-loader").show();
                        $(".label-ajax-info-register-success").hide();
                        $("#submit-registration").show();
                        $("#submit-votes").hide();
                        $(".active-topic").css({"pointer-events": "auto"}); 
                        $(document).find('.active-topic').removeClass('active-topic');
                        $(document).find('.topic-description').show();
                    }
                }
            });


        });


        $(document).on('click', '#submit-registration', function () {
            var nif = $("#nif").val();

            $(".preparing-user").show();
            var element = $(this);
            
            vote = 0;
            $("#remaining-votes").empty();
            $("#remaining-votes").append(vote);

            errors = false;
            $("#registration").find("input[required], select[required]").each(function (key, value) {
                currentInput = $(this);
                if (currentInput.val() == "") {
                    currentInput.css({"border": "1px solid #f00"});
                    errors = true;
                } else
                    currentInput.css({"border": ""});
            });
            if (errors == false) {
                var inputs = $("#registration").find('select,input').serializeArray();
                console.log(inputs);
                $.ajax({
                    method: 'POST', // Type of response and matches what we said in the route
                    url: "{{action('PublicUsersController@saveInPersonRegistration')}}", // This is the url we gave in the route
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "inputs": inputs,
                        "nif": nif,
                        "vote_event_key": "{{ $voteKey }}",
                        "doNotAttachToCode": true
                    }, success: function (response) { // What to do if we succeed
                        if (response.warning){
                            $("#myModalWarning").modal('show');
                            $(".preparing-user").hide();
                            setTimeout(function(d){
                                $("#confirm-votes").hide();
                                $("#registration").show();
                                $("#submit-registration").show();
                                $("#voting").hide();
                                $("#registration").find('input[type=text], textarea').val('');
                                $("#registration").find('option').prop("selected", false);
                                $("#registration").find('input[type=radio]').prop("checked", false);

                            }, 3000);
                        }else if(response.invalidnif) {
                            $("#myModalNif").modal('show');
                            $(".preparing-user").hide();
                            setTimeout(function(d){
                                $("#confirm-votes").hide();
                                $("#registration").show();
                                $("#submit-registration").show();
                                $("#voting").hide();
                                $("#registration").find('input[type=text], textarea').val('');
                                $("#registration").find('option').prop("selected", false);
                                $("#registration").find('input[type=radio]').prop("checked", false);

                            }, 3000);
                        }else if(response.error) {
                            $("#myModalError").modal('show');
                            $(".preparing-user").hide();
                            setTimeout(function(d){
                                $("#confirm-votes").hide();
                                $("#registration").show();
                                $("#submit-registration").show();
                                $("#voting").hide();
                                $("#registration").find('input[type=text], textarea').val('');
                                $("#registration").find('option').prop("selected", false);
                                $("#registration").find('input[type=radio]').prop("checked", false);

                            }, 3000);
                        } else {
                            var user_key = response.success;
                            $(".label-ajax-info-register-loader").hide();
                            $(".label-ajax-info-register-success").show();
                            $("#current-user-key").val(user_key);
                            $("#registration").hide();
                            $("#registration-msg").hide();
                            $("#submit-registration").hide();
                            $("#number-votes").show();
                            $("#voting-msg").show();
                            $("#voting").show();
                            $("#confirm-votes").show();
                        }
                    }
                });
            }
        });
    </script>
@endsection
