@extends('private._private.index')


@section('header_scripts')
    <!-- Le HTML5 shim, for IE6-8 support of HTML elements -->
{{--    <!--[if lt IE 9]>
    <script src="https://html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->--}}

{{-- <link rel="stylesheet" href="https://okfnlabs.org/recline/vendor/bootstrap/3.2.0/css/bootstrap.css" />--}}


    <link rel="stylesheet" href="{{ asset('vendor/recline/leaflet.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/MarkerCluster.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/MarkerCluster.Default.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/slick.grid.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/timeline.css') }}">

    <!-- Recline CSS components -->
    <link rel="stylesheet" href="{{ asset('vendor/recline/grid.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/slickgrid.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/flot.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/map.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/multiview.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/recline/timeline.css') }}">
    <!-- /Recline CSS components -->

    <!-- 3rd party JS libraries -->
    <!--script type="text/javascript" src="https://okfnlabs.org/recline/vendor/jquery/1.7.1/jquery.js"></script-->
    {{-- <script src="https://okfnlabs.org/recline/vendor/slickgrid/2.2/jquery-1.7.min.js"></script> --}}
    <script type="text/javascript" src="{{ asset('vendor/recline/underscore.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/backbone.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/mustache.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/bootstrap.js') }}"></script>
    <!--[if lte IE 8]>
    <script type="text/javascript" src="{{ asset('vendor/recline/excanvas.min.js') }}"></script>

    <![endif]-->
    <script type="text/javascript" src="{{ asset('vendor/recline/jquery.flot.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/jquery.flot.time.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/leaflet.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/leaflet.markercluster.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/jquery-ui-1.8.16.custom.min.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/jquery.event.drag-2.2.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/jquery.event.drag-2.2.js') }}"></script>

    <script type="text/javascript" src="{{ asset('vendor/recline/slick.core.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/slick.formatters.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/slick.editors.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/slick.grid.js') }}"></script>

    <script type="text/javascript" src="{{ asset('vendor/recline/slick.rowselectionmodel.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/slick.rowmovemanager.js') }}"></script>

    <script type="text/javascript" src="{{ asset('vendor/recline/moment.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/timeline.js') }}"></script>
    <!--[if lte IE 7]>
    <script language="javascript" type="text/javascript" src="{{ asset('vendor/recline/json2.js') }}"></script>
    <![endif]-->

    <!--
      ## Just use the all in one library version rather than individual files
    <script type="text/javascript" src="https://okfnlabs.org/recline/dist/recline.js"></script>
    -->

    <!-- model and backends -->
    <script type="text/javascript" src="{{ asset('vendor/recline/ecma-fixes.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/model.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/backend.memory.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/backend.dataproxy.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/backend.gdocs.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/csv.js') }}"></script>

    <!-- views -->
    <script type="text/javascript" src="{{ asset('vendor/recline/view.grid.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.slickgrid.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.flot.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.graph.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.map.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.timeline.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.pager.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.queryeditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.filtereditor.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.valuefilter.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.facetviewer.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/widget.fields.js') }}"></script>
    <script type="text/javascript" src="{{ asset('vendor/recline/view.multiview.js') }}"></script>
@endsection

@section('header_styles')
    <style type="text/css">
        .recline-slickgrid {
            height: {{  count($votesByChannel)*26 }}px;
        }

        .recline-timeline .vmm-timeline {
            height: 550px;
        }

        .changelog {
            display: none;
            border-bottom: 1px solid #ccc;
            margin-bottom: 10px;
        }

        /* Bootstrap4 fix */
        .btn-default:hover, .btn-default:focus, .btn-default:active, .btn-default.active, .open > .dropdown-toggle.btn-default {
            color: #333;
            background-color: #e6e6e6;
            border-color: #adadad;
        }

        .btn-default {
            color: #333;
            background-color: #fff;
            border-color: #ccc;
            border-radius: 2px!important;
        }


        [data-action~=fieldsView] {
            display:none;
        }

        .recline-pager .pagination .page-range{
            margin-right: 2px;
        }

        .pagination > li:first-child > a:before {
            content: "";
        }

        .pagination > li:last-child > a:after {
            content: "";
        }
    </style>
@endsection

@section('content')
    <div class="box box-primary background-white">
        <div class="box-body">
            <div class="data-explorer-here"></div>
        </div>
    </div>
@endsection


@section('scripts')
    <script>
        jQuery(function($) {
            window.multiView = null;
            window.explorerDiv = $('.data-explorer-here');

            // create the demo dataset
            var dataset = createDemoDataset();
            // now create the multiview
            // this is rather more elaborate than the minimum as we configure the
            // MultiView in various ways (see function below)
            window.multiview = createMultiView(dataset);

            // last, we'll demonstrate binding to changes in the dataset
            // this will print out a summary of each change onto the page in the
            // changelog section
            dataset.records.bind('all', function(name, obj) {
                var $info = $('<div />');
                $info.html(name + ': ' + JSON.stringify(obj.toJSON()));
                $('.changelog').append($info);
                $('.changelog').show();
            });
        });

        // create standard demo dataset
        function createDemoDataset() {
            var dataset = new recline.Model.Dataset({
                records: [
                    @foreach( $votesByChannel as $vote)
                    {
                        title: '{{ $vote->title }}',
                        total: {{ $vote->total }},
                        @foreach( $channels as $channel)
                        "channel{{ $channel }}_B": {{ $vote->channels->{$channel}->balance ?? 0 }},
                        "channel{{ $channel }}_P": {{ $vote->channels->{$channel}->positives ?? 0 }},
                        "channel{{ $channel }}_N": {{ $vote->channels->{$channel}->negatives ?? 0 }},
                        @endforeach
                    },
                    @endforeach
                ],
                // let's be really explicit about fields
                // Plus take opportunity to set date to be a date field and set some labels
                fields: [
                    {id: 'title' , 'label': 'Title'},
                    {id: 'total', 'label': 'Total', type: 'number'},
                    @foreach( $channels as $channel)
                        {id: "channel{{ $channel }}_B", 'label': "{{ trans('privateCbsVoteAnalysis.'.$channel) }} - B"},
                        {id: "channel{{ $channel }}_P", 'label': "{{ trans('privateCbsVoteAnalysis.'.$channel) }} - P"},
                        {id: "channel{{ $channel }}_N", 'label': "{{ trans('privateCbsVoteAnalysis.'.$channel) }} - N"},
                    @endforeach
                ]
            });
            return dataset;
        }

        // make MultivView
        //
        // creation / initialization in a function so we can call it again and again
        var createMultiView = function(dataset, state) {
            // remove existing multiview if present
            var reload = false;
            if (window.multiView) {
                window.multiView.remove();
                window.multiView = null;
                reload = true;
            }

            var $el = $('<div />');
            $el.appendTo(window.explorerDiv);

            // customize the subviews for the MultiView
            var views = [
                {
                    id: 'grid',
                    label: 'Grid',
                    view: new recline.View.SlickGrid({
                        model: dataset,
                        state: {
                            gridOptions: {
                                editable: false,
                                // Enable support for row add
                                enabledAddRow: false,
                                // Enable support for row delete
                                enabledDelRow: false,
                                // Enable support for row ReOrder
                                enableReOrderRow:false,
                                autoEdit: false,
                                enableCellNavigation: false
                            },
                            columnsEditor: [
                            ]
                        }
                    })
                },
                {
                    id: 'graph',
                    label: 'Graph',
                    view: new recline.View.Graph({
                        model: dataset

                    })
                },
                {
                    id: 'map',
                    label: 'Map',
                    view: new recline.View.Map({
                        model: dataset
                    })
                }
            ];

            var multiView = new recline.View.MultiView({
                model: dataset,
                el: $el,
                state: state,
                views: views
            });
            return multiView;
        }


        $("div[data-action='fieldsView']").remove();
        </script>
@endsection