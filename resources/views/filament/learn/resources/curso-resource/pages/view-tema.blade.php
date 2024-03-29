<x-filament-panels::page>
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
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
                    <a class="no-underline" href="/learn/cursos/{{ $modulo->curso_id }}/modulos/{{ $modulo->id }}/temas/{{ $tema->id }}/ejercicios">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/ejercicios') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Ejercicios</div>
                    </a>
                    <a class="no-underline" href="/learn/cursos/{{ $modulo->curso_id }}/examenes/-{{ $tema->id }}">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/examenes/') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Examen</div>
                    </a>
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
        <div class="fixed md:static bottom-0 md:bottom-auto w-full md:w-1/3 lg:w-1/4 h-1/4 md:h-auto md:block border-t-2 border-t-primary md:border-t-0 shadow-md" x-bind:data-theme="$store.theme">
            <div class="h-full md:h-screen sticky top-0 overflow-scroll">
                <div class="sticky col-start-5" x-bind:data-theme="$store.theme">
                    <div class="pl-3 prose h2 lead bg-primary"><b class="text-white">CONTENIDO</b></div>
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
                                <li><a href="/learn/cursos/{{ $t->modulo->curso->id }}/modulos/{{ $t->modulo_id }}/temas/{{ $t->id }}">{{ $t->nombre }}</a></li>
                            @endif
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    </div>
</x-filament-panels::page>
