@extends('layouts.app')

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$prueba->curso->nombre}}</li>
                <li class="breadcrumb-item">Simulaciones</li>
                <li class="breadcrumb-item">{{$prueba->nombre}}</li>
                <li class="breadcrumb-item active">Intentos</li>
            </ol>
        </nav>
        @php
            $pago = App\Pago::where('user_id', Auth::id())->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon\Carbon::today())->orderByDesc('promo_id')->first();
        @endphp
        @if ( $pago == null )
            <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
        @else
            <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
        @endif

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $prueba->curso->id }}/logros">Logros</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $prueba->curso->id }}/clases">Clases</a>
            </li>
            <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $prueba->curso->id }}/videos">Videos</a>
            </li>
            <li class="nav-item">
                <a class="nav-link active" href="/cursos/{{ $prueba->curso->id }}/examenes">Exámenes</a>
            </li>
        </ul>
    </div>
</div>
<div class="container">
    <div class="table-responsive">
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th class="text-center">Calificación</th>
                    <th class="text-center">Aciertos</th>
                    <th class="text-center">Revisión</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($intentos as $i)
                <tr>
                    <td>{{ $i->created_at }}</td>
                    <td class="text-center">{{ $i->calificacion }}</td>
                    <td class="text-center">{{ $i->aciertos }}</td>
                    <td class="text-center"><a href="/pruebas/{{$i->prueba_id}}/intentos/{{$i->id}}"><i class="fas fa-edit"></i></a></td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endsection
