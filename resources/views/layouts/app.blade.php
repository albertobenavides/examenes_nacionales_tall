<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@php fn (GeneralSettings $settings): string => $settings->app_name @endphp</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js" integrity="sha512-894YE6QWD5I59HgZOGReFYm4dnWc1Qt5NtvYSaNcOP+u1T9qYdvdihz0PPSiiqn/+/3e7Jo4EaG7TubfWGUrMQ==" crossorigin="anonymous" referrerpolicy="no-referrer"></script>

    @vite(['resources/sass/app.scss', 'resources/css/app.css', 'resources/js/app.js'])
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="/css/all.min.css">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/jasny-bootstrap/4.0.0/css/jasny-bootstrap.min.css">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/alertifyjs@1.13.1/build/css/alertify.min.css" />
    <link rel="stylesheet" href="/css/jquery-ui.min.css" />
    <script src="https://polyfill.io/v3/polyfill.min.js?features=es6"></script>
    <script id="MathJax-script" src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <!--Floating WhatsApp css-->
    <link rel="stylesheet" href="https://rawcdn.githack.com/rafaelbotazini/floating-whatsapp/3d18b26d5c7d430a1ab0b664f8ca6b69014aed68/floating-wpp.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/v/dt/dt-1.11.5/fh-3.2.2/datatables.min.css"/>
    <script type="text/javascript" src="https://cdn.datatables.net/v/dt/dt-1.11.5/fh-3.2.2/datatables.min.js"></script>
<style>
/* Preloder */

#preloder {
	position: fixed;
	width: 100%;
	height: 100%;
	top: 0;
	left: 0;
	z-index: 999999;
	/*background: #000; OLD*/
	background: rgba(84, 124, 161, 0.836);
}

.loader {
	width: 100px;
	height: 100px;
	position: absolute;
	top: 50%;
	left: 50%;
	margin-top: -13px;
	margin-left: -13px;
	border-radius: 60px;
	animation: loader 1.5s linear infinite;
	-webkit-animation: loader 1.5s linear infinite;
}

@keyframes loader {
	0% {
		-webkit-transform: rotateY(0deg);
		transform: rotateY(0deg);
		/*border: 4px solid #f44336;OLD*/
		border: 4px solid #ffffff;
		border-left-color: transparent;
	}
	50% {
		-webkit-transform: rotateY(180deg);
		transform: rotateY(180deg);
		/*border: 4px solid #673ab7;OLD*/
		border: 4px solid #002e5b;
		border-left-color: transparent;
	}
	100% {
		-webkit-transform: rotateY(360deg);
		transform: rotateY(360deg);
		/*border: 4px solid #f44336;OLD*/
		border: 4px solid #fde428;
		border-left-color: transparent;
	}
}

@-webkit-keyframes loader {
	0% {
		-webkit-transform: rotateY(0deg);
		border: 4px solid #ffffff;
		border-left-color: transparent;
	}
	50% {
		-webkit-transform: rotateY(180deg);
		border: 4px solid #002e5b;
		border-left-color: transparent;
	}
	100% {
		-webkit-transform: rotateY(360deg);
		border: 4px solid #fde428;
		border-left-color: transparent;
	}
}
</style>
    @livewireStyles
    @yield('styles')
</head>

<body>
    <x-impersonate::banner/>
<!-- START Page Preloder -->
<div id="preloder">
    <div class="loader"></div>
</div>
<!-- FIN Page Preloder -->
    <div id="app" style="min-height: 80vh">
        <nav class="navbar navbar-expand-lg navbar-dark bg-primary" id="app">
            <div class="container">
                <a class="navbar-brand" href="/">@php fn (GeneralSettings $settings) => $settings->app_name @endphp</a>
                <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    {{-- stackoverflow.com/a/53526338 --}}
                    <div class="navbar-nav w-100 nav-fill">
                        <div class="nav-item">
                            <a class="nav-link" href="{{ url('/promos') }}">PLANES</a>
                        </div>
                        <div class="nav-item">
                            <a class="nav-link" href="{{ url('/cursos') }}">CURSOS</a>
                        </div>
                        @auth
                            @if (Auth::user()->rol_id == 1)
                                {{--Ajustes de administrador--}}
                                <div class="nav-item">
                                    <a class="nav-link" href="{{ url('/admin') }}">Administración</a>
                                </div>
                            @endif
                            @if (Auth::user()->rol_id == 3)
                                <div class="nav-item">
                                    <a class="nav-link" href="/admin/users">
                                        Administración
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
        $(window).on('load', function () {
            $(".loader").fadeOut();
            $("#preloder").delay(200).fadeOut("slow");
        });
        $(document).ready(function(){
            $('#cerrar_sesion').click(function(){
                sessionStorage.clear();
                localStorage.clear(); 
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
                        phone: '+528136201380', //WhatsApp Business phone number International format-
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
