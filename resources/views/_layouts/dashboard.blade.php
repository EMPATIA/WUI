<!-- Details / resume - Top dashboard -->
<div class="row row-sm-eq-height row-md-eq-height row-lg-eq-height">
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <!-- Registered users -->
        <div class="dashboard-box">
            <div class="row">
                <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                    <img src="{{ asset('/images/dashboard-registered.svg') }}" alt="registered">
                </div>
                <div class="col-8 col-sm-12 col-lg-8">
                    {{ trans('private.registered_users') }}
                    <span class="info-box-number registered">--</span>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <!-- Logged users -->
        <div class="dashboard-box">
            <div class="row">
                <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                    <img src="{{ asset('/images/dashboard-logged.svg') }}" alt="logged">
                </div>
                <div class="col-8 col-sm-12 col-lg-8">
                    {{ trans('private.logged_users') }}
                    <span class="info-box-number logged_users">--</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <!-- Total pads -->
        <div class="dashboard-box">
            <div class="row">
                <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                    <img src="{{ asset('/images/dashboard-ideas.svg') }}" alt="ideas">
                </div>
                <div class="col-8 col-sm-12 col-lg-8">
                    {{ trans('private.total_pads') }}
                    <span class="info-box-number ideas">--</span>
                </div>
            </div>
        </div>
    </div>
    <div class="col-xs-12 col-sm-3 col-md-3 col-lg-3">
        <!-- Total comments -->
        <div class="dashboard-box">
            <div class="row">
                <div class="col-4 col-sm-12 col-lg-4 text-xs-center">
                    <img src="{{ asset('/images/dashboard-comments.svg') }}" alt="comments">
                </div>
                <div class="col-8 col-sm-12 col-lg-8">
                    {{ trans('private.total_comments') }}
                    <span class="info-box-number comments">--</span>
                </div>
            </div>
        </div>
    </div>
</div>


<!-- update status modal -->
<div class="modal fade" tabindex="-1" role="dialog" id="updateStatusModal">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{{trans("private.update_status")}}</h4>
            </div>
            <div class="modal-body">
                <div class="panel panel-default flat">
                    {!! Form::hidden('topicKeyStatus','', ['id' => 'topicKeyStatus']) !!}
                    <div class="panel-heading">{{trans('private.add_comments')}}</div>
                    <div class="panel-body">

                        <input type="text" id="status_type_code" class="form-control hidden" name="status_type_code"
                               value="">

                        <input id="cb_key_hidden" type="hidden" name="cb_key_hidden" value="">
                        <input id="type_hidden" type="hidden" name="type_hidden" value="">

                        <div class="form-group">
                            <label for="contentStatusComment">{{trans('private.private_comment')}}</label>
                            <textarea class="form-control" rows="5" id="contentStatusComment"
                                      name="contentStatusComment" style="resize: none;"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="contentStatusPublicComment">{{trans('private.public_comment')}}</label>
                            <textarea class="form-control" rows="5" id="contentStatusPublicComment"
                                      name="contentStatusPublicComment" style="resize: none;"></textarea>
                        </div>

                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default" id="closeUpdateStatus">{{trans("private.close")}}</button>
                <input type="hidden" name="_token" value="{{ csrf_token() }}">
                <button type="button" class="btn btn-primary"
                        id="updateStatus">{{trans("private.save_changes")}}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<div class="modal fade" tabindex="-1" role="dialog" id="showAbuses">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span>
                </button>
                <h4 class="modal-title">{{trans("privatePropositionModeration.show_abuses")}}</h4>
            </div>
            <div class="modal-body" id="abuses-body" style='overflow-y:auto'>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-default"
                        id="closeShowAbuses">{{trans("privatePropositionModeration.close")}}</button>
            </div>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<br>
<a href="#" class="btn-seemore pull-right" onclick='$("#myModal").modal()'>
    <i class="fa fa-plus" aria-hidden="true"></i>
</a>
<br>

