<?php

namespace App\Http\Controllers;

use App\Models\Pago;
use App\Models\Curso;
use App\Models\Promo;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

use App\Notifications\OxxoPay;
use App\Settings\GeneralSettings;
use Illuminate\Support\Facades\Cache;

// require_once(env('APP_BASE') . "/vendor/conekta/conekta-php/lib/Conekta.php");
// \Conekta\Conekta::setApiKey(setting('conekta_sk'));
// \Conekta\Conekta::setApiVersion("2.0.0");

class PagoController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1')->except(['create', 'store']);
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    public function paginate_tabla(Request $request)
    {
        $pagos = Pago::select(['id', 'fin', 'user_id', 'promo_id', 'curso_id'])->orderByDesc('id')->paginate(500);

        foreach ($pagos as $p) {
            $p->usuario = User::find($p->user_id)->name;
            $p->curso = Curso::find($p->curso_id)->nombre;
            $p->promo_nombre = Promo::find($p->promo_id)->nombre;
            $p->promo_costo = Promo::find($p->promo_id)->costo;
        }

        return $pagos;
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create(Request $request, GeneralSettings $settings)
    {
        $promos = Cache::remember('promos', 3600, function () {
            return Promo::all();
        });
        return view('pagos.crear', [
            'promos' => $promos,
            'cursos_activos' => Curso::where('activo', 1)->get(),
            'settings' => $settings
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(Auth::user()->rol_id == 1){
            $request->validate([
                'alumnos' => 'required',
                'curso' => 'required',
                'promo' => 'required',
                'inicio' => 'required',
                'fin' => 'required'
            ]);
            foreach ($request->alumnos as $a) {
                $pago = new Pago;
                $pago->user_id = $a;
                $pago->curso_id = $request->curso;
                $pago->promo_id = $request->promo;
                $pago->inicio = $request->inicio;
                $pago->fin = $request->fin;
                $pago->save();
            }

            return back()->with('exito', 'Pagos registrados');
        } else{
            if (isset($request->oxxo)) {
                $promo = Promo::find($request->promo_id);
                $curso = Curso::find($request->curso_id);
                if($promo == null || $curso == null){
                    return back()->with('mensaje', 'Promoción no válida');
                }
                
                try{
                    $thirty_days_from_now = Carbon::today()->addMonth()->timestamp;
                    $order = \Conekta\Order::create(
                      [
                        "line_items" => [
                          [
                            "name" => "$curso->nombre | $promo->nombre",
                            "unit_price" => $promo->costo * 100,
                            "quantity" => 1
                          ]
                        ],
                        "currency" => "MXN",
                        "customer_info" => [
                          "name" => Auth::user()->name,
                          "email" => Auth::user()->email,
                          "phone" => "+5218181818181"
                        ],
                        "charges" => [
                          [
                            "payment_method" => [
                              "type" => "oxxo_cash",
                              "expires_at" => $thirty_days_from_now
                            ]
                          ]
                        ]
                      ]
                    );

                    $info = new \stdClass();
                    $info->referencia = $order->charges[0]->payment_method->reference;
                    $info->cantidad = $order->line_items[0]->unit_price/100;
                    $info->curso = $curso->nombre;
                    $info->promo = $promo->nombre;
                    $info->usuario = Auth::user()->name;

                    Auth::user()->notify(new OxxoPay($info));

                    $pago = new Pago;
                    $pago->user_id = Auth::user()->id;
                    $pago->curso_id = $curso->id;
                    $pago->promo_id = $promo->id;
                    $pago->oxxo = $order->charges[0]->payment_method->reference;
                    $pago->save();

                    return view("pagos.oxxo", [
                        'pago' => $pago
                    ])->with('mensaje', 'Te enviamos un correo con indicaciones');
                } catch (\Conekta\ParameterValidationError $error){
                    echo $error->getMessage();
                } catch (\Conekta\Handler $error){
                    echo $error->getMessage();
                }
            } else {
                $curso = Curso::find($request->curso_id);
                $promo = Promo::find($request->promo_id);
                $pago = new Pago;
                $pago->user_id = Auth::id();
                $pago->curso_id = $request->curso_id;
                $pago->promo_id = $request->promo_id;
                $pago->fin = Carbon::today()->addMonths($promo->duracion);
                $pago->save();
                return redirect("/cursos/$pago->curso_id")->with('exito', "Curso $curso->nombre adquirido");
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function show(Pago $pago)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function edit(Pago $pago)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Pago $pago)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Pago  $pago
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $pago = Pago::find($id);
        $pago->delete();

        return back()->with('exito', 'Pago eliminado');
    }
}
