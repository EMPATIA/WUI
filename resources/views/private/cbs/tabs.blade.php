{{--<ul class="nav nav-tabs flex-sm-row nav-menu-empatia">--}}

    {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
        {{--<a class="nav-link @if($active=='details') active @endif" href="{{ action('CbsController@show', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.details') }}</a>--}}
    {{--</li>--}}

    {{--@if(in_array('topics', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='topics') active @endif" href="{{ action('CbsController@showTopics', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.pads_topic') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}

    {{--@if(in_array('pad_parameters', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='parameters') active @endif" href="{{ action('CbsController@showParameters', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.pads_parameter') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('pad_votes', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='votes') active @endif" href="{{ action('CbsController@showVotes', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.pads_vote') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('moderators', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='moderators') active  @endif" href="{{ action('CbsController@showModerators', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.pads_moderators') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('configurations', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='configurations') active @endif" href="{{ action('CbsController@showConfigurations', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.pads_configurations') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('vote_analysis', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='voteAnalysis') active @endif" href="{{ action('CbsController@voteAnalysis', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.vote_analysis') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('empaville_analysis', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='empavilleAnalysis') active @endif" href="{{ action('CbsController@voteAnalysisEmpaville', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.empaville_analytics') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}
    {{--@if(in_array('topic_permissions', Session::get('user_permissions_sidebar')) || sizeOf(Session::get('user_permissions_sidebar')) == 1)--}}
        {{--<li class="nav-item flex-sm-fill text-sm-center">--}}
            {{--<a class="nav-link @if($active=='permissions') active  @endif" href="{{ action('CbsController@showGroupPermissions', [$type, $cb->cb_key ?? $cbKey]) }}">{{ trans('privateSidebar.cb_group_permissions') }}</a>--}}
        {{--</li>--}}
    {{--@endif--}}



       {{--<li class="nav-item dropdown flex-sm-fill text-sm-center">--}}
           {{--<a class="dropdown-toggle nav-link" data-toggle="dropdown" href="#" role="button" aria-haspopup="true" aria-expanded="false" style="margin-right:0;padding-right: 17px;">--}}
               {{--Configurações--}}
               {{--<i class="fa fa-caret-down" aria-hidden="true"></i>--}}
           {{--</a>--}}
           {{--<div class="dropdown-menu tabs-dropdown-menu" style="margin-top:-2px;">--}}
               {{--<a class="dropdown-item" href="#">Gerais</a>--}}
               {{--<a class="dropdown-item" href="#">Parametros</a>--}}
               {{--<a class="dropdown-item" href="#">Voto</a>--}}
               {{--<a class="dropdown-item" href="#">Moderadores</a>--}}
               {{--<a class="dropdown-item" href="#">Segurança</a>--}}
               {{--<a class="dropdown-item" href="#">Permissões</a>--}}
{{----}}
               {{--<div class="dropdown-divider"></div>--}}
{{----}}
               {{--<a class="dropdown-item" href="#">Flags</a>--}}
           {{--</div>--}}
       {{--</li>--}}
        {{--<!----}}
       {{--<li class="nav-item">--}}
           {{--<a class="nav-link" href="#">Link</a>--}}
       {{--</li>--}}
       {{--<li class="nav-item">--}}
           {{--<a class="nav-link disabled" href="#">Disabled</a>--}}
       {{--</li>--}}
       {{---->--}}
{{--</ul>--}}




{{--<style>--}}
{{--@media (min-width:0) and (max-width: 1300px) {--}}
    {{--.nav-menu-empatia{--}}
        {{--display: list-item;--}}
    {{--}--}}
{{--}--}}
{{--</style>--}}
