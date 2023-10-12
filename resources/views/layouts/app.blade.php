<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ $settings->app_name }}</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/css/jasny-bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css"/>
    <link rel="stylesheet" href="/css/jquery-ui.min.css"/>
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" async src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <!--Floating WhatsApp css-->
    <link rel="stylesheet" href="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/floating-wpp.min.css">
    @livewireStyles
    @yield('styles')
</head>
<body>
    <div id="app" style="min-height: 80vh">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary" id="app">
            <div class="container">
                <a class="navbar-brand" href="/">{{$settings->app_name}}</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- stackoverflow.com/a/53526338 --}}
                    <div class="navbar-nav w-100 nav-fill">
                        <div class="nav-item">
                            <a class="nav-link" href="{{ url('/promos') }}">PLANES</a>
                        </div>
                        <div class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                CURSOS
                            </a>

                            <div class="dropdown-menu py-0 mt-2" aria-labelledby="navbarDropdown" style="width: 900px;left:-150px">
                                <div class="row">
                                    <div class="col-3 pr-0 py-0">
                                        <div class="list-group list-group-flush" id="v-pills-tab" role="tablist" aria-orientation="vertical">
                                            @php
                                                $cursos_activos = Cache::remember('cursos_activos', 3600, function () {
                                                    return App\Models\Curso::where('activo', 1)->get();
                                                });
                                            @endphp
                                            @foreach ($cursos_activos as $c)
                                                <a class="list-group-item list-group-item-course bg-primary text-white" id="v-pills-{{$c->id}}-tab" curso_id="{{$c->id}}" data-toggle="tab" href="#v-pills-{{$c->id}}" role="tab" aria-controls="v-pills-{{$c->id}}" aria-selected="true">{{$c->nombre}}</a>
                                            @endforeach
                                        </div>
                                    </div>
                                    <div class="col-9">
                                        <div class="tab-content" id="v-pills-tabContent">
                                            @foreach ($cursos_activos as $c)
                                            <div class="tab-pane show" id="v-pills-{{$c->id}}" role="tabpanel" aria-labelledby="v-pills-{{$c->id}}-tab">
                                                <div class="d-flex flex-wrap justify-content-around">
                                                    @foreach ($c->instituciones as $inst)
                                                        @if ($inst->imagen != null)
                                                            <a href="#" class="m-1"><img src="/storage/{{$inst->imagen}}" width="300" height="113"></a>
                                                        @else
                                                            <a href="#" class="m-1"><img src="https://fakeimg.pl/300x113/?text={{$c->nombre}}"></a>
                                                        @endif
                                                    @endforeach
                                                </div>
                                            </div>
                                            @endforeach
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        @auth
                            @if (Auth::user()->rol_id == 1)
                                {{--Ajustes de administrador--}}
                                <div class="nav-item dropdown">
                                    <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                        Administrar
                                    </a>
                                    <div class="dropdown-menu" aria-labelledby="navbarDropdown">
                                        <a class="dropdown-item" href="/ajustes">Plataforma</a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="/cursos">
                                            <span class="badge badge-pill">{{App\Models\Curso::count()}}</span>
                                            Cursos
                                        </a>
                                        <a class="dropdown-item" href="/usuarios">
                                            <span class="badge badge-pill">{{App\Models\User::count()}}</span>
                                            Usuarios
                                        </a>     
                                        <a class="dropdown-item" href="/pagos">
                                            <span class="badge badge-pill">{{App\Models\Pago::count()}}</span>
                                            Pagos
                                        </a>
                                        <a class="dropdown-item" href="/calificaciones">
                                            Calificaciones
                                        </a>
                                        <div class="dropdown-divider"></div>
                                        <a class="dropdown-item" href="/pagos">
                                            <span class="badge badge-pill">{{App\Models\Examen::count()}}</span>
                                            Exámenes
                                        </a>
                                        <a class="dropdown-item" href="/pagos">
                                            <span class="badge badge-pill">{{App\Models\Institucion::count()}}</span>
                                            Instituciones
                                        </a>
                                    </div>
                                </div>
                            @endif
                            @if (Auth::user()->rol_id == 3)
                                <div class="nav-item">
                                    <a class="nav-link" href="/inicio">
                                        Usuarios
                                    </a>
                                </div>
                                <div class="nav-item">
                                    <a class="nav-link" href="/calificaciones">
                                        Calificaciones
                                    </a>
                                </div>
                            @endif
                            <div class="nav-item dropdown">
                                <a class="nav-link dropdown-toggle" href="#" id="usuario" role="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                {{Auth::user()->name}}
                                </a>
                                <div class="dropdown-menu" aria-labelledby="usuario">
                                    <a class="dropdown-item" href="/usuarios/{{Auth::id()}}">Editar perfil</a>
                                    <div class="dropdown-divider"></div>
                                    <a class="dropdown-item" id="cerrar_sesion" href="#">Cerrar sesión</a>
                                    <form id="cerrar_sesion_form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                        @csrf
                                    </form>
                                </div>
                            </div>
                        @else
                            <div class="nav-item">
                                <a class="nav-link" href="{{ route('login') }}">ACCEDER</a>
                            </div>
                            <div class="nav-item">
                                <a class="nav-link" href="{{ route('register') }}">REGISTRO</a>
                            </div>
                        @endauth
                        </div>
                </div>
            </div>
        </nav>

        <main>
            @yield('content')
        </main>

        <!--Div where the WhatsApp will be rendered-->  
        <div id="WAButtonL"></div>
        <div id="WAButtonR"></div>
    </div>
    <footer class="footer bg-secondary p-5 mt-3">
        <div class="row">
            <div class="col-lg text-center">
                <h5>Dirección</h5>
            </div>
            <div class="col-lg text-center">
                <h5>Aviso de privacidad</h5>
            </div>
            <div class="col-lg text-center">
                <h5>Contacto</h5>
            </div>
        </div>
    </footer>
    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}"></script>
    <script src="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/js/jasny-bootstrap.min.js"></script>
    <script src="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/alertify.min.js"></script>
    <script src="/js/jquery-ui.min.js"></script>
    <!--Floating WhatsApp javascript-->
    <script type="text/javascript" src="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/floating-wpp.min.js"></script>
    <script>
        $(document).ready(function(){
            $(".list-group-item-course").hover(function(){
                $(this).tab('show');
                $(this).addClass('bg-warning');
                $(this).removeClass('text-white');
                $(this).removeClass('bg-primary');
            });
            $(".list-group-item-course").mouseout( function(){
                $(this).addClass('bg-primary');
                $(this).addClass('text-white');
                $(this).removeClass('bg-warning');
            });
            $(".list-group-item-course").click( function(){
                var curso_id = $(this).attr('curso_id')
                window.location.href = `/cursos/${curso_id}`;
            });

            $('#cerrar_sesion').click(function(){
                $('#cerrar_sesion_form').submit();
            });
            @if (Session::has('mensaje'))
                alertify.message('{{session('mensaje')}}');
            @endif
            @if (Session::has('exito'))
                alertify.success('{{session('exito')}}');
            @endif
            @if (Session::has('error'))
                alertify.error('{{session('error')}}');
            @endif
            @foreach ($errors->all() as $mensaje)
                alertify.error('{{$mensaje}}');
            @endforeach
            
            @if (Session::has('correctos'))
            var delay = alertify.get('notifier','delay');
            alertify.set('notifier','delay', 20);
            @foreach (session('correctos') as $mensaje)
                alertify.success('{{$mensaje}}');
            @endforeach
            alertify.set('notifier','delay', delay);
            @endif

            @if (Session::has('errores'))
            var delay = alertify.get('notifier','delay');
            alertify.set('notifier','delay', 20);
            @foreach (session('errores') as $mensaje)
                alertify.error('{{$mensaje}}');
            @endforeach
            alertify.set('notifier','delay', delay);
            @endif

            @auth
                @if(Auth::user()->rol_id == 2)
                    $('#WAButtonR').floatingWhatsApp({
                        phone: '+528131965935', //WhatsApp Business phone number International format-
                        //Get it with Toky at https://toky.co/en/features/whatsapp.
                        headerTitle: 'Chatea con nosotros', //Popup Title
                        popupMessage: 'Hola, ¿en qué podemos ayudarte?', //Popup Message
                        showPopup: true, //Enables popup display
                        buttonImage: '<img src="/img/botones-tutoria1.png" />', //Button Image
                        //headerColor: 'crimson', //Custom header color
                        //backgroundColor: 'crimson', //Custom background button color
                        position: "right",
                    });
                    $('#WAButtonR').css('z-index', 10);
                    $('.floating-wpp-button').css('width', '120px');
                    $('.floating-wpp-button').css('height', '90px');

                    $('#WAButtonL').floatingWhatsApp({
                        phone: '+528124285085', //WhatsApp Business phone number International format-
                        //Get it with Toky at https://toky.co/en/features/whatsapp.
                        headerTitle: 'Chatea con nosotros', //Popup Title
                        popupMessage: 'Hola, ¿en qué podemos ayudarte?', //Popup Message
                        showPopup: true, //Enables popup display
                        buttonImage: '<img src="/img/botones-tutoria2.png" />', //Button Image
                        //headerColor: 'crimson', //Custom header color
                        //backgroundColor: 'crimson', //Custom background button color
                        position: "left",
                    });
                    $('#WAButtonL').css('z-index', 10);
                    $('.floating-wpp-button').css('width', '120px');
                    $('.floating-wpp-button').css('height', '90px');
                @endif
            @endauth
        });
    </script>
    @livewireScripts
    @yield('scripts')
</body>
</html>
