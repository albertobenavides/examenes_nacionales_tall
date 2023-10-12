@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="//cdn.datatables.net/1.10.20/css/jquery.dataTables.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function(){
        $('.dt').DataTable({
            "language": {
                "sProcessing":     "Procesando...",
                "sLengthMenu":     "Mostrar _MENU_ registros",
                "sZeroRecords":    "No se encontraron resultados",
                "sEmptyTable":     "Ningún dato disponible en esta tabla",
                "sInfo":           "Mostrando registros del _START_ al _END_ de un total de _TOTAL_ registros",
                "sInfoEmpty":      "Mostrando registros del 0 al 0 de un total de 0 registros",
                "sInfoFiltered":   "(filtrado de un total de _MAX_ registros)",
                "sInfoPostFix":    "",
                "sSearch":         "Buscar:",
                "sUrl":            "",
                "sInfoThousands":  ",",
                "sLoadingRecords": "Cargando...",
                "oPaginate": {
                    "sFirst":    "Primero",
                    "sLast":     "Último",
                    "sNext":     "Siguiente",
                    "sPrevious": "Anterior"
                },
                "oAria": {
                    "sSortAscending":  ": Activar para ordenar la columna de manera ascendente",
                    "sSortDescending": ": Activar para ordenar la columna de manera descendente"
                },
                "buttons": {
                    "copy": "Copiar",
                    "colvis": "Visibilidad"
                }
            },
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1><a href="/ajustes">Administración</a> / <a href="/usuarios">Usuarios</a></h1>
    </div>
</div>
<div class="container">
    <div class="row">
        <div class="col-xl-4">
            <div class="card">
                <div class="card-header">
                    Información general
                </div>
                <div class="card-body">
                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <b>Pagos</b>
                                <p>Fin</p>
                            </div>
                        </div>
                        @foreach ($usuario->pagos as $p)
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <p>
                                    @if ($p->curso == null)
                                    Curso eliminado
                                    @else    
                                    <a href="/cursos/{{$p->curso->id}}">{{$p->curso->nombre}}</a> <br>
                                    @endif
                                    <small>{{$p->promo->nombre}}</small>
                                </p>
                                <p>
                                    @if ($p->fin == null)
                                    Pagar en OXXO
                                    @else
                                    {{Carbon\Carbon::parse($p->fin)->isoFormat('D MMMM YYYY')}}
                                    @endif
                                </p>
                            </div>
                        </div>
                        @endforeach
                    </div>

                    <div class="list-group list-group-flush">
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <b>Intentos</b>
                                <p>Calif.</p>
                            </div>
                        </div>
                        @foreach ($usuario->intentos as $i)
                        @if ($i->prueba == null)
                            @continue
                        @endif
                        <div class="list-group-item">
                            <div class="d-flex justify-content-between">
                                <p>
                                    <a href="/examenes/{{$i->prueba->id}}">{{$i->prueba->nombre}}</a> <br>
                                    <small>{{Carbon\Carbon::parse($i->created_at)->diffForHumans()}}</small>
                                </p>
                                <p>{{$i->calificacion}}/100</p>
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    Editar usuario
                </div>
                <div class="card-body">
                    <form action="/usuarios/{{$usuario->id}}" method="post" id="editarUsuario">
                        @csrf
                        @method('PUT')
                        @if (Auth::user()->rol_id == 1)
                        <label for="rolUsuario">Rol</label>
                        <select name="rolUsuario" id="rolUsuarioEditar" class="custom-select custom-select-sm" required>
                            @foreach(App\Rol::select('id', 'nombre')->get() as $r)
                            <option value="{{$r->id}}" {{$usuario->rol_id == $r->id ? 'selected' : ''}}>{{$r->nombre}}</option>
                            @endforeach
                        </select>
                        @endif
                        <label for="nombreUsuarioEditar">Nombre</label>
                        <input type="text" name="nombreUsuario" id="nombreUsuarioEditar" class="form-control form-control-sm" placeholder="Nombre del Usuario" required value="{{$usuario->name}}">
                        <label for="correoUsuarioEditar">Correo</label>
                        <input type="email" name="correoUsuario" id="correoUsuarioEditar" class="form-control form-control-sm" placeholder="Correo del Usuario" required value="{{$usuario->email}}">
                        <label for="contraUsuarioEditar">Contraseña nueva (opcional)</label>
                        <input type="password" name="clave" id="contraUsuarioEditar" class="form-control form-control-sm" placeholder="Escribe aquí una nueva contraseña">
                    </form>
                    <button type="submit" id="actualizarUsuario" type="button" class="btn btn-primary mt-3 btn-sm" form="editarUsuario">Confirmar</button>
                    <button class="btn btn-sm btn-default mt-3" onclick="window.location='/usuarios'">Regresar</button>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection
