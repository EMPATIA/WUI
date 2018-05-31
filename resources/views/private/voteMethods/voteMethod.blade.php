@extends('private._private.index')

@section('content')

    @php $form = ONE::form('methods')
            ->settings(["model" => isset($voteMethod) ? $voteMethod : null])
            ->show('VoteMethodsController@edit', 'VoteMethodsController@delete', ['id' => isset($voteMethod) ? $voteMethod->id : null], 'VoteMethodsController@index', ['id' => isset($voteMethod) ? $voteMethod->id : null])
            ->create('VoteMethodsController@store', 'VoteMethodsController@index', ['id' => isset($voteMethod) ? $voteMethod->id : null])
            ->edit('VoteMethodsController@update', 'VoteMethodsController@show', ['id' => isset($voteMethod) ? $voteMethod->id : null])
            ->open();
    @endphp
    {!! Form::oneText('code', trans('privateVoteMethods.code'), isset($voteMethod->code) ? $voteMethod->code : null, ['class' => 'form-control', 'id' => 'code']) !!}
    {!! Form::oneSelect('method_group_id', trans('privateVoteMethods.method_group'), isset($voteMethodsGroup) ? $voteMethodsGroup : null,  isset($voteMethod->method_group_id) ? $voteMethod->method_group_id : null ,isset($voteMethod->method_group->name) ? $voteMethod->method_group->name: null  ,  ['class' => 'form-control', 'required'] ) !!}

   @if(ONE::actionType('methods') == 'show')
        {!! Form::oneText('name', trans('privateVoteMethods.name'), isset($voteMethod->name) ? $voteMethod->name : null, ['class' => 'form-control', 'id' => 'name']) !!}
        {!! Form::oneTextArea('description', trans('privateVoteMethods.description'), isset($voteMethod->description) ? $voteMethod->description : null, ['class' => 'form-control', 'id' => 'description']) !!}
    @endif

    @if(isset($languages) and ONE::actionType('methods') != 'show')
        @foreach($languages as $language)
            @php $form->openTabs('tab-translation-' . $language->code, $language->name); @endphp
            <div style="padding: 10px;">
                {!! Form::oneText('name_'.$language->code .'', trans('privateVoteMethods.name'), isset($methodTranslation[$language->code]['name']) ? $methodTranslation[$language->code]['name'] : null, ['class' => 'form-control', 'id' => 'name_'.$language->code .'']) !!}
                {!! Form::oneTextArea('description_'.$language->code .'', trans('privateVoteMethods.description'), isset($methodTranslation[$language->code]['description']) ? $methodTranslation[$language->code]['description'] : null, ['class' => 'form-control', 'id' => 'description_'.$language->code .'']) !!}
            </div>
        @endforeach
        @php $form->makeTabs(); @endphp
    @endif
    {!! $form->make() !!}



@endsection
@section('scripts')
    <script>
        $(function () {

            $('#config_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('VoteMethodConfigController@tableConfigs',['methodId'=>isset($voteMethod->id)? $voteMethod->id : null]) !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
    </script>
@endsection