@extends('private._private.index')

@section('content')

    <!-- Entity Users Table -->
    @if(ONE::actionType('entityGroups') == 'show')

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityGroups.users_list') }}</h3>
            </div>
            <div class="box-body">
                <table id="users_list" class="table table-responsive table-hover">
                    <thead>
                    <tr>
                        <th>{{ trans('privateEntityGroups.name') }}</th>
                        <th>{{ trans('privateEntityGroups.email') }}</th>
                        <th width="10%">
                            @if(ONE::verifyUserPermissions('wui', 'entity_groups_users', 'create'))
                                <a href="#" class="btn btn-flat btn-add-small " data-toggle="modal" data-target="#managersModal" title="" data-original-title="Adicionar"><i class="fa fa-plus"></i></a>
                            @endif
                        </th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>


    @endif
    <!-- // Entity Users Table -->


    <!-- Entity Users/Managers Modal -->
    <div class="modal fade" tabindex="-1" role="dialog" id="managersModal" >
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="card-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                    <h4 class="modal-title">{{trans("privateEntityGroups.add_user_to_entity_group")}}</h4>
                </div>
                <div class="modal-body">



                    <!-- Entity Users Table -->
                    <div class="box box-primary">
                        <div class="box-header">
                            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityGroups.entity_users_list') }}</h3>
                        </div>
                        <div class="box-body">
                            <table id="entity_users_list" class="table table-responsive table-hover">
                                <thead>
                                <tr>
                                    <th>{{ trans('privateEntityGroups.name') }}</th>
                                    <th>{{ trans('privateEntityGroups.email') }}</th>
                                </tr>
                                </thead>
                            </table>
                        </div>
                    </div>
                    <!-- // Entity Users Table -->
                </div>
                <div class="modal-footer">
                    <button id="closeModal" type="button" class="btn btn-secondary" data-dismiss="modal">{{trans("privateEntityGroups.close")}}</button>
                </div>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
    <!-- // Entity Users/Managers Modal -->

@endsection


@section('scripts')
    <script>

        $(document).ready(function(){

            //call to datatable funtion where group users are listed
            showPostManagerDataTable();

            // remove horizontal scrollbar from datatables
            $('.table-responsive-container').css({
                "overflow-x": "hidden",
            });


        });
        //remove users from group
        $(document).on("click", "a.btn-danger", function(event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href')
                ,success: function(response) {
                    toastr.success('{{ trans('privateEntityGroups.user_removed_from_group') }}', '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                    showPostManagerDataTable();
                }
            })
            return false;

        });

        //add entity users to group on modal click
        $(document).on("click", ".user", function(event) {
            event.preventDefault();
            $.ajax({
                url: $(this).attr('href')
                ,success: function(response) {
                    showEntityUsersDataTable();
                    toastr.success('{{ trans('privateEntityGroups.user_added_to_group') }}', '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                    showPostManagerDataTable();
                }
            })
            return false;

        });

        //loads entity users datatable on modal call
        $('#managersModal').on('show.bs.modal', function (event) {
            showEntityUsersDataTable();
        });

        //datatable functions
        function showPostManagerDataTable() {


            var showManagers = 0;
            var showUsers = 0;

            if( $("#advancedFilter").val() != null && jQuery.inArray( "showManagers", $("#advancedFilter").val() )  > -1){
                showManagers = 1;
            }
            if( $("#advancedFilter").val() != null && jQuery.inArray( "showUsers", $("#advancedFilter").val() ) > -1 ){
                showUsers = 1;
            }

            $('#users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    "url" : '{!! action('EntityGroupsController@tableGroupUsers') !!}',
                    "data" : {
                        "showManagers" : showManagers,
                        "showUsers": showUsers,
                        "entityGroupKey": '{{ $entityGroupKey ?? null }}',
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},/*
                     {data: 'role', name: 'role'},*/
                    {data: 'action', name: 'action', searchable: false, orderable: false, width: "30px"},
                ],
                order: [['1', 'asc']]
            });
        }

        function showEntityUsersDataTable() {

            var table = $('#entity_users_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },

                processing: true,
                serverSide: true,
                bDestroy: true,

                ajax: {
                    "url" : '{!! action('EntityGroupsController@tableEntityUsers') !!}',
                    "data" : {
                        "entityGroupKey": '{{ $entityGroupKey ?? null }}'
                    }
                },
                columns: [
                    {data: 'name', name: 'name'},
                    {data: 'email', name: 'email'},
                    {data: 'action', name: 'action', searchable: false, orderable: false, width: "30px"},
                ],
                order: [['1', 'asc']]
            });

            $('.user').parent().on('click',function () {
                //console.log($(this));
                alert();
                showEntityUsersDataTable();

            })


        }
    </script>

@endsection

