@extends('private._private.index')

@section('content')
    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('groupTypes', trans('privateGroupTypes.details'))
                ->settings(["model" => isset($groupTypeKey) ? $groupTypeKey : null, 'id' => isset($groupTypeKey) ? $groupTypeKey : null])
                ->show('GroupTypesController@edit', 'GroupTypesController@delete', ['id' => isset($groupTypeKey) ? $groupTypeKey : null], 'GroupTypesController@index', ['id' => isset($groupTypeKey) ? $groupTypeKey : null])
                ->create('GroupTypesController@store', 'GroupTypesController@index', ['id' => isset($groupTypeKey) ? $groupTypeKey : null])
                ->edit('GroupTypesController@update', 'GroupTypesController@show', ['groupTypeKey' => isset($groupTypeKey) ? $groupTypeKey : null])
                ->open();
            @endphp


            {!! Form::oneText('name', trans('privateLayouts.name'), $groupType->name ?? null, ['class' => 'form-control', 'id' => 'name']) !!}
            {!! Form::oneText('code', trans('privateGroupTypes.code'), $groupType->code ?? null, ['class' => 'form-control', 'id' => 'code']) !!}

            {!! $form->make() !!}
        </div>
    </div>

@endsection
