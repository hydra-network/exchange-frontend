<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>{{ config('app.name') }}</title> 
    <link href="https://use.fontawesome.com/releases/v5.0.6/css/all.css" rel="stylesheet">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <link href="https://fonts.googleapis.com/css?family=PT+Sans+Caption" rel="stylesheet">
    <link rel="icon" type="image/png" href="/img/favicon.ico">
    
    <!-- Styles -->
    <style>
        body {
            font-family: 'PT Sans Caption', sans-serif;
            background-color: #0f2440;
        }
        header {
              -webkit-background-size: cover;
              -moz-background-size: cover;
              -o-background-size: cover;
              background-size: cover;
        }
        
        .follow {
            padding-top: 60px;
            text-align: center;
        }
        
        .follow a {
            color: white;
            font-size: 50px;
            padding: 14px;
        }
        
        a {
            color: #000000;
        }
        
        .header-item{
          min-height: 87vh;

        }

        .register-form {
            color: white;
        }
        
        .register-form input {
            color: #333 !important;
        }
        
        .carousel-inner {
            overflow: visible;
        }
        
        .portfolio-item {
          margin-bottom: 30px;
        }
        
        .carousel-caption {
            bottom: auto !important;
            text-align: left !important;
        }
        
        .register-form label {
            display: block;
            text-align: left;
        }
        
        .register-form label input {
            
        }
        
        .carousel-caption {
            text-shadow: 0 0 0 rgba(0,0,0,.6) !important;
        }
        
        .carousel-caption .welcome-message {
            text-shadow: 0 0 0 rgba(0,0,0,.6) !important;
        }
        
        @media (max-height: 680px) {
            .register-form h1 {
                font-size: 20px !important;
            }
            
            .register-form h3 {
                font-size: 16px !important;
            }
            
            .btn {
                white-space: normal !important;
            }

            .invalid-feedback {
                color: red;
            }
        }

        .form-group {
            padding-top: 10px;
            padding-left: 2px;
        }
        
    </style>
</head>
<body>
@yield('content')

<!-- Yandex.Metrika counter -->
<script type="text/javascript" >
    (function (d, w, c) {
        (w[c] = w[c] || []).push(function() {
            try {
                w.yaCounter49891063 = new Ya.Metrika2({
                    id:49891063,
                    clickmap:true,
                    trackLinks:true,
                    accurateTrackBounce:true,
                    webvisor:true
                });
            } catch(e) { }
        });

        var n = d.getElementsByTagName("script")[0],
            s = d.createElement("script"),
            f = function () { n.parentNode.insertBefore(s, n); };
        s.type = "text/javascript";
        s.async = true;
        s.src = "https://mc.yandex.ru/metrika/tag.js";

        if (w.opera == "[object Opera]") {
            d.addEventListener("DOMContentLoaded", f, false);
        } else { f(); }
    })(document, window, "yandex_metrika_callbacks2");
</script>
<noscript><div><img src="https://mc.yandex.ru/watch/49891063" style="position:absolute; left:-9999px;" alt="" /></div></noscript>
<!-- /Yandex.Metrika counter -->
<script
  src="https://code.jquery.com/jquery-3.3.1.min.js"
  integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
  crossorigin="anonymous"></script>
<script src="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/js/bootstrap.min.js" integrity="sha384-Tc5IQib027qvyjSMfHjOMaLkfuWVxZxUPnCJA7l2mCWNIpG9mGCD8wGNIcPD7Txa" crossorigin="anonymous"></script>
</body>
</html>