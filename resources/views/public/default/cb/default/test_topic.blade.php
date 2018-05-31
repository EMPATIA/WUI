@extends('public.default._layouts.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key={{ Session::get("SITE-CONFIGURATION.maps_api_key") ?? "AIzaSyBJtyhsJJX_5DCp59m8sNsPlhHp8aQZHIE" }}"></script>
@endsection

@section('header_styles')
    <style>

        .gmap{
            height: 300px;
            width: 100%;
        }

        .idea-content .idea-status .status-info, .idea-content .idea-details .detail-label{
            color: white!important;
        }

        .detail-info a{
            color: white!important;
        }

    </style>
@endsection

@section('content')

    {{--<div class="container">--}}
        {{--<div class="row idea-topic-title">--}}
            {{--<div class="col title">--}}
                {{--<span>{{ONE::transCb('cb_topic_detail_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>--}}
                {{--<a href="{{ action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) }}">{{ONE::transCb('cb_topic_detail_back', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>--}}
            {{--</div>--}}
            {{--<div clasS="col">--}}
                {{--<div class="row no-gutters" style="justify-content: flex-end;">--}}
                    {{--<div class="col-lg-2 col-md-2 col-sm-4 col-6">--}}
                        {{--@php--}}
                            {{--$previousTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cbKey,$topicKey,"previous");--}}
                            {{--$nextTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cbKey,$topicKey,"next");--}}
                        {{--@endphp--}}
                        {{--@if(!empty($previousTopic) || !empty($nextTopic))--}}
                            {{--<div class="row no-gutters ideas-nav-buttons">--}}
                                {{--<div class="col-6 nav-left">--}}
                                    {{--<a href="@if(!empty($previousTopic)) {{ action('PublicTopicController@show', [$cbKey , $previousTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>--}}
                                {{--</div>--}}
                                {{--<div class="col-6 nav-right">--}}
                                    {{--<a href="@if(!empty($nextTopic)) {{ action('PublicTopicController@show', [$cbKey , $nextTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>--}}
                                {{--</div>--}}
                            {{--</div>--}}
                        {{--@endif--}}
                        {{--FALTA VERIFICAÇÃO DE OWNER PODER EDITAR E O TEMPO EM QUE PODE--}}
                        {{--@{{ created_by }}--}}
                        {{--@{{ current_user }}--}}
                        {{--<div class="row no-gutters ideas-edit" v-if="--}}{{--created_by == current_user.user_key--}}{{----}}{{-- && Date.now() > new Date(cb.start_topic_edit)--}}{{--">--}}
                            {{--<div class="col">--}}
                                {{--<a href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}"><i class="fa fa-pencil" aria-hidden="true"></i></a>--}}
                            {{--</div>--}}
                        {{--</div>--}}
                    {{--</div>--}}
                {{--</div>--}}
            {{--</div>--}}

        {{--</div>--}}
    {{--</div>--}}

    <div id="root">


        <topic id="topic-template" :topic-key="'{!! $topicKey !!}'" :cb-key="'{!! $cbKey !!}'" :cb-type="'{!! $type !!}'" :current-language="'{!! $currentLanguage !!}'" :logged-user="'{{ json_encode($currentUser) }}'">
            <div>
                <div class="container">
                    <div class="row idea-topic-title">
                        <div class="col title">
                            <span>{{ONE::transCb('cb_topic_detail_title', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                            <a href="{{ action('PublicCbsController@show', [$cbKey, 'type'=> $type] ) }}">{{ONE::transCb('cb_topic_detail_back', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                        </div>
                        <div clasS="col">
                            <div class="row no-gutters" style="justify-content: flex-end;">
                                <div class="col-lg-2 col-md-2 col-sm-4 col-6">
                                    @php
                                        $previousTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cbKey,$topicKey,"previous");
                                        $nextTopic = App\Http\Controllers\PublicCbsController::getTopicKeyByIndex($cbKey,$topicKey,"next");
                                    @endphp
                                    @if(!empty($previousTopic) || !empty($nextTopic))
                                        <div class="row no-gutters ideas-nav-buttons">
                                            <div class="col-6 nav-left">
                                                <a href="@if(!empty($previousTopic)) {{ action('PublicTopicController@show', [$cbKey , $previousTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-left" aria-hidden="true"></i></a>
                                            </div>
                                            <div class="col-6 nav-right">
                                                <a href="@if(!empty($nextTopic)) {{ action('PublicTopicController@show', [$cbKey , $nextTopic["topic_key"], 'type' => $type] ) }} @else # @endif"><i class="fa fa-long-arrow-right" aria-hidden="true"></i></a>
                                            </div>
                                        </div>
                                    @endif
                                    {{--FALTA VERIFICAÇÃO DE OWNER PODER EDITAR E O TEMPO EM QUE PODE--}}
                                    <div class="row no-gutters ideas-edit" v-if="canEdit">
                                        <div class="col">
                                            <a href="{!! action('PublicTopicController@edit', ['cbKey' => $cbKey,'topicKey' => $topicKey, 'type' => $type, 'f'=> 'topic']) !!}"><i class="fa fa-pencil" aria-hidden="true"></i></a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>
                <div class="container-fluid banner-voting-info no-padding">
                    <div class="banner-voting-info">
                        {{--@{{ votes }}--}}
                        <div v-for="vote in votes" class="votes-info-bar votesInfoBar" v-if="vote.is_open">
                            <div id="voteBanner" class="row banner-voting-info votes-forbidden" v-if="'{{!SESSION::has('user')}}'">
                                <div class="col-12 banner-voting-col">
                                    <a href="{{action('AuthController@login')}}">
                                        {{ ONE::transCb('topic_please_login_to_participate', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                    </a>
                                </div>
                            </div>
                            <div id="voteBanner" class="row banner-voting-info votes-forbidden" v-else-if="!confirmed_email">
                                <div class="col-12 banner-voting-col">
                                    <a href="{{action('AuthController@sendConfirmEmail') }}" class="submit-votes-btn">{{ ONE::transCb('resend_email', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                                </div>
                            </div>
                            <div id="votesInfoBar" class="votes-info-bar votesInfoBar" v-else>

                                <div class="container">
                                    <div class="row no-gutters">
                                        <div class="col-md-8 col-12" style="text-align: left" v-if="!vote.user_has_submitted">
                                            Votos disponíveis
                                            <strong v-if="vote.total_votes_allowed" class="info">
                                                @{{ vote.total_votes_allowed }}
                                            </strong>
                                            <strong v-else>
                                                0
                                            </strong>
                                            <div class="between-bar"></div>
                                            <div class="info">
                                                Votos utilizados <strong>@{{ vote.user_registered_votes }}</strong>
                                            </div>
                                        </div>
                                        <div class="col-md-4 col-12 text-right"  v-if="!vote.user_has_submitted">
                                            <div v-if="vote.needs_submission">
                                                <div v-if="vote.user_has_submitted">
                                                    <div class="submit-votes-btn">
                                                        SUBMETEU EM @{{ vote.submitted_date.date }}
                                                    </div>
                                                </div>
                                                <div v-else>
                                                    <div v-if="vote.can_vote && vote.is_open">
                                                        <a id="submitVotesButton" href="{{ action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey,"type"=>$type]) }}" class="submit-votes-btn">
                                                            {{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                        </a>
                                                        <a :href="vote.submission_link">SUBMTER VOTO</a>
                                                    </div>
                                                    <div v-else>
                                                        NAO PODE SUBMETER
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div  v-if="vote.user_has_submitted" class="col-12" style="text-align: center;padding: 20px 15px;font-size: 1.2rem;font-weight: 400;text-transform: uppercase;">
                                            <div>{{ ONE::transCb('vote_submited', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                        </div>
                                        <div v-if="vote.user_has_submitted && !vote.is_open">

                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                        <div v-else-if="vote.user_has_submitted && !vote.is_open" style="text-align: center;padding: 20px 15px;font-size: 1.2rem;font-weight: 400;text-transform: uppercase;">
                            <div>
                                {{ ONE::transCb('vote_ended', !empty($cb) ? $cb->cb_key : $cbKey)}}
                            </div>
                        </div>
                    </div>
                </div>

                <div v-if="loading_topic" style="text-align: center">
                    <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                </div>


                <div class="container idea-content" v-if="showInformation">
                    <div class="row">
                        <div class="col-lg-4 col-md-5 col-sm-12 col-12 col-details med-grey-bg">
                            <div class="row">
                                <div class="col-12 idea-image" v-bind:style="{ 'background-image': 'url(' + featuredImage + ')' }">
                                    <img v-bind:src="featuredImage">
                                </div>
                                <div class="col-12 idea-map no-padding">
                                    <google-map></google-map>
                                </div>
                                <div class="col-12 idea-status green" v-for="configuration in configurations" v-if="configuration.code == 'show_status' && status[0].status_type.code == 'approved'">
                                    <div class="row no-margin status-row">
                                        <div class="col-6 status-label">
                                            {{ONE::transCb('cb_topic_detail_status', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                        </div>
                                        <div class="col-6 status-info">
                                            @{{ status[0].name }}
                                        </div>
                                    </div>
                                    <div v-else-if="configuration.code == 'show_status' && status[0].status_type.code == 'approved'" class="col-12 idea-status red">
                                        <div class="row no-margin status-row">
                                            <div class="col-6 status-label">
                                                {{ONE::transCb('cb_topic_detail_status', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                            </div>
                                            <div class="col-6 status-info">
                                                @{{ status[0].name }}
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-12 idea-details">
                                    <div class="row detail-row" v-for="parameter in parameters" v-if="!parameter.highlight && parameter.code != 'google_maps'">
                                        <div class="col-6 detail-label" style="color: white!important">@{{ parameter.title }}</div>
                                        <div class="col-6 detail-info" style="color: white!important">
                                            <div class="topic_param_option" v-for="option in parameter._options" v-if="parameter._options.length > 0">
                                                @{{ option.title }}
                                            </div>
                                            <div v-if="parameter.parameter_code == 'budget'">
                                                <i class="fa fa-eur" aria-hidden="true"></i>
                                                @{{ parameter.value }}
                                            </div>
                                            <div v-else>
                                                @{{ parameter.value }}
                                            </div>

                                        </div>
                                    </div>
                                    <div class="row detail-row">
                                        <div class="col-6 detail-label" style="color: white!important">{{ ONE::transCb('proposal_topic_owner', !empty($cb) ? $cb->cb_key : $cbKey) }}</div>
                                        <div class="col-6 detail-info">
                                            <div v-if="createdOnBehalf != '' && createdOnBehalf != null">
                                                @{{ createdOnBehalf }}
                                            </div>
                                            <div v-else>
                                                <div v-if="user_name == ''">
                                                    {{ONE::transCb("proposal_topic_anonymous", !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                </div>
                                                <div v-else>
                                                    @{{ user_name }}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row detail-row" v-if="childTopics != null">

                                        <div class="col-6 detail-label" style="color: white!important">{{ ONE::transCb('generated', !empty($cb) ? $cb->cb_key : $cbKey) }}</div>
                                        <div class="col-6 detail-info" v-if="childTopics.length > 1">
                                            <div  v-for="child in childTopics">
                                                <a :href="child.url">@{{ child.title }}</a>
                                                <br>
                                            </div>
                                        </div>
                                        <div  class="col-6 detail-info"  v-else>
                                            <a style="color:#fff!important;" :href="childTopics[0].url">@{{ childTopics[0].title }}</a>
                                        </div>
                                    </div>

                                    <div class="row detail-row" v-if="parentTopics != null">
                                        <div class="col-6 detail-label" style="color: white!important">{{ ONE::transCb('generated_by', !empty($cb) ? $cb->cb_key : $cbKey) }}</div>
                                        <div class="col-6 detail-info" v-if="parentTopics.length > 1">
                                            <div v-for="parent in parentTopics">
                                                <a style="color:#fff!important;" :href="parent.url">@{{ parent.title }}</a>
                                                <br>
                                            </div>
                                        </div>
                                        <div class="col-6 detail-info" v-else>
                                            <a style="color:#fff!important;" :href="parentTopics.url">@{{ parentTopics.title }}</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="offset-lg-1 col-lg-7 col-md-7 col-sm-12 col-12 col-text">
                            <div class="row">
                                <div class="col-12 idea-title" v-text="title">
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-12 idea-summary" v-text="summary"></div>
                            </div>
                            <div class="row">
                                <div class="col-12 idea-description" v-text="contents"></div>
                            </div>

                            <div v-for="parameter in parameters" v-if="parameter.highlight">
                                <div class="row idea-param text-highlights light-grey-bg">
                                    <div class="col-12 title idea-param-title">
                                        @{{ parameter.title }}
                                    </div>
                                    <div class="col-12 text idea-param-body">
                                        @{{ parameter.pivot.value }}
                                    </div>
                                </div>
                            </div>

                            <a v-for="file in files" :href="file.url" class="file-download-btn" v-text="file.name"><i class="fa fa-download" aria-hidden="true"></i></a>

                        </div>
                    </div>
                </div>
            </div>

        </topic>
    </div>
@endsection


@section('scripts')
    <script src="https://unpkg.com/vue@2.1.3/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="/js/test_topic.js"></script>
@endsection
