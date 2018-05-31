<style>
    label{
        margin: 0;
    }

    .oneFormSubmit{
        display: none;
    }

    .files-box .button:hover .upload_files{
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
    }

    @media (min-width: 767px) {
        .files-col {
            padding: 0px 15px 0px 0px !important;
        }
    }

    .personal-area-buttons .button a:hover{
        color: #f0f0f0 !important;
    }

    .personal-area-buttons .button a:focus{
        text-decoration: none;
    }

    .personal-area-buttons .button a.active{
        color: #f0f0f0 !important;
    }

    .idea-comments{
        padding: 0 30px;
        height: auto;
        max-height: 600px;
        overflow-y: auto;
    }

    .margin-left-auto {
        margin-left: auto;
    }

    .comments-input .input-group textarea {
        background-color: #4c4c4c;
        color: #fff;
        border: none;
        border-radius: 0;
        padding: 1rem 0.75rem;
    }

    .comments-input {
        margin-top: 20px;
        margin-bottom: 50px;
    }

    .comments-input .input-group span.input-group-addon{
        color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        cursor: pointer;
    }

    .comments-input .input-group span.input-group-addon:hover{
        background-color: {{ ONE::getSiteConfiguration("color_secondary") }}!important;
        color: {{ ONE::getSiteConfiguration("color_primary") }}!important;
    }

    .modal-footer .cancel-btn, .modal-footer .submit-btn {
            border: none;
            box-shadow: none;    
            text-align: center;
            padding: 5px 15px;
            line-height: 20px;
            display: block;
            width: 100%;
    }

    .modal-footer .cancel-btn{
        background-color: #4c4c4c !important;
        color: #fff;
    }

    .modal-footer .submit-btn{
        background-color: {{ ONE::getSiteConfiguration("color_primary") }} !important;
        color: #fff;
    }


    .modal-footer .submit-btn:hover {
        box-shadow: inset 0px 0px 0px 2px {{ ONE::getSiteConfiguration("color_primary") }} !important;
        background-color: #fff !important;
        color: {{ ONE::getSiteConfiguration("color_primary") }} !important;
        cursor: pointer;
    }

    .modal-footer .cancel-btn:hover {
        background-color: #383838 !important;
        color: {{ ONE::getSiteConfiguration("color_secondary") }};
        cursor: pointer;
        text-decoration: none;
    }

    @media (min-width: 576px){
        .modal-dialog {
            width: 70%;
            max-width: 700px;
            margin: 1.75rem auto;
        }
        .modal-body {
            padding: 3rem 6rem;
        }
        .modal-footer {
            margin-top: 3rem;
        }

        .form-container-padding, .idea-topic-title{
            padding:0;
        }

        .files-col {
            margin-top: 0;
        }
    }

    .form-container-padding{
        padding:30px;
    }
    .idea-topic-title{
        padding:15px;
    }

    .files-col {
        margin-top: 15px;
    }

</style>