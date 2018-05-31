@extends('private.wizards._layout')
@section('header_styles')
    <link rel="stylesheet" href="/fonts/fontello-bc7d3037/css/fontello.css">
@endsection

@section('content')
    <style>
        .icon-size{
            font-size: 55px;
        }

        .my-row{
            display: flex;
            flex-wrap: wrap;
        }

        .my-col{
            padding:15px;
            flex:1;
            display: flex;
            flex-direction: column;
            flex-basis: 100%;
            max-width: 100%;
        }

        @media (min-width: 768px){
            .my-col{
                flex-basis: 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 992px){
            .my-col{
                flex-basis: 33.3%;
                max-width: 33.3%;
            }
        }

        .my-col>a{
            flex: 1;
            border: 2px solid #3b8ab8;
        }

        .my-col>a:hover,
        .my-col>a:hover > div{
            background-color: #3b8ab8;
            color: #fff;
            cursor: pointer;
        }

        .my-col>a:hover .btn-presentation{
            background-color: transparent;
        }

        .btn-presentation{
            border: none;
        }

        .btn-presentation:hover{
            border:none;
            background-color: transparent;
        }


        .page-title{
            padding: 15px;
            margin-bottom: 30px;;

        }

        .page-title>h2{
            margin: 0;
        }

        .description-txt{
            line-height: normal;
            font-size: 1.2rem;
            margin-top: 10px;
        }


        .btn-grid > div > a{
            display: flex;
        }
        .btn-presentation{
            padding: 5px;
        }
        .btn-back:hover{
            background: #3b8ab8;
            color: white;
        }
        .btn-back{
            background: white;
            color: #3b8ab8;
        }
        .pointer-disabled{
            pointer-events: none;
        }
       .pointer-disabled .btn-presentation{
            background: #f5f5f5;
        }
    </style>
    @php
        $empavilleMode = env("EMPAVILLE_MODE",false);
    @endphp
    <div class="box-buffer-rap">
        <div class="page-title">
            <h2>{{trans("privateCbWizard.create_participation_process")}}</h2>
            <small>
                {{trans("privateCbWizard.create_participation_process_desc")}}
            </small>
        </div>
        <div class="my-row" id="participation">
            @if($empavilleMode)
                <div class="col-xs-11 col-xs-offset-1 not-button">
                    <div class="text-center alert alert-info">
                        {{ trans("privateCbsWizard.some_options_not_available_in_empaville_mode") }}
                    </div>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col">
                    <a id="show" class="text-center @if($empavilleMode) disabled @endif">
                        <div class="btn-presentation flex-container">
                            <div class="wrap-content">
                                <i class="demo-icon icon-participatorytools_participatory-budgeting_path icon-size"></i>
                                <div style="line-height: normal">
                                    {{trans("privateCbsWizard.create_participatory_budgeting_cb")}}
                                </div>
                                <div class="description-txt">
                                    {{trans("privateCbsWizard.create_participatory_budgeting_cb_desc")}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col">
                    <a href="{{ action('CbsController@createWizard',['type' => 'idea']) }}" class="text-center dflex1  @if($empavilleMode) disabled @endif">
                        <div class="btn-presentation flex-container">
                            <i class="demo-icon icon-wizard_ideas_path_2 icon-size"></i>
                            <div style="line-height: normal">
                                {{trans("privateCbsWizard.create_continuous_ideation_cb")}}
                            </div>
                            <div class="description-txt">
                                {{trans("privateCbsWizard.create_continuous_ideation_cb_desc")}}
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col">
                    <a href="{{ action('CbsController@createWizard',['type' => 'consultation']) }}" class="text-center pointer-disabled @if($empavilleMode) disabled @endif" disabled="disabled">
                        <div class="btn-presentation flex-container">
                            <i class="demo-icon icon-participatorytools_debate_path_2 icon-size"></i>
                            <div style="line-height: normal">
                                {{trans("privateCbsWizard.create_consultation_cb")}}
                            </div>
                            <div class="description-txt">
                                {{trans("privateCbsWizard.create_consultation_cb_desc")}}
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col">
                    <a href="{{ action('CbsController@createWizard',['type' => 'fix_my_street']) }}" class="text-center pointer-disabled @if($empavilleMode) disabled @endif">
                        <div class="btn-presentation flex-container">
                            <i class="demo-icon icon-participatorytools_fix-mystreet_path icon-size"></i>
                            <div style="line-height: normal">
                                {{trans("privateCbsWizard.create_fix_my_street_cb")}}
                            </div>
                            <div class="description-txt">
                                {{trans("privateCbsWizard.create_fix_my_street_cb_desc")}}
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col">
                    <a href="{{ action('CbsController@createWizard',['type' => 'vote_event']) }}" class="text-center pointer-disabled @if($empavilleMode) disabled @endif" disabled="disabled">
                        <div class="btn-presentation flex-container">
                            <i class="demo-icon icon-participatorytools_vote-event_path icon-size"></i>
                            <div style="line-height: normal">
                                {{trans("privateCbsWizard.create_vote_event_cb")}}
                            </div>
                            <div class="description-txt">
                                {{trans("privateCbsWizard.create_vote_event_cb_desc")}}
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            <div class="my-col">
                <a href="{{ action('CbsController@createWizard',['type' => 'empaville']) }}" class="text-center dflex1 ">
                    <div class="btn-presentation flex-container">
                        <i class="demo-icon icon-wizard_empaville_path_1 icon-size"></i>
                        <div style="line-height: normal">
                            {{trans("privateCbsWizard.create_empaville_cb")}}
                        </div>
                        <div class="description-txt">
                            {{trans("privateCbsWizard.create_empaville_cb_desc")}}
                        </div>
                    </div>
                </a>
            </div>

            <div class="col-md-12">
                <div class="background" >
                    <a href="{{ action('QuickAccessController@firstInstallWizard') }}" style="border: 2px solid" class="btn btn-back">
                        <i class="fa fa-times" aria-hidden="true">
                            <span style="padding-left: 10px">{{trans("privateCbsWizard.cancel")}}</span>
                        </i>
                    </a>
                </div>
            </div>
        </div>

        <div class="my-row" style="display: none" id="budgeting">
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col" >
                    <a id="proposal" href="{{ action('CbsController@createWizard',['type' => 'proposal']) }}" class="text-center">
                        <div class="btn-presentation flex-container">
                            <div class="wrap-content">
                                <i class="demo-icon icon-wizard_proposals_path icon-size"></i>
                                <div style="line-height: normal">
                                    {{trans("privateCbsWizard.create_proposal_cb")}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            @if(Session::get("firstInstallWizardStarted",false))
                <div class="my-col" >
                    <a id="project"  href="{{ action('CbsController@createWizard',['type' => 'project']) }}" class="text-center">
                        <div class="btn-presentation flex-container">
                            <div class="wrap-content">
                                <i class="demo-icon icon-wizard_projects_path icon-size"></i>
                                <div style="line-height: normal">
                                    {{trans("privateCbsWizard.create_project_cb")}}
                                </div>
                            </div>
                        </div>
                    </a>
                </div>
            @endif
            <div class="col-md-12">
                <div class="background" >
                    <a id="backstep" style="border: 2px solid" class="btn btn-back">
                        <i class="fa fa-arrow-left" aria-hidden="true">
                            <span style="padding-left: 10px">{{trans("privateCbsWizard.back")}}</span>
                        </i>
                    </a>
                </div>
            </div>
        </div>
    </div>


    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.3.1/jquery.min.js"></script>
    <script>
        $(document).ready(function(){
            @if(Session::get('participatory') || Session::get('participatory_second'))

                $("#participation").hide();
                $("#participation").css("display", "none");

                $("#budgeting").show();
                $("#budgeting").css("display", "flex");

                @if(Session::get('participatory') == 'proposal' || Session::get('participatory_second') == 'proposal')
                        $("#proposal").css('pointer-events', 'none');
                        $("#proposal").find('> div').css('background', '#f5f5f5');
                @endif
                @if(Session::get('participatory') == 'project' || Session::get('participatory_second') == 'project')
                        $("#project").css('pointer-events', 'none');
                        $("#project").find('> div').css('background', '#f5f5f5');
                @endif

                $("#show").click(function(){
                    $("#participation").hide();
                    $("#participation").css("display", "none");

                    $("#budgeting").show();
                });

                $("#backstep").click(function(){
                    $("#budgeting").hide();
                    $("#budgeting").css("display", "none");

                    $("#participation").show();
                });

            @else
                $("#show").click(function(){
                    $("#participation").hide();
                    $("#participation").css("display", "none");

                    $("#budgeting").show();
                });
                $("#backstep").click(function(){
                    $("#budgeting").hide();
                    $("#budgeting").css("display", "none");

                    $("#participation").show();
                });
            @endif
        });
    </script>
@endsection
