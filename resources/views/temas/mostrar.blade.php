@extends('layouts.app')

@section('styles')
    @filamentStyles
@endsection

@section('scripts')
    @filamentScripts
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if ($tema->contenido != null)
                @for ($i = 0; $i < count($tema->contenido); $i++)
                    @if ($tema->contenido[$i]['type'] == 'h5p')
                        var iframe = document.getElementById('embebed-{{ $i }}');
                        iframe.height = iframe.contentWindow.document.body.scrollHeight;

                        document.getElementById('embebed-{{ $i }}').contentWindow.H5P.externalDispatcher.on('xAPI', function(event) {
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
                    @for ($i = 0; $i < count($tema->contenido); $i++)
                        <div>
                            @if ($tema->contenido[$i]['type'] == 'texto')
                                {!! $tema->contenido[$i]['data']['texto'] !!}
                            @elseif ($tema->contenido[$i]['type'] == 'h5p')
                                <iframe onload="this.height=this.contentWindow.document.body.scrollHeight * 1.5;" src="/storage/{{ $tema->contenido[$i]['data']['h5p'] }}" id='embebed-{{ $i }}' frameborder="0" width="100%"
                                    allow="geolocation *; microphone *; camera *; midi *; encrypted-media *"></iframe>
                            @endif
                        </div>
                    @endfor
                @endif
            </div>
        </div>
    </div>
@endsection
