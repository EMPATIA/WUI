@extends('private._private.index')
@section('content')
    @php $form = ONE::form('homePageTypes', trans('privateHomePageTypes.details'), 'cm', 'home_page_type')
            ->settings(["model" => isset($homePageType) ? $homePageType : null,'id'=>isset($homePageType) ? $homePageType->home_page_type_key : null])
            ->show('HomePageTypesController@edit', 'HomePageTypesController@delete', ['key' => isset($homePageType) ? $homePageType->home_page_type_key : null], 'HomePageTypesController@index')
            ->create('HomePageTypesController@store', 'HomePageTypesController@index')
            ->edit('HomePageTypesController@update', 'HomePageTypesController@show', ['key' => isset($homePageType) ? $homePageType->home_page_type_key : null])
            ->open();
    @endphp

    @php /*
        {!! Form::oneSelect('type_code', trans('homePageType.type'), isset($types) ? $types : null, isset($homePageType->type_code) ? $homePageType->type_code : null, isset($homePageType->type_code) ? $homePageType->type_code : null, ['class' => 'form-control', 'id' => 'type_code']) !!}
    */ @endphp
    @if(ONE::actionType('homePageTypes') == 'show')
        {!! Form::oneSelect('type_code', trans('homePageType.type'), isset($types) ? $types : null, isset($homePageType->type_code) ? $homePageType->type_code : null, isset($homePageType->type_code) ? $homePageType->type_code : null, ['class' => 'form-control', 'id' => 'type_code']) !!}
    @else
        @if(!isset($homePageType->parent))
            {!! Form::oneText('type_code', '', 'group', ['class' => 'form-control hidden', 'id' => 'type_code']) !!}
        @else
            {!! Form::oneSelect('type_code', trans('homePageType.type'), isset($types) ? $types : null, isset($homePageType->type_code) ? $homePageType->type_code : null, isset($homePageType->type_code) ? $homePageType->type_code : null, ['class' => 'form-control', 'id' => 'type_code']) !!}
        @endif
    @endif

    <div id="parent" hidden>
        {!! Form::oneSelect('parent_key', trans('menus.parent'), isset($parents) ? $parents : null, isset($homePageType->parent) ? $homePageType->parent->home_page_type_key : null, isset($homePageType->parent) ? $homePageType->parent->name : null, ['class' => 'form-control', 'id' => 'parent_key']) !!}
    </div>

    {!! Form::oneText('name', trans('homePageType.name'), isset($homePageType) ? $homePageType->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
    {!! Form::oneText('code', trans('homePageType.code'), isset($homePageType) ? $homePageType->code : null, ['class' => 'form-control', 'id' => 'code']) !!}

    {!! $form->make() !!}



@endsection

@section('scripts')
    <script>
        $('#type_code').on('change', function() {
            var val = $('#type_code').val();
            if(val != 'group'){
                $('#parent').show();
            }
            else{
                $('#parent').val('');
                $('#parent').hide();
            }
        });
    </script>

@endsection