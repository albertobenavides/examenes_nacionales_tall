@extends('layouts.app')

@section('styles')
    <link rel="stylesheet" href="https://cdn.datatables.net/1.10.21/css/dataTables.bootstrap4.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/buttons/1.6.2/css/buttons.bootstrap4.min.css">
@endsection

@section('scripts')
<script src="https://cdn.datatables.net/1.10.21/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.21/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.bootstrap4.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.html5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.print.min.js"></script>
<script src="https://cdn.datatables.net/buttons/1.6.2/js/buttons.colVis.min.js"></script>
<script>
    $(function(){
        roles = {!! App\Rol::select('id', 'nombre')->get() !!};
        usuarios_tabla = $('#usuarios_tabla').DataTable({
            "order": [[ 0, "desc" ]],
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

        usuarios_pagina = 1;
        $('#usuarios_paginar').click(function(){
            usuarios_pagina += 1;
            const params = {
                page : usuarios_pagina,
            };

            axios.post('/usuarios/pagina/tabla', params).then((response) => {
                var us = response.data.data;
                for (let i = 0; i < us.length; i++) {
                    const u = us[i];
                    usuarios_tabla.row.add([
                        u.id,
                        roles[u.rol_id - 1]['nombre'],
                        u.name,
                        u.email,
                        `<a href="/usuarios/${u.id}/editar" class="btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>`
                    ]);
                }
                usuarios_tabla.draw();
            }); 
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1><a href="/ajustes">Administración</a> / Usuarios</h1>
    </div>
</div>
<div class="container">
    <div class="card">
        <div class="card-header">
            <i class="fas fa-users"></i> Usuarios
        </div>
        <div class="card-body">
            <form action="/usuarios" method="post" id="crearUsuario">
                @csrf
                <div class="row">
                    <div class="col-md-4">
                        <label>Nombre</label>
                        <input type="text" name="nombre" id="nombre" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label>Correo</label>
                        <input type="email" name="email" id="email" class="form-control form-control-sm" required>
                    </div>
                    <div class="col-md-4">
                        <label>Contraseña</label>
                        <input type="password" name="clave" id="clave" class="form-control form-control-sm" required>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-4">
                        <label>Curso</label>
                        <select name="curso_id" class="custom-select custom-select-sm" required>
                            <option value="-1" selected disbled>Elige un curso</option>
                            @foreach (App\Models\Curso::where('activo', 1)->get() as $c)
                                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Plan</label>
                        <select name="promo_id" class="custom-select custom-select-sm" required>
                            <option value="-1" selected disbled>Elige un curso</option>
                            @foreach (App\Promo::select('id', 'nombre') as $p)
                                <option value="{{ $p->id }}">{{ $p->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label>Hasta</label>
                        <input type="date" name="fin" class="form-control" required>
                    </div>
                </div>

                <div class="text-center my-3">
                    <button class="btn btn-sm btn-success" type="submit">Añadir usuario</button>
                </div>
            </form>
            <div class="my-3">
                <button id="usuarios_paginar" class="btn btn-sm btn-primary">Cargar 500 usuarios en la tabla</button>
            </div>
            <div class="table-responsive">
                <table id="usuarios_tabla" class="table table-sm table-stripped text-nowrap">
                    <thead>
                        <th>#</th>
                        <th>Rol</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th class="text-center">Acciones</th>
                    </thead>
                    <tbody>
                        @foreach(App\Models\User::select('id', 'name', 'email')->sortByDesc('id')->take(500) as $u)
                        <tr>
                            <td>{{$u->id}}</td>
                            <td>{{$u->rol->nombre}}</td>
                            <td>{{$u->name}}</td>
                            <td>{{$u->email}}</td>
                            <td class="text-center">
                                <a href="/usuarios/{{$u->id}}/editar" class="btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
