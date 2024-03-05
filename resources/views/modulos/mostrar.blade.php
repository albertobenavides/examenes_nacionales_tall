@extends('layouts.app')

@section('styles')
    @filamentStyles
@endsection

@section('scripts')
    @filamentScripts
@endsection

@section('content')
    <div class="jumbotron pb-0">
        <div class="container">
            <h4>{{ Auth::user()->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">{{ $modulo->curso->nombre }}</li>
                    <li class="breadcrumb-item"><a href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a></li>
                    <li class="breadcrumb-item active">{{ $modulo->nombre }}</li>
                </ol>
            </nav>
            @if ($pago == null)
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
                    <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/logros">Logros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a>
                </li>
                <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/examenes">Exámenes</a>
            </li>
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
                <h2 class="mt-3">Progreso</h2>
                @php
                    if ($totales <= 0) {
                        $val = 0;
                    } else {
                        $val = intval(($pasados / $totales) * 100);
                    }
                @endphp
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{ $val }}%" aria-valuenow="{{ $val }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="text-center">{{ $val }}%</p>
            </div>
            <div class="col-md-9">
                <p>Para avanzar en el porcentaje, tienes que sacar calificación mayor a 90 en los exámenes</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-9">
                <div class="table-responsive">
                    <table class="table table-sm table-stripped">
                        <thead>
                            <tr>
                                <th style="width:55%">
                                    <h2>Temas</h2>
                                </th>
                            </tr>
                        </thead>
                        <tbody id="tabla_temas">
                            @foreach ($temas as $tema)
                            <tr>
                                <th>
                                    <a href="/modulos/{{ $modulo->id }}/temas/{{ $tema['id'] }}">{{ $tema['nombre'] }}</a> 
                                </th>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
@endsection
