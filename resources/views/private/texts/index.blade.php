@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateTexts.titleList') }}</h3>
        </div>

        <div class="box-body">
            <table id="texts_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateTexts.title') }}</th>
                    <th>{{ trans('privateTexts.tag') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'TextsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#texts_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('TextsController@tableTexts') !!}',
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'tag', name: 'tag' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection



