@extends('private.presentation.index')

@section('content')
<div class="welcome-container">
    <div class="row box-buffer">
        <div class="col-12 text-center">
            <div class="welcome-title" style="padding-bottom: 20px;">{{trans("privatePresentation.vote_mechanism_title")}}</div>
        </div>
        <div class="col-12">
            <div class="row">
                <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.negative_vote") }}</div>
                <div class="col-6 h2">
                    <div class="onoffswitch">
                        <input id="public_access" name="public_access" type="checkbox" class="onoffswitch-checkbox" value="1">
                        <label for="public_access" class="onoffswitch-label">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12"></div>
                <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.multi_vote") }}</div>
                <div class="col-6 h2">
                    <div class="onoffswitch">
                        <input id="allow_comments" name="allow_comments" type="checkbox" checked="" class="onoffswitch-checkbox" value="1">
                        <label for="allow_comments" class="onoffswitch-label">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12"></div>
                <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.machines_vote") }}</div>
                <div class="col-6 h2">
                    <div class="onoffswitch">
                        <input id="anonymous_comments" name="anonymous_comments" type="checkbox" class="onoffswitch-checkbox" value="1">
                        <label for="anonymous_comments" class="onoffswitch-label">
                            <span class="onoffswitch-inner"></span>
                            <span class="onoffswitch-switch"></span>
                        </label>
                    </div>
                </div>
                <div class="col-12"></div>
                <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.budget") }}</div>
				<div class="col-6 h2">
					<div class="onoffswitch">
						<input id="anonymous_comments" name="anonymous_comments" type="checkbox" class="onoffswitch-checkbox" value="1">
						<label for="anonymous_comments" class="onoffswitch-label">
							<span class="onoffswitch-inner"></span>
							<span class="onoffswitch-switch"></span>
						</label>
					</div>
                </div>
            </div>
        </div>
        <div class="col-12 bottom-actions text-right" style="position:absolute;right:10px; bottom: 20px;">
            <a class="btn btn-presentation text-uppercase" href="#">
                {{trans("privatePresentation.more_configurations")}}
            </a>
            <a class="btn btn-presentation text-uppercase" href="{{action('PresentationController@show',['page' => 'voteMechanismSecond'])}}">
                {{trans("privatePresentation.next")}}
            </a>
        </div>
    </div>
</div>
@endsection
@section("scripts")
    <script>
        $("#budget").select2();
    </script>
@endsection