<div>
    <div class="row no-gutters dashboard">
        @if(Session::has('userDashboardElements'))
            @php
                if(!empty($entityDashboardElements))
                   $dashBoardElements = collect($entityDashboardElements)->keyBy('id')->toArray();
            @endphp

            @foreach(Session::get('userDashboardElements') as $currentUserElement)
                @if( !empty($dashBoardElements[$currentUserElement->dashboard_element_id]) )
                    @php
                        if (!empty(collect($currentUserElement->configurations ?? [])->where("code", "title")->first()->pivot->value ?? ""))
                            $dashboardElementTitle = collect($currentUserElement->configurations ?? [])->where("code", "title")->first()->pivot->value;
                        else if (!empty($dashBoardElements->title ?? ""))
                            $dashboardElementTitle = $dashBoardElements->title;
                        else
                            $dashboardElementTitle = "";

                        if (!empty(collect($currentUserElement->configurations ?? [])->where("code", "description")->first()->pivot->value ?? ""))
                            $dashboardElementDescription = collect($currentUserElement->configurations ?? [])->where("code", "description")->first()->pivot->value;
                        else if (!empty($dashBoardElements->description ?? ""))
                            $dashboardElementDescription = $dashBoardElements->description;
                        else
                            $dashboardElementDescription = "";
                    @endphp


                    <div class="col-12 col-lg-6 col-xl-4 ElementToDrag" data-item-id="{{ $currentUserElement->id }}">
                        <div class="box box-info box-side-by-side dashboardbox">
                            <div class="box-header with-border">
                                <h3 class="dashboard-title text-truncate" title="{{ $dashboardElementDescription }}">
                                    {{ $dashboardElementTitle }}
                                </h3>

                                <div class="box-tools pull-right">
                                    <button class="btn btn-box-tool reload-dashboard-button dash-btn"
                                            onclick='loadDashboardElement{{ $currentUserElement->id }}()'>
                                        <i class="fa fa-refresh" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-box-tool dash-btn config-dash-btn" data-id='{{ $currentUserElement->id }}'>
                                        <i class="fa fa-cog" aria-hidden="true"></i>
                                    </button>
                                    <button class="btn btn-box-tool draggable-dashboard-button dash-btn">
                                        <i class="fa fa-arrows" aria-hidden="true"></i>
                                    </button>
                                    <button type="button" class="btn btn-box-tool dash-btn" data-widget="remove"
                                            onclick="removeDashboardItem({{ $currentUserElement->id }});">
                                        <i class="fa fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="box-body dashboard-body">
                                <div class="dashboard-content">
                                    <div id="dash_board_element_{{ $currentUserElement->id }}" class="dash-board-list"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endif
            @endforeach
        @endif
    </div>
</div>


