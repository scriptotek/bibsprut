<!DOCTYPE html>
<html>
<head>
    <title>Blekkio</title>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap.min.css">
    <!--
    <link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/css/bootstrap-theme.min.css">
    -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/material-design-iconic-font/2.1.2/css/material-design-iconic-font.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.4/jquery.timepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/css/bootstrap-datepicker.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.css">
    <link rel="stylesheet" type="text/css" href="{{ URL::to('css/app.css') }}">
    @yield('head')
</head>
<body>

    <nav class="navbar navbar-inverse navbar-static-top">
        <div class="container">
            <div class="navbar-header">

                <!-- Collapsed Hamburger -->
                <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#app-navbar-collapse">
                    <span class="sr-only">Toggle Navigation</span>
                    <span class="icon-bar"></span>

                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>

                <!-- Branding Image -->
                <a class="navbar-brand" href="{{ url('/') }}">
                    🐙
                    Blekkio
                </a>
            </div>

            <div class="collapse navbar-collapse" id="app-navbar-collapse">

                <!-- Right Side Of Navbar -->
                <ul class="nav navbar-nav navbar-right">
                    <!-- Authentication Links -->
                    @if (Auth::guest())
                        <li>
                            <a href="{{ url('/saml2/login') }}"><i class="zmdi zmdi-account-box"></i> Login</a>
                        </li>
                    @else
                        <li class="dropdown">
                            <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">
                                <i class="zmdi zmdi-account-box"></i>
                                {{ Auth::user()->name }} <span class="caret"></span>
                            </a>

                            <ul class="dropdown-menu" role="menu">
                                <li>
                                    <a href="{{ url('/logout') }}"
                                        onclick="event.preventDefault();
                                                 document.getElementById('logout-form').submit();">
                                        Logout
                                    </a>

                                    <form id="logout-form" action="{{ url('/logout') }}" method="POST" style="display: none;">
                                        {{ csrf_field() }}
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @endif
                </ul>
            </div>
        </div>
    </nav>

    @if (Auth::check() && !Auth::user()->can('edit'))
    <div class="container">
        <div class="alert alert-warning">
            <p>
                Hei! Du er logget inn, men en administrator må aktivere kontoen din før du kan redigere.
            </p>
        </div>
    </div>
    @endif

    @if (Session::has('status'))
    <div class="container">
        <div class="alert alert-success">
            <p>
                {{ Session::get('status') }}
            </p>
        </div>
    </div>
    @endif

    @if (Session::has('error'))
    <div class="container">
        <div class="alert alert-danger">
            <p>
                {{ Session::get('error') }}
            </p>
        </div>
    </div>
    @endif

    <div class="container" style="margin-bottom:150px;">
            <img src="/octopus-33147_640.png" style="width:200px; float:right;">

        @if (isset($accounts))
        <div>
            Kontoer:
            @foreach ($accounts as $acc)
              <div style="display: inline-block;background:#eee;border-radius: 15px; padding-right:8px;">
                  <img src="{{ $acc->userinfo['picture'] }}" style="width:30px; border-radius:15px;">
                  {{ $acc->userinfo['name'] }}
                  @can('edit')
                  <a href="{{ action('GoogleAuthController@logout', ['email' => $acc->id]) }}">[X]</a>
                  @endcan
              </div>
            @endforeach
            @can('edit')
            <div style="display: inline-block;">
                <a href="{{ action('GoogleAuthController@initiate') }}">Legg til</a>
            </div>
            @endcan
        </div>
        @endif

        <div>
            Siste høsting:
            @if (isset($lastHarvest))
                @if ($lastHarvest->deleted_at)
                    {{ $lastHarvest->deleted_at->tz('Europe/Oslo')->formatLocalized('%d. %B %Y, %H:%M') }}
                @else
                    <em>oppdatering pågår</em>
                @endif
            @else
                aldri
            @endif
            @can('edit')
                <a href="{{ action('HarvestsController@harvest')  }}">[Start høsting]</a>
            @endcan
        </div>

        @yield('content')
    </div>

<!--    <script src="{{ URL::to('js/vendor.js') }}"></script>
    <script src="{{ URL::to('js/app.js') }}"></script>
-->

    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery/2.1.4/jquery.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/Readmore.js/2.0.5/readmore.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/3.3.5/js/bootstrap.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/jquery-timepicker/1.8.4/jquery.timepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/js/bootstrap-datepicker.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.5.0/locales/bootstrap-datepicker.no.min.js"></script>
    <script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/dropzone/4.2.0/min/dropzone.min.js"></script>
    @yield('script')
</body>
</html>
