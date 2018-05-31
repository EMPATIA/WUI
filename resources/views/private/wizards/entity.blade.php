@extends('private.wizards._layout')
@section('header_styles')
    <!-- select2 -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.6-rc.0/js/select2.min.js"></script>
    <style>
        .select2-container{
            width: 100%!important;
        }
    </style>
@endsection

@section('content')
    @php
        $defaultConfigurations = array(
            'timezone'  => 'Europe/Lisbon',
            'country'   => 'pt',
            'currency'  => 'EUR',
            'language'  => 'en',
            'layout'    => 'default',
        );
    @endphp
    <div class="col-xs-12 col-lg-12 text-center ">
        <h2>{{trans("privateEntitiesWizard.create_entity_title")}}</h2>
    </div>
    <div class="row box-buffer">
        <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
            <form role="form" action="{{action('EntitiesController@storeWizard')}}" method="post" name="formEntity" id="formEntity" >
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="layout" value="{{$defaultConfigurations['layout']}}">
                
                <div class="text-left">
                    <label for="name">{{trans("privateEntities.name")}}</label>
                    <div style="font-size: 80%; font-style: italic">
                        <label for="name">{{trans("privateEntities.name_desc")}}</label>
                    </div>
                    <input type="text" name="name" class="form-control" id="name" required>
                </div>
                <div class="text-left">
                    <label for="url" >{{trans("privateEntities.url")}}</label>
                    <div style="font-size: 80%; font-style: italic">
                        <label for="url">{{trans("privateEntities.url_desc")}}</label>
                    </div>
                    <input type="text" name="url" class="form-control" id="url" required pattern="https?://.+" title="Use the format: http://domain." @if(Session::get("firstInstallWizardStarted",false)) value="{{ request()->getHttpHost() }}" @endif>
                </div>
                <div class="text-left">
                    <label for="no_reply_email">{{trans("privateEntities.no_reply_email")}}</label>
                    <div style="font-size: 80%; font-style: italic">
                        <label for="no_reply_email">{{trans("privateEntities.no_reply_email_desc")}}</label>
                    </div>
                    <input type="email" name="no_reply_email" class="form-control" id="no_reply_email" required>
                </div>

                <div id="configurations" class="text-left collapse non-basic-configurations-container" style="margin: 10px 0 10px 0">
                    <div>
                        <h4>{{trans("privateEntities.languages")}}</h4>
                        <div>
                            <select class="js-example-basic-single" name="language">
                                @foreach($languages as $language)
                                    <div>
                                        <option value="{{ $language->id }}" @if(strcasecmp($defaultConfigurations["language"] ?? "",$language->code)==0) selected @endif>{{ $language->name }}</option>
                                    </div>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.countries")}}</h4>
                        <div>
                            <select class="js-example-basic-single" name="country">
                                @foreach($countries as $country)
                                    <div>
                                        <option value="{{ $country->id }}" @if(strcasecmp($defaultConfigurations["country"] ?? "",$country->code)==0) selected @endif>{{ $country->name }}</option>
                                    </div>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.currencies")}}</h4>
                        <div>
                            <select class="js-example-basic-single" name="currency">
                                @foreach($currencies as $currency)
                                    <div class="checkbox">
                                        <option value="{{ $currency->id }}" @if(strcasecmp($defaultConfigurations["currency"] ?? "",$currency->code)==0) selected @endif>{{ $currency->currency }}</option>
                                    </div>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.timezones")}}</h4>
                        <div>
                            <select class="js-example-basic-single" name="timezone">
                                @foreach($timezones as $timezone)
                                    <div class="checkbox">
                                        <option value="{{ $timezone->id }}" @if(strcasecmp($defaultConfigurations["timezone"] ?? "",$timezone->name)==0) selected @endif>{{ $timezone->name }}</option>
                                    </div>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
                <div class="text-left" style="margin: 10px 0 10px 0">
                <button type="button" class="btn btn-flat btn-info" data-toggle="collapse" data-target="#configurations">
                    {{trans('privateEntitiesWizard.show_all_configurations')}}
                </button>
                <button type="submit" class="btn btn-flat empatia pull-right">
                    {{trans("privateEntitiesWizard.create_entity")}}
                </button>
            </div>
            </form>
        </div>
    </div>

    <script>
        $(document).ready(function() {
            $('.js-example-basic-single').select2();
        });
    </script>

@endsection