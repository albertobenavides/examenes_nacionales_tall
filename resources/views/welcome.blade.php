@extends('layouts.new_app')

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", () => {
            let defaultTransform = 0;

            function goNext() {
                defaultTransform = defaultTransform - 398;
                var slider = document.getElementById("slider");
                if (Math.abs(defaultTransform) >= slider.scrollWidth / 1.7) defaultTransform = 0;
                slider.style.transform = "translateX(" + defaultTransform + "px)";
            }
            let next = document.getElementById('next');
            next.addEventListener("click", goNext);

            function goPrev() {
                var slider = document.getElementById("slider");
                if (Math.abs(defaultTransform) === 0) defaultTransform = 0;
                else defaultTransform = defaultTransform + 398;
                slider.style.transform = "translateX(" + defaultTransform + "px)";
            }
            let prev = document.getElementById('prev');
            prev.addEventListener("click", goPrev);
        });
    </script>
@endpush

@section('content')
    {{-- ANUNCIOS --}}
    <div class="carousel w-full">
        <div id="slide1" class="carousel-item relative w-full">
            <img src="/img/banner2.jpg" class="w-full" />
            <div class="absolute text-white bg-gradient-to-r from-primary w-full h-full">
                <div class="ps-20 flex flex-row h-full">
                    <div class="basis-[30%] self-center">
                        <h1 class="text-3xl font-bold">Curso de inducción</h1>
                        <p class="py-6">Aprende todo sobre la plataforma y recomendaciones para mejorar tu rendimiento de tus cursos.</p>
                    </div>
                </div>
            </div>
            <div class="absolute flex justify-between transform -translate-y-1/2 left-5 right-5 top-1/2">
                <a href="#slide4" class="btn btn-circle">❮</a>
                <a href="#slide2" class="btn btn-circle">❯</a>
            </div>
        </div>
    </div>
    {{-- .ANUNCIOS --}}

    <div class="flex items-center justify-center w-full h-full py-24 sm:py-8 px-4">
        <div class="w-full relative flex items-center justify-center">
            <button aria-label="slide backward" class="absolute z-30 left-0 ml-10 focus:outline-none focus:bg-gray-400 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400 cursor-pointer" id="prev">
                <svg class="dark:text-gray-900" width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M7 1L1 7L7 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
            <div class="w-full h-full mx-auto overflow-x-hidden overflow-y-hidden">
                <div id="slider" class="h-full flex lg:gap-8 md:gap-6 gap-14 items-center justify-start transition ease-out duration-700">
                    @foreach (App\Models\Curso::where('activo', 1)->get() as $curso)
                        <a href="/cursos/{{ $curso->id }}" class="flex flex-shrink-0 relative w-full sm:w-auto">
                            <div class="card w-96 image-full">
                                <figure><img src="https://examenesnacionales.com/storage/{{ $curso->imagen }}" alt="{{ $curso->nombre }}" /></figure>
                                <div class="card-body">
                                    <h2 class="card-title">{{ $curso->nombre }}</h2>
                                    <p>{!! $curso->descripcion !!}</p>
                                </div>
                            </div>
                        </a>
                    @endforeach
                </div>
            </div>
            <button aria-label="slide forward" class="absolute z-30 right-0 mr-10 focus:outline-none focus:bg-gray-400 focus:ring-2 focus:ring-offset-2 focus:ring-gray-400" id="next">
                <svg class="dark:text-gray-900" width="8" height="14" viewBox="0 0 8 14" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <path d="M1 1L7 7L1 13" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                </svg>
            </button>
        </div>
    </div>
@endsection