<link rel="stylesheet" href="//code.jquery.com/ui/1.12.1/themes/base/jquery-ui.css">
<script src="{{ asset("js/jquery-ui.js") }}"></script>
<script>
    @if(Session::has('userDashboardElements'))
        @foreach(Session::get('userDashboardElements') as $currentUserElement)
            @if(!empty($dashBoardElements[$currentUserElement->dashboard_element_id]))
                function loadDashboardElement{{ $currentUserElement->id }}() {
                    $(".dashboard div[data-item-id='{{ $currentUserElement->id }}'").find("button.reload-dashboard-button").attr("disabled","disabled");

                    HTMLLoader =
                        '<div class="text-center dashboard-element-message">' +
                            '<i aria-hidden="true" class="fa fa-spinner fa-spin fa-4x fa-fw"></i> ' +
                            '<h4>{{ trans("private.loading") }}</h4>' +
                        '</div>';
                    $("#dash_board_element_{{ $currentUserElement->id }}").html(HTMLLoader);

                    $.ajax({
                        method: 'POST', // Type of response and matches what we said in the route
                        url: "{{action('DashBoardElementsController@makeRequestAccordingToDashBoardElement')}}", // This is the url we gave in the route
                        data: {
                            "_token": "{{ csrf_token() }}",
                            "dashBoardElement": '<?php echo json_encode($currentUserElement);?>',
                            "code": '{{ $dashBoardElements[$currentUserElement->dashboard_element_id]->code }}'
                        }, success: function (response) {
                            $(".dashboard div[data-item-id='{{ $currentUserElement->id }}'").find("button.reload-dashboard-button").removeAttr("disabled");
                            $("#dash_board_element_{{ $currentUserElement->id }}").html(response);
                        }, error: function (jqXHR, textStatus, errorThrown) {
                            $(".dashboard div[data-item-id='{{ $currentUserElement->id }}'").find("button.reload-dashboard-button").removeAttr("disabled");
                            HTMLError =
                                '<div class="text-center dashboard-element-message">' +
                                '<i aria-hidden="true" class="fa fa-frown-o fa-4x"></i> ' +
                                '<h4>{{ trans("privateDashBoardElements.failed_to_load_data") }}</h4>' +
                                '</div>';
                            $("#dash_board_element_{{ $currentUserElement->id }}").html(HTMLError);
                        }
                    });
                }
            @endif
        @endforeach
    @endif

    $(document).ready(function() {
        localStorage.clear();
        @if(Session::has('userDashboardElements'))
            @foreach(Session::get('userDashboardElements') as $currentUserElement)
                @if(!empty($dashBoardElements[$currentUserElement->dashboard_element_id]))
                    loadDashboardElement{{ $currentUserElement->id }}();
                @endif
            @endforeach
        @endif

        $(".dashboard").sortable({
            forceHelperSize: true,
            items: '> div.ElementToDrag',
            scroll: true,
            handle: 'button.draggable-dashboard-button',
            cancel: '',
            placeholder: "col-12 col-sm-6 col-xl-4 ElementToDrag dashboard-sortable-placeholder",
            cursor: "move",
            helper: "clone",
            opacity: 0.7,
            stop: function(event, ui) {
                $(".dashboard").sortable("refresh");
                var positions = new Array();
                $(".dashboard > div.ElementToDrag").each(function(index, element) {
                    positions[index] = $(element).attr("data-item-id");
                });

                $.ajax({
                    method: 'PUT', // Type of response and matches what we said in the route
                    url: "{{action('DashBoardElementsController@reorderUserDashBoardElements')}}", // This is the url we gave in the route
                    data: {
                        "_token": "{{ csrf_token() }}",
                        "positions": positions
                    }, beforeSend: function () {

                    }, success: function (response) {

                    }, error: function (jqXHR, textStatus, errorThrown) { // What to do if we fail
                        toastr.error('{{ trans('privateDashBoardElements.failed_to_store_new_dashboard_elements_order') }}', '', {timeOut: 3000,positionClass: "toast-bottom-right"});
                        location.reload();
                    }
                });
            }
        });
    });

    $(document).on('click', '.config-dash-btn', function () {
        var element_id = $(this).attr('data-id');
        var elementIcon = $(this).find("i.fa");
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('DashBoardElementsController@loadConfigurationsView')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                "id": element_id,

            }, beforeSend: function () {
                elementIcon.addClass("fa-spin");
            }, success: function (response) { // What to do if we succeed
                elementIcon.removeClass("fa-spin");
                $("#dashboard_config").html(response);
                $("#dashboard_config").append("<input id='id' name='id' value='" + element_id + "' type='hidden'>");
                $('#configureDashBoardElementModal').modal('show')
            }
        });
    });
    $(document).on('click', '.add_dashboard_element', function () {
        var dashboard_element_id = $(this).attr('data-dashboard-element-id');
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('DashBoardElementsController@loadConfigurationsView')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                "dashboard_element_id": dashboard_element_id,

            }, beforeSend: function () {
                /*$("#registration").fadeOut();
                $("#voting").fadeIn();
        */
            }, success: function (response) { // What to do if we succeed
                $('#myModal').modal('hide');
                $("body").css("padding-right", 0);

                $("#dashboard_config").html(response);

                $("#dashboard_config").append("<input name='dash_board_element' value='" + dashboard_element_id + "' type='hidden'>");

                $('#configureDashBoardElementModal').modal('show')
            }
        });
    });
    $(document).on('click', '#add_new_dashboard',function () {
        $('#configureDashBoardElementModal').modal('hide');
        $("body").css("padding-right", 0);

        var datastring = $('#dashboard_config').find('select,input').serializeArray();
        $.ajax({
            method: 'POST', // Type of response and matches what we said in the route
            url: "{{action('DashBoardElementsController@setUserDashBoardElement')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                "inputs": datastring
            }, beforeSend: function () {

            }, success: function (response) { // What to do if we succeed
                location.reload();
            }
        });

    });

    function removeDashboardItem(id) {
        $.ajax({
            method: 'DELETE', // Type of response and matches what we said in the route
            url: "{{action('DashBoardElementsController@unsetUserDashBoardElement')}}", // This is the url we gave in the route
            data: {
                "_token": "{{ csrf_token() }}",
                "id": id
            }, beforeSend: function () {},
            success: function (response) {
                location.reload();
            }
        });
    }
</script>


<!-- Modal -->
<div class="modal fade" id="myModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"
                    id="exampleModalLabel">{{trans('privateDashBoardElements.addNewDashBoardElement')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                @if(!empty($entityDashboardElements))
                    <ul class="list-group">
                        @foreach($entityDashboardElements as $availableEntityElement)
                            <a href="#" class="list-group-item list-group-item-action add_dashboard_element"
                               data-dashboard-element-id="{{$availableEntityElement->id}}">
                                {{ $availableEntityElement->title }}
                            </a>
                        @endforeach
                    </ul>
                @else
                    {{trans('privateDashBoardElements.theEntityDoesNotHaveDashboardElementsAvailable')}}
                @endif

            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                {{--
                        <button type="button" class="btn btn-primary">Save changes</button>
                --}}
            </div>
        </div>
    </div>
</div>
<!-- Modal configurations -->
<div class="modal fade" id="configureDashBoardElementModal" tabindex="-1" role="dialog"
     aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="card-header">
                <h5 class="modal-title"
                    id="exampleModalLabel">{{trans('privateDashBoardElements.addNewDashBoardElementConfigurations')}}</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form id="dashboard_config">

                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button id="add_new_dashboard" type="button" class="btn btn-primary">Save changes</button>
            </div>
        </div>
    </div>
</div>