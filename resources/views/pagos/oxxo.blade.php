@extends('layouts.app')

@section('content')
<div class="container mt-5">
    <h2>¡Gracias por tu interés, {{ Auth::user()->name }}!</h2>
    
    <img src="/img/oxxopay_brand.png" class="img-fluid my-3">

    <ul class="lead">
        <li>Monto a pagar: <strong>${{ $pago->promo->costo }}</strong></li>
        <li>
            Referencia: <strong>{{$pago->oxxo}}</strong> <br>
            <small>OXXO cobrará una comisión adicional al momento de procesar el pago.</small>
        </li>
    </ul>
    
    
    <p class="lead">Instrucciones:</p>
    <ol class="lead">
        <li>Acude a la tienda OXXO más cercana. <a href='https://www.google.com.mx/maps/search/oxxo/&#039' target='_blank'>Encuéntrala aquí</a></li>
        <li>Indica en caja que quieres realizar un pago de <strong>OXXOPay</strong></li>
        <li>Dicta al cajero el número de referencia en esta ficha para que tecleé directamete en la pantalla de venta</li>
        <li>Realiza el pago correspondiente con dinero en efectivo</li>
        <li>Al confirmar tu pago, el cajero te entregará un comprobante impreso. <strong>En el podrás verificar que se haya realizado correctamente.</strong> Conserva este comprobante de pago</li>
    </ol>

    <p class="lead">Tienes hasta el {{ Carbon\Carbon::now()->addMonth()->format('d/m/y')}} para realizar tu pago.</p>

    <p class="lead">¡Agradecemos mucho tu preferencia!</p>

    <div class="row">
        <div class="col-md">
            <a href="#" onclick="window.print()" class="btn btn-primary btn-block">Imprimir</a>
        </div>
        <div class="col-md">
            <a href="/inicio" class="btn btn-primary btn-block">Ir a cursos</a>
        </div>
        <div class="col-md">
            <a href="/usuarios/{{ Auth::id() }}" class="btn btn-primary btn-block">Ir a Historial de pagos</a>
        </div>
    </div>
</div>
@endsection