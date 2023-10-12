@extends('layouts.app')

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1><a href="/ajustes">Administración</a> / <a href="/inicio">Usuarios</a></h1>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            Editar usuario
        </div>
        <div class="card-body">
            <form action="/usuarios/{{$usuario->id}}" method="post" id="editarUsuario">
                @csrf
                @method('PUT')
                <div class="row">
                    <div class="col-md-4">
                        <label for="nombreUsuarioEditar">Nombre</label>
                        <input type="text" name="nombreUsuario" id="nombreUsuarioEditar" class="form-control form-control-sm" placeholder="Nombre del Usuario" required value="{{$usuario->name}}">
                    </div>
                    <div class="col-md-4">
                        <label for="correoUsuarioEditar">Correo</label>
                        <input type="email" name="correoUsuario" id="correoUsuarioEditar" class="form-control form-control-sm" placeholder="Correo del Usuario" required value="{{$usuario->email}}">
                    </div>
                    <div class="col-md-4">
                        <label for="contraUsuarioEditar">Contraseña nueva (opcional)</label>
                        <input type="password" name="clave" id="contraUsuarioEditar" class="form-control form-control-sm" placeholder="Escribe aquí una nueva contraseña">
                    </div>
                </div>

                @php
                    $pago = App\Pago::where('user_id', $usuario->id)->where('fin', '>=', Carbon\Carbon::today())->orderByDesc('promo_id')->first();
                @endphp

                <div class="row">
                    <div class="col-md-4">
                        <label>Curso</label>
                        <select name="curso_id" class="custom-select custom-select-sm" required>
                            @if (!$pago)
                            <option value="-1" selected disabled>Elige un curso</option>
                            @endif
                            @foreach (App\Curso::where('activo', 1)->get() as $c)
                                <option value="{{ $c->id }}" {{ $pago && $c->id == $pago->curso_id ? 'selected' : '' }}>{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Plan</label>
                        <select name="promo_id" class="custom-select custom-select-sm" required>
                            @if (!$pago)
                            <option value="-1" selected disabled>Elige un plan</option>
                            @endif
                            @foreach (App\Promo::select('id', 'nombre')->get() as $p)
                                <option value="{{ $p->id }}" {{ $pago && $p->id == $pago->promo_id ? 'selected' : '' }}>{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Hasta</label>
                        <input type="date" name="fin" class="form-control" required value="{{ $pago->fin ?? '' }}">
                    </div>
                </div>
            </form>
            <button type="submit" id="actualizarUsuario" type="button" class="btn btn-primary mt-3 btn-sm" form="editarUsuario">Confirmar</button>
            <button class="btn btn-sm btn-default mt-3" onclick="window.location='/usuarios'">Regresar</button>
        </div>
    </div>
</div>
@endsection
