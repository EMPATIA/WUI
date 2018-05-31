@extends('private._private.index')
@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchart.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchartMain.css')}}" />
@endsection
@section('content')
    @if(ONE::actionType('mp') == "show")
        <div class="flowchart-mp">
            <div id="chart_container" style="height: 300px">
                <div class="" id="flowchart_mp"></div>
            </div>
        </div>

        <div class="row" style="padding-top: 20px">
            <div class="col-12">
                <div class="card flat">
                    <div class="card-header" style="font-weight: bold">
                        {{trans('privateMPs.configurations')}}
                        <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateMPs.press_in_operator_to_view_and_configure") }}</span>
                    </div>
                    <div class="card-body">
                        @php $finish = true; @endphp
                        @foreach($mpOperators as $operator)
                            @if(isset($operator->operator_type) && $operator->operator_type->code == 'start')
                                <a href="{{(empty($operator->component_key) ? action('MPOperatorsController@create',$operator->operator_key) : action('MPOperatorsController@show',['operator_key' => $operator->operator_key,'component_key' => $operator->component_key]))}}" type="button" class="btn {{(empty($operator->component_key) ? 'btn-secondary' : 'btn-success')}} {{'btn_'.$operator->operator_type->code}} btn-block btn_operator_config">
                                    @if(!empty($operator->component_key)) <span class="glyphicon glyphicon-ok"></span> @endif
                                    {{ trans("privateMPs.start_and_end_date") }}
                                </a>
                            @elseif(isset($operator->operator_type) && $operator->operator_type->code != 'start' && $operator->operator_type->code != 'end')
                                @if($operator->configurable)
                                    <a href="{{(empty($operator->component_key) ? action('MPOperatorsController@create',$operator->operator_key) : action('MPOperatorsController@show',['operator_key' => $operator->operator_key,'component_key' => $operator->component_key]))}}" type="button" class="btn {{(empty($operator->component_key) ? 'btn-secondary' : 'btn-success')}} {{'btn_'.$operator->operator_type->code}} btn-block btn_operator_config">
                                        @if(!empty($operator->component_key)) <span class="glyphicon glyphicon-ok"></span> @endif
                                        {{isset($operator->operator_type->name) ? $operator->operator_type->name : ''}}</a>
                                @else
                                    <a type="button" class="btn btn-secondary btn-block btn_operator_config" disabled>{{isset($operator->operator_type->name) ? $operator->operator_type->name : ''}}</a>
                                @endif
                            @endif

                            @php empty($operator->component_key) ? ($finish = false) : null @endphp
                        @endforeach
                    </div>
                    @if($finish && isset($mp) && $mp->finished)
                        <div class="card-footer" style="font-weight: bold">
                            <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateMPs.mp_configuration_finished") }}</span>
                        </div>
                    @endif
                </div>
            </div>
            @if($finish && isset($mp) && !$mp->finished)
                <div class="col-12" id="operator_configuration_stepper">
                    <a href="{{ action('MPsController@updateState',$mp->mp_key ?? null)}}" type="button" class="btn btn-secondary btn_operator_config">
                        {{ trans("privateMPs.finish_configuration") }}
                        <span class="form-text oneform-help-block" style="margin:6px 0px 0px;font-size:10px;">{{ trans("privateMPs.finish_configuration_help") }}</span>
                    </a>
                </div>
            @endif

        </div>
    @endif
@endsection

