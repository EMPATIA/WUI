@extends('public.empaville._layouts.index')

@section('content')


    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-pencil"></i> {{ trans('mp.create_proposal') }}</h3>
                </div>
                <div class="box-body">
                    <dl>
                        <div class="form-group ">
                            <label for="name">{{ trans('form.title') }}</label><input type="text" name="designation" id="name" class="form-control">
                        </div>
                        <div class="form-group ">
                            <label for="description">{{ trans('form.description') }}</label><textarea rows="10" cols="50" name="description" id="type" class="form-control"></textarea>
                        </div>
                        <div class="form-group ">
                            <label for="name">{{ trans('mp.geo_area') }}</label> <select class="form-control"><option><option></select>
                            <br/>
                            <div id="googleMap" style="width:540px;height:320px;"></div>
                            {{--<iframe src="https://www.google.com/maps/embed?pb=!1m10!1m8!1m3!1d12190.999124649246!2d-8.401986050000001!3d40.192381649999994!3m2!1i1024!2i768!4f13.1!5e0!3m2!1spt-PT!2spt!4v1450891652701" width="100%" height="350" frameborder="0" style="border:0" allowfullscreen></iframe>--}}
                        </div>
                        <div class="form-group ">
                            <label for="name">{{ trans('mp.area') }}</label> <select class="form-control"><option><option></select>
                        </div>
                        <div style="float: left; margin-top: 5px;">
                            <a href="#" class="btn btn-primary"><i class="pull-right"></i> {{ trans('form.post') }} </a>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('scripts')
    <script
            src="http://maps.googleapis.com/maps/api/js">
    </script>

    <script>
        function initialize() {
            var mapProp = {
                center:new google.maps.LatLng(40.00000,-8.120850),
                zoom:5,
                mapTypeId:google.maps.MapTypeId.ROADMAP
            };
            var map=new google.maps.Map(document.getElementById("googleMap"), mapProp);
        }
        google.maps.event.addDomListener(window, 'load', initialize);
    </script>

@endsection