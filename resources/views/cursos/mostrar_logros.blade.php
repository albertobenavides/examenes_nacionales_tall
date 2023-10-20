@extends('layouts.app')

@section('styles')
<style>
    .progress {
    width: 120px;
    height: 120px;
    background: none;
    position: relative;
    }

    .progress::after {
    content: "";
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 6px solid #eee;
    position: absolute;
    top: 0;
    left: 0;
    }

    .progress>span {
    width: 50%;
    height: 100%;
    overflow: hidden;
    position: absolute;
    top: 0;
    z-index: 1;
    }

    .progress .progress-left {
    left: 0;
    }

    .progress .progress-bar {
    width: 100%;
    height: 100%;
    background: none;
    border-width: 6px;
    border-style: solid;
    position: absolute;
    top: 0;
    }

    .progress .progress-left .progress-bar {
    left: 100%;
    border-top-right-radius: 80px;
    border-bottom-right-radius: 80px;
    border-left: 0;
    -webkit-transform-origin: center left;
    transform-origin: center left;
    }

    .progress .progress-right {
    right: 0;
    }

    .progress .progress-right .progress-bar {
    left: -100%;
    border-top-left-radius: 80px;
    border-bottom-left-radius: 80px;
    border-right: 0;
    -webkit-transform-origin: center right;
    transform-origin: center right;
    }

    .progress .progress-value {
    position: absolute;
    top: 0;
    left: 0;
    }
    .rounded-lg {
    border-radius: 1rem;
    }

</style>
@endsection

@section('scripts')
<script>
    $(function() {
        $(".progress").each(function() {

        var value = $(this).attr('data-value');
        var left = $(this).find('.progress-left .progress-bar');
        var right = $(this).find('.progress-right .progress-bar');

        if (value > 0) {
            if (value <= 50) {
            right.css('transform', 'rotate(' + percentageToDegrees(value) + 'deg)')
            } else {
            right.css('transform', 'rotate(180deg)')
            left.css('transform', 'rotate(' + percentageToDegrees(value - 50) + 'deg)')
            }
        }

        })

        function percentageToDegrees(percentage) {

        return percentage / 100 * 360

        }

        });
</script>
@endsection

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name ?? 'Invitado'}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$curso->nombre}}</li>
                <li class="breadcrumb-item active">Logros</li>
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
              <a class="nav-link" href="/cursos/{{ $curso->id }}/clases">Clases</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="/cursos/{{ $curso->id }}/logros">Logros</a>
            </li>
            {{-- <li class="nav-item">
              <a class="nav-link" href="/cursos/{{ $curso->id }}/examenes">Simulaciones</a>
            </li> --}}
          </ul>
    </div>
</div>
@php
$gran_totales = 0.0;
$gran_pasados = 0.0;
$gran_val = 0;
@endphp
@foreach($curso->modulos->sortby('orden') as $m)
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
@endforeach
@php
if ($gran_totales > 0){
    $gran_val = intval($gran_pasados / $gran_totales * 100);
}
@endphp
<div class="container">
    <h5 class="mb-3">Para avanzar en el porcentaje tiene que sacar calificación mayor a 90 en los exámenes</h5>
    <div class="media">
        <div class="mx-5">
            <h3 class="text-center">Global</h3>
            <div class="progress mx-auto" data-value='{{$gran_val}}'>
                <span class="progress-left">
                    <span class="progress-bar border-primary"></span>
                </span>
                <span class="progress-right">
                    <span class="progress-bar border-primary"></span>
                </span>
                <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                    <div class="h3 font-weight-bold">{{$gran_val}}%</div>
                </div>
            </div>
            <p class="lead text-center">{{$gran_pasados}} / {{$gran_totales}}</p>
        </div>
        <div class="media-body">
            <h4 class="mt-0"><strong>{{Auth::user()->name ?? 'Invitado'}}</strong></h4>
            @if ( $pago == null )
            <h5><a href="/pagos/crear" >Inscríbete ahora</a></h5>
            @else
                <h5>Plan: {{ $pago->promo->nombre }}</h5>
            @endif
        </div>
    </div>

    <div class="row">
        @foreach ($curso->modulos->sortby('orden') as $m)
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
                    $max = App\Models\Intento::where('user_id', Auth::id())->where('prueba_id', $t->id * -1)->max('calificacion');
                    $pasados = $max >= 90 ? $pasados + 1 : $pasados;
                    if ($totales <= 0){
                        $val = 0;
                    } else {
                        $val = intval($pasados / $totales * 100);
                    }
                @endphp
            @endforeach
        <div class="col-md-3 mb-3">
            <div class="card">
                <div class="card-body">
                    <h6 class="text-center"><a href="/modulos/{{$m->id}}">{{$m->nombre}}</a></h6>
                    <div class="progress mx-auto" data-value='{{$val}}'>
                        <span class="progress-left">
                            <span class="progress-bar border-primary"></span>
                        </span>
                        <span class="progress-right">
                            <span class="progress-bar border-primary"></span>
                        </span>
                        <div class="progress-value w-100 h-100 rounded-circle d-flex align-items-center justify-content-center">
                            <div class="h3 font-weight-bold">{{$val}}%</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>
@endsection
