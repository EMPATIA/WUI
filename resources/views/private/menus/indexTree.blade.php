@extends('private._private.index')

@section('content')

    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('title.menus') }}</h3>
        </div>
        <div class="box-body">
            <div class='row'>
                <div class="col-md-5">

                    <select id="accessType" onchange="indexFiltered()" class="form-control input-small">
                       <option value="">-- {!! trans("menu.select") !!} --</option>
                      @foreach ($accessMenu as $type)
                        <option value="{{$type->id}}" {{ (isset($accessM) && ($accessM == $type->id)) ? 'selected=selected' : "" }} >{{$type->name}}</option>
                      @endforeach
                    </select>
                </div>
                @if(!empty($menu))
                <div id="menu-buttons" class="col-md-4">
                    <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('menus.expand_all') }}</button>
                    <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('menus.collapse_all') }}</button>
                </div>
                @endif
                @if(!empty($accessM))
                <div class="@if(!empty($menu)) col-md-3 @else col-md-7 @endif" id="createMenu">
                    <span class="pull-right">
                        <a style="color: #fffbfe;" class="btn btn-flat btn-success"
                           title="" data-toggle="tooltip" href="javascript:createMenu()" data-original-title="{{trans('menus.create_menu')}}">
                                <i class="fa fa-plus"></i>
                            </a>
                    </span>
                </div>
                @endif
            </div>
            @if(!empty($menu))
            <div class="dd" id="nestable">
                {!! ONE::buildNestedMenu($menu) !!}
            </div>
            @else
            <div class="row">
                <div class="col-md-12 text-center">{{trans('menus.empty')}}</div>
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
        
        function createMenu(){
            var e = document.getElementById("accessType");
            var menuType = e.options[e.selectedIndex].value;
            window.location = '{{ action('MenusController@create', $accessM) }}' + '/' + menuType;
        }
        
        function indexFiltered(){
            var e = document.getElementById("accessType");
            var menuType = e.options[e.selectedIndex].value;
            window.location = '{{ action('MenusController@index') }}' + '/' + menuType;
        }

    </script>
@endsection
