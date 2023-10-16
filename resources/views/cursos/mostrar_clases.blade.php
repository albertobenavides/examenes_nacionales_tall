@extends('layouts.app')

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name ?? 'Invitado'}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$curso->nombre}}</li>
                <li class="breadcrumb-item active">Clases</li>
            </ol>
        </nav>
        @php
            $pago = App\Models\Pago::where('user_id', Auth::id())->where('curso_id', $curso->id)->where('fin', '>=', Carbon\Carbon::today())->orderByDesc('promo_id')->first();
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
              <a class="nav-link active" href="/cursos/{{ $curso->id }}/clases">Clases</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/cursos/{{ $curso->id }}/logros">Logros</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/cursos/{{ $curso->id }}/examenes">Simulaciones</a>
            </li>
        </ul>
    </div>
</div>
<div class="container">
    <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
    <h5>Para avanzar en el porcentaje tiene que sacar calificación mayor a 90 en los exámenes</h5>
    <div class="d-flex flex-wrap justify-content-center">
        @php
            $gran_totales = 0.0;
            $gran_pasados = 0.0;
            $gran_val = 0;
        @endphp
        @foreach($curso->modulos->sortby('orden') as $m)
        <div class="card mr-3 my-2 shadow" style="width: 20rem;">
            @if($m->imagen)
            <img src="/storage/{{$m->imagen}}" class="card-img-top">
            @else
            <img src="https://fakeimg.pl/320x200/?text={{$m->nombre}}" class="card-img-top">
            @endif
            <div class="card-body">
                <h5 class="card-title mb-5">{{$m->nombre}}</h5>
                @php
                    $totales = 0.0;
                    $pasados = 0.0;
                    $val = 0;
                @endphp
                @foreach ($m->temas as $t)
                @if ($t->preguntar <= 0)
                    @continue
                @endif
                @php
                    $totales = $totales + 1;
                    $gran_totales = $gran_totales + 1;
                    $max = App\Models\Intento::where('user_id', Auth::id())->where('prueba_id', $t->id * -1)->max('calificacion');
                    $pasados = $max >= 90 ? $pasados + 1 : $pasados;
                    $gran_pasados = $max >= 90 ? $gran_pasados + 1 : $gran_pasados;
                    if ($totales <= 0){
                        $val = 0;
                    } else {
                        $val = intval($pasados / $totales * 100);
                    }
                @endphp
                @endforeach
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{$val}}%" aria-valuenow="{{$val}}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="text-center">{{$val}}%</p>
                <a class="btn btn-block btn-primary" href="/modulos/{{$m->id}}">ESTUDIAR</a>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
