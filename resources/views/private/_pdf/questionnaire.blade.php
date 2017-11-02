<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Questionnaire Template</title>
</head>
<body>
    <div class="header">
    </div>    
    <div class="footer">
         <span class="pagenum"></span>
    </div>    
    
    <section class="content">
        @yield('content')
    </section>
    
    <style>
        .header,
        .footer {
            width: 100%;
            text-align: right;
            position: fixed;
            margin-top:-10px;    
            margin-right:10px;    
        }
        .header {
            top: 0px;
        }
        .footer {
            bottom: 20px;
        }
        .pagenum:before {
            content: counter(page);
        }        
    </style>      
</body>
</html>