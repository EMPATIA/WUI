@extends('private._private.index')

@section('content')
 
    @php $form = ONE::form('phases')
        ->settings(["model" => isset($phase) ? $phase : null])
        ->show('PhasesController@edit', 'PhasesController@delete', ['id' => isset($phase) ? $phase->id : null], 'PhasesController@index', ['id' => isset($phase) ? $phase->id : null])
        ->create('PhasesController@store', 'PhasesController@index', ['id' => isset($phase) ? $phase->id : null])
        ->edit('PhasesController@update', 'PhasesController@show', ['id' => isset($phase) ? $phase->id : null])
        ->open();
    @endphp
    
    {!! Form::oneText('name', trans('privatePhases.name'), isset($phase) ? $phase->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneDate('start_date', trans('privatePhases.startDate'), isset($phase) ? $phase->start_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'start_date']) !!}
    {!! Form::oneDate('end_date', trans('privatePhases.endDate'), isset($phase) ? $phase->end_date : null, ['class' => 'form-control oneDatePicker', 'id' => 'end_date']) !!}
    {!! Form::hidden('mp_id', isset($mp_id) ? $mp_id : null) !!}
    
    {!! $form->make() !!}
     
@endsection

@section('scripts')
    <script>
        $('.dates').datepicker({
            format: 'dd/mm/yyyy',
            language: 'pt',
            orientation: 'bottom auto'
        })
    </script>
@endsection