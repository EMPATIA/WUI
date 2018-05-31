<style>
    body{
        font-size: 14px;
    }

    .sticky-wrapper .sticky-menu-wrapper{
        -webkit-transition: background-color 0.5s ease-out;
        -moz-transition: background-color 0.5s ease-out;
        -o-transition: background-color 0.5s ease-out;
        transition: background-color 0.5s ease-out;
    }
    .sticky-wrapper.is-sticky .sticky-menu-wrapper{
        background-color:#fff !important;
        -webkit-box-shadow: 0px 5px 5px 0px rgba(0,0,0,0.04);
        -moz-box-shadow: 0px 5px 5px 0px rgba(0,0,0,0.04);
        box-shadow: 0px 5px 5px 0px rgba(0,0,0,0.04);
    }

    .sticky-wrapper.is-sticky  .top-bar.sticky-menu-wrapper .nav-item a.nav-link{
        color: #383838;
    }

    .no-padding{
        padding: 0;
    }
    @if(ONE::siteConfigurationExists("color_primary"))
        {{-- Background Color Changes --}}
        .form-empatia input,
        .form-empatia .form-control,
        .form-empatia .pickLocation-map,
        .form-empatia [type="radio"]:checked + label:after,
        .form-empatia [type="radio"]:not(:checked) + label:after,
        .form-empatia .files-col .files-box .button,
        .modal-regulations .modal-footer .submit-btn,
        .form-empatia .submit-btn button,
        .primary-color,
        .modal-footer .btn-secondary, .modal-header{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        .modal-footer .btn.btn-secondary,
        .modal-body .btn-success,
        .top-bar .nav-item.dropdown .dropdown-menu a:hover,
        .top-bar .navbar-nav .nav-item.login-btn:hover,
        .banner-bottom a.banner-button:hover,
        .ideas-grid .idea-card a.a-wrapper:hover .see-more-btn hr,
        .footer .footer-col .subscribe .subscribe-btn button:hover,
        .idea-topic-title .ideas-nav-buttons>.nav-right:hover,
        .idea-topic-title .ideas-edit>.col,
        .idea-details-buttons .buttons-row .button-like,
        .social-buttons .follow-btn:hover{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        .social-buttons .share-social-btn:hover,
        .idea-comments .input-group.comments-input span.input-group-addon,
        .submit-idea-btn,
        .votes-info-bar,
        .ideas-grid.white-ideas .idea-card a.a-wrapper:hover,
        .ideas-grid.white-ideas .idea-card .a-wrapper .idea-details hr,
        .ideas-grid .idea-card .vote-container .button-like,
        .loader a,
        .login-bg .login-row .login-box,
        .user-activity-tabs .nav-item .nav-link:hover{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
            color: white!important;
        }
        .table-hover tbody tr:hover,
        .idea-of-comments a:hover,
        .comments-input .input-group span.input-group-addon,
        .personal-area-buttons .button a:hover,
        .personal-area-buttons .button a.active,
        .user-profile .form-row .edit-button a,
        .user-profile input,
        .user-profile .form-control,
        .user-profile .pickLocation-map,
        .user-profile [type="radio"]:checked + label:after{
            background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        }
        .user-profile [type="radio"]:not(:checked) + label:after,
        .user-profile .files-col .files-box .button,
        .user-profile .submit-btn a {
            background-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        {{-- Text Color Changes --}}
        .form-empatia .pickLocation-map:hover
        [type="checkbox"]:not(:checked) + label:after,
        [type="checkbox"]:checked + label:after
        .form-empatia .files-col .files-box .button:hover a,
        .form-empatia .files-col .files-box .button a:hover,
        .modal-regulations .modal-footer .cancel-btn:hover,
        .modal-regulations .modal-footer .submit-btn:hover,
        .form-empatia .submit-btn button:hover,
        .modal-footer .btn-secondary:hover {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        .modal-footer .btn.btn-secondary:hover,
        .small-top-bar .navbar .navbar-nav a.nav-link:hover i,
        .small-top-bar .lang-dropdown a:hover,
        .top-bar .nav-item.dropdown .dropdown-menu a,
        .top-bar .navbar-nav .nav-item.login-btn a,
        .top-bar .navbar-nav .nav-item.user-btn:hover > a,
        .banner-bottom a.banner-button,
        .op-process-box a:hover {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        @media (min-width:768px){
            .container.page-items-container {
                max-width: 100%;
            }
        }
        .ideas-grid .idea-card a.a-wrapper:hover,
        .ideas-grid .idea-card a.a-wrapper:hover .see-more-btn,
        .footer .footer-col .subscribe .subscribe-btn button,
        .footer .footer-col .social-btns a:hover,
        .news-title .title a,
        .idea-topic-title .title a,
        .idea-topic-title .ideas-edit>.col:hover,
        .idea-topic-title .ideas-edit>.col:hover a,
        .idea-content .idea-title,
        .news-content .news-description .see-more-btn {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        .idea-content .idea-description .see-more-btn,
        .idea-content .files .file-row .file-download-btn,
        .idea-content .content-title,
        .idea-content .idea-details .detail-label,
        .idea-details-buttons .buttons-row .button-dislike:hover a,
        
        .social-buttons .follow-btn,
        .social-buttons .share-social-btn,
        .social-buttons .follow-btn a,
        .social-buttons .share-social-btn a,
        .idea-comments .comments-title,
        .idea-comments .input-group.comments-input span.input-group-addon {
            color: {{ ONE::getSiteConfiguration("color_primary") }};
        }

        .idea-comments .input-group.comments-input span.input-group-addon:hover,
        .page-title,
        .ideas-list-tile .title a,
        .map-container .map-btn,
        .votes-info-bar .submit-votes-btn,
        .ideas-grid.white-ideas .idea-card a.a-wrapper .title,
        .icon-loader {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        .login-bg .login-row .login-box .footer-buttons .login-btn a, .login-bg .login-row .login-box .footer-buttons .login-btn button,
        .login-bg .login-row .login-box .footer-buttons .login-btn a, .login-bg .login-row .login-box .footer-buttons .login-btn button,
        .user-activity-tabs .nav-item .nav-link.active,
        .user-activity-tabs .nav-item .nav-link.active:hover,
        .idea-of-comments a,
        .comments-input .input-group span.input-group-addon:hover,
        .user-profile-body .page-title-user,
        .personal-area-buttons .button a,
        .user-profile .pickLocation-map:hover {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }
        [type="checkbox"]:not(:checked) + label:after,
        [type="checkbox"]:checked + label:after,
        .user-profile .files-col .files-box .button:hover a,
        .user-profile .files-col .files-box .button a:hover,
        .user-profile .cancel-btn a:hover,
        .user-profile .submit-btn a:hover {
            color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        {{-- Border Color Changes --}}
        [type="checkbox"]:checked:focus + label:before,
        [type="checkbox"]:not(:checked):focus + label:before,
        .modal-footer .btn-secondary,
        .modal-footer .btn.btn-secondary,
        .modal-footer .btn-secondary:hover,
        .modal-footer .btn.btn-secondary:hover,
        .banner-bottom a.banner-button,
        .activity-table table tbody tr:first-child > td,
        [type="checkbox"]:checked:focus + label:before,
        [type="checkbox"]:not(:checked):focus + label:before {
            border-color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

        {{-- Other Color Changes --}}
        .modal-regulations .modal-footer .submit-btn:hover{
            box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_primary") }}!important;
        }

    @endif

    @if(ONE::siteConfigurationExists("color_secondary"))
        {{-- Background Color Changes --}}
        .form-empatia .form-control:focus,
        .form-empatia .files-col .files-box,
        .secondary-color,
        .top-bar .navbar-nav .nav-item.user-btn.show,
        .top-bar .navbar-nav .nav-item.user-btn.show .dropdown-menu,
        .events-grid .event-card .card-content>.date-title:hover,
        .idea-topic-title .ideas-delete>.col:hover,
        .idea-details-buttons .buttons-row .button-dislike,
        .votes-info-bar .submit-votes-btn:hover,
        .ideas-grid .idea-card .status-idea.green,
        .submit-idea-btn:hover {
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }
        .loader a:hover,
        .idea-comments .user-info.sent,
        .user-profile .form-control:focus {
            background-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }

        {{-- Text Color Changes --}}
        .form-empatia .form-label,
        .home-row-title>a:hover,
        .news-title .title a:hover,
        .idea-topic-title .title a:hover,
        .user-profile .form-label {
            color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }

        {{-- Border Color Changes --}}
        .top-bar .navbar-nav .nav-item.user-btn.show,
        .top-bar .navbar-nav .nav-item.user-btn.show .dropdown-menu {
            border-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        }

        {{-- Other Color Changes --}}
        .map-container .map-btn:hover{
            border-color: transparent transparent {{ ONE::getSiteConfiguration("color_secondary") }} transparent!important;
        }
    @endif

    .sticky-top-bar{
        margin: 0 -15px;
        background-color: #fff;
    }

    .sticky-top-bar img{
        max-height: 40px;
    }

    .sticky-top-bar .navbar-nav{
        width: 100%;
    }

    .sticky-top-bar .nav-item a{
        color: #1d5aa6;
        padding: 3px 5px;
    }

    .sticky-top-bar .nav-item a:hover{
        background-color: #1d5aa6;
        color:#fff;
    }

    .sticky-top-bar .nav-item.login-btn,
    .sticky-top-bar .nav-item.user-btn{
        margin-left: 0;
    }

    .sticky-top-bar .nav-item.login-btn>a,
    .sticky-top-bar .nav-item.user-btn>a{
        border: solid 1px #1d5aa6;
        margin-left: 0;
    }

    .sticky-top-bar .nav-item.user-btn a:hover{
        background-color: #e7302a;
        color: #fff;
    }

    .sticky-top-bar .nav-item.user-btn .dropdown-menu{
        left: auto;
        right: -1px;
    }

    .sticky-top-bar .nav-item.user-btn .dropdown-menu .nav-item a{
        border: none;
    }


    .sticky-top-bar .login-btn a{
        background-color: #1d5aa6;
        color: #fff;
        padding: 5px 15px;
    }

    .sticky-top-bar .login-btn a:hover{
        background-color: #e7302a;
    }

    .sticky-wrapper .navbar .navbar-toggler{
        color: #fff;
        margin-left:auto;
    }

    .sticky-wrapper.is-sticky .navbar .navbar-toggler{
        color: #1d5aa6;
    }

    .sticky-wrapper .navbar .navbar-toggler:hover{
        background-color: #fff;
        color:#1d5aa6;
    }

    .top-bar .navbar-nav .nav-item.user-btn{
        margin:0;
        border:none;
    }

    /* .top-bar .navbar-nav .nav-item.user-btn a.nav-link{
        border: solid 1px #fff;
    } */
    
    .top-bar .navbar-nav .nav-item{
        text-align: left;
    }
    
    .top-bar .navbar-nav .nav-item{
        border-bottom: 2px solid {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }

    .top-bar .navbar-nav .nav-item.user-btn:hover{
        background-color: transparent;
    } 

    .top-bar .navbar-nav .nav-item.user-btn a.nav-link{
        border: solid 1px #fff;
    }

    .top-bar .navbar-nav .nav-item.user-btn a.nav-link:hover{
        background-color: #fff;
        color:{{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }


    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .navbar-nav .nav-item{
        border-bottom: solid 2px #fff;
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .navbar-nav .nav-item:hover{
        border-bottom: solid 2px {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }


    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn{
        margin-left:auto;
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn{
        margin-left:0;
    }

    @media (min-width: 992px){
        .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn {
            margin-left:auto;
        }
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn:hover,
    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn{
        background-color: transparent;
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn:hover{
        border-bottom: solid 2px #fff;
    }


    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn a.nav-link{
        border-width:1px;
        border-style: solid;
        border-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        background-color: #fff;
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn>.show>a.nav-link:hover,
    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .nav-item.user-btn a.nav-link:hover{
        border-width:1px;
        border-style: solid;
        border-color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        color:#fff;
        background-color:  {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }


    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .dropdown-menu.show{
        background-color:  {{ Session::get("SITE-CONFIGURATION.color_primary") }};
        color.#fff;
    }

    .sticky-wrapper.is-sticky .top-bar.sticky-menu-wrapper .dropdown-menu.show a{ 
        color: #fff !important;
    }


    .background-image{
        min-height: 40vh;
        height: auto;
    }

    .button-nav-demo{
        border: 1px solid {{ ONE::getSiteConfiguration("color_secondary") }}!important;
    }

    .button-nav-demo i{
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
    }

    .login-bg .login-row .login-box .login-form ::placeholder {
        color: lightgrey;
        opacity: 1; /* Firefox */
    }

    @media (min-width: 992px) {
        .navbar .nav-item {
            display: block;
        }

        .top-bar .navbar-nav .nav-item.user-btn {
            margin-left: auto;
            margin-top: 0;
        }

        .top-bar .navbar-nav .nav-item.login-btn {
            margin-top:0;
        }

        .sticky-top-bar .nav-item.login-btn,
        .sticky-top-bar .nav-item.user-btn{
            margin-left: auto;
        }
    }

    @media (max-width: 992px) {
        .nav-link-responsive{
            width: 85%!important;
        }

        .user-btn{
            margin-top: 10px!important;
        }

        .navbar .nav-item {
            display: block;
        }

        .top-bar .navbar-nav .nav-item.user-btn {
            margin-top: 0;
            border: none;
        }

        .top-bar .navbar-nav .nav-item.user-btn a.nav-link{
            display:inline-block;
        }

        .top-bar .navbar-nav .nav-item.login-btn {
            margin-top:10px;
        }

        .sticky-top-bar .nav-item.login-btn,
        .sticky-top-bar .nav-item.user-btn{
            margin-left: auto;
        }
    }

    .container.page-items-container {
        padding:0;
    }

    .op-process-box>a{
        padding: 10px 35px;
    }


    @media (min-width: 576px){
        .container.page-items-container {
            width: 100%;
            padding:0;
        }

        .op-process-box>a{
            padding: 10px 35px;
        }
    }    

    @media (min-width:768px){
        .container.page-items-container {
            width: 720px;
            padding: 0 15px;
        }
        
        .op-process-box>a{
            padding: 40px 35px;
        }
    }    

    @media (min-width: 992px){
        .container.page-items-container {
            width: 960px;
            max-width: 100%;
            padding: 0 15px;
        }
    }
      
    @media (min-width: 1200px){
        .container.page-items-container {
            width: 1140px;
            max-width: 100%;
            padding: 0 15px;
        }
    }



    a.button-link-wrapper:hover .button-like{
        background-color: #4c4c4c!important;
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important
    }

    a.button-link-wrapper:hover .button-dislike{
        background-color: #4c4c4c!important;
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important
    }

    
    .top-bar .navbar-nav .nav-item.user-btn.show, .top-bar .navbar-nav .nav-item.user-btn.show .dropdown-menu {
        background-color: #fff!important;
        border-color:#fff!important;
    }
    .nav-item.dropdown.user-btn.show .nav-link {
        color: {{ Session::get("SITE-CONFIGURATION.color_primary") }};
    }

    
    .ideas-grid .idea-details-buttons .buttons-row .button-link-wrapper:hover .button-like {
        background-color: #4c4c4c!important;
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important
    }




    /* ######################################################### Styles for the list of topics #########################################################*/
    .search-box{
        margin-top:5px;
    }
    
    .page-title{
        font-size: 1.8rem;
        color: #fff;
        text-transform: uppercase;
    }

    .title-description{
        color:#fff;
        font-size: 0.9rem;
        line-height: normal;
    }

    .topics-detail .page-title,
    .topics-list .page-title{
        text-align: center;
        font-weight: 600;
    }

    .banner-voting-info{
        text-align: center;
        /*padding: 10px 5px;*/
        font-size: 0.75rem;
        z-index: 1000;
    }


    /* The sticky class is added to the header with JS when it reaches its scroll position */
    .banner-voting-info.sticky {
        position: fixed;
        top: 4.1rem;
        left: 0;
        width: 100%;
        margin: 0;
    }

    /* Add some top padding to the page content to prevent sudden quick movement (as the header gets a new position at the top of the page (position:fixed and top:0) */
    .banner-voting-info.sticky + .back-btn-row {
        padding-top: 200px;
    }

    /* ####### Banner votes Allowed ######*/
    .banner-voting-info.votes-allowed{
        background-color: rgba(255, 255, 255, 0.7);
        color: #383838;
    }

    .banner-voting-info.votes-allowed .n-votes-txt{
        text-transform: uppercase;
        font-size: 0.75rem;
    }

    .banner-voting-info.votes-allowed .n-votes{
        font-weight: 700;
        font-size: 1.4rem;
    }

    .voted-confirm-topic-wrapper .idea-title-box .a-wrapper{
        background-color: #fff;
        padding: 20px 30px;
        line-height: normal;
        display: flex;
        align-items: center;
        margin-top: 10px;
        text-decoration: none;
        cursor: default;
    }

    .voted-confirm-topic-wrapper .idea-title-box .a-wrapper .idea-number{
        color: #c4c4c4;
        font-size: 1rem;
        margin-right: 5px;
    }
    .voted-confirm-topic-wrapper .idea-title-box .a-wrapper .idea-name{
        color:  {{ ONE::getSiteConfiguration("color_primary") }};
        text-transform: uppercase;
        font-size: 1.2rem;
        font-weight: 500;
    }

    .voted-btn{
        background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
        max-height: 42px;
        padding: 10px 0px;    
        font-size: 0.7rem;
        text-transform: uppercase;
        vertical-align: middle;
        text-align: center;
        font-weight: bold;
        position: absolute;
        top: 114px;
    }

    /* ####### Banner votes Forbidden ######*/

    .banner-voting-info.votes-forbidden{
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        font-size: 1.2rem;
        font-weight: 400;
        text-transform: uppercase;
    }

    .banner-voting-col{
        display: flex;
        padding: 0px;
    }

    .banner-voting-info.votes-forbidden i.fa{
        font-size: 1.5rem;
        margin-right: 5px;
    }

    .banner-voting-info.votes-forbidden a{
        font-weight: 600;
        color: #fff;
        padding: 20px 15px;
        text-align: center;
        flex: 1;
    }

    .banner-voting-info.votes-forbidden a:hover{
        text-decoration: none;
        background-color: #fff;
        color: #383838;
    }

    /* ####### Banner enough votes to submit ######*/

    .banner-voting-info.votes-to-submit{
        background-color: rgba(0, 0, 0, 0.7);
        color: #fff;
        padding: 30px 15px;
    }

    .banner-voting-info.votes-to-submit a.submit-votes-btn{
        border: solid 2px #fff;
        color: #fff;
        padding:10px 30px;
        text-transform: uppercase;
        font-size: 1rem;
    }

    .banner-voting-info.votes-to-submit a.submit-votes-btn:hover{
        border: solid 2px #fff;
        color: #383838;
        background-color: #fff;
        text-decoration: none;
    }

    /* ####### Banner submited Votes######*/

    .banner-voting-info.submited-votes{
        background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        color: #fff;
        padding: 30px 15px;
        color: #fff;
        font-size: 1.2rem;
        text-transform: uppercase;
    }

    /* ### End specific vote banners ###*/


    /* ####### In person votes ########*/

    
    .inperson-bg{
        background-size: cover;
        display: flex;
        flex-direction: column;
        height:100%;
    }

    .inperson-bg .topbar{
        background-color: #fff;
        justify-content: center;
        padding: 7px 0;
    }


    .inperson-bg .inperson-row{
        justify-content: center;
        align-items: center;
        margin: 0;
        height: 100%;
    }

    .inperson-bg .inperson-row .inperson-title {
        background-color: #fff;
        color:  {{ ONE::getSiteConfiguration("color_primary") }};
        font-size: 1.2rem;
        text-transform: uppercase;
        text-align: right;
        font-weight: 600;
        line-height: 35px;
    }

    .inperson-bg .inperson-row .inperson-box{
        background-color:  {{ ONE::getSiteConfiguration("color_primary") }};
        color: #fff;
    }

    .inperson-bg .inperson-row .inperson-box .remaining-votes{
        font-size: 0.8rem;
        background-color:  {{ ONE::getSiteConfiguration("color_secondary") }};
        line-height: 35px;
    }

    .inperson-bg .inperson-row .inperson-box .remaining-votes .number{
        font-size: 1.2rem;
        font-weight: 600;
    }

    .inperson-bg .inperson-row .inperson-box .remaining-votes .between-bar{
        width: 2px;
        display: inline-block;
        height: 25px;
        background-color: #fff;
        vertical-align: middle;
        margin: 0 10px;
    }

    .inperson-bg .inperson-row .inperson-box .title{
        text-transform: uppercase;
        font-size: 1rem;
        font-weight: 600;
        margin: 35px 0 0 0;
    }

    .inperson-bg .inperson-row .inperson-box .description{
        font-size: 0.9rem;
        margin-bottom: 15px;
    }

    .inperson-bg .inperson-row .inperson-box .content{
        padding: 10px 10px;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea{
        display: flex;
        flex-direction: row;
        padding: 5px 10px;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper{
        display: flex;
        width: 100%;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper:hover{
        text-decoration: none;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .icon {
        color: #fff;
        display: flex;
        justify-content: center;
        align-items: center;
        font-size: 3rem;
        padding: 0 15px;
    }


    .inperson-bg .inperson-row .inperson-box .content .idea .img{
        background-size: cover;
        width: 100%;
        flex: 1;
        min-height: 80px;
        min-width: 80px;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .idea-title{
        background-color: #fff;
        margin-left: 5px;
        color: #5f5f5f;
        font-size: 0.85rem;
        padding: 3px 5px;
        line-height: normal;
        flex: 2;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper:hover .idea-title{
        background-color:   {{ ONE::getSiteConfiguration("color_secondary") }};
        color: #fff;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper.active .idea-title{
        background-color:   {{ ONE::getSiteConfiguration("color_secondary") }};
        color: #fff;
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper.active .icon{
        color:   {{ ONE::getSiteConfiguration("color_secondary") }};
    }

    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper.active .icon,
    .inperson-bg .inperson-row .inperson-box .content .idea .a-wrapper:hover .icon {
        color:   {{ ONE::getSiteConfiguration("color_secondary") }};
    }


    .inperson-bg .inperson-row .inperson-box .footer-buttons{
        padding: 30px 0 20px 0;
        text-align: right;
    }
    .inperson-bg .inperson-row .inperson-box .footer-buttons input,
    .inperson-bg .inperson-row .inperson-box .footer-buttons a{
        display: inline-block;
        padding: 7px 30px;
        text-align: center;
        font-weight: 600;
        text-transform: uppercase;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .login-btn,
    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn{
        padding: 0 10px;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .login-btn input,
    .inperson-bg .inperson-row .inperson-box .footer-buttons .login-btn a{
        border: solid 1px #fff;
        background-color: #fff;
        color:  {{ ONE::getSiteConfiguration("color_primary") }};
        cursor:pointer;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .login-btn input:hover,
    .inperson-bg .inperson-row .inperson-box .footer-buttons .login-btn a:hover{
        background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
        color:  #fff;
        text-decoration: none;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn{
        text-align: left;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn input,
    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn a{
        border: solid 1px #fff;
        background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        color:  #fff;
        cursor:pointer;
    }

    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn input:hover,
    .inperson-bg .inperson-row .inperson-box .footer-buttons .back-btn a:hover{
        background-color: #fff;
        color:  {{ ONE::getSiteConfiguration("color_primary") }};
        text-decoration: none;
    }

    input.form-control{
        border-radius: 0;
    }

    #registration .form-group{
        width: 100%;
    }

    .topics{
        height: 400px;
        overflow-y:auto;
        overflow-x: hidden;
    }

    #voting-msg .title{
        margin-top: 5px;
    }

    #voting .img{
        background-color: #fff;
    }

    #voting .idea-title{
        padding: 7px 15px;
        font-size: 1rem;
        font-weight: 400;
        cursor: pointer;
    }
    #voting .active-topic .idea-title {
        color: #fff;
        background-color: {{ ONE::getSiteConfiguration("color_secondary") }};
    }

    #voting .active-topic:hover .idea-title {
        box-shadow: inset 0px 0px 0px 2px #fff;
    }
    

    @media (min-width: 576px){
        .modal-dialog.in-person-modal {
            max-width: 800px;
        }
    }

    .modal-dialog.in-person-modal .modal-header,
    .modal-dialog.in-person-modal .modal-content {
        border-radius: 0;
    }

    /* .topic-key.a-wrapper.active-topic{
        max-height: 160px;
    } */

    .votesConfirmed{
        height: 160px;
        vertical-align: middle;
        line-height: normal;
    }

    .alignVertical{
        display: flex;
        align-items: center;
    }

    .in-submit-btn{
        pointer-events: none;
        background-color: #c0c0c0;
        border:1px solid #c0c0c0;
    }

    /* ### End inperson vote ###*/

    ul.square-bullet {
        list-style-type: square;
    }
    
    .modal-footer .cancel-btn,
    .modal-footer .cancel-btn button,
    .modal-footer .cancel-btn a {
        background-color: #4c4c4c;
        color: #fff;
    }
    .modal-footer .cancel-btn:hover,
    .modal-footer .cancel-btn button:hover,
    .modal-footer .cancel-btn a:hover {
        background-color: #c4c4c4;
        color: #fff;
        cursor: pointer;
        text-decoration: none;
    }
    .modal-footer .cancel-btn,
    .modal-footer .submit-btn {
        text-align: center;
        padding: 5px 15px;
        line-height: 20px;
        display: block;
        width: 100%;
    }
    .modal-footer .cancel-btn,
    .modal-footer .submit-btn {
        border: none;
        box-shadow: none;
    }
    .modal-footer .submit-btn,
    .modal-footer .submit-btn button,
    .modal-footer .submit-btn a {
        background-color: {{ ONE::getSiteConfiguration("color_primary") }};
        color: #fff;
    }
    .modal-footer .submit-btn:hover,
    .modal-footer .submit-btn button:hover,
    .modal-footer .submit-btn a:hover {
        background-color: #fff;
        color:  {{ ONE::getSiteConfiguration("color_primary") }};
        cursor: pointer;
        text-decoration: none;
    }

    .modal-footer .submit-btn:hover {
        box-shadow: inset 0px 0px 0px 2px  {{ ONE::getSiteConfiguration("color_primary") }};
    }
    
    .modal-footer {
        padding: 7px 15px 15px 15px;
        border: none;
        justify-content: center;
    
    }

    .alert-btn-inside{
        border-radius: 0;
        background-color: #fff;
        color:#bf524e;
        font-size: 0.8rem;
        box-shadow: none;
        border: none;
        padding: 3px 15px;
    }
    .alert-btn-inside:hover{
        background-color: #bf524e;
        color:#fff;
        cursor: pointer;
    }

    input[type="checkbox"]{
        width:auto;
    }

    input[type="checkbox"].custom-control-input{
        left: 0;
        top: 7px;
    }

    
    .user-profile .files-col .button-container.dark-grey-bg a:hover{
        background-color: #797979;
        color: {{ ONE::getSiteConfiguration("color_secondary") }};
    }


    .custom-form-row {
        padding: 15px 0px;
        padding-top: 15px;
        padding-right: 0px;
        padding-bottom: 15px;
        padding-left: 0px;
        margin-bottom: 5px;
    }

    .custom-form-row .form-group {
        margin-bottom:0;
    }

    
    .user-profile input:disabled,
    .user-profile .form-control[readonly]{
        background-color: #4c4c4c;
        color: #fff;
    }

    .user-profile input:disabled::placeholder,
    .user-profile .form-control[readonly]::placeholder{
        color: #fff;
    }

    .user-profile .files-col .photo-box .box.image-div{
        height: 200px;
        justify-content: center;
        align-items: center;
        display: flex;
    }

    input.form-control.form-control-success:disabled{
        background-color: #d1f2d6;
        color: #5cb85c;
    }

    input.form-control.form-control-warning:disabled{
        background-color: #ffdc66;
        color: #ca8400;
    }

    .form-group .form-small-btn{
        font-size: 0.8rem;
        border: none;
        box-shadow: none;
        cursor: pointer;
        padding: 3px 10px;
        text-decoration: none;
    }

    .form-group .form-small-btn.yellow{
        background-color:#eb9221;
        color: #fff;
    }

    .form-group .form-small-btn.yellow:hover{
        background-color:#ffdc66;
        color: #eb9221;
    }

    #user-image{
        font-size:14px,
    }
</style>