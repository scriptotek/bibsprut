<!DOCTYPE html>
<html>
<head>
    <title>Blekkio</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('css/app.css') }}">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
    <!--
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
    -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.1.2/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.4/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.css">
    @yield('head')
</head>
<body>

    <div class="container" style="margin-bottom:150px;">
        <header>
            <img src="/octopus-33147_640.png" style="width:200px; float:right;">
            <h1><a href="/">Blekkio</a></h1>
        </header>

        @if (session('status'))
            <div class="alert alert-success">
                {{ session('status') }}
            </div>
        @endif

        @if ($accounts)
        <div>
            Kontoer:
            @foreach ($accounts as $acc)
              <div style="display: inline-block;background:#eee;border-radius: 15px; padding-right:8px;">
                  <img src="{{ $acc->userinfo['picture'] }}" style="width:30px; border-radius:15px;">
                  {{ $acc->userinfo['name'] }}
                  <a href="{{ action('GoogleAuthController@logout', ['email' => $acc->id]) }}">[X]</a>
              </div>
            @endforeach
            <div style="display: inline-block;">
                <a href="{{ action('GoogleAuthController@initiate') }}">Legg til</a>
            </div>
        </div>
        @endif

        @if ($lastHarvest)
            <div>
                Sist oppdatert:
                @if ($lastHarvest->completed_at)
                    {{ $lastHarvest->completed_at->tz('Europe/Oslo')->formatLocalized('%d. %B %Y, %H:%M') }}
                    <a href="{{ action('HarvestsController@harvest')  }}">Oppdater nå</a>
                @else
                    <em>oppdatering pågår</em>
                @endif
            </div>
        @endif

        @yield('content')
    </div>

<!--    <script src="{{ URL::to('js/vendor.js') }}"></script>
    <script src="{{ URL::to('js/app.js') }}"></script>
-->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.0.5/readmore.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.4/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/locales/bootstrap-datepicker.no.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
    @yield('script')
</body>
</html>
