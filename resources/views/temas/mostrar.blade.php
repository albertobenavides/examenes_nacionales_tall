@extends('layouts.new_app')

@push('scripts')
    @filamentScripts
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        function completar(contenido_id) {
            var xhr = new XMLHttpRequest();

            //Esta función se ejecutará tras la petición
            xhr.onload = function() {

                //Si la petición es exitosa
                if (xhr.status >= 200 && xhr.status < 300) {
                    //Mostramos un mensaje de exito y el contenido de la respuesta
                    console.log('¡Éxito!', xhr.response);
                } else {
                    //Si la conexión falla
                    console.log(xhr.error);
                }
            };
            //Por el primer parametro enviamos el tipo de petición (GET, POST, PUT, DELETE)
            //Por el segundo parametro la url de la API
            xhr.open('POST', `/content/{{ $tema->id }}/${contenido_id}`);
            xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded; charset=UTF-8');
            token = document.querySelector('meta[name="csrf-token"]').content;
            xhr.setRequestHeader('X-CSRF-TOKEN', token);
            //Se envía la petición
            xhr.send();
        }
        document.addEventListener("DOMContentLoaded", function() {
            @if ($tema->contenido != null)
                @for ($i = 0; $i < count($tema->contenido); $i++)
                    @if ($tema->contenido[$i]['type'] == 'h5p')
                        document.getElementById('embebed-{{ $i }}').nextElementSibling.children[0].contentWindow.H5P.externalDispatcher.on('xAPI', function(event) {
                            try {
                                let score = event.data.statement.result.score;
                                if (score.scaled > 0.9) {
                                    confetti({
                                        particleCount: 100,
                                        spread: 70,
                                        origin: {
                                            y: 0.6
                                        }
                                    });
                                    var audio = new Audio('/sounds/success.mp3');
                                    audio.play();
                                } else {
                                    var audio = new Audio('/sounds/error.mp3');
                                    audio.play();
                                }
                            } catch (error) {
                                //
                            }
                        });
                    @endif
                @endfor
                let currentParagraphName = document.getElementById('current-paragraph-name');
                let currentParagraphPercent = document.getElementById('current-paragraph-percent');

                new ScrollProgress.Init(
                    "#cursor",
                    "#menu",
                    progress => {
                        let value_t = document.getElementById(progress.Id + '-p').value;
                        document.getElementById(progress.Id + '-p').value = (value_t < progress.Percent) ? progress.Percent : value_t;
                    },
                    id => {
                        document.querySelectorAll('a[href*="embebed-"]').forEach(element => element.classList.remove('active-meny-item'));
                        document.querySelector(`[href="#${id}"]`).classList.add('active-meny-item');
                    }
                );
            @endif
        });
    </script>
@endpush

@section('content')

    <div class="navbar bg-primary text-white">
        <div class="text-sm breadcrumbs">
            <ul class="text-xl">
                <li><a href="/cursos">Cursos</a></li>
                <li><a href="/cursos/{{ $modulo->curso->id }}/clases">{{ $modulo->curso->nombre }}</a></li>
                <li><a href="/modulos/{{ $modulo->id }}">{{ $modulo->nombre }}</a></li>
                <li>{{ $tema->nombre }}</li>
            </ul>
        </div>
    </div>
    <div class="grid grid-cols-5">
        <div class="mr-auto w-full px-4 col-span-4 prose">
            <main class="col-md-9">
                <div class="container p-4">
                    <h1 class="lead">{{ $tema->nombre }}</h1>
                    <hr>
                    @if ($tema->contenido != null)
                        @for ($i = 0; $i < count($tema->contenido); $i++)
                            <h2 class="lead mt-3" id='embebed-{{ $i }}'>{{ $tema->contenido[$i]['data']['titulo'] ?? 'Sección ' . $i + 1 }}</h2>
                            @if ($tema->contenido[$i]['type'] == 'texto')
                                {!! $tema->contenido[$i]['data']['texto'] !!}
                            @elseif ($tema->contenido[$i]['type'] == 'h5p')
                                <p class="bg-white rounded">
                                    <iframe onload="this.height=this.contentWindow.document.body.scrollHeight * 1.5;" src="/storage/{{ $tema->contenido[$i]['data']['h5p'] }}" class="w-full" frameborder="0">
                                        <style>
                                            *{
                                                overflow-y: none;
                                            }
                                        </style>
                                        <script src="https://raw.githubusercontent.com/h5p/h5p-php-library/master/js/h5p-resizer.js"></script>
                                    </iframe>
                                </p>
                            @elseif ($tema->contenido[$i]['type'] == 'embebido')
                                <p class="flex justify-center">
                                    {!! $tema->contenido[$i]['data']['embebido'] !!}
                                </p>
                            @elseif ($tema->contenido[$i]['type'] == 'video')
                                <p class="flex justify-center">
                                    <video controls class="my-5">
                                        <source src="/storage/{{ $tema->contenido[$i]['data']['video'] }}" type="video/mp4">
                                    </video>
                                </p>
                            @endif
                            <div class="text-center flex justify-end">
                                <button class="btn btn-primary text-center " onclick="completar({{ $i }})">
                                    @if (isset(auth()->user()->notes) && isset(auth()->user()->notes[$tema->id]) && array_key_exists($i, auth()->user()->notes[$tema->id]))
                                        Desmarcar
                                    @else
                                        Completado
                                    @endif
                                </button>
                            </div>
                        @endfor
                    @endif
                </div>
            </main>
        </div>
        <div class="bg-white h-full">
            <div class="h-32 bg-white sticky top-0">
                <div class="sticky col-start-5">
                    <ul class="menu">
                        @foreach ($tema->modulo->temas->sortBy('order') as $t)
                            @if ($t->id == $tema->id)
                                <li>
                                    <details open>
                                        <summary><b>{{ $t->nombre }}</b></summary>
                                        <div id="cursor"></div>
                                        <ul id='menu'>
                                            @for ($i = 0; $i < count($tema->contenido); $i++)
                                                <li>
                                                    <a href="#embebed-{{ $i }}"
                                                        class="relative flex flex-row items-center h-11 focus:outline-none hover:bg-gray-50 text-gray-600 hover:text-gray-800 border-l-4 border-transparent hover:border-indigo-500 pr-6">
                                                        <span class="inline-flex justify-center items-center ml-4">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4">
                                                                </path>
                                                            </svg>
                                                        </span>
                                                        <span class="ml-2 text-sm tracking-wide truncate">{{ $tema->contenido[$i]['data']['titulo'] ?? 'Sección ' . $i + 1 }}</span>
                                                        <span class="px-2 py-0.5 ml-auto text-xs font-medium tracking-wide text-indigo-500 bg-indigo-50 rounded-full">
                                                            <progress id='embebed-{{ $i }}-p' class="progress w-8" value="{{ isset(auth()->user()->notes[$tema->id][$i]) ? '100' : '0' }}" max="100"></progress>
                                                        </span>
                                                    </a>
                                                </li>
                                            @endfor
                                        </ul>
                                    </details>
                                </li>
                            @else
                                <li><a href="{{ $t->id }}">{{ $t->nombre }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>

@endsection
