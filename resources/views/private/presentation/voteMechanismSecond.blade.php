@extends('private.presentation.index')

@section('content')
    <div class="welcome-container">
        <div class="row box-buffer-rap">
            <div class="col-12 text-center">
                <div class="welcome-title" style="padding-bottom: 20px;">End</div>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-12 col-lg-offset-1 text-center" style="margin-top: 50px;">
				<a href="{{action('CbsController@show',['type'=>"idea",'cbKey' => $cbKey])}}" class="text-center" target="_blank">
					<div class="btn-presentation" style="padding-top: 50px;">
						<h2 style="height: 10vh;">{{trans('privatePresentation.start_up')}}</h2>
					</div>
				</a>
            </div>
            <div class="col-lg-4 col-md-4 col-sm-4 col-12 col-lg-offset-2 text-center" style="margin-top: 50px;">
				<a href="{{action('CbsController@show',['type'=>"idea",'cbKey' => $cbKey])}}" class="text-center" target="_blank">
					<div class="btn-presentation" style="padding-top: 50px;">
						<h2 style="height: 10vh;">{{trans('privatePresentation.simulate_process')}}</h2>
					</div>
				</a>
            </div>
        </div>
		<!--
            <div class="col-12 bottom-actions text-right" style="position:absolute;right:10px; bottom: 20px;">
                <a class="btn btn-presentation text-uppercase" href="/private">
                    {{trans("privatePresentation.backToBackOffice")}}
                </a>
            </div>
		-->
    </div>
@endsection