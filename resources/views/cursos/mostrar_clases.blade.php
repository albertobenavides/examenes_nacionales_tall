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
            @forelse ($curso->meetings as $meeting)
                <div class="card shadow-lg my-3">
                    <div class="card-title flex bg-primary text-white p-1 rounded-t-md">
                        <div class="w-5">
                            @if ($meeting->status == 'grabada')
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M5.25 5.653c0-.856.917-1.398 1.667-.986l11.54 6.347a1.125 1.125 0 0 1 0 1.972l-11.54 6.347a1.125 1.125 0 0 1-1.667-.986V5.653Z" />
                                </svg>
                            @elseif($meeting->status == null)
                                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                        d="m15.75 10.5 4.72-4.72a.75.75 0 0 1 1.28.53v11.38a.75.75 0 0 1-1.28.53l-4.72-4.72M4.5 18.75h9a2.25 2.25 0 0 0 2.25-2.25v-9a2.25 2.25 0 0 0-2.25-2.25h-9A2.25 2.25 0 0 0 2.25 7.5v9a2.25 2.25 0 0 0 2.25 2.25Z" />
                                </svg>
                            @else
                            @endif
                        </div>
                        <div class="w-full">
                            <h2 class="text-center">
                                {{ $meeting->created_at }}
                            </h2>
                        </div>
                    </div>
                    <div class="card-body bg-white rounded-b-md">
                        <p class="text-center">{{ $meeting->meetingName }}</p>
                    </div>
                </div>
            @empty
            <div class="card shadow-lg my-3">
                <div class="card-body bg-white rounded-b-md">
                    <p class="text-center">No hay sesiones aún</p>
                </div>
            </div>
                
            @endforelse
        </div>
    </div>
@endsection
