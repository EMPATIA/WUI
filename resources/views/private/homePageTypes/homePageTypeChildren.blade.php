@extends('private._private.index')
@section('content')



{{--    @if((ONE::actionType('homePageTypes') == 'show' || ONE::actionType('homePageTypes') == 'show') && !isset($homePageType->parent))--}}
        <!-- List Types from this Group -->
        <input type="hidden" id="home_page_type_key" value="{{ $homePageType->home_page_type_key }}">

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('homePageType.title') }} children </h3>
            </div>
            <div class="box-body">
                <table id="homePageGroupTypes_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                    <thead>
                    <tr>

                        <th>{{ trans('homePageType.home_page_type_key') }}</th>
                        <th>{{ trans('homePageType.name') }}</th>
                        <th>parent_id</th>
                        <th>@if(Session::get('user_role') == 'admin' || ONE::verifyUserPermissionsCreate('cm', 'home_page_types_children')){!! ONE::actionButtons(['home_page_type_key' => isset($homePageType) ? $homePageType->home_page_type_key : ""], ['create' => 'HomePageTypesController@createGroupType']) !!}@endif</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    {{--@endif--}}
@endsection

@section('scripts')
    <script>
        $('#type_code').on('change', function() {
            var val = $('#type_code').val();
            if(val != 'group'){
                $('#parent').show();
            }
            else{
                $('#parent').val('');
                $('#parent').hide();
            }
        });
    </script>
    <script>
        $(function () {


            $('#homePageGroupTypes_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                ajax: {
                    "url" : '{!! action('HomePageTypesController@getGroupTypesTable') !!}',
                    "type": "POST",
                    "data" : {
                        "home_page_type_key" : $('#home_page_type_key').val(),
                    }
                },
                columns: [

                    { data: 'home_page_type_key', name: 'home_page_type_key', width: "20px" },
                    { data: 'name', name: 'name' },
                    { data: 'parent_id', name: 'parent_id' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
                ],
                order: [['1', 'asc']]
            });
        });
    </script>
@endsection