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
            <div class="px-4 flex bg-transparent text-white justify-between">
                <div class="rounded-b-lg bg-primary px-4 py-0">Contenido</div>
                <div class="flex gap-x-8">
                    <div class="rounded-b-lg outline outline-2 outline-primary px-4 py-0 text-primary">Ejercicios</div>
                    <div class="rounded-b-lg outline outline-2 outline-primary px-4 py-0 text-primary">Examen</div>
                </div>
            </div>
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
        <div class="fixed md:static bottom-0 md:bottom-auto w-full h-1/4 md:h-auto md:w-1/3 lg:w-1/4 md:block bg-white border-t-2 border-t-primary md:border-t-0 shadow-md">
            <div class="h-full md:h-screen bg-white sticky top-0 overflow-scroll">
                <div class="sticky col-start-5">
                    <div class="pl-3 prose h2 lead bg-primary text-white text"><b>CONTENIDO</b></div>
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
