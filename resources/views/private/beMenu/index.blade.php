@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateBEMenu.title') }}</h3>
        </div>

        <div class="box-body">
            <div class='row'>
                <div id="menu-buttons" class="col-10">
                    <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('privateBEMenu.expandAll') }}</button>
                    <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('privateBEMenu.collapseAll') }}</button>
                </div>
                <div class="col-2 pull-right" id="createMenu">
                    <span class="pull-right">
                        <a href="#import-confirm" data-toggle="modal" data-target="#import-confirm" style="color: #fffbfe;"
                           class="btn btn-sm btn-flat btn-warning" title="{{trans('privateBEMenu.import_menu')}}">
                            <i class="fa fa-download"></i>
                        </a>
                        @if(!empty($currentUser) && !empty($userKey))
                            <a href="{{action('UserBEMenuController@create',['f'=>'BEMenu']) }}" style="color: #fffbfe;" 
                                class="btn btn-sm btn-flat btn-success" title="{{trans('privateBEMenu.createMenu')}}">
                        @elseif(!empty($userKey))
                            <a href="{{action('UserBEMenuController@userCreate',['userKey' => $userKey, 'f'=>'BEMenu']) }}" style="color: #fffbfe;" 
                                class="btn btn-sm btn-flat btn-success" title="{{trans('privateBEMenu.createMenu')}}">
                        @else
                            <a href="{{action('BEMenuController@create',['f'=>'BEMenu']) }}" style="color: #fffbfe;" 
                                class="btn btn-sm btn-flat btn-success" title="{{trans('privateBEMenu.createMenu')}}">
                        @endif                            
                            <i class="fa fa-plus"></i>
                        </a>
                    </span>
                </div>
            </div>
            @if(isset($menuData->ordered_elements) && !empty($menuData->ordered_elements))
                <div class="dd" id="nestable">
                    {!! ONE::buildNestedBEMenuManagement($menuData->ordered_elements, 0, 0, !empty($userKey), (empty($currentUser) && !empty($userKey))?$userKey:false) !!}
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center">
                        {{trans('privateBEMenu.empty')}}
                        <br>
                        <a href="#import-confirm" data-toggle="modal" data-target="#import-confirm" style="color: #fffbfe;"
                           class="btn btn-sm btn-flat btn-warning" title="{{trans('privateBEMenu.import_menu')}}">
                            <i class="fa fa-download"></i>
                            {{trans('privateBEMenu.import_menu_text')}}
                        </a>
                    </div>
                </div>
            @endif
        </div>
    </div>

    <div class="modal fade" id="import-confirm" tabindex="-1" role="dialog" aria-labelledby="import-confirm-label" aria-hidden="true">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="card-header">
                    <h3 class="modal-title" id="import-confirm-label">{{trans('privateBEMenu.import_menu_modal_title')}}</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    {{trans('privateBEMenu.import_menu_modal_title')}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        {{trans('privateBEMenu.back')}}
                    </button>
                    <button type="button" class="btn btn-warning" id="import-confirmation-button">
                        {{trans('privateBEMenu.import_menu_modal_confirm')}}
                    </button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        $('.dd').nestable({
            maxDepth: 2,
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

                @if(!empty($currentUser) && !empty($userKey))
                    $.post('{{ URL::action('UserBEMenuController@updateOrder') }}',
                @elseif(!empty($userKey))
                    $.post('{{ URL::action('UserBEMenuController@userUpdateOrder',["userKey" => $userKey]) }}',
                @else
                    $.post('{{ URL::action('BEMenuController@updateOrder') }}',
                @endif
                    {
                        _token: "{{ csrf_token() }}",
                        source: details.sourceId,
                        destination: details.destId,
                        order: JSON.stringify(order),
                        rootOrder: JSON.stringify(rootOrder)
                    })
                    .done(function (result) {
                        toastr.success('{{ trans('privateBEMenu.successfully_moved_menu_item') }}');
                        @if(!(empty($currentUser) && !empty($userKey)))
                            goSidebar("private");
                        @endif
                    })
                    .fail(function () {
                        toastr.error('{{ trans('privateBEMenu.error_moving_menu_item') }}');
                        window.location.reload();
                    })
                    .always(function () {
                    });
            }
        });

        $('.dd').nestable('collapseAll');

        $('#menu-buttons').on('click', function (e) {
            var target = $(e.target),
                action = target.data('action');
            if (action === 'expand-all')
                $('.dd').nestable('expandAll');
            if (action === 'collapse-all')
                $('.dd').nestable('collapseAll');
        });

        $("#import-confirmation-button").on("click",function(e) {
            @if(!empty($currentUser) && !empty($userKey))
                window.location.href = "{{action('UserBEMenuController@import') }}";
            @elseif(!empty($userKey))
                window.location.href = "{{action('UserBEMenuController@userImport',["userKey" => $userKey]) }}";
            @else
                window.location.href = "{{action('BEMenuController@import') }}";
            @endif
        })
    </script>
@endsection



