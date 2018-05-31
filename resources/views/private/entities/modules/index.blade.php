@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-cube"></i> {{ trans('privateModules.entityModuleTitle') }}</h3>
                </div>
                <div class="box-body">
                    <table id="entityModules_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th>{{ trans('privateModules.ModuleName') }}</th>
                            <th width="10%">
                                {!! ONE::actionButtons(null, ['add' => 'EntitiesDividedController@addEntityModule']) !!}
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@if(ONE::actionType('entities') == "show")
@section('scripts')
    <script>
        $(function () {
            $('#entityModules_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableEntityModule") !!}',
                columns: [
                    { data: 'name', name: 'name' }
                ]
            });
        });
    </script>
@endsection
@endif