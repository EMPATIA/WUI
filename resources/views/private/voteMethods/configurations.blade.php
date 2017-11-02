@extends('private._private.index')

@section('content')
@if(ONE::actionType('methods') == 'show')
    <div class="box-private">
        <div class="box-header">
            <h3 class="box-title">{{ trans('privateVoteMethods.configurations') }}</h3>
        </div>
        <div class="box-body">
            <table id="config_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateVoteMethods.id') }}</th>
                    <th>{{ trans('privateVoteMethods.name') }}</th>
                    <th>{!! ONE::actionButtons(['methodId'=>$voteMethod->id], ['create' => 'VoteMethodConfigController@create']) !!}</th>
                </tr>
                </thead>
            </table>
        </div>
        <div class="card-footer">
            <a class="btn btn-flat btn-success" href=" {!!  action('VoteMethodsController@index') !!}"><i class="fa fa-arrow-left"></i> {!! trans('privateVoteMethods.back') !!}</a>
        </div>
    </div>
@endif
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