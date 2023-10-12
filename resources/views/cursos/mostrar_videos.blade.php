@extends('layouts.app')

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$curso->nombre}}</li>
                <li class="breadcrumb-item active">Videos</li>
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
              <a class="nav-link active" href="/cursos/{{ $curso->id }}/videos">Videos</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/cursos/{{ $curso->id }}/examenes">Exámenes</a>
            </li>
          </ul>
    </div>
</div>
<div class="container">
    <h1 class="heading-3">Próximamente</h1>
</div>
@endsection
