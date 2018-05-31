@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateQuestionnaire.list') }}</h3>
        </div>

        <div class="box-body">
            <table id="questionnaire_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateQuestionnaire.title') }}</th>
                    <th>@if(Session::get('user_role') == 'admin'){!! ONE::actionButtons(null, ['create' => 'QuestionnairesController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>

        $(function () {
            $('#questionnaire_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,

                ajax: '{!! action('QuestionnairesController@getIndexTable') !!}',
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['0', 'asc']]
            });

        });

    </script>
@endsection
