@extends('private._private.index')

@section('header_styles')
@endsection
@section('content')
    <div class="row">
        <div class="col-md-12">
            @php
            $form = ONE::form('node', trans('privateMPQuestionnaires.questionnaires'))
                    ->settings(["model" => $operator ?? null, 'id' => $operator->operator_key ?? null])
                    ->show('MPQuestionnairesController@edit', 'MPQuestionnairesController@delete', ['operatorKey' => $operator->operator_key ?? null],
                            'MPsController@showConfigurations', ['mp_key' => $operator->mp->mp_key ?? null])
                    ->create('MPQuestionnairesController@store', 'MPsController@showConfigurations', ['mp_key' => $operator->mp->mp_key ?? null])
                    ->edit('MPQuestionnairesController@update', 'MPQuestionnairesController@index', ['operatorKey' => $operator->operator_key ?? null,'mp_key' => $operator->mp->mp_key ?? null])
                    ->open();
            @endphp
            {!! Form::hidden('operator_key', ($operator->operator_key ?? null)) !!}
            {!! Form::hidden('mp_key', ($operator->mp->mp_key ?? null)) !!}

            {!! Form::oneSelect('questionnaire_key',['name' => trans('privateMPQuestionnaires.questionnaire'),'description' => trans("privateMPQuestionnaires.questionnaire_help")], $questionnaires, $operator->component_key ?? null, $questionnaires[$operator->component_key] ?? null, ['class' => 'form-control'] ) !!}

            {!! $form->make() !!}
        </div>
    </div>
@endsection
@section('scripts')
    <script>
        $(function() {
            getSidebar('{{ action("OneController@getSidebar") }}', 'configurations', '{{$operator->mp->mp_key ?? null}}', 'mp_configurations' )
        });
    </script>

@endsection
