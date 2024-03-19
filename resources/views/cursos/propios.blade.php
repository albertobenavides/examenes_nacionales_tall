@extends('layouts.new_app')

@section('content')
    @foreach (auth()->user()->pagos as $pago)
    @php
        $temas = App\Models\Tema::whereIn('modulo_id', $pago->curso->modulos->pluck('id'))->where('preguntar', '>', 0)->count();
        $avance = round(($intentos / $temas * 100));
    @endphp
        <div class="flex justify-center p-10">
            <a href="/cursos/{{ $pago->curso_id }}" class="md:basis-1/3 lg:basis-1/4 card bg-base-100 shadow-xl rounded-md">
                <figure><img src="/storage/{{ $pago->curso->imagen }}" alt="{{ $pago->curso->nombre }}" /></figure>
                <div class="card-body p-2">
                    <h2 class="card-title justify-center text-[1rem]">{{ $pago->curso->nombre }}</h2>
                    <div class="card-actions w-full">
                        <div class="flex w-full items-center">
                            <progress class="progress progress-primary grow me-2" value="{{ $avance }}" max="100"></progress> <span class="shrink">{{ $avance }}%</span>
                        </div>
                        <div class="btn btn-sm btn-primary btn-block">Continuar</div>
                    </div>
                </div>
            </a>
        </div>
    @endforeach
@endsection
