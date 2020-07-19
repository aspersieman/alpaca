<!DOCTYPE html>
<html lang="en">
    <head>
        <meta http-equiv="Refresh" content="10">
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>@yield('title')</title>

        <!-- Fonts -->
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

        <!-- Styles -->
        <style>
            html, body {
                background-color: #fff;
                color: #636b6f;
                font-family: 'Nunito', sans-serif;
                font-weight: 100;
                height: 100vh;
                margin: 0;
            }

            .full-height {
                height: 100vh;
            }

            .flex-center {
                align-items: center;
                display: flex;
                justify-content: center;
            }

            .position-ref {
                position: relative;
            }

            .code {
                font-size: 26px;
                padding: 0 15px 0 15px;
                text-align: center;
            }

            .message {
                padding: 30px 10px 30px 10px;
                font-size: 18px;
                text-align: center;
                max-width: 200px;
            }

          .h{
            top: 20px;
            left: 65px;
            margin: 50 auto;
            width: 100px;
            height: 60px;
            background:#3399ff;
            border-radius: 50px 10px 10px 20px;
            position: relative;
            z-index: 5;
            box-shadow: 
            3px 3px 0 0px #003399,
            /*body*/
            75px 115px 0 20px #3399ff,
            /*body shadow*/
            77.5px 120px 0 20px #003399   
          }

          .h:after{
            content: "";
            display: block;
            width: 12.5px;
            height: 10px;
            border-radius:50%;
            position: absolute;
            left: 70px;
            top: 5px;
            z-index: 10;
            box-shadow:
            /*main eyelid*/
            3px 5px 0 5px #fff,
            /*main eyelid shadow*/
            3px 10px 0 10px #003399,
            /*nose*/
            -65px 20px 0 0 #fff,
            /*nose shadow*/
            -65px 22.5px 0 0 #003399,
            /*mouth*/
            -50px 40px 0 -4px #fff,
            /*mouth shadow*/
            -50px 42.5px 0 -3px #003399
          }

          .h:before{
            content: "";
            display: block;
            width: 12.5px;
            height: 35px;
            background:#3399ff;
            border-radius: 75px 10px 0 10px;
            position: absolute;
            left: 72.5px;
            top: -22.5px;
            z-index: 1; 
            box-shadow:
            /*right ear*/
            15px -5px 0 0px #3399ff,
            /*right ear shadow*/
            16.5px -5px 0 0px #003399,
            /*neck*/
            5px 85px 0 12.5px #3399ff,
            5px 115px 0 12.5px #3399ff,
            /*neck longer*/
            10px 87.5px 0 12.5px #003399,
            /*tail*/
            110px 120px 0 0 #3399ff,
            /*tail shadow*/
            112.5px 120px 0 0 #003399,
            /*front right leg*/
            17.5px 212.5px 0 10px #3399ff,
            /*back right shadow leg*/
            15px 212.5px 0 10px #003399,
            /*front left leg*/
            0px 205px 0 10px #3399ff,
            /*back right leg*/
            100px 212.5px 0 10px #3399ff,
            /*back right shadow leg*/
            97.5px 212.5px 0 10px #003399,
            /*back left leg*/
            82.5px 205px 0 10px #3399ff   
          }

          .speech-bubble {
            position: relative;
            background: #bbdefa;
            border-radius: .4em;
          }

          .speech-bubble:after {
            content: '';
            position: absolute;
            right: 0;
            top: 50%;
            width: 0;
            height: 0;
            border: 36px solid transparent;
            border-left-color: #bbdefa;
            border-right: 0;
            border-bottom: 0;
            margin-top: -18px;
            margin-right: -36px;
          }
        </style>
    </head>
    <body>
        <div class="flex-center position-ref full-height">
            <div class="code">
                @yield('code')
            </div>

            <div class="message speech-bubble">
                @yield('message')
            </div>
            <div class="h"></div>
        </div>
    </body>
</html>
