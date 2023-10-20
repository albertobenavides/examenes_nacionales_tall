@extends('layouts.app')

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function(){
        roles = {!! Spatie\Permission\Models\Role::select('id', 'name')->get() !!};
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

        pagos_tabla = $('#pagos_tabla').DataTable({
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

        pagos_pagina = 1;
        $('#pagos_paginar').click(function(){
            pagos_pagina += 1;
            const params = {
                page : pagos_pagina,
            };

            axios.post('/pagos/pagina/tabla', params).then((response) => {
                var ps = response.data.data;
                for (let i = 0; i < ps.length; i++) {
                    const p = ps[i];
                    var csrf = $('meta[name=csrf-token]').attr("content");
                    pagos_tabla.row.add([
                        p.id,
                        p.usuario,
                        p.curso,
                        p.promo_nombre,
                        p.promo_costo,
                        p.fin,
                        `<form action="/pagos/${p.id}" method="post" class="borrarPago">
                            <input type="hidden" name="_token" value="${csrf}">
                            <input type="hidden" name="_method" value="DELETE">
                            <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                        </form>`
                    ]);
                }
                pagos_tabla.draw();
                $('.borrarPago').submit(function(e) {
                    e.preventDefault();
                    if(confirm('¿Eliminar pago?')){
                        $(this).unbind('submit').submit();
                    }
                });
            }); 
        });
        
        $('.borrarPago').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar pago?')){
                $(this).unbind('submit').submit();
            }
        });

        cargar_usuarios_pagina = 1;
        $('#cargar_usuarios').click(function(e){
            e.preventDefault();
            cargar_usuarios_pagina += 1;
            const params = {
                page : cargar_usuarios_pagina,
            };

            axios.post('/usuarios/pagina', params).then((response) => {
                var us = response.data.data;
                for (let i = 0; i < us.length; i++) {
                    const u = us[i];
                    $('#alumnos').append(`<option value="${u.id}">${u.name}</option>`);
                }
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
    <div class="row">
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-users"></i> Usuarios
                </div>
                <div class="card-body">
                    <form action="/usuarios" method="post" id="crearUsuario">
                        @csrf
                        <label for="">Crear usuario(s)</label>
                        <textarea name="usuarios" rows="5" class="form-control" placeholder="Nombre apellido,correo@electronico,clave (opcional)" form="crearUsuario"></textarea>
                        <small class="form-text text-muted">Añade un usuario por línea. Sigue la estructura:</small>
                        <small class="form-text text-muted">Nombre Apellido,ejemplo@correo.com,clave(opcional, mín. 8 caracteres)</small>
                        <small class="form-text text-muted">Si no especificas una clave, se asignará <code>examenes</code> por defecto.</small>
                        <div class="text-center my-3">
                            <button class="btn btn-sm btn-success" type="submit">Añadir usuario(s)</button>
                        </div>
                    </form>
                    <div class="mt-3">
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
                                @foreach(App\Models\User::with('roles')->select(['id', 'name', 'email', 'rol_id'])->get()->sortByDesc('id')->take(500) as $u)
                                <tr>
                                    <td>{{$u->id}}</td>
                                    <td>{{$u->rol_id}}</td>
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
        <div class="col-xl-6">
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-receipt"></i> Pagos
                </div>
                <div class="card-body">
                    <p class="mb-2">
                        @if ($eliminados)
                        <a href="/usuarios">Ver pagos de cursos vigentes</a>
                        @else
                        <a href="/usuarios?eliminados=1">Ver pagos de cursos eliminados</a>
                        @endif
                    </p>
                    <form action="/pagos" method="post" id="crearPago" class="mb-3">
                        @csrf
                        <div class="row mb-2">
                            <div class="col">
                                <label>Alumnos</label>
                                <select id="alumnos" class="custom-select custom-select-sm" form="crearPago" name="alumnos[]" multiple="multiple" required>
                                    @foreach (App\Models\User::where('rol_id', 2)->get()->sortByDesc('id')->take(500) as $a)
                                    <option value="{{$a->id}}">{{$a->name}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="mt-3">
                            <button id="cargar_usuarios" class="btn btn-sm btn-primary">Cargar 500 alumno en el selector</button>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label>Curso</label>
                                <select name="curso" class="custom-select custom-select-sm" form="crearPago" required>
                                    <option selected disabled>Selecciona</option>
                                    @foreach (App\Models\Curso::select('id', 'nombre')->get() as $c)
                                        <option value="{{$c->id}}">{{$c->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col">
                                <label>Promoción</label>
                                <select name="promo" class="custom-select custom-select-sm" form="crearPago" required>
                                    <option selected disabled>Selecciona</option>                                            
                                    @foreach (App\Models\Promo::select('id', 'nombre')->get() as $p)
                                        <option value="{{$p->id}}">{{$p->nombre}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        <div class="row mb-2">
                            <div class="col">
                                <label>Desde</label>
                                <input type="date" class="form-control form-control-sm" name="inicio" value="{{Carbon\Carbon::today()->format('Y-m-d')}}">
                            </div>
                            <div class="col">
                                <label>Hasta</label>
                                <input type="date" class="form-control form-control-sm" name="fin" value="{{Carbon\Carbon::today()->addMonth()->format('Y-m-d')}}">
                            </div>
                        </div>
                        <div class="text-center my-3">
                            <button class="btn btn-sm btn-success" type="submit">Añadir pago</button>
                        </div>
                    </form>
                    <div class="mt-3">
                        <button id="pagos_paginar" class="btn btn-sm btn-primary">Cargar 500 pagos en la tabla</button>
                    </div>
                    <div class="table-responsive">
                        <table id="pagos_tabla" class="table table-sm table-stripped text-nowrap">
                            <thead>
                                <th>#</th>
                                <th>Nombre</th>
                                <th>Curso</th>
                                <th>Promoción</th>
                                <th>Costo</th>
                                <th>Finaliza</th>
                                <th>Acciones</th>
                            </thead>
                            <tbody>
                                @foreach(App\Models\Pago::all()->sortByDesc('id')->take(500) as $p)
                                    @if ($eliminados && $p->curso != null)
                                        @continue
                                    @elseif (!$eliminados && $p->curso == null)
                                        @continue
                                    @endif
                                    <tr>
                                        <td>{{$p->id}}</td>
                                        <td>{{$p->usuario->name}}</td>
                                        @if ($p->curso == null)
                                            <td>Curso eliminado</td>    
                                        @else
                                            <td><a href="/cursos/{{$p->curso->id}}/editar">{{$p->curso->nombre}}</a></td>
                                        @endif
                                        <td>{{$p->promo->nombre}}</td>
                                        <td>${{$p->promo->costo}}</td>
                                        <td>
                                            @if ($p->fin == null)
                                            Pendiente pago OXXO
                                            @else
                                            {{Carbon\Carbon::parse($p->fin)->isoFormat('D MMMM YYYY')}}
                                            @endif
                                        </td>
                                        <td>
                                            <form action="/pagos/{{$p->id}}" method="post" class="borrarPago">
                                                @csrf
                                                @method('DELETE')
                                                <button class="btn btn-danger btn-sm"><i class="fas fa-trash"></i></button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

@endsection
