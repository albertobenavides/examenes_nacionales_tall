@extends('temas.base')

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script>
        function mostrarPregunta() {
            $('#revisar').hide();
            $('#siguiente').hide();
            $('#ayuda').html('');
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/preguntas/{{ $tema->id }}',
                type: 'GET',
                success: function(data) {
                    $('#revisar').attr('pregunta_id', data.id);
                    $('#contenido').html(data.contenido);
                    $('#respuestas').empty();
                    for (var i = 0; i < data.r.length; i++) {
                        var t = `<li class="respuesta bg-neutral m-1 rounded" respuesta_id="${data.r[i].id}" pregunta_id="${data.id}">
                                <div>${data.r[i].contenido}</div>
                                </li>`;
                        $('#respuestas').append(t);
                        $('.respuesta').click(function() {
                            $('.respuesta').removeClass('bg-primary text-white');
                            $(this).addClass('bg-primary text-white');
                            $('#revisar').show();
                        });
                    }
                    MathJax.typeset();
                }
            });
        }
        $(function() {
            mostrarPregunta();
            racha = 0;
        });

        $('#revisar').click(function(e) {
            $('#revisar').hide();
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/preguntas/revisar/' + $(this).attr('pregunta_id'),
                type: 'POST',
                success: function(data) {
                    $('#siguiente').show();
                    var t = document.querySelector('.respuesta[respuesta_id="' + data.id + '"]');
                    if (t.classList.contains('bg-primary')) {
                        racha += 1;
                        t.classList.remove('bg-primary');
                        var scalar = 5;
                        confetti({
                            spread: 360,
                            ticks: 50,
                            gravity: 0,
                            decay: 1,
                            startVelocity: 10,
                            origin: {
                                y: 0.6,
                                x: 0.5
                            },
                            shapes: [
                                confetti.shapeFromText({
                                    text: '⭐',
                                    scalar
                                })
                            ],
                            scalar
                        });
                        var audio = new Audio('/sounds/success.mp3');
                        audio.play();
                    } else {
                        racha = 0;
                        var audio = new Audio('/sounds/error.mp3');
                        audio.play();
                    }
                    $('#racha').html(racha);
                    t.classList.add('bg-success');
                    $('.respuesta').off('click');
                    $('#ayuda').html(data.ayuda)
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
    <div class="flex justify-center">
        <h2>Racha: <span id="racha">0</span></h2>
    </div>
    <div class="pregunta">
        <h2 class="text-center">Pregunta <span id='pregunta_num'></span></h2>
        <p id="contenido">CONTENIDO</p>
        <div class="card-body px-0">
            {{-- RESPUESTA --}}
            <div class="menu menu-sm" id="respuestas">

            </div>
            {{-- .RESPUESTA --}}
        </div>
        <div class="card-footer">
            <div id="ayuda"></div>
        </div>
    </div>
    {{-- .PREGUNTA --}}
    <div class="flex justify-between">
        <button id="revisar" class="btn btn-sm btn-primary">Revisar</button>
        <button id="siguiente" tema_id="{{ $tema->id }}" class="btn btn-sm btn-primary" onclick="mostrarPregunta()">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
            </svg>
        </button>
    </div>
@endsection

@section('sidebar')
    <div class="pl-3 prose h2 lead bg-primary text-white text"><b>CONTENIDO</b></div>
    <ul class="menu">
        @foreach ($modulo->temas->sortBy('order') as $t)
            @if ($t->id == $tema->id)
                <li><a href="/modulos/{{ $t->modulo_id }}/temas/{{ $t->id }}"><b>{{ $t->nombre }}</b></a></li>
            @else
                <li><a href="/modulos/{{ $t->modulo_id }}/temas/{{ $t->id }}">{{ $t->nombre }}</a></li>
            @endif
        @endforeach
    </ul>
@endsection
