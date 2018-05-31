@extends('private._private.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title"><i class="fa fa-file-text-o"></i> {{ trans('privateEntities.templates') }}</h3>
                </div>
                <div class="box-body">
                    <table id="layouts_list" class="table table-striped dataTable no-footer table-responsive">
                        <thead>
                        <tr>
                            <th width="90%">{{ trans('privateEntities.templateName') }}</th>
                            <th width="10%">
                                @if(Session::get('user_role') == 'admin')
                                    {!! ONE::actionButtons(null, ['add' => 'EntitiesDividedController@addLayout']) !!}
                                @endif
                            </th>
                        </tr>
                        </thead>
                    </table>
                </div>
            </div>
        </div>
    </div>

@endsection

@section('scripts')
    <script>

        $(function () {
            $('#layouts_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action("EntitiesDividedController@tableLayoutsEntity") !!}',
                columns: [
                    { data: 'name', name: 'name' },
                    { data: 'action', name: 'action', searchable: false, orderable: false },
                ],
                order: [['0', 'asc']]
            });
        });
    </script>
@endsection