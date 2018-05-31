@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntityGroups.entity_groups') }}</h3>
        </div>

        <div class="box-body">
            <div class='row'>
                @if(isset($entityGroups) && !$entityGroups->isEmpty())
                    <div id="menu-buttons" class="col-sm-5">
                        <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('privateEntityGroups.expandAll') }}</button>
                        <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('privateEntityGroups.collapseAll') }}</button>
                    </div>
                @endif
                <div class="col-sm-7 pull-right" id="createMenu">
                    <span class="pull-right">
                        <a style="color: #fffbfe;" class="btn btn-flat empatia"
                           title="" data-toggle="tooltip" href="{{action('EntityGroupsController@create', ["groupTypeKey" => is_null($groupTypeKey) ? null : $groupTypeKey]) }} " data-original-title="{{trans('privateEntityGroups.create_entity_groups')}}">
                            {{ trans('privateEntityGroups.createEntityGroup') }}
                            <i class="fa fa-plus"></i>
                        </a>
                    </span>
                </div>
            </div>
            @if(isset($entityGroups) && !$entityGroups->isEmpty())
                <div class="dd" id="nestable">
                    {!! ONE::buildNestedEntityGroups($entityGroups) !!}
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center">{{trans('privateEntityGroups.empty')}}</div>
                </div>
            @endif
        </div>
    </div>
@endsection


@section('scripts')
    <script>

        {{--        getSidebar('{{ action("OneController@getSidebar") }}', 'entity_groups_tree', '{{$groupTypeKey ?? null }}' , 'entityGroup' );--}}

                $('.dd').nestable({
            dropCallback: function (details) {

                var order = [];
                $("li[data-id='" + details.destId + "']").find('ol:first').children().each(function (index, elem) {
                    order[index] = $(elem).attr('data-id');

                });
                if (order.length === 0) {
                    var rootOrder = [];
                    $("#nestable > ol > li").each(function (index, elem) {
                        rootOrder[index] = $(elem).attr('data-id');
                    });
                }
                $.post('{{ URL::action('EntityGroupsController@updateOrder')}}',
                    {
                        _token: "{{ csrf_token() }}",
                        source: details.sourceId,
                        destination: details.destId,
                        order: JSON.stringify(order),
                        rootOrder: JSON.stringify(rootOrder)
                    },
                    function (data) {
                    })
                    .done(function (result) {

                    })
                    .fail(function () {
                    })
                    .always(function () {
                    });
            }
        });

        $('.dd').nestable('collapseAll');

        $('#menu-buttons').on('click', function (e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all') {
                $('.dd').nestable('expandAll');
            }
            if (action === 'collapse-all') {
                $('.dd').nestable('collapseAll');
            }
        })

        $(document).ready(function() {

            //sort root Entity Groops
            var element = $("#nestable > ol");

            $("#nestable > ol > li").sort(sortRootGroups).appendTo(element);

        })

        //Sorts by position value, root groups
        function sortRootGroups(groupA, groupB){
            return ($(groupB).data('order')) < ($(groupA).data('order')) ? 1 : -1;
        }

    </script>
@endsection
