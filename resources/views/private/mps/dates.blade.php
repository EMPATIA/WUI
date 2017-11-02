@extends('private._private.index')

@section('header_styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('node', trans('privateMPs.details'))
                ->settings(["model" => isset($operator) ? $operator->operator_key : null, 'id' => isset($operator) ? $operator->operator_key : null])
                ->show('MPOperatorsController@edit', null, ['operator_key' => isset($operator) ? $operator->operator_key : null,'component_key' => isset($operator) ? $operator->component_key : null],
                    'MPOperatorsController@index', ['operator_key' => isset($operator) ? $operator->operator_key : null,'mp_key' => isset($mp) ? $mp->mp_key : null ])
                ->create('MPOperatorsController@store', 'MPOperatorsController@index', ['operator_key' => isset($operator) ? $operator->operator_key : null,'mp_key' => isset($mp) ? $mp->mp_key : null ])
                ->edit('MPOperatorsController@update', 'MPOperatorsController@index',  ['operator_key' => isset($operator) ? $operator->operator_key : null,'mp_key' => isset($mp) ? $mp->mp_key : null])
                ->open();
            @endphp
            {!! Form::hidden('mp_key', isset($mp) ? $mp->mp_key : null) !!}
            {!! Form::oneDate('start_date',['name' => trans('privateMPs.mp_start_date'),'description' => trans("privateMPs.help_mp_start_date")], isset($mp) ? substr($mp->start_date, 0, 10) : null, ['id' => 'startDate','required']) !!}
            {!! Form::oneDate('end_date',['name' => trans('privateMPs.mp_end_date'),'description' => trans("privateMPs.help_mp_end_date")], isset($mp) ? substr($mp->end_date, 0, 10) : null, ['id' => 'endDate','required']) !!}


            {!! $form->make() !!}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'configurations', '{{$mp->mp_key ?? null}}', 'mp_configurations' )
        });
    </script>

@endsection
