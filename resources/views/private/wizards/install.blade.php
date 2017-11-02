@extends('private.wizards._layout')

@section('content')
    @php
        $entityName = Session::get("firstInstallWizardEntityName","");
        $cbName = Session::get("firstInstallWizardCBName","");
    @endphp
    <div class="row box-buffer-rap btn-grid">
        <div class="col-xs-12 col-lg-12 text-center">
            <h2>{{ trans("privateFirstInstall.title") }}</h2>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <a href="{{ action('EntitiesController@createWizard') }}" class="text-center @if(!empty($entityName)) disabled @endif">
                <div class="btn-presentation">
                    <div class="text-padding">
                        {{ trans("privateFirstInstall.create_entity") }}
                        @if(!empty($entityName))
                            <br>
                            <small>({{ $entityName }})</small>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <a href="{{ action('CbsController@createWizard') }}" class="text-center @if(!empty($cbName) || empty($entityName)) disabled @endif">
                <div class="btn-presentation">
                    <div class="text-padding">
                        {{ trans("privateFirstInstall.create_cb")}}
                        @if(!empty($cbName))
                            <br>
                            <small>({{ $cbName }})</small>
                        @endif
                    </div>
                </div>
            </a>
        </div>
        <div class="col-xs-12 col-sm-6 col-lg-4">
            <a href="{{ action('QuickAccessController@firstInstallWizardFinish') }}" class="text-center @if(empty($entityName) || empty($cbName)) disabled @endif">
                <div class="btn-presentation">
                    <div class="text-padding">
                        {{ trans("privateFirstInstall.go_to_dashboard")}}
                    </div>
                </div>
            </a>
        </div>
    </div>
@endsection