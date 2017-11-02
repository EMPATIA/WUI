@extends('private.wizards._layout')

@section('content')
    @php $empavilleMode = env("EMPAVILLE_MODE",false); @endphp
    <div class="row box-buffer-rap btn-grid">
        @if($empavilleMode)
            <div class="col-xs-11 col-xs-offset-1 not-button">
                <div class="text-center alert alert-info">
                    {{ trans("privateCbsWizard.some_options_not_available_in_empaville_mode") }}
                </div>
            </div>
        @endif
        @if(ONE::verifyModuleAccess('cb','idea') || Session::get("firstInstallWizardStarted",false))
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-lg-offset-1">
                <a href="{{ action('CbsController@createWizard',['type' => 'idea']) }}" class="text-center @if($empavilleMode) disabled @endif">
                    <div class="btn-presentation">
                        <div class="text-padding">
                            {{trans("privateCbsWizard.create_idea_cb")}}
                        </div>
                    </div>
                </a>
            </div>
        @endif
        @if(ONE::verifyModuleAccess('cb','proposal') || Session::get("firstInstallWizardStarted",false))
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-lg-offset-1">
                <a href="{{ action('CbsController@createWizard',['type' => 'proposal']) }}" class="text-center @if($empavilleMode) disabled @endif">
                    <div class="btn-presentation">
                        <div class="text-padding">
                            {{trans("privateCbsWizard.create_proposal_cb")}}
                        </div>
                    </div>
                </a>
            </div>
        @endif
        @if(ONE::verifyModuleAccess('cb','project') || Session::get("firstInstallWizardStarted",false))
            <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-lg-offset-1">
                <a href="{{ action('CbsController@createWizard',['type' => 'project']) }}" class="text-center @if($empavilleMode) disabled @endif">
                    <div class="btn-presentation">
                        <div class="text-padding">
                            {{trans("privateCbsWizard.create_project_cb")}}
                        </div>
                    </div>
                </a>
            </div>
        @endif
        <div class="col-lg-3 col-md-4 col-sm-6 col-xs-12 col-lg-offset-1">
            <a href="{{ action('CbsController@createWizard',['type' => 'empaville']) }}" class="text-center">
                <div class="btn-presentation">
                    <div class="text-padding">
                        {{trans("privateCbsWizard.create_empaville_cb")}}
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection

