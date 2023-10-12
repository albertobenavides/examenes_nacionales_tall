@extends('layouts.app')

@section('content')
<div class="jumbotron">
    <div class="container">
        <h4>{{Auth::user()->name}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Inicio</li>
                <li class="breadcrumb-item active">Cursos</li>
            </ol>
        </nav>
        @php
            $pago = App\Pago::where('user_id', Auth::id())->where('fin', '>=', Carbon\Carbon::today())->orderByDesc('promo_id')->first();
        @endphp
        @if ( $pago == null )
            <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
        @else
            <h5>Curso: <a href="/cursos/13">{{ $pago->curso->nombre }}</a></h5>
            <h5>Plan: {{ $pago->promo->nombre }} 
                @if (Auth::user()->por_admin == 1 && $pago->promo_id == 1)
                <a href="/pagos/crear" class="btn btn-sm btn-secondary">Convenio CEAA</a>
                @endif
            </h5>
            <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
        @endif
    </div>
</div>
<div class="container">
    <div class="d-flex align-content-md-start flex-wrap">
        @foreach(App\Curso::where('activo', 1)->get() as $c)
        <div class="card m-2" style="width:18rem">
            @if($c->imagen)
            <img src="/storage/{{$c->imagen}}" class="card-img-top">
            @else
            <img src="https://fakeimg.pl/320x200/?text={{$c->nombre}}" class="card-img-top">
            @endif
            <div class="card-body">
                <h4 class="card-title text-center">{{$c->nombre}}</h4>
                <p class="card-text">{{$c->descripcion, 20}}</p>
                <p><small class="text-muted">{{$c->examen->nombre}}</small></p>
                <a href="/cursos/{{$c->id}}" class="btn btn-sm btn-primary btn-block mt-2" form="nuevoCurso">Ver</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection