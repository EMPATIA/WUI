@extends('public.cascais._layouts.index')

@section('header_scripts')
    <!-- Maps -->
    <script src="https://maps.googleapis.com/maps/api/js?key=AIzaSyDqDs-_NUO4eqGuBLCq7JmNduc6N_MqUZY"></script>
@endsection

@section('header_styles')
    <style>
        .statisticsInformation span{
            font-size:1.1em;
        }

        .filter:hover:not(.active), .filter:active:not(.active){
            text-decoration: none;
            background-color: #313131 !important;
        }

        .gmap{
            min-height: 40vh;
        }

        .red {
            background: #F7606F;
            color: white;
        }

        .filter:focus{
            text-decoration: none;
        }
        .filter{
            cursor: pointer;
        }

        .jscroll-inner, .jscroll-added  {
            margin-right: -15px;
            margin-left: -15px;
        }
        .jscroll-loading{
            width:100%;
        }

        .ideas-grid .idea-card{
            padding-right: 0px!important;
            padding-left: 0px!important;
            display:flex;
        }

        .jscroll-inner, .jscroll-added {
            display: -webkit-box;
            display: -webkit-flex;
            display: -ms-flexbox;
            display: flex;
            -webkit-flex-wrap: wrap;
            -ms-flex-wrap: wrap;
            flex-wrap: wrap;
            margin-right: 0px;
            margin-left: 0px;
            width:100%;
        }

        .jscroll-inner col{
            padding-right: 0!important;
            padding-left: 0!important;
        }

        #my-map{
            height: auto!important;
        }
        .active, .active:hover, .active:active, .active:focus{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            text-decoration: none;
        }

        .not-active{
            background: blue!important;
            color: white!important;
        }
        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .title{color:#fff!important}
        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .idea-details hr{background-color:#fff!important}
        .news-title,
        .idea-topic-title{
            align-items: flex-end;
        }


        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .description{color:#fff!important}

        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .ideas-grid.white-ideas .idea-card{
            opacity: 0.1;
        }

        .ideas-grid.white-ideas .idea-card a.a-wrapper{
            flex:1;
        }

        .news-title .title,
        .idea-topic-title .title{
            min-width: 50%;
        }

        .idea-topic-title .title {
            padding: 30px 0px 15px 0px;
        }

        .news-title .title span,
        .idea-topic-title .title span{
            text-transform: uppercase;
            font-size: 1.3rem;
            font-weight: 600;
        }

        .news-title .title a,
        .idea-topic-title .title a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            font-size: 1.1rem;
            margin-left: 3px;
            font-weight: 600;
        }

        .news-title .title a:hover,
        .idea-topic-title .title a:hover {
            text-decoration: none;
            color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons{
            background-color: #f4f4f4;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left,
        .idea-topic-title .ideas-nav-buttons>.nav-right{
            text-align: center;
            flex-basis: 70px;
            padding: 30px 0px 7px 0px;
            background-color: #f4f4f4;
            font-size: 1.3rem;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left a{
            color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-right a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover,
        .idea-topic-title .ideas-nav-buttons>.nav-right:hover{
            cursor: pointer;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover{
            background-color: #383838;
        }

        .idea-topic-title .ideas-nav-buttons>.nav-right:hover{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-nav-buttons>.nav-left:hover a,
        .idea-topic-title .ideas-nav-buttons>.nav-right:hover a{
            color:#fff;
        }

        .idea-topic-title .ideas-nav-buttons>.col:hover a>i.fa{
            color: #fff;
        }

        .idea-topic-title .ideas-edit>.col,
        .idea-topic-title .ideas-delete>.col{
            padding:3px 7px;
            text-align: center;
        }

        .idea-topic-title .ideas-edit>.col:hover,
        .idea-topic-title .ideas-delete>.col:hover{
            cursor: pointer;
        }

        .idea-topic-title .ideas-edit>.col a,
        .idea-topic-title .ideas-delete>.col a{
            color: #fff;
        }

        .idea-topic-title .ideas-edit>.col{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-edit>.col:hover{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-edit>.col:hover a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-topic-title .ideas-delete>.col{
            background-color: #4c4c4c;
        }

        .idea-topic-title .ideas-delete>.col:hover{
            color:#fff;
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }


        .idea-topic-title .ideas-delete,
        .idea-topic-title .ideas-edit {
            color: #fff;
            font-size: 0.9rem;
        }

        .idea-content{
            margin-top:60px;
            padding-bottom: 30px;
        }

        .idea-content .col-details {
            order: 2;
        }

        .idea-content .col-text{
            order:1;
        }

        @media (min-width: 768px) {
            .idea-content .col-details {
                order: 1;
                margin-top:0px;
            }

            .idea-content .col-text {
                order: 2;
            }
        }

        .idea-content .idea-title{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            font-size: 1.3rem;
            line-height: normal;
            font-weight: 600;
        }

        .news-content .news-summary,
        .idea-content .idea-summary{
            font-size: 0.9rem;
            font-weight: 600;
            line-height: normal;
        }

        .idea-content .idea-summary {
            margin-top: 30px;
        }


        .news-content .news-description,
        .idea-content .idea-description{
            margin-top: 30px;
            font-size: 0.9rem;
            line-height: normal;
            margin-bottom: 40px;
        }

        .news-content .news-description .see-more-btn,
        .idea-content .idea-description .see-more-btn {
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .news-content .news-description .see-more-btn:hover,
        .idea-content .idea-description .see-more-btn:hover {
            color: #383838;
            text-decoration: none;
        }

        .idea-content .files{
            margin-bottom: 30px;
        }

        .idea-content .files .file-row{
            margin-bottom: 5px;
        }

        .idea-content .files .file-row .file-download-btn{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            margin-left: 10px;
        }

        .idea-content .files .file-row .file-download-btn:hover{
            color: #383838;
            text-decoration: none;
        }

        .idea-content .text-highlights{
            padding-bottom:15px;
        }

        .idea-content .text-highlights:first-child{
            padding:30px 0;
        }

        .idea-content .text-highlights .title{
            color: #383838;
            font-size: 1.1rem;
            font-weight: 600;
        }

        .idea-content .idea-image{
            height: 150px;
            background-size: cover;
        }

        .idea-content .idea-status{
            background-color: #d6c12c;
            padding: 5px 0;
        }

        .idea-content .idea-status .status-label{
            color: #383838;
        }

        .idea-content .detail-row{
            margin-bottom: 10px;
        }

        .idea-content .details-title{
            color: #fff;
            font-size: 1rem;
            font-weight: 600;
            margin: 0 0 15px 0;
            text-align: right;
        }

        .idea-content .idea-details{
            padding: 30px 15px;
        }

        .idea-content .idea-status .status-label,
        .idea-content .idea-details .detail-label{
            text-transform: uppercase;
            font-size: 0.75rem;
            font-weight: 600;
            text-align: right;
            line-height: 20px;
        }

        .idea-content .idea-details .detail-label{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-content .idea-status .status-info,
        .idea-content .idea-details .detail-info{
            color: #fff;
            font-size: 0.85rem;
            line-height: 20px;
            font-weight: 300;
        }

        .idea-content .idea-details .detail-info.detail-big{
            font-size: 1.1rem;
            font-weight: 600;
        }

        .idea-details-buttons .buttons-row .button-dislike,
        .idea-details-buttons .buttons-row .button-like{
            color: #fff;
            padding: 7px 15px;
            text-align: center;
        }


        .idea-details-buttons .buttons-row .button-dislike{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};

        }

        .idea-details-buttons .buttons-row .button-like{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .idea-details-buttons .buttons-row .button-dislike a,
        .idea-details-buttons .buttons-row .button-like a{
            color: #fff;
        }

        .idea-details-buttons .buttons-row .button-dislike a:hover,
        .idea-details-buttons .buttons-row .button-like a:hover{
            color: #fff;
            text-decoration: none;
        }

        .idea-details-buttons .buttons-row .button-dislike:hover,
        .idea-details-buttons .buttons-row .button-like:hover{
            background-color: #fff;
            cursor: pointer;
        }

        .idea-details-buttons .buttons-row .button-dislike:hover a,
        .idea-details-buttons .buttons-row .button-like:hover a{
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            text-decoration: none;
        }

        .idea-details-buttons .buttons-row .button-dislike .fa,
        .idea-details-buttons .buttons-row .button-like .fa{
            font-size: 30px;
        }

        .idea-details-buttons .buttons-row .button-dislike span,
        .idea-details-buttons .buttons-row .button-like span{
            font-size: 0.8rem;
            text-transform: uppercase;
            font-weight: 300;
            margin-left: 7px;
            line-height: 30px;
        }

        .social-buttons{
            display: flex;
            padding:0;
        }

        @media (min-width: 992px) {
            .social-buttons{
                padding: 0 15px;
            }
        }

        .social-buttons .follow-btn,
        .social-buttons .share-social-btn{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            margin: 5px 0;
            padding: 0 25px;
        }

        .social-buttons .share-social-btn {
            align-items: flex-end;
            margin-left: 10px;
        }

        .social-buttons .follow-btn a,
        .social-buttons .share-social-btn a{
            line-height: 40px;
            vertical-align: middle;
            display: block;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .social-buttons .follow-btn:hover,
        .social-buttons .share-social-btn:hover{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        }

        .social-buttons .follow-btn:hover a,
        .social-buttons .share-social-btn:hover a{
            color: #fff;
        }

        .social-buttons .follow-btn a:hover,
        .social-buttons .share-social-btn a:hover{
            text-decoration: none;
            color:#fff;
        }

        .idea-comments{
            background-color: #f4f4f4;
            padding:40px 0 80px 0;
        }

        .idea-content>.row{
            flex-wrap: wrap;
        }

        .idea-comments .comments-title{
            text-transform: uppercase;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            text-align: center;
            font-size: 1.3rem;
            margin: 30px auto;
        }

        .idea-comments .comments-title span{
            color: #383838;
            font-size: 1.1rem;
        }

        .idea-comments .comment-row{
            padding: 15px 0;
        }

        .idea-comments .user-info{
            text-align: center;
            color:#fff;
            padding:15px;
            flex:1;
            flex-basis: 200px;
        }

        .idea-comments .user-info .user-name{
            font-size: 1.2rem;
            font-weight: 500;
            line-height: normal;
            margin-top: 10px;
        }

        .idea-comments .user-info .time{
            font-weight: 300;
        }


        .idea-comments .user-info .user-img{
            min-height: 80px;
            max-height: 100px;
            max-width: 80px;
            background-size: cover;
            margin:auto;
        }

        .idea-comments .message{
            flex:2;
            padding: 10px 15px;
            color: #fff;
            font-weight: 300;
            flex-basis: 300px;
        }

        .idea-comments .input-group.comments-input textarea{
            background-color: #4c4c4c;
            color: #fff;
            border:none;
            border-radius: 0;
            padding: 1rem 0.75rem;
        }

        .idea-comments .input-group.comments-input span.input-group-addon{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            color: #fff;
            border-radius: 0;
            border:none;
        }

        .idea-comments .input-group.comments-input span.input-group-addon:hover{
            background-color: #fff;
            color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
            cursor: pointer;
        }

        .map-container .map-btn{
            border-color: transparent transparent {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .grid-view{
            margin-top: 55px;
        }


        .like-vote-button-voted{
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }

        .voted{
            /*box-shadow: inset 0 0 0 3px {{ Session::get("SITE-CONFIGURATION.color_secondary") }};*/
            background-color: {{ Session::get("SITE-CONFIGURATION.color_secondary") }};
        }

        .ideas-grid .idea-card .vote-container .vote-button a:hover {
            background-color: #4c4c4c;
            text-decoration: none;
        }

        .ideas-grid {
            padding-top:0px;
        }

        .ideas-grid>.row>.col-12 {
            margin-top: 0px;
        }

        .ideas-grid{
            background-color: initial;
        }
    </style>
    <style>
        .points {
            color: #383838;
            font-weight: 600;
            text-align: center;
            line-height: 50px;
        }

        .button-like,
        .button-dislike{
            text-align: center;
            padding: 0;
        }

        .button-like{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
            color: #fff !important;
        }

        .button-dislike{
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
            color: #fff !important;
        }

        .button-like a,
        .button-dislike a{
            padding: 10px 10px;
            color: #fff;
            line-height: 30px;
            vertical-align: middle;
            display: block;
            text-decoration: none;
        }

        .button-like a:hover,
        .button-dislike a:hover{
            background-color: #fff;
            color: {{ ONE::getSiteConfiguration("color_secondary") }} !important;
            cursor: pointer;
            text-decoration: none;
        }

        .ideas-grid .idea-card .vote-container .button-like i.fa, .ideas-grid .idea-card .vote-container .button-dislike i.fa {
            font-size: 0.8rem;
        }

        .button-like:hover{
            color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
            background-color: #c4c4c4!important;
        }
        .create-proposal-banner{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        }

        .create-proposal-banner .submit-idea-btn{
            background-color: #fff !important;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            text-transform: uppercase;
            font-size: 1.2rem;
            padding:10px 30px;
        }

        .create-proposal-banner .submit-idea-btn:hover{
            color: #fff!important;
        }

        .banner-voting-info.submited-votes{
            background-color: rgba(0, 64, 113, 0.7);
            color: #fff;
            padding: 30px 15px;
            color: #fff;
            font-size: 1.2rem;
            text-transform: uppercase;
        }

        .votesInfoBar{
            z-index:100;
        }

        .banner-voting-info {
            text-align: center!important;
        }

        .multipleVoteBtn{
            line-height: 50px;
            cursor: pointer;
        }

        .button-like-voted{

        }
        .selected_filter{
            background-color: #133e71!important;
        }
        /*.vote_allowed{*/
        /*background-color:green!important;*/
        /*}*/
        .vote_not_allowed_reasons{
            background-color:yellow!important;
        }


        .pointer{
            cursor: pointer;
        }

        /* TRANSITIONS EXAMPLE START */
        .topic_block-enter-active, .topic_block-leave-active {
            transition: opacity .5s
        }
        .topic_block-enter, .topic_block-leave-to /* .fade-leave-active in <2.1.8 */ {
            opacity: 0
        }
        .vote_button-enter-active, .vote_button-leave-active {
            transition: opacity .5s;
            transition: opacity 200ms ease-in-out;
            transition-delay: 100ms;

        }
        .vote_button-enter, .vote_button-leave-to /* .fade-leave-active in <2.1.8 */ {
            opacity: 0
        }
        /* TRANSITIONS EXAMPLE FINISH */

        .gmap{
            height: 300px;
            width: 100%;
        }




        .modal-mask {
            position: fixed;
            z-index: 9998;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, .5);
            display: table;
            transition: opacity .3s ease;
        }

        .modal-wrapper {
            display: table-cell;
            vertical-align: middle;
        }

        .modal-container {
            width: 300px;
            margin: 0px auto;
            padding: 20px 30px;
            background-color: #fff;
            border-radius: 2px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, .33);
            transition: all .3s ease;
            font-family: Helvetica, Arial, sans-serif;
        }

        .modal-header h3 {
            margin-top: 0;
            color: #42b983;
        }

        .modal-body {
            margin: 20px 0;
        }

        .modal-default-button {
            float: right;
        }

        /*
         * The following styles are auto-applied to elements with
         * transition="modal" when their visibility is toggled
         * by Vue.js.
         *
         * You can easily play with the modal transition by editing
         * these styles.
         */

        .modal-enter {
            opacity: 0;
        }

        .modal-leave-active {
            opacity: 0;
        }

        .modal-enter .modal-container,
        .modal-leave-active .modal-container {
            -webkit-transform: scale(1.1);
            transform: scale(1.1);
        }
        .hidden{
            display: none;
        }

        .filters-row{
            padding: 7px 0px;
        }

        .vote_allowed>div>div, .vote_not_allowed>div{
            background: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: white;
            padding: 7px 10px;
            text-align: center;
        }

        .vote_allowed>div>div:hover{
            background: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
            color: white!important;
            /*box-shadow: inset 0 0 0 2px {{ ONE::getSiteConfiguration("color_secondary") }}!important;*/
        }

        .vote_allowed>div>.voted, .vote_not_allowed>.voted{
            background: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
            color: white;
            /*box-shadow: inset 0 0 0 2px {{ ONE::getSiteConfiguration("color_secondary") }}!important;*/

        }

        /*.vote_not_allowed{*/
        /*background-color:red!important;*/
        /*}*/

        .banner-voting-info.votes-forbidden{
            width: 100%;
            margin-left: 0px;
            background: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }



        .modal-header{
            border-radius: 0;
        }

        .modal-container{
            width: 50%;
            padding: 0;
        }

        .idea-card .card-img{
            height: 70px;
            /*height: 150px;*/
            background-size: contain!important;
            border-radius: 0;
            background-color: blue;
            background-position: center;
            background-repeat: no-repeat;
        }

        .ideas-grid .idea-card .title{
            height: 50px;
            text-align: center;
        }

        .ideas-grid .idea-card{
            min-height: 0!important;
        }

        .ideas-grid .idea-card {
            padding-right: 15px !important;
            padding-left: 15px !important;
        }

        .idea-card a.a-wrapper{
            border: 1px solid #c5c5c5;
            padding: 10px 10px 0 10px!important;
        }

        .idea-card a.a-wrapper:hover{
            background: white!important;
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .ideas-grid>.row{
            background: white;
        }

        .votes-info-bar{
            background: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        .banner-voting-info.votes-forbidden a:hover{
            background: lightgrey!important;
            color: white!important;
        }

        .ideas-grid.white-ideas .idea-card a.a-wrapper .title{
            color: black!important;
        }

        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover{
            background: white!important;
        }

        #submitVotesButton{
            background: #58595B!important;
        }

        #submitVotesButton:hover{
            background: lightgrey!important;
        }

        .submit-votes-btn-off{
            pointer-events:none;
            background: lightgrey;
        }

        a.a-wrapper .title{
            color: black!important;
        }

        .voted-btn {
            background-color: #dc9119 !important;
            max-height: 42px;
            padding: 10px 0px;
            font-size: 0.7rem;
            text-transform: uppercase;
            vertical-align: middle;
            text-align: center;
            color: white !important;
            font-weight: bold;
            position: relative !important;
            top: 80% !important;
        }

        .idea-comments .input-group.comments-input span.input-group-addon:hover, .page-title, .ideas-list-tile .title a, .map-container .map-btn, .votes-info-bar .submit-votes-btn, .ideas-grid.white-ideas .idea-card a.a-wrapper .title, .icon-loader{
            color: black!important;
        }

    </style>
@endsection

@section('content')

    <div id="root">
        {{--<user id="user-bar" :logged-user="'{{ json_encode($currentUser) }}'">--}}
        {{--<div class="row">--}}
        {{--<div v-if="!confirmed_email">--}}
        {{--<h1>tem de confirmar o email</h1>--}}
        {{--</div>--}}
        {{--<div v-if="showLoginBar">--}}
        {{--<h1>Faça login</h1>--}}
        {{--</div>--}}
        {{--</div>--}}
        {{--</user>--}}

        <cb id="cb-template" :cb-key="'{!! $cbKey !!}'" :cb-type="'{!! $type !!}'" :current-language="'{!! $currentLanguage !!}'" :logged-user="'{{ json_encode($currentUser) }}'" :default-image="'{{ $defaultImage }}'" :cb_layout="'{!! One::getEntityLayout() !!}'">
            <div>
                <div class="container">
                    <div class="row ideas-list-tile">
                        <div clasS="col title">
                            <span>
                    {{ ONE::transCb('cb_title', !empty($cb) ? $cb->cb_key : $cbKey) }}
                </span>
                        </div>
                        <hr>
                    </div>
                    <div class="row">
                        <div class="col-12" style="margin-bottom: 40px"{{-- v-text="contents"--}}>
                            @if(!empty($cb->page_key))
                                <?php
                                $pageContent = \App\Http\Controllers\PublicContentManagerController::showContent('pages', $cb->page_key);
                                if(!empty($pageContent)){
                                    $pageSection = collect($pageContent->sections)->first();

                                    if(!empty($pageSection)){
                                        $sectionValue = collect($pageSection->section_parameters)->first()->value ?? '';
                                    }
                                }
                                ?>
                                {!! $sectionValue !!}
                            @endif
                        </div>
                    </div>
                </div>
                <div class="container-fluid" v-if="showFilters">
                    <div class="row med-grey-bg">
                        <div clasS="col-12 no-padding">
                            <div v-for="parameter in parameters" v-if="parameter.code == 'google_maps'">
                                <div class="map-container">
                                    <div class="no-padding" style="padding: 0">
                                        <div class="collapse" id="collapseExample">
                                            <div class="card card-block map no-padding" id="map" >
                                                <google-map></google-map>
                                            </div>
                                        </div>
                                        <button class="map-btn"  type="button" data-toggle="collapse" data-target="#collapseExample" aria-expanded="false" aria-controls="collapseExample">
                                            <a data-toggle="collapse" href="#collapseExample" aria-expanded="false" aria-controls="collapseExample" style="font-size:14px;">
                                                <i class="fa fa-map-marker" aria-hidden="true"></i>
                                                <p>{{ONE::transCb('cb_list_map', !empty($cb) ? $cb->cb_key : $cbKey)}}</p>
                                            </a>
                                        </button>
                                    </div>
                                </div>

                            </div>
                            <div class="container">
                                <div class="row">
                                    <div class="col-12 ideas-filters">
                                        <div class="row">
                                            <div class="col-lg-8 col-md-7 col-sm-12 col-12">


                                                <?php $i = 0; ?>

                                                <div v-for="parameter in parameters" v-if="parameter.use_filter && parameter.visible">
                                                    <?php $i++; ?>

                                                    <div class="row filters-row" >
                                                        <div class="col-12">
                                                            <div class="filters-labels" style="padding-left:0">
                                                    <span href="#" class="default-button-topics-label">
                                                        <span class="light-blue">@{{parameter.title}}</span>
                                                    </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row filters-row">
                                                        <div class="col-12">
                                                            <div class="topicFilter">
                                                                <div class="filters" style="padding-left:0">
                                                                    <div v-for="option in parameter._options" v-bind:class="{ selected_filter: option.selected }" class='filter my-filter-selector'>
                                                                        <div v-on:click="filterByParameter(option.id,parameter.id)">@{{ option.title }} </div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                                <div v-for="operation in operation_schedules" v-if="operation.action_code == 'show' && operation.type_code == 'ta_results'">
                                                    <div class="row filters-row">
                                                        <div class="col-12">
                                                            <div class="filters-labels" style="padding-left: 0">
                                                                                <span href="#" class="default-button-topics-label">
                                                                                        <span class="light-blue">{{ONE::transCb('cb_status', !empty($cb) ? $cb->cb_key : $cbKey)}}</span>
                                                                                </span>
                                                            </div>
                                                        </div>
                                                    </div>
                                                    <div class="row filters-row">
                                                        <div class="col-12">
                                                            <div class="topicFilter">
                                                                <div class="filters" style="padding-left:0">
                                                                    <div v-bind:class="{ selected_filter : filterStatus == 'approved' }" class="filter my-filter-selector">
                                                                        <div v-on:click="filterByStatus('approved')">{{ONE::transCb('status_approved', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                                                    </div>
                                                                    <div v-bind:class="{ selected_filter : filterStatus == 'not_accepted' }" class="filter my-filter-selector">
                                                                        <div v-bind:class="{ selected_filter : filterStatus }" v-on:click="filterByStatus('not_accepted')">{{ONE::transCb('status_rejected', !empty($cb) ? $cb->cb_key : $cbKey)}}</div>
                                                                    </div>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>

                                            </div>
                                            <div class="col-12 col-sm-12 col-md-5 offset-lg-1 col-lg-3 search-box">
                                                <div class="input-group">
                                                    <input type="text" v-model="search" class="form-control searchTopics" placeholder="{{ONE::transCb('cb_list_search', !empty($cb) ? $cb->cb_key : $cbKey)}}">
                                                    <span class="input-group-btn">
                                            <button class="search-tbn" type="button">
                                                <i class="fa fa-search" aria-hidden="true"></i>
                                            </button>
                                        </span>
                                                </div>
                                            </div>
                                        </div>

                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="container-fluid banner-voting-info no-padding">
                    <div class="banner-voting-info">
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
                                                        <div v-if="configuration.code == 'total_votes_allowed' && configuration.value == vote.user_registered_votes" v-for="configuration in vote.configurations">
                                                            <a id="submitVotesButton" href="{{ action("PublicCbsController@showTopicsVoted",["cbKey"=>$cbKey,"type"=>$type]) }}" class="submit-votes-btn">
                                                                {{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                            </a>
                                                        </div>
                                                        <div v-else-if="configuration.code == 'total_votes_allowed' && configuration.value < vote.user_registered_votes" v-for="configuration in vote.configurations">
                                                            @{{ configuration.value < vote.user_registered_votes }}
                                                            <span class="submit-votes-btn submit-votes-btn-off">
                                                                {{ ONE::transCb('submit_votes', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                            </span>
                                                        </div>
                                                        {{--<a :href="vote.submission_link">SUBMTER VOTO</a>--}}
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

                <div v-for="configuration in configurations" v-if="configuration.code == 'security_create_topics'" class="row create-proposal-banner">
                    <a href="{!! action('PublicTopicController@create', ['cbKey' => $cbKey, 'type' => $type]) !!}" class="submit-idea-btn mx-auto">{{ONE::transCb('proposal_list_create_proposal', !empty($cb) ? $cb->cb_key : $cbKey)}}</a>
                </div>

                <div class="container-fluid ideas-grid white-ideas">

                    <div class="row">
                        <div class="col-12">
                            <div class="grid-view">
                                <div class="container" style="/*height: 100%*/">
                                    <div v-if="loading_topics" style="text-align: center">
                                        <i class="fa fa-circle-o-notch fa-spin fa-3x fa-fw"></i>
                                    </div>
                                    <transition-group name="topic_block" tag="div" class="row" style="display: flex; flex-direction: row;">
                                        <div v-for="topic in filteredTopics" class="topic_block col-12 col-sm-6 col-md-3 idea-card color-text-primary" :key="topic.topic_key" v-if="topic.visible">

                                            <a class="a-wrapper" style="pointer-events: none">
                                                <div v-for="configuration in configurations" v-if="configuration.code == 'show_status'">
                                                    <div class="status-idea green" v-if="topic.status[0].status_type.code == 'approved'">
                                                        @{{ topic.status[0].name }}
                                                    </div>
                                                    <div v-else class="status-idea red">
                                                        @{{ topic.status[0].name }}
                                                    </div>
                                                </div>
                                                <div class="card-img" v-bind:style="{ 'background-image': 'url(' + topic.featuredImage + ')' }">
                                                    <div class="col-12 voted-btn" v-for="vote in topic.vote_events" v-if="vote.user_has_submitted && topic.has_voted[vote.vote_key]">
                                                        <span>
                                                            {{ONE::transCb('cb_voted', !empty($cb) ? $cb->cb_key : $cbKey)}}
                                                        </span>
                                                    </div>
                                                </div>

                                                <div class="title" style="text-align: center; color: black!important;">
                                                    @{{ topic.title }}
                                                </div>
                                            </a>
                                            <transition-group name="vote_button" tag="div" >

                                                <div v-for="vote in topic.vote_events" class="vote_button" :key="topic.topic_key+vote.vote_key" v-if="vote.is_open" style="background: lightgrey">
                                                    <div v-if="vote.can_vote && vote.is_open" class="vote_allowed">
                                                        <div v-if="!vote.allowed" class="vote_not_allowed_reasons">
                                                            <div v-bind:class="{ voted: topic.has_voted[vote.vote_key] }" v-on:click="supplyReasons(vote.vote_key)" style="text-align: center;">
                                                                <span v-if="topic.has_voted[vote.vote_key]"><?php echo ONE::transCb('cb_voted', $cbKey); ?></span>
                                                                <span v-else>VOTE</span>
                                                            </div>
                                                        </div>
                                                        <div v-else style="padding: 7px 30%">

                                                            <div v-bind:class="{ voted: topic.has_voted[vote.vote_key] }" class="pointer"  style="text-align: center;padding: 7px 10px; background: #dc9119" v-on:click="triggerVote(vote,topic,topic.vote_value[vote.vote_key])">
                                                                <div v-if="topic.has_voted[vote.vote_key]">
                                                                    <span class=""><?php echo ONE::transCb('cb_voted', $cbKey); ?></span>
                                                                </div>
                                                                <div v-else>
                                                                    <span class=""><?php echo ONE::transCb('cb_vote', $cbKey); ?></span>

                                                                </div>

                                                            </div>
                                                        </div>
                                                    </div>

                                                </div>
                                            </transition-group>
                                        </div>

                                    </transition-group>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </cb>
    </div>

    <style>
        /*.idea-comments .input-group.comments-input span.input-group-addon:hover, .page-title, .ideas-list-tile .title a, .map-container .map-btn, .votes-info-bar .submit-votes-btn, .ideas-grid.white-ideas .idea-card a.a-wrapper .title, .icon-loader {*/
            /*color: initial!important;*/
        /*}*/

        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover .title{
            color:black!important;
        }

        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover, .ideas-grid.white-ideas .idea-card .a-wrapper .idea-details hr, .ideas-grid .idea-card .vote-container .button-like, .loader a, .login-bg .login-row .login-box, .user-activity-tabs .nav-item .nav-link:hover {
            background-color: initial!important;
            color: black!important;
        }
    </style>
@endsection

@section('scripts')
    <script src="https://unpkg.com/vue@2.1.3/dist/vue.js"></script>
    <script src="https://unpkg.com/axios/dist/axios.min.js"></script>
    <script src="/js/vue_topics.js"></script>
@endsection

