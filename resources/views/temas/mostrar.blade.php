@extends('layouts.app')

@section('styles')
    @filamentStyles
    <style>
        .sidebar nav {
            position: sticky;
            top: 10%;
            margin: auto;
        }

        circle-progress::part(base) {
            width: 1rem;
            height: 1rem;
        }

        circle-progress::part(value) {
            stroke-width: 50px;
            width: 100%;
            height: 100%;
            stroke: rgb(0, 46, 91);
        }

        circle-progress::part(circle) {
            stroke-width: 50px;
            stroke: #999;
        }

        circle-progress::part(text) {
            display: none;
        }
    </style>
@endsection

@section('scripts')
    @filamentScripts
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        function completar(contenido_id) {
            console.log(contenido_id);
            var xhr = new XMLHttpRequest();

            //Esta función se ejecutará tras la petición
            xhr.onload = function() {

                //Si la petición es exitosa
                if (xhr.status >= 200 && xhr.status < 300) {
                    //Mostramos un mensaje de exito y el contenido de la respuesta
                    console.log('¡Éxito!', xhr.response);
                } else {
                    //Si la conexión falla
                    console.log('Error en la petición!');
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
                    "menu",
                    progress => {
                        console.log(progress);
                        document.getElementById(progress.Id + '-p').value = progress.Percent;
                    },
                    id => {
                        document.querySelectorAll('a[href*="embebed-"]').forEach(element => element.classList.remove('active-meny-item'));
                        document.querySelector(`[href="#${id}"]`).classList.add('active-meny-item');
                    }
                );
            @endif
        });
    </script>
@endsection

@section('content')
    <div class="jumbotron pb-0">
        <div class="container">
            <h4>{{ Auth::user()->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">{{ $modulo->curso->nombre }}</li>
                    <li class="breadcrumb-item"><a href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a></li>
                    <li class="breadcrumb-item active">{{ $modulo->nombre }}</li>
                </ol>
            </nav>
            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/logros">Logros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a>
                </li>
                {{-- <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/examenes">Exámenes</a>
            </li> --}}
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="rowjustify-content-center">
            <div class="col-md-9">
                <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
            </div>
            <div class="col-md-9">
                <p>Para avanzar en el porcentaje, tienes que sacar calificación mayor a 90 en los exámenes</p>
            </div>
        </div>
    </div>
    <div class="wrapper row">
        <main class="col-md-9">
            <div class="container p-4">
                <h1 class="lead">{{ $tema->nombre }}</h1>
                <hr>
                @if ($tema->contenido != null)
                    @for ($i = 0; $i < count($tema->contenido); $i++)
                        <h2 class="lead mt-3" id='embebed-{{ $i }}'>{{ $tema->contenido[$i]['data']['titulo'] }}</h2>
                        @if ($tema->contenido[$i]['type'] == 'texto')
                            {!! $tema->contenido[$i]['data']['texto'] !!}
                        @elseif ($tema->contenido[$i]['type'] == 'h5p')
                            <p>
                                <iframe onload="this.height=this.contentWindow.document.body.scrollHeight * 1.5;" src="/storage/{{ $tema->contenido[$i]['data']['h5p'] }}" frameborder="0" width="100%"
                                    allow="geolocation *; microphone *; camera *; midi *; encrypted-media *"></iframe>
                            </p>
                        @elseif ($tema->contenido[$i]['type'] == 'embebido')
                            <p class="ratio ratio-16x9">
                                {!! $tema->contenido[$i]['data']['embebido'] !!}
                            </p>
                        @elseif ($tema->contenido[$i]['type'] == 'video')
                            <p>
                                <video controls class="my-5">
                                    <source src="/storage/{{ $tema->contenido[$i]['data']['video'] }}" type="video/mp4">
                                </video>
                            </p>
                        @endif
                        <div class="text-center my-2">
                            <button class="btn btn-primary text-center" onclick="completar({{ $i }})">
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
        <nav class="col-md-3 sidebar pl-5">
            <nav id="home" class="text-center">
                <menu>
                    <ul class="list-group">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <h1 class="lead text-center">Tabla de contenidos</h1>
                        </li>
                        @for ($i = 0; $i < count($tema->contenido); $i++)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <a href="#embebed-{{ $i }}">{{ $tema->contenido[$i]['data']['titulo'] ?? 'Sección ' . $i + 1 }}</a>
                                <span class="">
                                    <circle-progress id='embebed-{{ $i }}-p' value="0" max="100"></circle-progress>
                                </span>
                            </li>
                        @endfor
                    </ul>
                    <div id="cursor"></div>
                </menu>
            </nav>
        </nav>
    </div>
@endsection
