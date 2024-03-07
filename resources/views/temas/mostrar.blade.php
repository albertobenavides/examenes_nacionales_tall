@extends('layouts.new_app')

@push('scripts')
    @filamentScripts
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
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
                            <livewire:ver-contenido :tema="$tema" :i="$i" />
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
                                                <livewire:toc-temas :tema="$tema" :i="$i" />
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
