@extends('private.presentation.index')

@section('content')
    <div class="welcome-container">
        <div class="row box-buffer">
            <div class="col-12 text-center">
                <div class="welcome-title" style="padding-bottom: 20px;">{{trans("privatePresentation.idea_config_title")}}</div>
            </div>
            <div class="col-12">
                <div class="row">
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.start") }}</div>
                    <div class="col-6 h2" style="height: 0;">
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th"></i>
                            </span>
                            <input class="form-control oneDatePicker gray-color" id="start_date" required="required" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="start_date" type="text" value="{{ Carbon\Carbon::today()->format("Y-m-d") }}">
                        </div>
                        <br>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.end") }}</div>
                    <div class="col-6 h2" style="height: 0;">
                        <div class="input-group date">
                            <span class="input-group-addon">
                                <i class="glyphicon glyphicon-th"></i>
                            </span>
                            <input class="form-control oneDatePicker gray-color" id="start_date" required="required" placeholder="yyyy-mm-dd" data-date-format="yyyy-mm-dd" name="start_date" type="text" value="{{ Carbon\Carbon::today()->addDays(2)->format("Y-m-d") }}">
                        </div>
                        <br>
                    </div>
                    <div class="col-12"></div>
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.public_access") }}</div>
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
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.allow_comments") }}</div>
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
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.anonymous_comments") }}</div>
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
                    <div class="col-6 gray-color h2 text-right">{{ trans("privatePresentation.allow_files") }}</div>
                    <div class="col-6 h2">
                        <div class="onoffswitch">
                            <input id="allow_files" name="allow_files" type="checkbox" checked="" class="onoffswitch-checkbox" value="1">
                            <label for="allow_files" class="onoffswitch-label">
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
                <a class="btn btn-presentation text-uppercase" href="{{action('PresentationController@show',['page' => 'voteMechanism'])}}">
                    {{trans("privatePresentation.next")}}
                </a>
            </div>
        </div>

    </div>
@endsection