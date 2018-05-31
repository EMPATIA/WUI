@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateLoginLevels.reorder_levels') }}</h3>
        </div>
        <div class="box-body">
            <div class=''>
                <div class="" id="createMenu">
                    <span class="">
                        <a style="color: #fffbfe;" class="btn btn-sm btn-flat btn-create"
                           title="" data-toggle="tooltip" href="{{action('LoginLevelsController@create') }} " data-original-title="{{trans('privateLoginLevels.create_login_level')}}">
                                <i class="fa fa-plus"></i>
                        </a>
                    </span>
                </div>
            </div>
            @if(!empty($loginLevels))
                <div class="dd" id="nestable">
                    {!! ONE::buildStackedLoginLevels($loginLevels) !!}
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center">{{trans('privateLoginLevels.empty')}}</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        var nestableList = $("#nestable").find("ol");

        //Sort Levels
        nestableList.find("li").sort(sortLevels).appendTo(nestableList);

        //Sorts by level
        function sortLevels(levelA, levelB){
            return ($(levelB).data('order')) < ($(levelA).data('order')) ? 1 : -1;
        }

        $('.dd').nestable({
            maxDepth: 1,
            dropCallback: function () {
                var order = [];
                nestableList.find("li").each(function (index, elem) {
                    order[index] = $(elem).attr('data-id');
                });

                $.post('{{ URL::action('LoginLevelsController@updateOrder')}}',
                        {
                            _token: "{{ csrf_token() }}",
                            order: JSON.stringify(order)
                        })
                        .done(function (result) {
                        })
                        .fail(function () {
                            toastr.error('{{trans('privateLoginLevels.update_order_error_message') }}', '{{trans('privateLoginLevels.error') }}', {timeOut: 2000,positionClass: "toast-bottom-right"});
                            window.location.reload();
                        });
            }
        });
    </script>
@endsection