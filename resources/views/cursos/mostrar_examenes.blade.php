@extends('layouts.app')

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name ?? 'Invitado'}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$curso->nombre}}</li>
                <li class="breadcrumb-item active">Simulaciones</li>
            </ol>
        </nav>
        @php
            $pago = App\Pago::where('user_id', Auth::id())->where('curso_id', $curso->id)->where('fin', '>=', Carbon\Carbon::today())->orderByDesc('promo_id')->first();
        @endphp
        @if ( $pago == null )
            <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
        @else
        <h5>Plan: {{ $pago->promo->nombre }} 
            @if (Auth::user()->por_admin == 1 && $pago->promo_id == 1)
            <a href="/pagos/crear" class="btn btn-sm btn-secondary">Convenio CEAA</a>
            @endif
        </h5>
        <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
        @endif

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $curso->id }}/clases">Clases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $curso->id }}/logros">Logros</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/cursos/{{ $curso->id }}/examenes">Simulaciones</a>
            </li>
        </ul>
    </div>
</div>
<div class="container">
    <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
    <h2>Exámenes</h2>
    <div class="row mb-3">
        @php
            $i = 0
        @endphp
        @foreach ($curso->pruebas as $p)
        @if ($p->temas->pluck('pivot.preguntas')->sum() == 0)
            @continue
        @else
        @php
        $i = $i + 1
        @endphp
        @endif
        <div class="col-md-6 mb-3">
            <a href="/examenes/{{$p->id}}" class="list-group-item list-group-item-action">
                <h6>{{$p->nombre}}</h6>
                <small>{{$p->temas->pluck('pivot.preguntas')->sum()}}</small> preguntas
            </a>
        </div>
        @endforeach
        @if ($i == 0)
            <h4 class="my-3">Próximamente</h4>
        @endif
    </div>

    <h2>Historial <small>NA indica que no ha presentado la Simulación</small></h2>
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Simulación</th>
                    <th class="text-center">Calif. mayor</th>
                    <th class="text-center">Calif. menor</th>
                    <th class="text-center">Calif. promedio</th>
                    <th class="text-center">Intentos</th>
                </tr>
            </thead>
            <tbody>
                @php
                    $totales = 0.0;
                    $pasados = 0.0;
                @endphp
                @foreach ($curso->pruebas as $p)
                @php
                    $intentos = App\Intento::where('user_id', Auth::id())->where('prueba_id', $p->id)->where('calificacion', '>', -1);
                    $max = $intentos->max('calificacion');
                    $min = $intentos->min('calificacion');
                    $avg = $intentos->avg('calificacion');
                    $total = $intentos->count();
                @endphp
                <tr>
                    <td><strong><a href="/pruebas/{{$p->id}}/intentos">{{ $p->nombre }}</a></strong></td>
                    <td class="text-center">{{ $max === null ? 'NA' : $max }}</td>
                    <td class="text-center">{{ $min === null ? 'NA' : $min }}</td>
                    <td class="text-center">{{ $avg === null ? 'NA' : $avg }}</td>
                    <td class="text-center">{{ $total }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
