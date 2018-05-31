@extends('private._private.index')

@section('header_scripts')
    <!-- Plupload Javascript fix and bootstrap fix @ start -->
    <link rel="stylesheet" href="/bootstrap/plupload-fix/bootstrap.css">
    <script src="/bootstrap/plupload-fix/bootstrap.min.js"></script>
    <!-- Plupload Javascript fix and bootstrap fix @ End -->
    <script src="{{ asset('vendor/jildertmiedema/laravel-plupload/js/plupload.full.min.js') }}"></script>

    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/moment-with-locales.js')) !!}"></script>
    <script src="{!! asset(elixir('js/bootstrap-datetimepicker/bootstrap-datetimepicker.js')) !!}"></script>
@endsection

@section('header_styles')
    <link href="{!! asset(elixir('css/bootstrap-datetimepicker/bootstrap-datetimepicker.css')) !!}" rel="stylesheet" type="text/css"/>
@endsection

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('operationSchedules')
               ->settings(["model" => isset($currency) ? $currency : null])
               ->show('OperationSchedulesController@edit', null, ['type' => $type ?? null,  'cbKey' => $cbKey ?? null, 'key' => isset($operationSchedule) ? $operationSchedule->cb_operation_schedule_key : null], 'OperationSchedulesController@index', ['type' => $type ?? null,  'cbKey' => $cbKey ?? null, 'key' => isset($operationSchedule) ? $operationSchedule->cb_operation_schedule_key : null])
               ->create('OperationSchedulesController@store', 'OperationSchedulesController@index', ['type' => $type ?? null,  'cbKey' => $cbKey ?? null])
               ->edit('OperationSchedulesController@update', 'OperationSchedulesController@show', ['type' => $type ?? null,  'cbKey' => $cbKey ?? null, 'key' => isset($operationSchedule) ? $operationSchedule->cb_operation_schedule_key : null])
               ->open();
            @endphp

            {!! Form::hidden('type',$type, ['id' => 'type']) !!}
            {!! Form::hidden('cbKey',$cbKey, ['id' => 'cbKey']) !!}

            @if(!empty($operationActions))
                <br>
                <div class="col-md-6">
                    <label for="operationActionSelect">{{trans('privateOperationSchedules.action')}}</label>
                    <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateOperationSchedules.action_description')}}</span>
                    <br>
                    <select style="min-width: 300px" id="operationActionSelect" name="operationActionSelect" class="select2-searchable" required @if(ONE::actionType('operationSchedules') != 'create') disabled @endif>
                        <option value="">{{ trans('privateOperationSchedules.select_action') }}</option>
                        @foreach($operationActions as $operationAction)
                            <option value="{!! $operationAction->code !!}" @if(isset($operationSchedule) && ($operationAction->id == $operationSchedule->operation_action_id)) selected @endif>{!! $operationAction->name !!} </option>
                        @endforeach
                    </select>
                </div>
            @endif

            @if(!empty($operationTypes))
                <br>
                <div class="col-md-6">
                    <label for="operationActionSelect">{{trans('privateOperationSchedules.type')}}</label>
                    <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateOperationSchedules.type_description')}}</span>
                    <br>
                    <select style="min-width: 300px" id="operationTypeSelect" name="operationTypeSelect" class="select2-searchable" required @if(ONE::actionType('operationSchedules') != 'create') disabled @endif >
                        <option value="">{{ trans('privateOperationSchedules.select_type') }}</option>
                        @foreach($operationTypes as $operationType)
                            <option value="{!! $operationType->code !!}" @if(isset($operationSchedule) && ($operationType->id == $operationSchedule->operation_type_id)) selected @endif>{!! $operationType->name !!}</option>
                        @endforeach
                    </select>
                </div>
            @endif
            <br>
            <div class='col-md-6'>
                <label for="startDate">{{trans('privateOperationSchedules.start_date_time')}}</label>
                <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateOperationSchedules.start_date_time_description')}}</span>
                <br>
                <div class="form-group">
                    <div class='input-group date' id='startDate' style="max-width: 300px">
                        <input type='text' class="form-control" name="startDate" @if(isset($operationSchedule)) value="{{$operationSchedule->start_date}}" @endif required @if(ONE::actionType('operationSchedules') == 'show') disabled @endif/>
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                    </div>
                </div>
            </div>
            <br>
            <div class='col-md-6'>
                <label for="endDate">{{trans('privateOperationSchedules.end_date_time')}}</label>
                <span class="help-block oneform-help-block" style="margin:-4px 0;font-size:10px;">{{trans('privateOperationSchedules.end_date_time_description')}}</span>
                <br>
                <div class="form-group">
                    <div class='input-group date' id='endDate' style="max-width: 300px">
                        <input type='text' class="form-control" name="endDate" @if(isset($operationSchedule)) value="{{$operationSchedule->end_date}}" @endif required @if(ONE::actionType('operationSchedules') == 'show') disabled @endif/>
                        <span class="input-group-addon">
                                <span class="glyphicon glyphicon-calendar">
                                </span>
                            </span>
                    </div>
                </div>
            </div>

            <div class="col-md-6">
                {!! Form::oneSwitch("active", array("name"=>trans('privateOperationSchedules.active'),"description"=>trans('privateOperationSchedules.private_operation_schedule_description')), isset($operationSchedule) ? (($operationSchedule->active == 1) ? 'checked':''):'', ["id"=>"active"]) !!}
            </div>

            {!! $form->make() !!}
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $("#operationActionSelect").select2({
                tags: true
            });
            $("#operationTypeSelect").select2({
                tags: true
            });

            $('.date').datetimepicker({
                format: 'YYYY-MM-D HH:mm'
            });
        });
    </script>
@endsection
