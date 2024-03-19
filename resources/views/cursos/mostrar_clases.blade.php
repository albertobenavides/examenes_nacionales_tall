@extends('layouts.new_app')

@section('content')
    <div class="lg:flex p-5">
        <div class="lg:basis-3/4">
            <h2><b>Módulos del curso</b></h2>
            @foreach ($curso->modulos->sortby('orden') as $m)
                @php
                    $totales = 0.0;
                    $pasados = 0.0;
                    $avance = 0;
                @endphp
                @foreach ($m->temas as $t)
                    @if ($t->preguntar <= 0)
                        @continue
                    @endif
                    @php
                        $totales = $totales + 1;
                        $max = App\Models\Intento::where('user_id', Auth::id())
                            ->where('prueba_id', $t->id * -1)
                            ->max('calificacion');
                        $pasados = $max >= 90 ? $pasados + 1 : $pasados;
                        if ($totales <= 0) {
                            $avance = 0;
                        } else {
                            $avance = intval(($pasados / $totales) * 100);
                        }
                    @endphp
                @endforeach
                {{-- [ ] Actualizar URLs --}}
                <a href="/modulos/{{ $m->id }}" class="card lg:card-side bg-white shadow-xl my-3"> 
                    <figure class="w-full lg:w-60">
                        @if ($m->imagen)
                            <img src="/storage/{{ $m->imagen }}">
                        @else
                            <img src="https://fakeimg.pl/320x200/?text={{ $m->nombre }}">
                        @endif
                    </figure>
                    <div class="card-body p-2 flex flex-row">
                        <div class="basis-3/5 lg:basis-3/4 flex flex-col justify-between">
                            <div>
                                <h5 class="card-title mb-5">{{ $m->nombre }}</h5>
                                <p>{{ $m->descripcion }}</p>
                            </div>
                            <progress class="progress progress-primary w-full" value="{{ $avance }}" max="100"></progress>
                        </div>
                        <div class="basis-2/5 lg:basis-1/4 border-s border-primary p-2 flex flex-col justify-between">
                            <div>
                                <p class="text-sm"><b>Tema</b></p>
                            </div>
                            <div class="btn btn-sm btn-primary btn-block">Continuar</div>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
        <div class="lg:basis-1/4 ms-4">
            <h2 class="text-center"><b>Sesiones</b></h2>
        </div>
    </div>
@endsection
