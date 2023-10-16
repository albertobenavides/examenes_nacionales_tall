<?php

namespace App\Http\Controllers;

use App\Models\Curso;
use App\Models\Pago;
use App\Promo;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class UsuarioController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('revisar.rol:1')->except(['store', 'show', 'edit', 'update', 'paginate_tabla']);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (Auth::user()->rol_id == 3){
            return view('usuarios.consulta');
        } else {
            $eliminados = false;
            if ($request->eliminados){
                $eliminados = true;
            }
            return view('usuarios.index', [
                'eliminados' => $eliminados
            ]);
        }
    }

    public function paginate(Request $request)
    {
        $usuarios = User::where('rol_id', 2)->orderByDesc('id')->select(['id', 'name'])->paginate(500);

        return $usuarios;
    }
    
    public function paginate_tabla(Request $request)
    {
        if(Auth::user()->rol_id != 2){
            $usuarios = User::select(['id', 'rol_id', 'name', 'email'])->orderByDesc('id')->paginate(500);

            return $usuarios;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            $usuarios =  preg_split("/\r\n|\n|\r/", $request->usuarios);
            $correctos = array();
            $errores = array();
            foreach ($usuarios as $u) {
                $t = explode(',', $u);
                if(count($t) > 1){
                    $nuevo = new User;
                    if (strlen($t[0]) > 0) {
                        $nuevo->name = $t[0];
                    } else {
                        array_push($errores, 'Nombre no especificado');
                        continue;
                    }

                    if(filter_var($t[1], FILTER_VALIDATE_EMAIL)){
                        if (User::where('email', $t[1])->count() <= 0) {
                            $nuevo->email = $t[1];
                        } else{
                            array_push($errores, 'Correo ' . $t[1] . ' en uso.');
                            continue;
                        }
                    } else {
                        array_push($errores, $t[1] . ' no es un correo válido.');
                        continue;
                    }

                    if(count($t) > 2 && strlen($t[2]) >= 8){
                        $clave = $t[2];
                        $nuevo->password = Hash::make($clave);
                    } else {
                        array_push($correctos, 'Contraseña generada para ' . $t[0]);
                        $clave = Str::random(8);
                        $nuevo->password = Hash::make('examenes');
                    }
                    $nuevo->rol_id = 2;
                    $nuevo->por_admin = 1;
                    $nuevo->save();
                } else {
                    array_push($errores, 'No se pudo guardar ' . $t);
                }
            }
            return back()->with(
                ['correctos' => $correctos,
                'errores' => $errores]
            );
        } elseif (Auth::user()->rol_id == 3){
            $request->validate([
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            ]);

            $user = new User;
            $user->name = $request->nombre;
            $user->email = $request->email;
            $user->password = Hash::make($request->clave);
            $user->rol_id = 2;
            $user->save();

            $promo = Promo::find($request->promo_id);
            $curso = Curso::find($request->curso_id);
            $pago = new Pago();
            $pago->user_id = $user->id;
            $pago->curso_id = $curso->id;
            $pago->promo_id = $promo->id;
            $pago->fin = $request->fin;
            $pago->save();

            return redirect('/inicio')->with('mensaje', 'Usuario creado');
        } else {
            return redirect('/inicio')->with('mensaje', 'Acceso denegado');
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $usuario = Auth::user();
        if ($usuario == null){
            return redirect('/')->with('mensaje', 'Usuario no encontrado');
        }
        return view('usuarios.editar', [
            'usuario' => $usuario
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(Auth::user()->rol_id != 2){
            $usuario = User::find($id);
            if ($usuario == null){
                return redirect('/')->with('mensaje', 'Usuario no encontrado');
            }
            $dir = Auth::user()->rol_id == 1 ? 'usuarios.editar' : 'usuarios.editar_consulta';
            return view($dir, [
                'usuario' => $usuario
            ]);
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if (Auth::user()->rol_id == 1){

            $request->validate([
                'nombreUsuario' => 'required',
                'correoUsuario' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            ]);
    
            if(Auth::user()->rol_id == 1){
                $usuario = User::find($id);
                $usuario->rol_id = $request->rolUsuario;
            } else {
                $usuario = Auth::user();
            }
            if($request->contra != ''){
                $usuario->password = Hash::make($request->contra);
            }
            $usuario->name = $request->nombreUsuario;
            $usuario->email = $request->correoUsuario;
            $usuario->save();
    
            if(Auth::user()->rol_id == 1){
                return redirect('/usuarios')->with('exito', 'Usuario editado');
            } else {
                return redirect('/inicio')->with('exito', 'Información actualizada');
            }
        } elseif (Auth::user()->rol_id == 3){
            $request->validate([
                'correoUsuario' => ['required', 'string', 'email', 'max:255', 'unique:users,email,'.$id],
            ]);

            $user = User::find($id);
            $user->name = $request->nombreUsuario;
            $user->email = $request->correoUsuario;
            if($request->clave != ''){
                $user->password = Hash::make($request->clave);
            }
            $user->save();

            $pago = Pago::where('user_id', $user->id)->where('fin', '>=', Carbon::today())->orderByDesc('promo_id')->first();
            $promo = Promo::find($request->promo_id);
            $curso = Curso::find($request->curso_id);

            if (!$pago){
                $pago = new Pago();
                $pago->user_id = $user->id;
            }
            if($curso && $promo){
                $pago->curso_id = $curso->id;
                $pago->promo_id = $promo->id;
                $pago->fin = $request->fin;
                $pago->save();
            }

            return redirect('/inicio')->with('mensaje', 'Usuario actualizado');
        } else {
            return redirect('/inicio')->with('mensaje', 'Acceso restringido');
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
