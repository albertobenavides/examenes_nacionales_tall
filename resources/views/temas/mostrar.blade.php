@extends('layouts.app')

@section('styles')
    @filamentStyles
    <style>
        .sidebar {
  position: fixed;
  right: 0;
  z-index: 100; /* Behind the navbar */
}
    </style>
@endsection

@section('scripts')
    @filamentScripts
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
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
                        document.getElementById(progress.Id + '-p').innerHTML = progress.Percent;
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
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
            </div>
            <div class="col-md-9">
                <p>Para avanzar en el porcentaje, tienes que sacar calificación mayor a 90 en los exámenes</p>
            </div>
        </div>
        <div class="row justify-content-center mt-5">
            <div class="col-md-9">
                <h1 class="lead">{{ $tema->nombre }}</h1>
                <hr>
                @if ($tema->contenido != null)
                    <img src="https://fakeimg.pl/650x480/">
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
                            <p class="my-5">
                                {!! $tema->contenido[$i]['data']['embebido'] !!}
                            </p>
                        @elseif ($tema->contenido[$i]['type'] == 'video')
                            <p>
                                <video controls class="my-5">
                                    <source src="/storage/{{ $tema->contenido[$i]['data']['video'] }}" type="video/mp4">
                                </video>
                            </p>
                        @endif
                    @endfor
                @endif
            </div>
            <div class="col-md-3 sidebar">
                <nav id="home">
                    <div id="cursor"></div>
                    <menu>
                        <ul>
                            @for ($i = 0; $i < count($tema->contenido); $i++)
                                <li><a href="#embebed-{{ $i }}">Tema {{ $i + 1 }}</a> <span id='embebed-{{ $i }}-p'></span></li>
                            @endfor
                        </ul>
                    </menu>
                </nav>
            </div>
        </div>
    </div>
@endsection
