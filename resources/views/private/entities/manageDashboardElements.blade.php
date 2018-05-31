@extends('private._private.index')

@section('content')
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title"><i class="fa"></i> {{ trans('privateEntities.manage_entity_dashboard_elements') }}</h3>
        </div>

        <div class="box-body">
            <div class="container-fluid">
                <div class="row">
                    @forelse($availableDashBoardElements as $key => $dashBoardElement)
                        <div class="col-12 col-sm-6 col-md-4 col-lg-3 col-xl-2">
                            {!! Form::oneSwitch("parameter_".$dashBoardElement->id,
                                    array("name"=>$dashBoardElement->title,"description"=>$dashBoardElement->description),
                                    !empty(collect($entity->entityDashBoardElements)->where('dashboard_element_id','=',$dashBoardElement->id)->first()),
                                    ["readonly"=>false,"onchange"=>"updateEntityDashBoardElements('".action('EntitiesDividedController@updateEntityDashBoardElements',$dashBoardElement->id)."')"]) !!}
                        </div>
                    @empty
                        {{ trans('privateEntities.no_dashboard_elements_available') }}
                    @endforelse
                </div>
            </div>
            {{--@include('private.dashBoardElements.availableDashBoardElementList')--}}

        </div>
    </div>
@endsection

@section('scripts')


<script>
    function updateEntityDashBoardElements(url){
        $.ajax({
            method: 'POST',
            url: url,
            success: function (response) {
                if(response.error){
                    toastr.error(response.error, '', {timeOut: 2000,positionClass: "toast-bottom-right"});
                }else
                    toastr.success(response.success, '', {timeOut: 2000,positionClass: "toast-bottom-right"});
            },
            error: function () {
                toastr.error(trans("privateEntities.failedToChangeDashboardElementStatus"), '', {timeOut: 2000,positionClass: "toast-bottom-right"});
            }
        });
    }
</script>
@endsection
