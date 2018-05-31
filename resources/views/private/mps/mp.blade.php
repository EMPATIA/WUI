@extends('private._private.index')

@section('header_styles')
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchart.css')}}" />
    <link rel="stylesheet" href="{{ asset('css/flowchart/flowchartMain.css')}}" />
@endsection
@section('content')
    <!-- Form -->
    @php $form = ONE::form('mp', trans('privateMPs.details'), 'mp', 'mp')
        ->settings(["model" => isset($mp) ? $mp : null,'id'=>isset($mp) ? $mp->mp_key : null])
        ->show((isset($mp) && $mp->finished) ? null : 'MPsController@edit',(isset($mp) && $mp->finished) ? null : 'MPsController@delete', ['key' => isset($mp) ? $mp->mp_key : null], 'MPsController@index')
        ->create('MPsController@store', 'MPsController@index')
        ->edit('MPsController@update', 'MPsController@show', ['key' => isset($mp) ? $mp->mp_key : null])
        ->open();
    @endphp
    <!-- Mp details -->
    <div class="row">
        <div class="col-12">
            @if(count($languages) > 0)
                @foreach($languages as $language)
                    @php $form->openTabs('tab-translation' . $language->code, $language->name); @endphp
                    <div style="">
                        {!! Form::oneText($language->default == true ? 'required_name_'.$language->code :'name_'.$language->code, ['name' => trans('privateMPs.name'),'description' => trans('privateMPs.help_name')],
                            ($translations[$language->code]->name ??  null),
                          ['class' => 'form-control', 'id' => 'name_'.$language->code, (isset($language->default) && $language->default == true ? 'required' : null)]) !!}
                        {!! Form::oneTextArea('description_'.$language->code, [ 'name' => trans('privateMPs.description'),'description' => trans('privateMPs.help_description')],
                            ($translations[$language->code]->description ??  null),
                         ['class' => 'form-control', 'id' => 'description_'.$language->code ,(isset($language->default) && $language->default == true ? 'required' : null),'rows' => 4]) !!}

                    </div>
                @endforeach
                @php $form->makeTabs(); @endphp
            @endif
        </div>
    </div>

    @if(ONE::actionType('mp') != "show")
        <div class="flowchart-mp">
            <h4 id="advanced">{{trans('privateMPs.zoom_view_and_drag_and_drop_diagram_creation')}}</h4>
            <div id="chart_container">
                <div class="" id="flowchart_mp"></div>
            </div>
            <div class="draggable_operators">
                <div class="draggable_operators_label">
                    {{trans('privateMPs.drag_and_drop_the_available_items')}}
                </div>
                <div class="draggable_operators_divs">
                    @if(isset($operatorTypes))
                        @foreach($operatorTypes as $type)
                            @if(ONE::actionType('mp') != "create" && ($type->code == 'start' || $type->code == 'end'))
                                <div id="{{$type->code}}" class="draggable_operator btn btn-secondary" data-nb-inputs="{{$type->inputs}}" data-nb-outputs="{{$type->outputs}}" data-type="{{$type->code}}" data-operator-key="" disabled>{{$type->name ?? null}}</div>
                            @else
                                <div id="{{$type->code}}" class="draggable_operator btn btn-secondary" data-nb-inputs="{{$type->inputs}}" data-nb-outputs="{{$type->outputs}}" data-type="{{$type->code}}" data-operator-key="">{{$type->name ?? null}}</div>
                            @endif
                        @endforeach
                    @endif
                </div>
            </div>
            <button type="button" class="btn btn-secondary delete_selected_button">{{trans('privateMPs.delete_operator_or_link')}}</button>
            {{--Button to get data and print on text-area--}}
            {{--<button type="button" class="btn btn-secondary get_data">Get data</button>--}}
            {{--<button type="button" class="btn btn-secondary set_data">Reset data</button>--}}

            {{--<div id="flowchart_data_view">--}}
            {{--<textarea id="flowchart_data" name="flowchart_data"></textarea>--}}
            {{--</div>--}}
            {!! Form::hidden('flowchart_data', $mp->diagram_code ?? '', ['id' => 'flowchart_data']) !!}

        </div>
    @elseif(ONE::actionType('mp') == "show")
        <div class="flowchart-mp">
            <h4 id="advanced">{{trans('privateMPs.flowchart_zoom_view')}}</h4>
            <div id="chart_container">
                <div class="" id="flowchart_mp"></div>
            </div>
        </div>
    @endif

    {!! $form->make() !!}
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

@endsection