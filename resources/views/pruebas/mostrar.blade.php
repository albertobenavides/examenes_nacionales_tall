@extends('layouts.app')

@section('scripts')
<script>
    $(function(){
        respuestas_elegidas = [];
        actual = 0;
        $('#leyenda').hide();
        $('.card-footer').hide();
        $('.pregunta').hide();
        $('#revisando').hide();
        $('#0').show();
        $('.mostrar').click(function () {
            var id = $(this).attr('data-toggle');
            actual = parseInt(id);
            $('.mostrar').removeClass('bg-light');
            $(this).addClass('bg-light');
            $('.pregunta').hide();
            $('#' + id).show();
        });
        $('#siguiente').click(function () {
            if (actual == {{$preguntas->count() - 1}}){
                return;
            }
            $('.mostrar').removeClass('bg-light');
            actual = actual + 1;
            $('button[data-toggle=' + actual + ']').addClass('bg-light');
            $('.pregunta').hide();
            $('#' + actual).show();
        });
        $('#anterior').click(function () {
            if(actual == 0){
                return;
            }
            $('.mostrar').removeClass('bg-light');
            actual = actual - 1;
            $('button[data-toggle=' + actual + ']').addClass('bg-light');
            $('.pregunta').hide();
            $('#' + actual).show();
        })
        $('.respuesta').click(function(){
            var r_id = parseInt($(this).attr('respuesta_id'));
            axios.post(`/pruebas/intento/{{$intento->id}}/${r_id}`).then((response) => {
                
                $(this).siblings().removeClass('bg-info');
                $(this).addClass('bg-info');
            });
        });
        $('#revisar').submit(function(e){
            e.preventDefault();
            if (confirm('¿Terminar examen?')) {
                $('#respuestas_input').val(respuestas_elegidas);
                $(this).unbind('submit');
                $(this).submit();
            }
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h4>{{Auth::user()->name}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">{{$prueba->curso->nombre}}</li>
                <li class="breadcrumb-item">Simulaciones</li>
                <li class="breadcrumb-item">{{$prueba->nombre}}</li>
                <li class="breadcrumb-item active">Revisión</li>
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
                <a class="nav-link active" href="/cursos/{{ $prueba->curso->id }}/examenes">Exámenes</a>
            </li>
        </ul>
    </div>
</div>
<div class="container">
    <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
    <div class="row">
        <div class="col-md-3">
            <h2 class="mt-3 text-center">Preguntas</h2>
            <div class="text-center">
                @php
                    $modulo = null;
                @endphp
                @for ($i = 0; $i < $preguntas->count(); $i++)
                    @if ($modulo != $preguntas->get($i)->tema->modulo->nombre)
                    @php
                        $modulo = $preguntas->get($i)->tema->modulo->nombre;
                    @endphp
                        <h5>{{ $modulo }}</h5>
                    @endif
                    <button class="btn btn-link mostrar {{$i == 0 ? 'bg-light' : ''}}" data-toggle="{{$i}}">{{$i + 1}}</button>
                @endfor
            </div>
            <form action="/examenes/revisar" method="post" class="mt-3" id="revisar">
                @csrf
                <input type="hidden" name="intento_id" value="{{ $intento->id }}">
                <button class="btn btn-block btn-success btn-lg">
                    Terminar
                    <div class="spinner-border text-light" role="status" id="revisando">
                        <span class="sr-only">Revisando...</span>
                    </div>
                </button>
            </form>
            <div id="leyenda">
                <h5 class="mt-3 text-center">Calificación</h5>
                <div class="progress">
                    <div class="progress-bar" role="progressbar" aria-valuenow="0" aria-valuemin="0" aria-valuemax="100" id="calificacion"></div>
                </div>
                <h5 class="mt-3 text-center">Aciertos</h5>
                <p class="text-center" id="calificacion2"></p>
            </div>
        </div>
        <div class="col-md-9">
            @for ($i = 0; $i < $preguntas->count(); $i++)
            <div class="card pregunta" id="{{$i}}" pregunta_id="{{$preguntas->get($i)->id}}">
                <div class="card-header bg-primary text-white">
                    <div class="media">
                        <img src="https://fakeimg.pl/75/?text={{ $i + 1 }}" class="mr-3 img-fluid rounded">
                        <div class="media-body">
                          <p class="lead text-center" id="contenido">{!!$preguntas->get($i)->contenido!!}</p>
                        </div>
                    </div>
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush" id="respuestas">
                        @php
                        $j = 1;
                        @endphp
                        @foreach ($preguntas->get($i)->respuestas->shuffle() as $r)
                            <div class="list-group-item list-group-item-action respuesta" respuesta_id="{{$r->id}}">
                                <ol type="A" start="{{ $j }}">
                                    <li>
                                        {!!$r->contenido!!}
                                    </li>
                                </ol>
                            </div>
                            @php
                                $j += 1;
                            @endphp
                        @endforeach
                    </div>
                </div>
                <div class="card-footer">
                </div>
            </div>
            @endfor
            <button id="anterior" class="btn btn-primary">Anterior</button>
            <button id="siguiente" class="btn btn-primary">Siguiente</button>
        </div>
    </div>
</div>
@endsection
