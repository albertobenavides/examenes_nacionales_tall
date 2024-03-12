@extends('temas.base')

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        function actualizarPregunta(pregunta_id) {
            $('.pregunta')
                .attr('id', parseInt(pregunta_id) + 1)
                .attr('pregunta_id', preguntas[pregunta_id].id);
            $('#pregunta_num').html(parseInt(pregunta_id) + 1 + '/' + preguntas.length );
            $('#pregunta_contenido').html(preguntas[pregunta_id].contenido);

            respuestas = '';
            let i = 1;
            preguntas[pregunta_id].respuestas.forEach(respuesta => {
                respuestas += `<li class="respuesta bg-neutral m-1 rounded" respuesta_id="${respuesta.id}" pregunta_id="${respuesta.pregunta_id}">
                    ${respuesta.contenido}
                </li>`;
                i += 1;
            });

            $('#respuestas').html(respuestas);

            $(document.body).on('click', '.respuesta', function() {
                var r_id = parseInt($(this).attr('respuesta_id'));
                var q_id = parseInt($(this).attr('pregunta_id'));
                respuestas_elegidas[q_id] = r_id;

                $(this).siblings().addClass('bg-neutral');
                $(this).siblings().removeClass('bg-gray-400');

                $(this).addClass('bg-gray-400');
                $(this).removeClass('bg-neutral');
            });

            if (preguntas[pregunta_id].id in respuestas_elegidas) {
                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).siblings().addClass('bg-neutral');
                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).siblings().removeClass('bg-gray-400');

                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).addClass('bg-gray-400');
                $(`[respuesta_id="${respuestas_elegidas[preguntas[pregunta_id].id]}"]`).removeClass('bg-neutral');
            }

            MathJax.typeset();
        }
        $(function() {
            window.onbeforeunload = function(e) {
                return "¿Salir del examen?";
            };
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
                    $('#respuestas_input').val(JSON.stringify(respuestas_elegidas));
                    $(this).unbind('submit');
                    $(this).submit();
                }
            });
        });
    </script>
@endpush

@push('styles')
    <style>
        p {
            margin: 0% !important;
        }
    </style>
@endpush

@section('contenido')
    {{-- PREGUNTA --}}
    <div class="pregunta" id="-1" pregunta_id="-1">
        <h2 class="text-center">Pregunta <span id='pregunta_num'></span></h2>
        <p id="pregunta_contenido">CONTENIDO</p>
        <div class="card-body px-0">
            {{-- RESPUESTA --}}
            <div class="menu menu-sm" id="respuestas">

            </div>
            {{-- .RESPUESTA --}}
        </div>
        <div class="card-footer">
        </div>
    </div>
    {{-- .PREGUNTA --}}
    <div class="flex justify-between">
        <button id="anterior" class="btn btn-sm btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5 8.25 12l7.5-7.5" />
            </svg>
        </button>
        <form action="/examenes/revisar" method="post" id="revisar">
            @csrf
            <input type="hidden" name="intento_id" value="{{ $intento->id }}">
            <input type="hidden" name="respuestas" id="respuestas_input">
            <button class="btn btn-sm btn-primary">
                Terminar
                <div class="spinner-border text-light" role="status" id="revisando">
                    <span class="sr-only">Revisando...</span>
                </div>
            </button>
        </form>
        <button id="siguiente" class="btn btn-sm btn-primary">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </button>
    </div>
@endsection

@section('sidebar')
    <div class="pl-3 prose h2 lead bg-primary text-white text"><b>PREGUNTAS</b></div>
    <div class="text-center">
        @php
            $modules = $preguntas->groupby(function ($pregunta) {
                return $pregunta->tema->modulo;
            });
            $i = 0;
        @endphp
        @foreach ($modules as $m)
            <h5>{{ $m[0]->tema->modulo->nombre ?? '' }}</h5>
            @foreach ($m as $pregunta)
                <button class="btn btn-link mostrar {{ $i == 0 ? 'bg-light' : '' }}" data-toggle="{{ $i }}">{{ $i + 1 }}</button>
                @php
                    $i += 1;
                @endphp
            @endforeach
        @endforeach
    </div>
    <div id="leyenda">
        <h5 class="mt-3 text-center">Calificación</h5>
        <div class="progress">
            <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="calificacion"></div>
        </div>
        <h5 class="mt-3 text-center">Aciertos</h5>
        <p class="text-center" id="calificacion2"></p>
    </div>
@endsection
