@extends('layouts.new_app')

@push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/js-circle-progress/dist/circle-progress.min.js" type="module"></script>
    <script src="/js/scroll-progress/scroll-progress.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.9.2/dist/confetti.browser.min.js"></script>
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
                <a class="text-white no-underline" href="/modulos/{{ $modulo->id }}/temas/{{ $tema->id }}">
                    <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/temas/') && !str_contains(url()->full(), '/ejercicios') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Contenido</div>
                </a>
                <div class="flex gap-x-8">
                    <a class="no-underline" href="/modulos/{{ $modulo->id }}/temas/{{ $tema->id }}/ejercicios">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/ejercicios') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Ejercicios</div>
                    </a>
                    <a class="no-underline" href="/examenes/-{{ $tema->id }}">
                        <div class="rounded-b-lg px-4 py-0 {{ str_contains(url()->full(), '/examenes/') ? 'bg-primary text-white' : 'outline outline-2 outline-primary text-primary' }}">Examen</div>
                    </a>
                </div>
            </div>
            <div class="container p-4">
                @yield('contenido', 'asdfasdfdasdfasdfasd')
            </div>
        </div>
        <div class="fixed md:static bottom-0 md:bottom-auto w-full h-1/4 md:h-auto md:w-1/3 lg:w-1/4 md:block bg-white border-t-2 border-t-primary md:border-t-0 shadow-md">
            <div class="h-full md:h-screen bg-white sticky top-0 overflow-scroll">
                <div class="sticky col-start-5">
                    @yield('sidebar', '')
                </div>
            </div>
        </div>
    </div>
@endsection