@section('scripts')

    <script src="{{ asset("js/flowchart/flowchart.js") }}"></script>
    <script src="{{ asset("js/flowchart/panzoom.js") }}"></script>
    <script src="{{ asset("js/flowchart/jquery-ui.js") }}"></script>
    <script>

        $(document).ready(function() {
            var $flowchart = $('#flowchart_mp');

            var $container = $flowchart.parent();

            var cx = $flowchart.width() / 2;
            var cy = $flowchart.height() / 2;


            // Panzoom initialization...
            $flowchart.panzoom();

            // Centering panzoom
            $flowchart.panzoom('pan', -cx + $container.width() / 2, -cy + $container.height() / 2);

            // Panzoom zoom handling...
            var possibleZooms = [0.5, 0.75, 1, 2];
            var currentZoom = 1;
            $container.on('mousewheel.focal', function( e ) {
                e.preventDefault();
                var delta = (e.delta || e.originalEvent.wheelDelta) || e.originalEvent.detail;
                var zoomOut = delta ? delta < 0 : e.originalEvent.deltaY > 0;
                currentZoom = Math.max(0, Math.min(possibleZooms.length - 1, (currentZoom + (zoomOut * 2 - 1))));
                $flowchart.flowchart('setPositionRatio', possibleZooms[currentZoom]);
                $flowchart.panzoom('zoom', possibleZooms[currentZoom], {
                    animate: false,
                    focal: e
                });
            });

            @if(ONE::actionType('mp') != "show")
            // Apply the plugin on a standard, empty div...
            $flowchart.flowchart({
                multipleLinksOnInput: true,
                multipleLinksOnOutput: true,
                defaultLinkColor: '#0e2d8c'
            });

            //delete selected operator
            $flowchart.parent().siblings('.delete_selected_button').click(function() {
                var operatorId = $flowchart.flowchart('getSelectedOperatorId');
                var operator = $flowchart.flowchart('getOperatorData',operatorId);
                switch (operator.type){
                    case 'start':
                    case 'end':
                        $('#'+operator.type).removeAttr('disabled');
                        break;
                }
                $flowchart.flowchart('deleteSelected');

            });

            @else
            // Apply the plugin on a standard, empty div...
            $flowchart.flowchart({
                canUserEditLinks: false,
//                canUserMoveOperators: false,
                multipleLinksOnInput: true,
                multipleLinksOnOutput: true,
                defaultLinkColor: '#0e2d8c'

            });
            @endif



            //Get data from Flowchart
            $('.get_data').click(function() {
                var data = $flowchart.flowchart('getData');
                $('#flowchart_data').val(JSON.stringify(data));
            });

            //Reset Flowchart
            $('.set_data').click(function() {
                var data = JSON.parse($('#flowchart_data').val());
                $flowchart.flowchart('setData', data);
            });

            //Flowchart - print diagram form database
            $flowchart.flowchart('setData', JSON.parse('{!! (isset($mp->diagram_code) ?  $mp->diagram_code : '{}') !!}'));


                    @if(ONE::actionType('mp') != "show")
            var $draggableOperators = $('.draggable_operator');

            //get operator data
            function getOperatorData($element,$id) {
                var nbInputs = parseInt($element.data('nb-inputs'));
                var nbOutputs = parseInt($element.data('nb-outputs'));
                var type = $element.data('type');
                var operatorKey = $element.data('operator-key');
                var data = {
                    properties: {
                        title: $element.text(),
                        inputs: {},
                        outputs: {},
                        class: "flowchart-" + type + "-operator"
                    },
                    type : type,
                    operator_key: operatorKey

                };

                var i = 0;
                for (i = 0; i < nbInputs; i++) {
                    data.properties.inputs['input_'+ $id +'_'+ i] = {
                        label: 'In'
                    };
                }
                for (i = 0; i < nbOutputs; i++) {
                    data.properties.outputs['output_'+ $id +'_' + i] = {
                        label: 'Out'
                    };
                }

                return data;
            }

            var operatorId = 0;

            //start draggable event for the operators
            $draggableOperators.draggable({
                cursor: "move",
                opacity: 0.7,
                helper: function(e) {
                    var $this = $(this);
                    var data = getOperatorData($this,0);
                    return $flowchart.flowchart('getOperatorElement', data);
                },
                stop: function(e, ui) {
                    var $this = $(this);
                    var elOffset = ui.offset;
                    var containerOffset = $container.offset();
                    if (elOffset.left > containerOffset.left &&
                        elOffset.top > containerOffset.top &&
                        elOffset.left < containerOffset.left + $container.width() &&
                        elOffset.top < containerOffset.top + $container.height()) {

                        var flowchartOffset = $flowchart.offset();

                        var relativeLeft = elOffset.left - flowchartOffset.left;
                        var relativeTop = elOffset.top - flowchartOffset.top;

                        var positionRatio = $flowchart.flowchart('getPositionRatio');
                        relativeLeft /= positionRatio;
                        relativeTop /= positionRatio;

                        //verify if operator exists in chart
                        do{
                            operatorId++;
                            var operatorData = $flowchart.flowchart('getOperatorData', 'op_' + operatorId);
                        }while(Object.keys(operatorData).length != 0);

                        var data = getOperatorData($this,operatorId);
                        data.left = relativeLeft;
                        data.top = relativeTop;
                        $flowchart.flowchart('createOperator', 'op_' + operatorId, data);
                        operatorId++;
                        switch(data.type){
                            case 'start':
                            case 'end':
                                $('#'+data.type).attr('disabled','disabled');
                                break;
                        }
                    }

                }
            });

            //submit mp form and get diagram data
            $('#mp').submit(function(){
                var data = $flowchart.flowchart('getData');
                $('#flowchart_data').val(JSON.stringify(data));
            });


            //delete button press event - if operator is select and delete button is pressed, the operator will be deleted
            $('html').keyup(function(e){
                if(e.keyCode == 46) {
                    var operatorId = $flowchart.flowchart('getSelectedOperatorId');
                    var operator = $flowchart.flowchart('getOperatorData',operatorId);
                    if(Object.keys(operator).length == 0){
                        console.log('no_operator_selected_to_delete');
                        return false;
                    }
                    console.log('operator_selected_to_delete_id_'+operatorId);
                    switch (operator.type){
                        case 'start':
                        case 'end':
                            $('#'+operator.type).removeAttr('disabled');
                            break;
                    }
                    $flowchart.flowchart('deleteSelected');
                }
            });
            @endif

        });

    </script>

    <script>
        $('.btn_start').hover(function(){
            $('.flowchart-start-operator').addClass('flowchart-start-hover');
            $('.flowchart-end-operator').addClass('flowchart-end-hover');

        });
        $('.btn_start').mouseout(function(){
            $('.flowchart-start-operator').removeClass('flowchart-start-hover');
            $('.flowchart-end-operator').removeClass('flowchart-end-hover');
        });

        $('.btn_idea').hover(function(){
            $('.flowchart-idea-operator').addClass('flowchart-idea-hover');
        });
        $('.btn_idea').mouseout(function(){
            $('.flowchart-idea-operator').removeClass('flowchart-idea-hover');

        });
        $('.btn_proposal').hover(function(){
            $('.flowchart-proposal-operator').addClass('flowchart-proposal-hover');
        });
        $('.btn_proposal').mouseout(function(){
            $('.flowchart-proposal-operator').removeClass('flowchart-proposal-hover');
        });

        $('.btn_vote').hover(function(){
            $('.flowchart-vote-operator').addClass('flowchart-vote-hover');
        });
        $('.btn_vote').mouseout(function(){
            $('.flowchart-vote-operator').removeClass('flowchart-vote-hover');

        });

        $('.btn_questionnaire').hover(function(){
            $('.flowchart-questionnaire-operator').addClass('flowchart-questionnaire-hover');
        });
        $('.btn_questionnaire').mouseout(function(){
            $('.flowchart-questionnaire-operator').removeClass('flowchart-questionnaire-hover');
        });
        $('.btn_review').hover(function(){
            $('.flowchart-review-operator').addClass('flowchart-review-hover');
        });
        $('.btn_review').mouseout(function(){
            $('.flowchart-review-operator').removeClass('flowchart-review-hover');
        });

        $('.btn_face_to_face').hover(function(){
            $('.flowchart-face_to_face-operator').addClass('flowchart-face_to_face-hover');
        });

        $('.btn_face_to_face').mouseout(function(){
            $('.flowchart-face_to_face-operator').removeClass('flowchart-face_to_face-hover');
        });

        $('.btn_scan_machines').hover(function(){
            $('.flowchart-scan_machines-operator').addClass('flowchart-scan_machines-hover');
        });
        $('.btn_scan_machines').mouseout(function(){
            $('.flowchart-scan_machines-operator').removeClass('flowchart-scan_machines-hover');
        });


    </script>
@endsection