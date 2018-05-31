@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('kiosk.addProposal') }}</h3>
        </div>

        <div class="box-body">
            {!! ONE::messages() !!}
            <table id="proposals_list" class="table table-hover table-striped dataTable no-footer table-responsive">
                <thead>
                <tr>
                    <th>{{ trans('kiosk.proposal') }}</th>
                    <th></th>
                </tr>
                </thead>
            </table>
        </div>
        
        <div class="box-footer">
            <a class="btn btn-flat empatia" href='{!! action("KiosksController@show", $kioskKey ) !!}'><i class="fa fa-arrow-left"></i> {{ trans('kiosk.back') }}</a>
        </div>        
    </div>

@endsection


@section('scripts')
    <script>

        $(function () {
            <!-- kiosk_key --> 
            $('#proposals_list').DataTable({
                language: {
                    url: '{!! asset('/datatableLang/'.Session::get('LANG_CODE').'.json') !!}',
                    search: '<a class="btn searchBtn" id="searchBtn"><i class="fa fa-search"></i></a>'
                },
                processing: true,
                serverSide: true,
                bDestroy: true,
                ajax: {
                    "url": '{!! action("KiosksController@tableAddProposal", $kioskKey ) !!}',
                },
                columns: [
                    { data: 'title', name: 'title' },
                    { data: 'action', name: 'action', searchable: false, orderable: false, width: "30px" },                    
                ],
                order: [['0', 'asc']]
            });
        });

    </script>
@endsection