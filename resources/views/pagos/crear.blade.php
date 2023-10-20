@extends('layouts.app')

@section('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    precio = 0;
    promo = -1;
    curso = -1;
    promos = {!!$promos!!};
    cursos = {!!$cursos_activos!!};
    pagos = {!! json_encode(Auth::user()->pagos->where('fin', '>=', Carbon\Carbon::today())) !!};
    $(function(){
        var stripe = Stripe({!! $settings->stripe_pk !!});
        var elements = stripe.elements();

        var style = {
            base: {
                color: "#32325d",
            }
        };

        var card = elements.create("card", { style: style });
        card.mount("#card-element");

        card.addEventListener('change', function(event) {
        var displayError = document.getElementById('card-errors');
            if (event.error) {
                displayError.textContent = event.error.message;
            } else {
                displayError.textContent = '';
            }
        });

        var form = document.getElementById('payment-form');
        form.addEventListener('submit', function(ev) {
            ev.preventDefault();
            for (const pago in pagos) {
                const p = pagos[pago];
                if (promo <= p.promo_id && curso_id == p.curso_id){
                    alertify.success('Ya tienes contratado este curso');
                    return;
                }                
            }
            stripe.confirmCardPayment(clientSecret, {
                payment_method: {
                    card: card,
                    billing_details: {
                        name: '{{Auth::user()->name}}'
                    }
                }
            }).then(function(result) {
                    if (result.error) {
                    // Show error to your customer (e.g., insufficient funds)
                    alertify.error(result.error.message);
                } else {
                    // The payment has been processed!
                    if (result.paymentIntent.status === 'succeeded') {
                        $('#venta').submit();
                    }
                }
            });
        });

        $('#oxxo_form').submit(function(e){
            e.preventDefault();
            for (const pago in pagos) {
                const p = pagos[pago];
                if (promo <= p.promo_id && curso_id == p.curso_id){
                    alertify.success('Ya tienes contratado este curso');
                    return;
                }                
            }
            $(this).unbind('submit');
            $(this).submit();
        });
        
        $('#payment-form').hide();
        $('#stripe').hide();
        $('.promo').hide();

        $('.curso').change(function () {
            $('.promo') .show();
            curso_id = $(this).val();
            $('#curso_id').val(curso_id);

            var nombre = "";
            //https://www.samanthaming.com/tidbits/76-converting-object-to-array/
            var arregloTemporal = Object.values(cursos);
            for (let i = 0; i < arregloTemporal.length; i++) {
                const c = arregloTemporal[i];
                if(c.id == curso_id){
                    nombre = c.nombre;
                    break;
                }
            }
            $('#curso_nombre').html(nombre);
        });

        $('.promo').change(function(){
            $('#stripe').show();
            promo = $(this).val();
            $('#promo_nombre').html(promos[promo - 1].nombre);
            var date = new Date();
            var newDate = new Date(date.setMonth(date.getMonth() + promos[promo - 1].duracion));
            var dd = String(date.getDate()).padStart(2, '0');
            var mm = String(date.getMonth() + 1).padStart(2, '0'); //January is 0!
            var yyyy = date.getFullYear();
            $('#promo_meses').html('Hasta: ' + dd + '/' + mm + '/'  + yyyy);
            $('#total').html('<strong>$' + promos[promo - 1].costo + '</strong>');
            precio = promos[promo - 1].costo;
            $('#promo_id').val(promo);

            const params = {
				promo_id: promo,
			};

			axios.post('/stripe/intent', params).then((response) => {
				clientSecret = response.data.client_secret;
			});
        });

        $('#tarjeta').click(function(){
            $(this).hide();
            $('#oxxo').hide();
            $('#payment-form').show();
        });

        $('#no_tarjeta').click(function(){
            $('#tarjeta').show();
            $('#oxxo').show();
            $('#payment-form').hide();
        });
        
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h4>{{Auth::user()->name}}</h4>
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb">
                <li class="breadcrumb-item">Subscripción</li>
                <li class="breadcrumb-item active">Pago</li>
            </ol>
        </nav>
        @php
            $pago = App\Models\Pago::where('user_id', Auth::id())->where('fin', '>=', Carbon\Carbon::today())->first();
        @endphp
        @if ( $pago == null )
            <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
        @else
            <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
        @endif
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-lg-4">
            <form action="/pagos" method="post" class="mb-3" id="venta">
                @csrf
                <select class="custom-select mb-3 curso" name="curso_id">
                    <option selected disabled>Selecciona un curso</option>
                    @if (Auth::user()->pagos->where('curso_id', 16)->count() > 0)
                        <option value="16">{{ App\Models\Curso::find(16)->nombre }}</option>
                    @elseif (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 1)
                        @foreach ($cursos_activos as $c)
                            <option value="{{$c->id}}">{{$c->nombre}}</option>
                        @endforeach
                    @elseif (Auth::user()->por_admin == 1)
                        @if (Auth::user()->pagos->first()->curso_id == 12)
                            <option value="12">{{ App\Models\Curso::find(12)->nombre }}</option>
                        @else
                            <option value="13">{{ App\Models\Curso::find(13)->nombre }}</option>
                        @endif
                    @else
                        @foreach ($cursos_activos as $c)
                            <option value="{{$c->id}}">{{$c->nombre}}</option>
                        @endforeach
                    @endif
                </select>
                <select class="custom-select mb-3 promo" name="promo_id">
                    <option selected disabled>Selecciona una subscripción</option>
                    @if (Auth::user()->pagos->where('curso_id', 16)->count() > 0)
                        <option value="6">Premium ${{App\Models\Promo::find(6)->costo}}</option>
                    @elseif (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 1)
                        @foreach ($promos as $p)
                            @if ($p->id > 3)
                                @break
                            @endif
                            <option value="{{$p->id}}">{{$p->nombre}} ${{$p->costo}}</option>
                        @endforeach
                    @elseif (Auth::user()->por_admin == 1)
                        @if (Auth::user()->pagos->first()->curso_id == 12)
                        <option value="4">Premium $195</option>
                        @else
                        <option value="5">Premium $295</option>
                        @endif
                    @else
                        @foreach ($promos as $p)
                            @if ($p->id > 3)
                                @break
                            @endif
                            <option value="{{$p->id}}">{{$p->nombre}} ${{$p->costo}}</option>
                        @endforeach
                    @endif
                </select>
            </form>
            <div class="card mb-3">
                <div class="card-header">
                    Detalle de la compra
                </div>
                <div class="card-body">
                    <h4 class="card-title" id="curso_nombre"></h4>
                    <div class="card-text">
                        <p id="promo_nombre"></p>
                        <p id="promo_meses"></p>
                    </div>
                    
                    <div class="d-flex w-100 justify-content-between mt-3">
                        <p class="mb-1 font-weight-bolder">Total a pagar</p>
                        <p class="lead" id="total"></p>
                    </div>
                </div>
            </div>

            <div class="card my-3" id="stripe">
                <div class="card-header bg-secondary">
                    Paga aquí
                </div>
                <div class="card-body">
                    <div id="pagar" class="text-center">
                        <form id="payment-form">
                            <div id="card-element">
                                <!-- Elements will create input elements here -->
                            </div>

                            <!-- We'll put the error messages in this element -->
                            <div id="card-errors" role="alert"></div>

                            <button id="submit" class="btn btn-primary mt-5 mb-2">Pagar ahora</button>
                            <input type="button" id="no_tarjeta" class="btn btn-secondary mt-5 mb-2" value="Atrás">
                        </form>

                        <form action="/pagos" method="post" id="oxxo_form">
                            @csrf
                            <input type="hidden" name="oxxo" value="1">
                            <input type="hidden" name="promo_id" id="promo_id">
                            <input type="hidden" name="curso_id" id="curso_id">
                            <input type="button" id="tarjeta" class="btn btn-primary mt-2" value="Pagar con tarjeta">
                            <button type="submit" id="oxxo" class="btn btn-dark mt-2">Pagar en OXXO</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-lg-8">

            @if (Auth::user()->rol_id == 3 || Auth::user()->rol_id == 1)
                <img src="/img/promoprepa.jpg" class="img-fluid mx-auto">
            @elseif (Auth::user()->por_admin == 1)
                @if (Auth::user()->pagos->first()->curso_id == 12)
                <img src="/img/promoprepa.jpg" class="img-fluid mx-auto">
                @else
                <img src="/img/promofacu.jpg" class="img-fluid mx-auto">
                @endif
            @endif
            <div class="table-responsive">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>Características</th>
                            @foreach ($promos as $p)
                            @if($p->id > 3)
                                @break
                            @endif
                            <th>{{$p->nombre}}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td scope="row">Acceso a lecciones</td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>                            
                        </tr>
                        <tr>
                            <td scope="row">Videos explicativos</td>
                            <td>-</td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td scope="row">Repasos y exámenes</td>
                            <td>-</td>
                            <td>-</td>
                            <td><i class="fa fa-check" aria-hidden="true"></i></td>
                        </tr>
                        <tr>
                            <td scope="row">Meses de vigencia</td>
                            <td>4 Meses</td>
                            <td>5 Meses</td>
                            <td>6 Meses</i></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection