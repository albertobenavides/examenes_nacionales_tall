<x-filament-panels::page>
    <script id="MathJax-script" src="https://cdn.jsdelivr.net/npm/mathjax@3/es5/tex-mml-chtml.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
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
                                <span>${data.r[i].contenido}</span>
                                </li>`;
                        $('#respuestas').append(t);
                    }
                    MathJax.typeset();
                    $('.respuesta').click(function() {
                        $('.respuesta').removeClass('bg-primary text-white');
                        $(this).addClass('bg-primary text-white');
                        $('#revisar').show();
                    });
                }
            });
        }
        $(function() {
            mostrarPregunta();
            racha = 0;
            $('#revisar').click(function(e) {
            console.log('test');
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
        });

    </script>
    <div class="navbar bg-primary text-white">
        <div class="text-sm breadcrumbs">
            <ul class="text-xl">
                <li><a href="/learn/cursos">Cursos</a></li>
                <li><a href="/learn/cursos/{{ $modulo->curso->id }}">{{ $modulo->curso->nombre }}</a></li>
                <li>{{ $modulo->nombre }}</li>
                <li>{{ $tema->nombre }}</li>
            </ul>
        </div>
    </div>
    <div class="flex">
        <div class="md:w-2/3 lg:w-3/4 prose" x-bind:data-theme="$store.theme">
            <div class="px-4 flex bg-transparent text-white justify-between">
                <a class="text-white no-underline" href="/learn/cursos/{{ $modulo->curso_id }}/modulos/{{ $modulo->id }}/temas/{{ $tema->id }}">
                    <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/temas/') && !str_contains(url()->full(), '/ejercicios') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Contenido</div>
                </a>
                <div class="flex gap-x-8" x-bind:data-theme="$store.theme">
                    <a class="no-underline" href="/modulos/{{ $modulo->id }}/temas/{{ $tema->id }}/ejercicios">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/ejercicios') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Ejercicios</div>
                    </a>
                    <a class="no-underline" href="/examenes/-{{ $tema->id }}">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/examenes/') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Examen</div>
                    </a>
                </div>
            </div>
            <div class="container p-4">
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
            </div>
        </div>
        <div class="fixed md:static bottom-0 md:bottom-auto w-full md:w-1/3 lg:w-1/4 h-1/4 md:h-auto md:block border-t-2 border-t-primary md:border-t-0 shadow-md" x-bind:data-theme="$store.theme">
            <div class="h-full md:h-screen sticky top-0 overflow-scroll">
                <div class="sticky col-start-5" x-bind:data-theme="$store.theme">
                    <div class="pl-3 prose h2 lead bg-primary"><b class="text-white">CONTENIDO</b></div>
                    <ul class="menu">
                        @foreach ($modulo->temas->sortBy('order') as $t)
                            @if ($t->id == $tema->id)
                                <li><a href="/learn/cursos/{{ $t->modulo->curso_id }}/modulos/{{ $t->modulo_id }}/temas/{{ $t->id }}"><b>{{ $t->nombre }}</b></a></li>
                            @else
                                <li><a href="/learn/cursos/{{ $t->modulo->curso_id }}/modulos/{{ $t->modulo_id }}/temas/{{ $t->id }}">{{ $t->nombre }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
