@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateCurrencies.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="currencies_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCurrencies.id') }}</th>
                    <th>{{ trans('privateCurrencies.currency') }}</th>
                    <th>{{ trans('privateCurrencies.symbol_left') }}</th>
                    <th>{{ trans('privateCurrencies.symbol_right') }}</th>
                    <th>{{ trans('privateCurrencies.code') }}</th>
                    <th>{!! ONE::actionButtons(null, ['create' => 'CurrenciesController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            $('#currencies_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: '{!! action('CurrenciesController@tableCurrencies') !!}',
                columns: [
                    { data: 'id', name: 'id', width: "20px" },
                    { data: 'currency', name: 'currency' },
                    { data: 'symbol_left', name: 'symbol_left' },
                    { data: 'symbol_right', name: 'symbol_right' },
                    { data: 'code', name: 'code' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });

        });
        
    </script>
@endsection


