
@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" style="margin-bottom: 25px">
        <p><label for="contentStatusComment" style="margin-left:5px; margin-top:5px;">{{trans('privateCbs.pad')}}</label>  {{$cb->title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment" style="margin-left:5px;">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment" style="margin-left:5px; margin-bottom:5px;">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>
    <div class="box box-primary">
        <div class="box-body">
            <div class="card-title">{{ trans('privateCbs.list') }}</div>
            <br>

            <table id="parameters_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCbs.parameter_title') }}</th>
                    <th>{{ trans('privateCbs.parameter_author') }}</th>
                    <th>{{ trans('privateCbs.parameter_type') }}</th>
                    <th>{{ trans('privateCbs.parameter_code') }}</th>
                    <th>@if(Session::get('user_role') == 'admin'){!! ONE::actionButtons(['type'=>$type,'cbKey'=>$cb->cb_key], ['create' => 'CbsParametersController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $('#parameters_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            processing: true,
            serverSide: true,
            ajax: '{!! action('CbsParametersController@getIndexTableParameters',['type'=>$type,'cbKey'=>$cb->cb_key]) !!}',
            columns: [
                { data: 'title', name: 'title' },
                { data: 'name', name: 'name' },
                { data: 'code', name: 'code' },
                { data: 'parameter_code', name: 'parameter_code' },
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
            ],
            order: [['1', 'asc']]
        });
    </script>
@endsection
