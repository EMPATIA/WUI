<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    {{--<meta charset="UTF-8">--}}
    <title>Topic Template</title>
    {{--@yield('header_styles')--}}
    {{--<link href="{{ asset(ltrim(elixir("css/general.css"), "/"))}}" rel="stylesheet" type="text/css"/>--}}
    {{--<!-- Main CSS -->--}}


    <style>
        body{
            font-size: 50%;
            font-family: "DejaVu Sans", Sans-Serif;
            overflow-x: hidden;
        }
        .topBarLine{
            background-color: #7EAC37;
            height: 15px;
        }


        .logoDefaultBig{
            text-align: left;
            max-height: 150px;
            width:auto;
        }

        .pageSectionTitle h1{
            margin-top: 50px;
            font-size: 2em;
            color: #7EAC37;
        }

        .proposalContentContainer{
            margin-top: 10px;
        }

        .proposalTopicLeftColumn{
            margin-top: 15px;
        }

        .proposalCb-title h4{
            margin: 0;
            margin-top:20px;
            font-size: 1.5em;
        }

        .proposalTopic-title h3{
            margin: 1px 0;
            font-size: 2em;
            display: inline-block;
            padding:0
        }

        .proposalTopicSummary{
            font-size: 1.5em;
            font-weight: 500;
            margin-bottom: 20px;
        }

        .proposalTopicdescription{
            font-size: 1.5em;
        }

        .parameter-text{
            font-size: 1.5em;
        }

        .proposalTopicDetails{
            margin-top: 30px;
            margin-bottom: 30px;
            font-size: 1.5em;
        }

        .proposalTopicDetails i.fa{
            font-size: 1.5em;
            margin-right: 3px;
            color: #7EAC37;
        }

        .proposalTopicRightColumn-titleLine{
            border-bottom:solid 1px #818181;
            margin-bottom: 20px;
        }

        .proposalTopicRightColumn-title h4{
            color:#7EAC37;
            font-size: 2.7em;
            margin: 0;
        }

        .proposalDetailsSection{
            margin-top: 10px;
            margin-bottom: 70px;
        }

        .proposalTopicRightColumnAlert{
            background-color: #7EAC37;
            color: #ffffff;
            padding:10px;
            border-radius:5px;
        }

        .proposalTopicRightColumnAlert a{
            color: #ffffff;
        }

        .spaceDiv{
            margin: 10px;
            display: inline-block;
        }

        .editProposalBtn{
            background-color: #7EAC37;
            padding: 5px 10px;
            color:#ffffff;
            border:solid 2px #7EAC37;
            font-size: 1.4em;
            border-radius: 5px;
        }

        .deleteProposalBtn{
            background-color: #ffffff;
            color: #818181;
            padding: 5px 10px;
            border:solid 2px #818181;
            font-size: 1.4em;
            border-radius: 5px;
        }

        .proposalNavigationButtons{
            margin-top: 10px;
            margin-bottom: 10px;
        }

        .proposalTopicBackBtn{
            background-color: #f5f0f0;
            color: #000000;
            padding: 5px 15px;
            border:solid 2px #818181;
            font-size: 1.4em;
            border-radius: 5px;
            display: inline-block;
            margin-top: 10px;
        }

        .proposalTopicBackBtn:active,
        .proposalTopicBackBtn:focus,
        .proposalTopicBackBtn:hover{
            background-color: #d4d0d0;
            color: #000000;
        }

        .proposalParameters{
            font-size: 1.5em;
        }

        .proposalParameters i.fa{
            color: #7EAC37;
            font-size: 1.6em;
            margin-right: 5px;
        }

        .proposalComments-container{
            margin-top: 20px;
            font-size: 1.5em;
        }

        .proposalComments-title h3{
            font-size: 1.5em;
            color: #7EAC37;
            margin-top: 3px;
            margin-bottom: 5px;
        }

        .proposalTopiComments-titleLine{
            border-bottom:solid 2px #e9e4e4;
            margin-bottom: 10px;
        }

        .insertCommentRow{
            margin-top: 20px;
        }

        .commentsUserDefaultIcon .fa{
            font-size: 5em;
            color: #7EAC37;
        }

        .proposalCommentInput{
            width:100%;
            border-radius:8px;
        }

        .proposalCommentInput .form-control:last-child,
        .proposalCommentInput .form-control:first-child,
        .proposalCommentInput .input-group,
        .proposalCommentInput input[type=text]{
            min-height:70px;
            border-radius:10px;
            width:100%;
        }

        .commentSubmitBtn{
            float: right;
            margin-top: 15px;
            margin-bottom: 15px;
        }

        .commentSubmitBtn button{
            background-color: #7EAC37;
            padding: 5px 15px;
            color:#ffffff;
            border:solid 2px #7EAC37;
            font-size: 1em;
            border-radius: 5px;
        }

        .commentsPersonDefaultIcon{
            font-size: 4em;
            color: #818181;
            text-align: right;
        }

        .personsComments{
            margin-top: 10px;
        }

        .proposalPersonsComments-line{
            border-bottom:solid 2px #e9e4e4;
            margin-bottom: 20px;
            margin-left: 30px;
            margin-right: 30px;
        }

        .personsCommentsCreationDate{
            font-size: 0.9em;
            margin-top:3px;
            margin-bottom: 5px;
        }

        .personsCommentsName{
            font-weight: 700;
            font-size: 1.5em;
            margin: 0;
        }

        .personsCommentContent{
            margin-top: 10px;
            margin-bottom: 10px;
            padding-right: 30px;
        }

        .personsCommentsEditButton{
            padding-right: 30px;
        }

        .filesBox{
            font-size: 1.5em;
        }


        /* Voting Buttons */

        .buttonVote{
            max-height: 30px;
        }

        .voteSeparator{
            border-top:1px solid #e7e7e7;
        }

        .oneLikesRowStyle{
            margin-top: 20px;
            margin-left:15px;
        }

        .oneLikesRowStyleList{
            margin-left:0px;
        }

        .likeDislikeBtnDivStyle{
            padding: 0;
            max-width: 40px;
        }

        /* Proposals in List */

        .ideaBoxList {
            border: 2px solid #7EAC37;
            display:table;
            width: 100%;
            margin-bottom: 25px;
        }

        .row .no-float {
            display: table-cell;
            float: none;
        }

        .ideaInListRow{
            height: 100%;
            display: table-row;
        }

        .ideaImageWrapperInList{
            position: relative;
        }

        .ideaContentInList{
            font-size: 1.5em;
            margin:0px 10px;
            height:100px;
        }

        .ideaImageInList{
            background: #dedede;
            background-color: #dedede;
            background-size: auto 60%;
            background-repeat: no-repeat;
            background-position: center;
            position: absolute;
            top: 0px;
            right: 0px;
            bottom: 0px;
            left: 0px;
            height:100%;
            width:100%;
        }

        .ideaInListText{
        }

        .ideaViewMoreInList{
            background-color: #7EAC37;
            color: #ffffff;
            padding-left: 0;
        }

        .ideaViewMoreInList a{
            font-size: 1.5em;
            color: #ffffff;
            margin: auto;
            display: block;
            text-align: center;
            padding: 5px;
        }

        .proposalShareLinks a{
            font-size: 1.4em;
        }

        .proposalShareLinks .fa{
            font-size: 2.5em;
        }

        .topic-detail-title {
            margin-top: 30px;
            margin-bottom: 40px;
        }

        h1, h2, h3, h4, h5, h6 {
            color: #006b72;
        }

        .param-text-area-title {
            display: block;
            font-weight: bold;
            border-bottom: 3px solid #8C8C8C;
            margin-bottom: 10px;
            margin-top: 30px;
            padding-bottom: 5px;
            font-size:10px;
        }
    </style>

</head>
<body>
    <div class="footer">
         <span class="pagenum"></span>
    </div>    
    
    <section class="content">
        @yield('content')
    </section>
    

</body>
</html>