@extends('layouts.new_app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if ($tema->contenido != null)
                let currentParagraphName = document.getElementById('current-paragraph-name');
                let currentParagraphPercent = document.getElementById('current-paragraph-percent');

                new ScrollProgress.Init(
                    "#cursor",
                    "#menu",
                    progress => {
                        try {
                            let value_t = document.getElementById(progress.Id + '-p').value;
                            document.getElementById(progress.Id + '-p').value = (value_t < progress.Percent) ? progress.Percent : value_t;
                        } catch (error) {
                            //
                        }
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
    <div class="flex">
        <div class="md:w-2/3 lg:w-3/4 prose">

            <div class="container p-4">
                <h1 class="lead">{{ $tema->nombre }}</h1>
                <hr>
                @if ($tema->contenido != null)
                    @for ($i = 0; $i < count($tema->contenido); $i++)
                        <livewire:ver-contenido :tema="$tema" :i="$i" />
                    @endfor
                @endif
            </div>
        </div>
        <div class="fixed md:static bottom-0 md:bottom-auto w-full h-1/4 md:h-auto md:w-1/3 lg:w-1/4 md:block bg-white">
            <div class="h-full md:h-screen bg-white sticky top-0 overflow-scroll">
                <div class="sticky col-start-5">
                    <ul class="menu menu-xs">
                        <li class="prose h2 lead">CONTENIDO</li>
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
