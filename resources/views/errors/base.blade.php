<!DOCTYPE html>
<html>
<head>
    @hasSection("pageTitle")
        @yield("pageTitle")
    @else
        <title>Page not available</title>
    @endif
    <!-- Fontawesome-->
    <script src="https://use.fontawesome.com/c6882c66de.js"></script>

    <!-- Bootstrap-->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/css/bootstrap.min.css" integrity="sha384-/Y6pD6FV/Vv2HJnA6t+vslU6fwYXjCFtcEpHbNJ0lyAFsXTsjBbfaDjzALeQsN6M" crossorigin="anonymous">
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js" integrity="sha384-KJ3o2DKtIkvYIK3UENzmM7KCkRr/rE9/Qpg6aAZGJwFDMVNA/GpGFF93hXpG5KkN" crossorigin="anonymous"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.11.0/umd/popper.min.js" integrity="sha384-b/U6ypiBEHpOf/4+1nzFpr53nxSS+GLCkfwBdFNTxtclqqenISfwAzpKaMNFNmj4" crossorigin="anonymous"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0-beta/js/bootstrap.min.js" integrity="sha384-h0AbiXch4ZDo7tp9hKZ4TsHbi047NrKGLO3SEJAg45jXxnGIfYzk4Si90RDIqNm1" crossorigin="anonymous"></script>

    <style>
        body{
            color:#656565;
            line-height: normal;
            font-size:25px;
        }

        .image-column{
            padding: 0 50px;
            align-self:center;
        }

        .image-column img{
            width:100%;
            max-width: 500px;
            height:auto;
            margin:auto;
        }

        .text-column{
            align-self:center;
        }

        .text-small{
            margin-top: 20px;
            font-size: 1.2em;
        }

        .column-icon{
            padding:0 15px;
            font-size: 2.2em;
            flex:0;
        }

        .column-title{
            padding:0 15px;
            flex:1;
        }

        .title-big{
            margin:0;
            font-weight: 400;
            font-size: 1.4em;
        }

        .title-small{
            margin:0;
            font-weight: 400;
            color: #6b6b6b;
            font-size: 1.1em;
        }

        .button-wrapper{
            margin-top:42px;
        }

        .btn-goBack{
            padding: 5px 30px;
            border: solid 2px #e2e2e2;
            color: #888888;
            border-radius: 8px;
        }

        .btn-goBack:hover, .btn-goBack:active, .btn-goBack:focus{
            border: solid 2px  #888888;
            color: white;
            background-color: #888888;
        }

        .h-100{
            height:100vh!important;
        }


        /** Responsive like Bootstrap Media Queries  ---------------------------------------------------------*/
        /* Large desktops and laptops */
        @media (min-width: 1200px) {

        }

        /* Landscape tablets and medium desktops */
        @media (min-width: 992px) and (max-width: 1199px) {

        }

        /* Portrait tablets and small desktops */
        @media (min-width: 768px) and (max-width: 991px) {
            body {
                font-size:16px;
            }

        }

        /* Landscape phones and portrait tablets */
        @media (max-width: 767px) {
            body {
                font-size:12px;
            }

            .image-column{
                padding: 0 15px;
                align-self: flex-end;
                margin-bottom: 20px;
            }

            .text-column{
                align-self:flex-start;
            }

            .image-column img {
                max-width: 300px;
            }
        }

        /* Portrait phones and smaller */
        @media (max-width: 480px) {
            body {
                font-size:12px;
            }
            .image-column{
                padding: 0 15px;
            }

            .image-column img {
                max-width: 200px;
            }

            .text-column{
                align-self:normal;
            }
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row h-100">
        <div class="col-12 col-sm-12 col-md-6 flex-sm-middle image-column">
            @yield("image")
        </div>
        <div class="col-12 col-sm-12 col-md-6 text-column">
            <div class="row">
                <div class="column-icon">
                    @yield("icon")
                </div>
                <div class="column-title align-self-center">
                    @hasSection("subtitle")
                        <h6 class="title-small">
                            @yield("subtitle")
                        </h6>
                    @endif
                    @hasSection("title")
                        <h6 class="title-big">
                            @yield("title")
                        </h6>
                    @endif
                </div>
            </div>
            <div class="row">
                <div class="col-12">
                    @hasSection("description")
                        <p class="text-small">
                            @yield("description")
                        </p>
                    @endif
                    @hasSection("button")
                        <div class="button-wrapper">
                            @yield("button")
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>
</body>
</html>