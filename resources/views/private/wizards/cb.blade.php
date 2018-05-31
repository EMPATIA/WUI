@extends('private.wizards._layout')

@section('content')
    @php
        $defaultConfigurations = array(
            'empaville' => array(

            ), '3gN7rsyzMvZrxBhZPiaFonMBpt9BcWZF' => array(
                'idea' => array(
                    'security_public_access',
                    'security_anonymous_comments',
                    'security_create_topics',
                    'topic_options_allow_pictures',
                    'topic_options_allow_share',
                    'topic_options_allow_follow',
                    'topic_comments_allow_comments',
                    'topic_comments_normal'
                )
            ),
        );

        $entityKey = \ONE::getEntityKey();

        if (strtolower($type)=="empaville")
            $currentConfigurations = $defaultConfigurations["empaville"] ?? [];
        else
            $currentConfigurations = $defaultConfigurations[$entityKey][$type] ?? [];
    @endphp
    <div class="row box-buffer">
        <div class="col-xs-12 col-lg-12 text-center" style="margin-bottom: 2%">
            <h2>
                @if ($type!="empaville")
                    @if($type =="idea")
                        <h1>{{trans("privateCbWizard.create_new_continous_ideation_process")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_continuous_ideation_cb_desc")}}
                        </small>
                    @endif
                    @if($type =="project")
                        <h1>{{trans("privateCbWizard.create_new_project")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_participatory_budgeting_cb_desc")}}
                        </small>
                    @endif
                    @if($type == "proposal")
                        <h1>{{trans("privateCbWizard.create_new_proposal")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_participatory_budgeting_cb_desc")}}
                        </small>
                    @endif
                    @if($type =="consultation")
                        <h1>{{trans("privateCbWizard.create_new_consultation")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_consultation_cb_desc")}}
                        </small>
                    @endif
                    @if($type =="fix_my_street")
                        <h1>{{trans("privateCbWizard.create_new_fix_my_street")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_fix_my_street_cb_desc")}}
                        </small>
                    @endif
                    @if($type =="vote_event")
                        <h1>{{trans("privateCbWizard.create_new_vote_event")}}</h1>
                        <small>
                            {{trans("privateCbsWizard.create_vote_event_cb_desc")}}
                        </small>
                    @endif
                @else
                    <h1>{{ trans("privateCbsWizard.create_cb_empaville") }}</h1>
                    <small>
                        {{ trans("privateCbsWizard.create_empaville_cb_desc")}}
                    </small>
                @endif
            </h2>
        </div>
        <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
            <form role="form" action="{{action('CbsController@storeWizard', ['type' => $type])}}" method="post"
                  name="formCb" id="formCb">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="flagNewCb" value="1">
                <input type="hidden" name="parameterItensIds" value="">
                <input type="hidden" name="voteItensIds" value="">

                @if(empty($currentConfigurations) && $type=="empaville")
                    <div class="text-left alert alert-danger">
                        {{ trans("privateCbsWizard.no_default_configurations_defined") }}
                    </div>
                @endif
                <div class="text-left">
                    <label for="title">{{trans("privateCbs.title")}}</label>
                    <input type="text" name="title" class="form-control" id="title" required>
                </div>
                @if ($type!="empaville")
                    <div class="text-left">
                        <label for="start_date">{{trans("privateCbs.start_date")}}</label>
                        <input type="date" name="start_date" class="form-control"
                               value="{{(\Carbon\Carbon::now())->toDateString()}}">
                    </div>
                @endif

                <div class="text-left" style="margin: 10px 0 10px 0">
                    <a href="{{ action("QuickAccessController@index") }}" class="btn btn-primary pull-left">
                        {{trans("privateCbsWizard.go_to_dashboard")}}
                    </a>

                    <button type="submit" class="btn btn-flat empatia pull-right">
                        {{trans("privateCbsWizard.create_cb")}}
                    </button>
                </div>
            </form>
        </div>
    </div>

@endsection