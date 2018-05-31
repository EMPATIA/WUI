@extends('private._private.index')


@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privatePages.title') }}</h3>
        </div>
        <div class="box-body">
          <div class="form-group  "><label for="end_date">{!! trans("privateCbs.filterDate") !!}</label>
           <div class="date">
               <div class="row">
                   <div class="col-12 col-md-4">
                       <div class="input-group date"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                        <input class="form-control oneDatePicker"  id="start_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="start_date" type="text" value="" required>
                       </div>
                   </div>
                   <div class="col-12 col-md-4">
                       <div class="input-group date"><span class="input-group-addon"><i class="glyphicon glyphicon-th"></i></span>
                        <input class="form-control oneDatePicker"  id="end_date" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="end_date" type="text" value="" onchange="selectDateFilter()" required>
                       </div>
                   </div>
               </div>
         </div>
          <br><br>
            <table id="pages-list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('privatePages.title') }}</th>
                    <th>{{ trans('privatePages.publishDate') }}</th>
                    <th>@if(Session::get('user_role') == 'admin'){!! ONE::actionButtons($type, ['create' => 'ContentsController@create']) !!}@endif</th>
                </tr>
                </thead>
            </table>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $(function () {
            $('#pages-list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                  ajax: '{!! action('ContentsController@contentsDataTable', $type, $start_date ?? null) !!}',
                columns: [

                    { data: 'title', name: 'title' },
                    { data: 'publish_date', name: '"publish_date' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "50px" },
                ],
                order: [['1', 'asc']]
            });
        });



        function selectDateFilter() {

            var url = "{{action('ContentsController@contentsDataTable', $type)}}";

            var start_date = '';
            var end_date = '';

            if ($('#start_date').val() != '' && $('#end_date').val() != '' ){

                start_date = $('#start_date').val();
                end_date=$('#end_date').val();

                  if ($('#start_date').val()< $('#end_date').val() ){

            url = url+'?start_date='+start_date+'?end_date='+end_date;

            var tableUsers = $('#pages-list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax:url,

               columns: [

                   { data: 'title', name: 'title' },
                   { data: 'publish_date', name: '"publish_date' },
                   { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },
               ],
                order: [['1', 'asc']],

            });
        }

        }
        }


    </script>
@endsection
