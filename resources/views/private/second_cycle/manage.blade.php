@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('secondCycle.second_cycle') }}</h3>
        </div>
        <div class="box-body">
            <div class='row'>
                @if(!$empty2C)
                    <div id="menu-buttons" class="col-sm-5">
                        <button class="btn btn-flat btn-secondary" data-action="expand-all" type="button">{{ trans('secondCycle.expandAll') }}</button>
                        <button class="btn btn-flat btn-secondary" data-action="collapse-all" type="button">{{ trans('secondCycle.collapseAll') }}</button>
                    </div>
                @endif
                    <div class="col-sm-1 pull-right" id="createMenu">
                    <span class="pull-right">
                        <a style="color: #fffbfe;" class="btn btn-sm btn-flat btn-success"
                           title="" data-toggle="tooltip" href="{{action('SecondCycleController@create', ["cbKey" => $cbKey,"level" => $root_level]) }} " data-original-title="{{trans('secondCycle.create')}} {{$root_level_name}}">
                                <i class="fa fa-plus"></i>
                        </a>
                    </span>
                    </div>
            </div>

            @if(!$empty2C)
                <div class="dd" id="nestable">
                    {!! \App\Unimi\NestedCbs::buildSecondCycleTree($cbKey, $data, $root_level."-root") !!}
                </div>
            @else
                <div class="row">
                    <div class="col-md-12 text-center">{{trans('secondCycle.empty')}}</div>
                </div>
            @endif
        </div>
    </div>

@endsection

@section('scripts')

    <script>
        $(function() {
            var array = ["{{ $type }}", "{{$cbKey}}"];
	    getSidebar('{{ action("OneController@getSidebar") }}', 'second_cycle', array, 'padsType' );
	});


	$(document).ready(function(){
	$(".dd-nodrag").on("click", function(event) { // click event
		event.preventDefault();
		return false;
	});
	$(".dd-nodrag").on("mousedown", function(event) { // mousedown prevent nestable click
		event.preventDefault();
		return false;
	});

	$('.dd').nestable();
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
        });

	});

    </script>

@endsection
