@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateAccessMenu.menus') }}</h3>
        </div>

        <div class="box-body">
            <div class='row'>
                @if(!empty($menu))
                    <div id="menu-buttons" class="col-sm-11">
                        <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('privateAccessMenu.expandAll') }}</button>
                        <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('privateAccessMenu.collapseAll') }}</button>
                    </div>
                @endif
                @if(!empty($accessM))
                    <div class="col-sm-1 pull-right" id="createMenu">
                        <span class="pull-right">
                            <a class="btn btn-flat empatia"
                               title="" data-toggle="tooltip" href="{{action('MenusController@create', $accessM) }} " data-original-title="{{trans('privateAccessMenu.createMenu')}}">
                                <i class="fa fa-plus"></i>  {{ trans('privateAccessMenu.createMenu') }}
                            </a>
                        </span>
                    </div>
                @endif
            </div>
            @if(!empty($menu))
                <div class="dd" id="nestable">
                    {!! ONE::buildNestedMenu($menu, $accessM) !!}
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center">{{trans('privateAccessMenu.empty')}}</div>
                </div>
            @endif
        </div>
    </div>
@endsection

@section('scripts')
    <script>
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

                $.post('{{ URL::action('MenusController@updateOrder')}}',
                        {
                            _token: "{{ csrf_token() }}",
                            source: details.sourceId,
                            destination: details.destId,
                            order: JSON.stringify(order),
                            rootOrder: JSON.stringify(rootOrder)
                        },
                        function (data) {
                            // console.log('data '+data);
                        })
                        .done(function (result) {
                            if (result == 'true') {
                                $("#menuReorder").fadeIn(100).delay(1500).fadeOut(100);
                            } else {
                                indexFiltered();
                            }
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

        function indexFiltered(){
            window.location = '{{ action('AccessMenusController@show', $accessM) }}';
        }

    </script>
@endsection



