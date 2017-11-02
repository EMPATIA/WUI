@extends('public.empaville._layouts.index')

@section('content')

    <div class="row">
        <div class="col-md-12">
            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title"><i class="glyphicon glyphicon-question-sign"></i> {{ trans('mp.create_idea') }}</h3>
                </div>
                <div class="box-body">
                    <!--<div class="container">-->
                        <div class="row">
                            <h1 class="text-center">{{ trans('mp.create_idea') }}</h1>
                            <p class="lead margin-big text-center">Lorem ipsum ...</p>
                            <div class="form-group text-center">
                                <a href="{{action('MPController@ideas')}}" class="btn btn-primary btn-lg">{{ trans('mp.explore_idea') }}</a>
                            </div>
                            <hr>
                        </div>
<!--                        <form>-->
                            <div class="col-xs-12 col-sm-12 col-md-6 BP-top-2">
                                <div class="form-group">
                                    <label for="">{{ trans('form.title') }} <span class="small">[Max 60 characters]</span></label>
                                    <textarea class="form-control" rows="1"></textarea>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ trans('form.description') }}  <span class="small">[Max 600 characters]</span></label>
                                    <textarea class="form-control" rows="18">Lorem ipsum ... </textarea>
                                </div>
                            </div>
                            <div class="col-xs-12 col-sm-12 col-md-6 BP-top-2">
                                <div class="form-group">
                                    <label for="">{{ trans('mp.area') }}</label>
                                    <select class="form-control">
                                        <option>Select Area</option>
                                        <option>Environment</option>
                                        <option>Health</option>
                                        <option>Sports</option>
                                        <option>Food</option>
                                        <option>Pets</option>
                                    </select>
                                </div>
                                <div class="form-group">
                                    <label for="">{{ trans('mp.where_r_u') }}</label><br>
                                    <input type="text" class="form-control" placeholder="{{ trans('mp.city') }}"><br>
                                    <div class="top-1">
                                        <div id="googleMap" style="width:540px;height:320px;"></div>
                                    </div>
                                </div>
                            </div>
                        <!--</form>-->
                     <!--</div>-->
                    <div class="col-xs-12 col-sm-12 col-md-12 text-center">
                            <div class="form-group">
                                <a href="{{action('MPController@ideas')}}" class="btn btn-primary btn-lg">{{ trans('form.publish') }}</a>
                            </div>
                    </div>
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
