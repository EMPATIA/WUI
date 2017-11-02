@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateBudget.budgets') }}</h3>
        </div>

        <div class="box-body">
            <table id="budgets_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateBudget.id') }}</th>
                    <th>{{ trans('privateBudget.categoryId') }}</th>
                    <th>{{ trans('privateBudget.value') }}</th>
                    <th>{{ trans('privateBudget.mpId') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'BudgetsController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>
        
        $(function () {
            $('#budgets_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('BudgetsController@tableBudgets') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'category_id', name: 'category_id' },
                    { data: 'value', name: 'value' },
                    { data: 'mp_id', name: 'mp_id' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection




