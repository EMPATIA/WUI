@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            @php $form = ONE::form('entityGroups', trans('privateEntityGroups.details'), 'wui', 'entity_groups')
                    ->settings(["model" => isset($entityGroup) ? $entityGroup->entity_group_key : null, 'id' => isset($entityGroup) ? $entityGroup->entity_group_key : null])
                    ->show('EntityGroupsController@edit', 'EntityGroupsController@delete', ['groupTypeKey' => isset($entityGroup) ? $entityGroup->entity_group_key : null], 'EntityGroupsController@showGroups', ['groupTypeKey' => isset($entityGroup) ? $entityGroup->group_type->group_type_key : null])
                    ->create('EntityGroupsController@store', 'EntityGroupsController@showGroups', ['groupTypeKey' => isset($groupTypeKey) ? $groupTypeKey : null])
                    ->edit('EntityGroupsController@update', 'EntityGroupsController@show', ['groupTypeKey' => isset($entityGroup) ? $entityGroup->entity_group_key : null])
                    ->open();
            @endphp

            {!! Form::oneText('name', trans('privateEntityGroups.name'), isset($entityGroup) ? $entityGroup->name : null, ['class' => 'form-control', 'id' => 'name', 'required']) !!}
            {!! Form::oneText('designation', trans('privateEntityGroups.designation'), isset($entityGroup) ? $entityGroup->designation : null, ['class' => 'form-control', 'id' => 'designation', 'required']) !!}

            @if(ONE::actionType('entityGroups') == 'show')
                {!! Form::oneText('groupTypeCode', trans('privateEntityGroups.group_type_code'), isset($entityGroup) ? $entityGroup->group_type->code : null, ['class' => 'form-control', 'id' => 'groupTypeCode']) !!}
            @endif
            {!! Form::hidden('groupTypeKey', isset($groupTypeKey) ? $groupTypeKey : null, ['id' => 'groupTypeKey']) !!}

            @if(ONE::actionType('entityGroups') == 'show')
                {!! Form::oneText('parentGroupName', trans('privateEntityGroups.parent_entity_group_name'), !empty($entityGroup->entity_group->name) ? $entityGroup->entity_group->name : null, ['class' => 'form-control', 'id' => 'parentGroupName']) !!}
            @endif

            @if(ONE::actionType('entityGroups') == 'create' || 'update')

                @if(!empty($tree))
                    <div class="form-group">
                        <label for="parentEntityGroupKey">{{ trans('privateEntityGroups.parent_group') }}</label>
                        <select class="form-control" id="parentEntityGroupKey" name="parentEntityGroupKey">
                            <option value="" selected>{{ trans('privateEntityGroups.select_value') }}</option>
                            @foreach($tree as $item)
                                <option value="{!! $item['item']->entity_group_key !!}">
                                    @for($i=0; $i<$item['position'];$i++ )
                                        >
                                    @endfor
                                    {!! $item['item']->name !!}</option>
                            @endforeach
                        </select>
                    </div>
                @else
                    <div class="form-group">
                        <select class="form-control hidden" id="parentEntityGroupKey" name="parentEntityGroupKey">
                            <option value="" selected>{{ trans('privateEntityGroups.select_value') }}</option>
                        </select>
                    </div>
                @endif
            @endif
            {!! $form->make() !!}
        </div>
    </div>

@endsection

@section('scripts')

    <script type="text/javascript">
        @if(ONE::actionType('entityGroups') != 'create')
            var array = ['{{ isset($entityGroup) ? $entityGroup->entity_group_key : null }}', '{{ isset($groupTypeKey) ? $groupTypeKey : null }}']
getSidebar('{{ action("OneController@getSidebar") }}', 'entity_group_details', array , 'entityGroupDetails');
                @endif
        var selected = null;
        selected = '{!! !empty($entityGroup->entity_group->entity_group_key) ? $entityGroup->entity_group->entity_group_key: null !!}';

        $(document).ready(function() {

            if(selected) {
                $('#parentEntityGroupKey').val(selected);
            }
        });
    </script>
@endsection

