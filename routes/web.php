<?php

use App\Http\Controllers\CalificacionController;
use App\Models\Curso;
use App\Http\Controllers\CursoController;
use App\Http\Controllers\ExamenController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InstitucionController;
use App\Http\Controllers\IntentoController;
use App\Http\Controllers\ModuloController;
use App\Http\Controllers\ModuloTemaController;
use App\Http\Controllers\PagoController;
use App\Http\Controllers\PreguntaController;
use App\Http\Controllers\PromoController;
use App\Http\Controllers\PruebaController;
use App\Http\Controllers\RespuestaController;
use App\Http\Controllers\TemaController;
use App\Http\Controllers\UsuarioController;
use App\Models\Intento;
use App\Models\Meeting;
use App\Models\User;
use App\Models\Pago;
use Illuminate\Http\Request;
use Iman\Streamer\VideoStreamer;

use Carbon\Carbon;
use App\Notifications\OxxoPagado;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

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

Route::get('/', function (GeneralSettings $settings) {
    return view('welcome', ['settings' => $settings]);
});

Route::group(['middleware' => 'revisar.acceso'], function() {
    Auth::routes();

    Route::get('/inicio', [HomeController::class, 'index'])->name('home');

    Route::resources([
        '/pruebas' => ExamenController::class,
        '/instituciones' => InstitucionController::class,
        '/cursos' => CursoController::class,
        '/modulos' => ModuloController::class,
        '/temas' => TemaController::class,
        '/promos' => PromoController::class,
        '/pagos' => PagoController::class,
        '/preguntas' => PreguntaController::class,
        '/respuestas' => RespuestaController::class,
        '/examenes' => PruebaController::class,
        '/intentos' => IntentoController::class,
        '/usuarios' => UsuarioController::class,
    ]);

    Route::resource('modulos.temas', ModuloTemaController::class);

    Route::post('/usuarios/pagina', [UsuarioController::class, 'paginate']);
    Route::post('/usuarios/pagina/tabla', [UsuarioController::class, 'paginate_tabla']);

    Route::post('/pagos/pagina/tabla', [PagoController::class, 'paginate_tabla']);

    Route::get('/cursos/{id}/{seccion}', [CursoController::class, 'seccion']);

    Route::get('/pruebas/{id}/intentos', [PruebaController::class, 'intentos']);
    Route::get('/pruebas/{id}/intentos/{intento_id}', [IntentoController::class, 'revision']);
    Route::post('/pruebas/intento/{intento_id}/{respuesta_id}', [PruebaController::class, 'actualizar_respuestas']);

    Route::get('/preguntas/editar/{id}', function($id){
        $pregunta = App\Models\Pregunta::find($id);
        return view('preguntas.editar', [
            'pregunta' => $pregunta
        ]);
    });

    Route::post('/preguntas/revisar/{pregunta_id}', [PreguntaController::class, 'revisar']);

    Route::post('/examenes/revisar', [PruebaController::class, 'revisar']);

    Route::get('/calificaciones', [CalificacionController::class, 'index']);
    Route::get('/calificaciones/simulaciones', [CalificacionController::class, 'index_simulaciones']);
    Route::post('/calificaciones/usuarios', [CalificacionController::class, 'usuarios']);
    Route::post('/calificaciones/simulaciones', [CalificacionController::class, 'simulaciones']);
    Route::post('/calificaciones', [CalificacionController::class, 'calificaciones']);

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

    $promo = App\Models\Promo::find($request->promo_id);

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

//DocumentViewer Library
Route::any('ViewerJS/{all?}', function(){
    return View::make('ViewerJS.index');
});

Route::get('/meeting/{id}/{status}', function($id, $status){
    $m = Meeting::find($id);
    $m->status = $status;
    $m->saveQuietly();
});