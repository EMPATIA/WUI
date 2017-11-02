
@extends('private._private.index')

@section('content')
    @include('private.cbs.tabs')

    <div class="card flat topic-data-header" >
        <p><label for="contentStatusComment">{{trans('privateCbs.pad')}}</label>  {{$cb->title}}</p>
        @if(!empty($cbAuthor))
        <p><label for="contentStatusComment">{{trans('privateCbs.author')}}</label>
            <a href="{{action('UsersController@show', ['userKey' => $cbAuthor->user_key, 'role' => $cbAuthor->role ?? null])}}">{{$cbAuthor->name}}</a>
        </p>
        @endif
        <p><label for="contentStatusComment">{{trans('privateCbs.start_date')}}</label>  {{$cb->start_date}}</p>
    </div>
    <div class="box box-primary box-default-margin">
        <div class="box-body">
            <div style="margin-bottom:50px">
                <div class="pull-right">
                    @if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('cb', 'pad_votes'))
                        <a href="{{ action('CbsVoteController@create', ['type'=>$type,'cbKey'=>$cb->cb_key]) }}" class="btn btn-flat empatia">
                            <i class="fa fa-plus"></i>
                            {{ trans('privateCbs.create') }}
                        </a>
                    @endif
                </div>
                <div class="card-title">{{ trans('privateCbs.votes') }}</div>

            </div>



           <table id="votes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
               <thead>
               <tr>
                    <th>{{ trans('privateCbs.vote_name') }}</th>
                    <th>{{ trans('privateCbs.vote_method') }}</th>
                    <!-- <th>{{ trans('privateCbs.statistics') }}</th> -->
                    <th></th>
                </tr>
                </thead>
            </table>
            {{--
              <span class="btn btn-flat btn-warning" ></span> - {{ trans('privateCbsVote.statistics') }}
            --}}
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        // Votes List
        $('#votes_list').DataTable({
            language: {
                url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
            },
            processing: true,
            serverSide: true,
            ajax: '{!! action('CbsVoteController@getIndexTableVote',['type'=>$type,'cbKey'=>$cb->cb_key]) !!}',
            columns: [
                { data: 'name', name: 'name' },
                { data: 'title', name: 'title' },
                /* { data: 'statistics', name: 'statistics',width: "10px", searchable: false, orderable: false}, */
                { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
            ],
            order: [['1', 'asc']]
        });
    </script>
@endsection
