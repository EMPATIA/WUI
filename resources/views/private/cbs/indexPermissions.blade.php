@extends('private._private.index')

@section('content')

    @include('private.cbs.tabs')

    <div class="box box-primary">
        <div class="box-body">
            <div class="adv-search">

                <form name="advSearch" class="form-horizontal">
                    <fieldset>

                        <!-- Advanced search -->
                        <label class="filterBy-title">{{ trans("privateCbsPermissions.advancedSearch")}}</label>

                        <div class="form-group">
                            <label class=" form-control-label" for="advancedFilter">{{ trans("privateCbsPermissions.advFilter")}}</label>
                            <div class="">
                                <select id="advancedFilter" name="advancedFilter" multiple="multiple" class="select2privatePosts" onchange="showCbPermissionsDataTable()">
                                    <option value="groups" selected>{{ trans("privateCbsPermissions.groups")}}</option>
                                    <option value="users" selected>{{ trans("privateCbsPermissions.users") }}</option>
                                </select>
                            </div>
                        </div>
                    </fieldset>
                </form>

            </div>

            <table id="all_groups" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privateCbsPermissions.type') }}</th>
                    <th>{{ trans('privateCbsPermissions.name') }}</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>

@endsection


@section('scripts')
    <script>

        showCbPermissionsDataTable();

        function showCbPermissionsDataTable(){

            var filterType = $("#advancedFilter").val();
            {{--var filterType = ['{{ $typeFilter ?? null}}'];--}}

            $('#all_groups').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                responsive: true,
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    'url': '{!! action('CbsController@getGroupsPads', ['type'=>$type,'cbKey'=>$cbKey]) !!}',
                    "type": "post",
                    "data": {
                        filterType : filterType
                    }
                },
                columns: [
                    {data: 'type', name: 'type', width: "100px" },
                    {data: 'title', name: 'title'},
                ]
            });
        }

        $(".select2privatePosts").select2({
            templateResult: function (data) {
                var $res = $('<span></span>');
                var $check = $('<input type="checkbox" class="inputCheckBoxSelect2" style="margin-right:5px;" />');

                $res.text(data.text);

                if (data.element) {
                    $res.prepend($check);
                    $check.prop('checked', data.element.selected);
                }

                return $res;
            }
        });
    </script>
@endsection

@section('header_styles')
    <style>
        .adv-search{
            margin-top: 20px;
            margin-bottom: 30px;
        }

        .select2-container--default .select2-search--inline .select2-search__field {
            border: 0;
        }
        .select2-container--default .select2-results__option[aria-selected="true"] {
            background-color: #f4f4f5;
        }
        .select2privatePosts{
            width: 80%;
        }
    </style>
@endsection
