@extends('private.wizards._layout')

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
    <div class="row box-buffer">
        <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
            <form role="form" action="{{action('EntitiesController@storeWizard')}}" method="post" name="formEntity" id="formEntity">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="layout" value="{{$defaultConfigurations['layout']}}">
                
                <div class="text-left">
                    <label for="name">{{trans("privateEntities.name")}}</label>
                    <input type="text" name="name" class="form-control" id="name" required>
                </div>
                <div class="text-left">
                    <label for="name">{{trans("privateEntities.url")}}</label>
                    <input type="text" name="url" class="form-control" id="url" required @if(Session::get("firstInstallWizardStarted",false)) value="{{ request()->getHttpHost() }}" @endif>
                </div>
                <div class="text-left">
                    <label for="link">{{trans("privateEntities.siteLink")}}</label>
                    <input type="text" name="link" class="form-control" id="link" required>
                </div>
                <div class="text-left">
                    <label for="no_reply_email">{{trans("privateEntities.no_reply_email")}}</label>
                    <input type="email" name="no_reply_email" class="form-control" id="no_reply_email" required>
                </div>

                <div class="text-left" style="margin: 10px 0 10px 0">
                    <button type="button" class="btn btn-flat btn-info" data-toggle="collapse" data-target="#configurations">
                        {{trans('privateEntitiesWizard.show_all_configurations')}}
                    </button>
                    <button type="submit" class="btn btn-flat empatia pull-right">
                        {{trans("privateEntitiesWizard.create_entity")}}
                    </button>
                </div>
                <div id="configurations" class="text-left collapse non-basic-configurations-container">
                    <div>
                        <label for="description">{{trans("privateEntities.description")}}</label>
                        <input type="text" name="description" class="form-control"/>
                    </div>
                    <div>
                        <label for="designation">{{trans("privateEntities.designation")}}</label>
                        <input type="text" name="designation" class="form-control"/>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.languages")}}</h4>
                        <div class="configuration-table-container">
                            <table class="table table-striped table-hover">
                                @foreach($languages as $language)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="language" @if(strcasecmp($defaultConfigurations["language"] ?? "",$language->code)==0) checked @endif value="{{ $language->id }}">
                                                    {{ $language->name }}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.countries")}}</h4>
                        <div class="configuration-table-container">
                            <table class="table table-striped table-hover">
                                @foreach($countries as $country)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="country" @if(strcasecmp($defaultConfigurations["country"] ?? "",$country->code)==0) checked @endif value="{{ $country->id }}">
                                                    {{ $country->name }}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.currencies")}}</h4>
                        <div class="configuration-table-container">
                            <table class="table table-striped table-hover">
                                @foreach($currencies as $currency)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="currency" @if(strcasecmp($defaultConfigurations["currency"] ?? "",$currency->code)==0) checked @endif value="{{ $currency->id }}">
                                                    {{ $currency->currency }}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                    <div>
                        <h4>{{trans("privateEntities.timezones")}}</h4>
                        <div class="configuration-table-container">
                            <table class="table table-striped table-hover">
                                @foreach($timezones as $timezone)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="radio" name="timezone" @if(strcasecmp($defaultConfigurations["timezone"] ?? "",$timezone->name)==0) checked @endif value="{{ $timezone->id }}">
                                                    {{ $timezone->name }}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </table>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>

@endsection