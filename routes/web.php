<?php

use App\Curso;
use App\Http\Controllers\HomeController;
use App\Intento;
use App\User;
use App\Pago;
use Illuminate\Http\Request;
use Iman\Streamer\VideoStreamer;

use Carbon\Carbon;
use App\Notifications\OxxoPagado;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::group(['middleware' => 'revisar.acceso'], function() {
    Auth::routes();

    Route::get('/inicio', HomeController::class, 'index')->name('home');

    Route::resources([
        '/pruebas' => 'ExamenController',
        '/instituciones' => 'InstitucionController',
        '/cursos' => 'CursoController',
        '/modulos' => 'ModuloController',
        '/temas' => 'TemaController',
        '/promos' => 'PromoController',
        '/pagos' => 'PagoController',
        '/preguntas' => 'PreguntaController',
        '/respuestas' => 'RespuestaController',
        '/examenes' => 'PruebaController',
        '/intentos' => 'IntentoController',
        '/usuarios' => 'UsuarioController',
    ]);

    Route::post('/usuarios/pagina', 'UsuarioController@paginate');
    Route::post('/usuarios/pagina/tabla', 'UsuarioController@paginate_tabla');

    Route::post('/pagos/pagina/tabla', 'PagoController@paginate_tabla');

    Route::get('/cursos/{id}/{seccion}', 'CursoController@seccion');

    Route::get('/pruebas/{id}/intentos', 'PruebaController@intentos');
    Route::get('/pruebas/{id}/intentos/{intento_id}', 'IntentoController@revision');
    Route::post('/pruebas/intento/{intento_id}/{respuesta_id}', 'PruebaController@actualizar_respuestas');

    Route::get('/preguntas/editar/{id}', function($id){
        $pregunta = App\Pregunta::find($id);
        return view('preguntas.editar', [
            'pregunta' => $pregunta
        ]);
    });

    Route::post('/preguntas/revisar/{pregunta_id}', 'PreguntaController@revisar');

    Route::post('/examenes/revisar', 'PruebaController@revisar');

    Route::get('/calificaciones', 'CalificacionController@index');
    Route::get('/calificaciones/simulaciones', 'CalificacionController@index_simulaciones');
    Route::post('/calificaciones/usuarios', 'CalificacionController@usuarios');
    Route::post('/calificaciones/simulaciones', 'CalificacionController@simulaciones');
    Route::post('/calificaciones', 'CalificacionController@calificaciones');

    Route::get('/descargar/{dir}/{archivo}', function($dir, $archivo){
        $fileContents = Storage::get($dir . '/' . $archivo);
        $response = Response::make($fileContents, 200);
        $response->header('Content-Type', 'application/pdf');
        return $response;
    })->middleware(['auth', 'revisar.pago']);

    Route::get('/mostrar/{dir}/{archivo}', function($dir, $archivo){
        // https://github.com/imanghafoori1/laravel-video
        VideoStreamer::streamFile(__DIR__ . "/../storage/app/video/$archivo");
    })->middleware(['auth', 'revisar.pago']);

    Route::get('/down', function(){
        \Artisan::call('down --message="Próximamente"');
        return redirect('/');
    });
});

Route::post('/stripe/intent', function(Request $request){
    \Stripe\Stripe::setApiKey(setting('stripe_sk'));

    $promo = App\Promo::find($request->promo_id);

    $intent = \Stripe\PaymentIntent::create([
        'amount' => $promo->costo * 100, // Se multiplica por 100 para tener centavos
        'currency' => 'mxn',
        // Verify your integration in this guide by including this parameter
        'metadata' => ['integration_check' => 'accept_a_payment'],
    ]);

    return $intent;
});

Route::post('/oxxo', function(Request $request){
    $reference = $request->data['object']['payment_method']['reference'] ?? null;
    $status = $request->data['object']['status'] ?? null;

    if ($status != null && $status == "paid"){
        $pago = Pago::where('oxxo', $reference)->first();
        
        if ($pago != null){
            $pago->inicio = Carbon::today();
            $pago->fin = Carbon::today()->addMonths($pago->promo->duracion);
            $pago->save();
            
            $pago->usuario->notify(new OxxoPagado($pago->curso->id));
            return response("Pago efectuado con éxito", 200);
        } else{
            //Log::error("El pago de OXXO con referencia $reference no se ha encontrado en la plataforma.");
            return response("El pago de OXXO con referencia $reference no se ha encontrado en la plataforma.", 200);
        }    
    } else {
        //http_response_code(200); // Return 200 OK
        return response("Error en referencia $reference", 200);
    }
});