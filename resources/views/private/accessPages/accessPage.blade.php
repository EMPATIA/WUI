@extends('private._private.index')

@section('content')

{!! ONE::form('accessPages')
    ->show('AccessPagesController@edit', 'AccessPagesController@destroy', ['id' => isset($accessPage) ? $accessPage->id : null], 'AccessPagesController@index', ['id' => isset($accessPage) ? $accessPage->id : null])
    ->create('AccessPagesController@store', 'AccessPagesController@index', ['id' => isset($accessPage) ? $accessPage->id : null])
    ->edit(isset($accessPage) ? $accessPage : null, 'AccessPagesController@update', 'AccessPagesController@show', ['id' => isset($accessPage) ? $accessPage->id : null])
    ->addSelectEditCreate('access_type_id', trans('privateAccessPages.accessType'), Form::select('access_type_id', isset($accessType) ? $accessType : null, isset($accessPage) ? $accessPage->access_type_id : null, ['class' => 'form-control', 'id' => 'access_type_id']), isset($accessType['name']) ? $accessType['name'] : null)
    ->addField('name', trans('privateAccessPages.name'), Form::text('name', isset($accessPage) ? $accessPage->name : null, ['class' => 'form-control', 'id' => 'name']), isset($accessPage) ? $accessPage->name : null)
    ->addField('description', trans('privateAccessPages.description'), Form::textarea('description', isset($accessPage) ? $accessPage->description : null, ['class' => 'form-control', 'id' => 'description']), isset($accessPage) ? $accessPage->description : null)
    ->addField('active', trans('privateAccessPages.active'), Form::text('active', isset($accessPage) ? $accessPage->active : null, ['class' => 'form-control', 'id' => 'active']), isset($accessPage) ? $accessPage->active : null)
    ->addField('entity_id', trans('privateAccessPages.entityId'), Form::hidden('entity_id', isset($entity_id) ? $entity_id : null, ['class' => 'form-control', 'id' => 'entity_id']), isset($entity_id) ? $entity_id : null)
    ->make()
!!}
     
@endsection

