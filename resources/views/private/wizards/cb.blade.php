@extends('private.wizards._layout')

@section('content')
    @php
        $defaultConfigurations = array(
        );

        $entityKey = \ONE::getEntityKey();

        if (strtolower($type)=="empaville")
            $currentConfigurations = $defaultConfigurations["empaville"] ?? [];
        else
            $currentConfigurations = $defaultConfigurations[$entityKey][$type] ?? [];
    @endphp
    <div class="row box-buffer">
        <div class="col-xs-12 col-lg-12 text-center">
            <h2>
                @if ($type!="empaville")
                    {{ trans("privateCbsWizard.create_cb_title") }}
                @else
                    {{ trans("privateCbsWizard.create_cb_empaville") }}
                @endif
            </h2>
        </div>
        <div class="col-xs-12 col-md-8 col-md-offset-2 text-center">
            <form role="form" action="{{action('CbsController@storeWizard', ['type' => $type])}}" method="post" name="formCb" id="formCb">
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <input type="hidden" name="flagNewCb" value="1">
                <input type="hidden" name="parameterItensIds" value="">
                <input type="hidden" name="voteItensIds" value="">

                @if(empty($currentConfigurations) && $type!="empaville")
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
                        <input type="date" name="start_date" class="form-control" value="{{(\Carbon\Carbon::now())->toDateString()}}">
                    </div>
                @endif

                <div class="text-left" style="margin: 10px 0 10px 0">
                    @if ($type!="empaville")
                        <button type="button" class="btn btn-flat btn-info" data-toggle="collapse" data-target="#configurations">
                            {{trans('privateCbsWizard.show_all_configurations')}}
                        </button>
                    @endif
                    <a href="{{ action("QuickAccessController@index") }}" class="btn btn-primary pull-left">
                        {{trans("privateCbsWizard.go_to_dashboard")}}
                    </a>

                    <button type="submit" class="btn btn-flat empatia pull-right">
                        {{trans("privateCbsWizard.create_cb")}}
                    </button>
                </div>
                @if ($type!="empaville")
                    <div id="configurations" class="text-left collapse" style="max-height: 500px; overflow: auto">
                        <div>
                            <label for="description">{{trans("privateCbs.description")}}</label>
                            <textarea name="description" rows="4" class="form-control"></textarea>
                        </div>
                        <div>
                            <label for="end_date">{{trans("privateCbs.end_date")}}</label>
                            <input type="date" name="end_date" class="form-control" value="">
                        </div>
                        <br>
                        <label for="configurations">{{trans("privateCbs.configurations")}}</label>
                        <table class="table table-striped table-hover table-condensed">
                            @foreach($configurations as $configuration)
                                @foreach($configuration->configurations as $configurationValue)
                                    <tr>
                                        <td>
                                            <div class="checkbox">
                                                <label>
                                                    <input type="checkbox" name="configuration_{{$configurationValue->id}}"  @if(in_array($configurationValue->code, $currentConfigurations)) checked @endif>
                                                    {{$configurationValue->title}}
                                                </label>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            @endforeach
                        </table>
                    </div>
                @endif
            </form>
        </div>
    </div>

@endsection