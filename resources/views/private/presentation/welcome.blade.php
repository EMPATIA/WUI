
@extends('private.presentation.index')

@section('content')
    <div class="welcome-container">
        <div class="row box-buffer" style="padding-top: 20vh">
            <div class="col-12 text-center">
                <div class="welcome-title text-uppercase">
                    <i class="fa fa-play-circle-o fa-5" aria-hidden="true"></i>
                    {{trans("privatePresentation.welcome")}}
                </div>
            </div>
            <div class="col-12 text-center">
                <div class="welcome-description">{{trans("privatePresentation.welcome_description")}}</div>
            </div>
            <div class="col-12 text-center welcome-button-div">
                <a class="btn btn-presentation text-uppercase" href="{{action('PresentationController@show',['page' => 'rapPage'])}}">
                        {{trans("privatePresentation.next")}}
                </a>
            </div>
        </div>
    </div>
@endsection