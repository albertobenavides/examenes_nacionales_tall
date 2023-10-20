@extends('layouts.app')

@section('styles')
@endsection

@section('scripts')
@endsection

@section('content')
<div class="text-center my-5">
    <h2>Prepárate con el curso de ingreso mejor calificado de México</h2>
    <h3>Entra a la universidad o bachillerato que siempre soñaste</h3>
</div>
<div class="container">
    <div class="card-columns">
        @foreach (App\Models\Promo::all() as $p)
        @if ($p->id > 3)
            @break
        @endif
        <div class="card">
            <img class="card-img-top" src="{{ $p->imagen }}" alt="">
            <div class="card-body text-center">
                <h4 class="card-title font-weight-bold">{{$p->nombre}}</h4>
                <p class="card-text">{{$p->descripcion}}</p>
                <h4>${{$p->costo}} MXN</h4>
                <a href="/pagos/crear" class="btn btn-primary">Comprar</a>
            </div>
        </div>
        @endforeach
    </div>
</div>

<div class="d-flex justify-content-center my-5">
    <p class="col-md-4 lead"><b>Garantía de satisfacción</b>: <br> <br>
        Queremos que estés contento. Si no te gusta el curso cuéntanos por qué y te regresamos tu dinero (Aplican Términos y Condiciones).
    </p>
</div>

<div class="jumbotron jumbotron-fluid">
    <div class="container text-center">
        <h1>El mejor curso al mejor precio</h1>
        <p class="lead">Descubre porque al estudiar con Exámenes Nacionales tienes más y mejores posibilidades de cumplir tus objetivos académicos.</p>
        <div class="container my-5">
            <div class="row">
                <div class="col-md">
                    <h3>Tus posibilidades crecen</h3>
                    <p>
                        3 veces más posibilidad de ingreso a las mejores universidades del país.
                    </p>
                </div>
                <div class="col-md">
                    <h3>Mide tus resultados</h3>
                    <p>Nuestros exámenes de simulación pueden predecir hasta en un 90% tu resultado en el examen real.</p>
                </div>
                <div class="col-md">
                    <h3>Aumenta tu probabilidad</h3>
                    <p>Las calificación de los alumnos de Exámenes Nacionales es 36% mayor al promedio nacional.</p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="d-flex justify-content-center my-5">
    <div class="col-md-4">
        <h3>Conoce nuestras herramientas de estudio</h3>
        <p>
            Con Exámenes Nacionales podrás prepararte bajo una metodología de estudio innovadora y efectiva, en el momento y lugar que tú elijas.
        </p>
    </div>  
</div>
@endsection