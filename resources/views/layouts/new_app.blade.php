<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" data-theme="light">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@php fn (GeneralSettings $settings): string => $settings->app_name @endphp</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    @vite('resources/css/app.css')
    <!-- Styles -->
    <script id="MathJax-script" src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    @livewireStyles
    @yield('styles')
</head>

<body class="text-accent">
    <x-impersonate::banner />

    <div class="navbar">
        <div class="navbar-start">
            <div class="sm:hidden inset-y-0 left-0 flex items-center">
                <div class="dropdown dropdown-bottom">
                    <button type="button" class="relative inline-flex items-center justify-center rounded-md p-2 text-gray-400 hover:bg-gray-700 hover:text-white focus:outline-none focus:ring-2 focus:ring-inset focus:ring-white"
                        aria-controls="mobile-menu" aria-expanded="false">
                        <span class="absolute -inset-0.5"></span>
                        <span class="sr-only">Open main menu</span>
                        <svg class="block h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                        </svg>
                        <svg class="hidden h-6 w-6" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                    <ul tabindex="0" class="menu dropdown-content z-[1] p-2 shadow bg-base-100 rounded-box w-52 mt-4">
                        <li><a href="/cursos">Mis cursos</a></li>
                      </ul>
                </div>
            </div>
            <div class="w-10 rounded-full">
                <a href="/"><img alt="Logo CEAA" src="/img/logo-ceaa-horizontal.png" /></a>
            </div>
        </div>
        <div class="navbar-center">
            <label class="input input-bordered flex items-center">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" placeholder="Buscar... Próximamente" disabled />
            </label>
            @auth
            <ul class="menu menu-horizontal px-1">
                <li><a href="/cursos" class="btn btn-ghost text-xl hidden sm:flex">Mis cursos</a></li>
            </ul>
            @endauth
        </div>
        <div class="navbar-end">
            @auth
                <div class="dropdown dropdown-end">
                    <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                        <div class="w-10 rounded-full">
                            <img alt="Imagen de perfil" src="https://source.boringavatars.com/beam/120/{{ urlencode(auth()->user()->name) }}?colors=edf000,002e5b,fde428,0073d8,ffcc01" />
                        </div>
                    </div>
                    <ul tabindex="0" class="mt-3 z-[1] p-2 shadow menu menu-sm dropdown-content bg-base-100 rounded-box w-52">
                        {{-- <li>
                        <a class="justify-between">
                            Profile
                            <span class="badge">New</span>
                        </a>
                    </li> --}}
                        {{-- <li><a>Settings</a></li> --}}
                        <li><a onclick="document.getElementById('cerrar_sesion_form').submit();return false;">Cerrar sesión</a></li>
                    </ul>
                    <form id="cerrar_sesion_form" action="{{ route('logout') }}" method="POST" style="display: none;">
                        @csrf
                    </form>
                </div>
            @else
                <ul class="menu menu-horizontal px-1">
                    <li><a href="/login">Iniciar sesión</a></li>
                </ul>
            @endauth
        </div>
    </div>

    @yield('content')

    <footer class="footer p-10 bg-primary text-neutral-content">
        <aside>
            <a href="/"><img src="/img/logo-ceaa-horizontal.png" class="object-cover h-24 w-48"></a>
            {{-- <p>CEAA<br>2024</p> --}}
        </aside>
        <nav class="text-white">
            <h6 class="footer-title">Redes sociales</h6>
            <div class="grid grid-flow-col gap-4">
                {{-- <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"></path></svg></a>
            <a><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current"><path d="M19.615 3.184c-3.604-.246-11.631-.245-15.23 0-3.897.266-4.356 2.62-4.385 8.816.029 6.185.484 8.549 4.385 8.816 3.6.245 11.626.246 15.23 0 3.897-.266 4.356-2.62 4.385-8.816-.029-6.185-.484-8.549-4.385-8.816zm-10.615 12.816v-8l8 3.993-8 4.007z"></path></svg></a> --}}
                <a href="https://www.facebook.com/ceaa.asesorias.3/?locale=es_LA" target="_blank"><svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" class="fill-current">
                        <path d="M9 8h-3v4h3v12h5v-12h3.642l.358-4h-4v-1.667c0-.955.192-1.333 1.115-1.333h2.885v-5h-3.808c-3.596 0-5.192 1.583-5.192 4.615v3.385z"></path>
                    </svg></a>
            </div>
        </nav>
    </footer>

    @livewireScripts
    @stack('scripts')
</body>

</html>
