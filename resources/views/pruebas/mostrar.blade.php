@extends('layouts.app')

@section('scripts')
    <script>
        function actualizarPregunta(pregunta_id) {
            $('.pregunta')
                .attr('id', parseInt(pregunta_id) + 1)
                .attr('pregunta_id', preguntas[pregunta_id].id);
            $('#pregunta_num').attr('src', `https://fakeimg.pl/75/?text=${parseInt(pregunta_id) + 1}`);
            $('#pregunta_contenido').html(preguntas[pregunta_id].contenido);

            respuestas = '';
            preguntas[pregunta_id].respuestas.forEach(respuesta => {
                respuestas += `<div class="list-group-item list-group-item-action respuesta" respuesta_id="${respuesta.id}" pregunta_id="${respuesta.pregunta_id}">
                    <ol type="A">
                        <li>
                            ${respuesta.contenido}
                        </li>
                    </ol>
                </div>`;
            });

            $('#respuestas').html(respuestas);

            $(document.body).on('click', '.respuesta', function() {
                var r_id = parseInt($(this).attr('respuesta_id'));
                var q_id = parseInt($(this).attr('pregunta_id'));
                respuestas_elegidas[q_id] = r_id;
                
                $(this).siblings().removeClass('bg-info');
                $(this).addClass('bg-info');
            });

            if (preguntas[pregunta_id].id in respuestas_elegidas){
                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).siblings().removeClass('bg-info');
                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).addClass('bg-info');
            }
            
            MathJax.typeset();
        }
        $(function() {
            preguntas = {!! $preguntas !!};
            respuestas_elegidas = {};
            actual = 0;
            $('#leyenda').hide();
            $('.card-footer').hide();
            $('#revisando').hide();

            // Primera pregunta
            actualizarPregunta(0);

            $(document.body).on('click', '.mostrar', function() {
                    var id = $(this).attr('data-toggle');
                    actual = parseInt(id);
                    $('.mostrar').removeClass('bg-light');
                    $(this).addClass('bg-light');
                    actualizarPregunta(id);
                })
                .on('click', '#siguiente', function() {
                    if (actual == preguntas.length - 1) {
                        return;
                    }
                    $('.mostrar').removeClass('bg-light');
                    actual = actual + 1;
                    $('button[data-toggle=' + actual + ']').addClass('bg-light');
                    actualizarPregunta(actual);
                })
                .on('click', '#anterior', function() {
                    if (actual == 0) {
                        return;
                    }
                    $('.mostrar').removeClass('bg-light');
                    actual = actual - 1;
                    $('button[data-toggle=' + actual + ']').addClass('bg-light');
                    actualizarPregunta(actual);
                });

            $('#revisar').submit(function(e) {
                e.preventDefault();
                if (confirm('¿Terminar examen?')) {
                    $('#respuestas_input').val(Object.values(respuestas_elegidas));
                    $(this).unbind('submit');
                    $(this).submit();
                }
            });
        });
    </script>
@endsection

@section('content')
    <div class="jumbotron pb-0">
        <div class="container">
            <h4>{{ Auth::user()->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">{{ $prueba->curso->nombre }}</li>
                    <li class="breadcrumb-item">Simulaciones</li>
                    <li class="breadcrumb-item">{{ $prueba->nombre }}</li>
                    <li class="breadcrumb-item active">Revisión</li>
                </ol>
            </nav>
            @php
                $pago = App\Models\Pago::where('user_id', Auth::id())
                    ->where('curso_id', $prueba->curso->id)
                    ->where('fin', '>=', Carbon\Carbon::today())
                    ->orderByDesc('promo_id')
                    ->first();
            @endphp
            @if ($pago == null)
                <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
            @else
                <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
            @endif

            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link" href="/cursos/{{ $prueba->curso->id }}/logros">Logros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" href="/cursos/{{ $prueba->curso->id }}/clases">Clases</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/cursos/{{ $prueba->curso->id }}/examenes">Exámenes</a>
                </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
        <div class="row">
            <div class="col-md-3">
                <h2 class="mt-3 text-center">Preguntas</h2>
                <div class="text-center">
                    @php
                        $modulo = null;
                    @endphp
                    @for ($i = 0; $i < $preguntas->count(); $i++)
                        @if ($modulo != App\Models\Pregunta::find($preguntas->get($i)->id)->tema->modulo->nombre)
                            @php
                                $modulo = App\Models\Pregunta::find($preguntas->get($i)->id)->tema->modulo->nombre;
                            @endphp
                            <h5>{{ $modulo }}</h5>
                        @endif
                        <button class="btn btn-link mostrar {{ $i == 0 ? 'bg-light' : '' }}" data-toggle="{{ $i }}">{{ $i + 1 }}</button>
                    @endfor
                </div>
                <form action="/examenes/revisar" method="post" class="mt-3" id="revisar">
                    @csrf
                    <input type="hidden" name="intento_id" value="{{ $intento->id }}">
                    <button class="btn btn-block btn-success btn-lg">
                        Terminar
                        <div class="spinner-border text-light" role="status" id="revisando">
                            <span class="sr-only">Revisando...</span>
                        </div>
                    </button>
                </form>
                <div id="leyenda">
                    <h5 class="mt-3 text-center">Calificación</h5>
                    <div class="progress">
                        <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="calificacion"></div>
                    </div>
                    <h5 class="mt-3 text-center">Aciertos</h5>
                    <p class="text-center" id="calificacion2"></p>
                </div>
            </div>
            <div class="col-md-9">
                {{-- PREGUNTA --}}
                <div class="card pregunta" id="-1" pregunta_id="-1">
                    <div class="card-header bg-primary text-white">
                        <div class="media">
                            <img id='pregunta_num' src="https://fakeimg.pl/75/?text=-1" class="mr-3 img-fluid rounded">
                            <div class="media-body">
                                <p class="lead text-center" id="pregunta_contenido">CONTENIDO</p>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        {{-- RESPUESTA --}}
                        <div class="list-group list-group-flush" id="respuestas">

                        </div>
                        {{-- .REPUESTA --}}
                    </div>
                    <div class="card-footer">
                    </div>
                </div>
                {{-- .PREGUNTA --}}
                <button id="anterior" class="btn btn-primary">Anterior</button>
                <button id="siguiente" class="btn btn-primary">Siguiente</button>
            </div>
        </div>
    </div>
@endsection
