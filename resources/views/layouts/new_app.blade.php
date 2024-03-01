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
            <div class="w-10 rounded-full">
                <img alt="Logo CEAA" src="/img/logo-ceaa-horizontal.png" />
            </div>
        </div>
        <div class="navbar-center">
            <label class="input input-bordered flex items-center gap-2">
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-3 h-3">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <input type="text" class="grow" placeholder="Buscar curso..." />
            </label>
            @auth
                <a class="btn btn-ghost text-xl">Mis cursos</a>
            @endauth
        </div>
        <div class="navbar-end">
            @auth
            <div class="dropdown dropdown-end">
                <div tabindex="0" role="button" class="btn btn-ghost btn-circle avatar">
                    <div class="w-10 rounded-full">
                        <img alt="Imagen de perfil" src="https://source.boringavatars.com/beam/120/{{ urlencode(auth()->user()->name)}}?colors=edf000,002e5b,fde428,0073d8,ffcc01" />
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
    
    @livewireScripts
    @stack('scripts')
</body>

</html>
