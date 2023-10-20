@extends('layouts.app')

@section('styles')
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
        $('.borrarCurso').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar curso? También se borrarán los módulos, temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });
        $('.borrarExamen').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar examen? También se quitará de las instituciones asignadas.')){
                $(this).unbind('submit').submit();
            }
        });
        $('.borrarPago').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar pago?')){
                $(this).unbind('submit').submit();
            }
        });

		Livewire.on('confirmDelete', postId => {
			$('#borrarInstitucionModal').modal('show');
		})

        $('#borrarInstitucionModal').on('hidden.bs.modal', function (e) {
            Livewire.emit('resetInstitucionFilters')
        })
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1>Administración</h1>
    </div>
</div>
<div class="container-fluid">
    <div class="row">
        <div class="col-xl-4">
            @include('app_settings::_settings')
        </div>
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-toggle="tab" href="#cursos" role="tab"><i class="fas fa-book"></i> Cursos</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#examenes" role="tab"><i class="fas fa-file"></i> Pruebas</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-toggle="tab" href="#instituciones" role="tab"><i class="fas fa-school"></i> Instituciones</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="myTabContent">
                        <div class="tab-pane show active" id="cursos" role="tabpanel">
                            <form action="/cursos" method="post" enctype="multipart/form-data" id="nuevoCurso">
                                @csrf
                                <div class="row">
                                    <div class="col-md-4 text-center">
                                        <label for="imagenCurso">Imagen</label>
                                        <div class="fileinput fileinput-new" data-provides="fileinput">
                                            <div class="fileinput-preview" data-trigger="fileinput">
                                                <img src="https://fakeimg.pl/160x100/?text=320x200" class="card-img-top">
                                            </div>
                                            <div>
                                                <span class="btn btn-sm btn-primary btn-file">
                                                    <span class="fileinput-new">Elegir</span>
                                                    <span class="fileinput-exists">Cambiar</span>
                                                    <input type="file" id="imagenCurso" name="imagenCurso" form="nuevoCurso">
                                                </span>
                                                <a href="#" class="btn btn-sm btn-primary fileinput-exists" data-dismiss="fileinput">Quitar</a>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-md-8">
                                        <label for="nombreCurso">Nombre</label>
                                        <input type="text" id="nombreCurso" name="nombreCurso" class="form-control form-control-sm" placeholder="Nombre del curso" required form="nuevoCurso">
                                    
                                        <label for="descripcionCurso">Descripción</label>
                                        <textarea id="descripcionCurso" name="descripcionCurso" class="form-control form-control-sm" placeholder="Descripción" form="nuevoCurso"></textarea>
                                    
                                        <label for="examenCurso">Examen asociado</label>
                                        <select name="examenCurso" id="examenCurso" class="custom-select custom-select-sm" form="nuevoCurso" required>
                                            <option value="-1" selected disabled>Elige</option>
                                            @foreach(App\Models\Examen::select(['id', 'nombre'])->get() as $e)
                                                <option value="{{$e->id}}">{{$e->nombre}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                <div class="text-center">
                                    <button class="btn btn-sm btn-success my-3" type="submit">Añadir curso</button>
                                </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table table-striped table-sm dt text-nowrap">
                                    <thead class="thead-inverse">
                                        <tr>
                                            <th>#</th>
                                            <th>Nombre</th>
                                            <th class="text-center">Módulos</th>
                                            <th class="text-center">Temas</th>
                                            <th class="text-center">Exámenes</th>
                                            <th class="text-center">Activo</th>
                                            <th class="text-center">Acciones</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                            @foreach (App\Models\Curso::select(['id', 'nombre'])->get() as $c) 
                                            <tr>
                                                <td scope="row">{{$c->id}}</td>
                                                <td>{{$c->nombre}}</td>
                                                <td class="text-center">{{$c->modulos->count()}}</td>
                                                <td class="text-center">{{App\Models\Tema::where('modulo_id', $c->modulos->pluck('id'))->count()}}</td>
                                                <td class="text-center">{{$c->pruebas->count()}}</td>
                                                <td class="text-center">{{$c->activo == 0 ? 'No' : 'Sí'}}</td>
                                                <td class="text-center">
                                                    <form action="/cursos/{{$c->id}}" method="post" class="borrarCurso" id="borrarCurso{{$c->id}}">
                                                        @csrf
                                                        @method('DELETE')
                                                    </form>
                                                    <a href="/cursos/{{$c->id}}" class="btn btn-sm"><i class="fa fa-eye" aria-hidden="true"></i></a>
                                                    <a href="/cursos/{{$c->id}}/editar" class="btn btn-sm"><i class="fa fa-edit" aria-hidden="true"></i></a>
                                                    <button class="btn btn-sm btn-danger" form="borrarCurso{{$c->id}}"><i class="fa fa-trash" aria-hidden="true"></i></button>
                                                </td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="examenes" role="tabpanel">
                            <form action="/pruebas" method="post" id="nuevoExamen">
                                @csrf
                                    <label for="nombreExamen">Nombre del examen</label>
                                    <input type="text" id="nombreExamen" name="nombreExamen" class="form-control form-control-sm" required form="nuevoExamen">
                                    <label for="descripcionExamen">Descripción</label>
                                    <textarea id="descripcionExamen" name="descripcionExamen" class="form-control form-control-sm" form="nuevoExamen" required></textarea>
                                    <div class="text-center my-3">
                                        <button type="submit" id="crearExamen" class="btn btn-sm btn-success mt-2" form="nuevoExamen">Añadir examen</button>
                                    </div>
                            </form>
                            <div class="table-responsive">
                                <table class="table text-nowrap">
                                    <thead>
                                        <th>#</th>
                                        <th>Nombre</th>
                                        <th>Descripción</th>
                                        <th>Acciones</th>
                                    </thead>
                                    <tbody>
                                        @foreach(App\Models\Examen::all() as $e)
                                        <tr>
                                            <td>{{$e->id}}</td>
                                            <td>{{$e->nombre}}</td>
                                            <td class="text-left">{{Str::limit($e->descripcion, 50)}}</td>
                                            <td>
                                                <form action="/pruebas/{{$e->id}}" method="post" class="borrarExamen" id="borrarExamen{{$e->id}}">
                                                    @csrf
                                                    @method('DELETE')
                                                </form>
                                                <button examen_id="{{$e->id}}" type="button" class="editarExamen btn btn-primary btn-sm" data-toggle="modal" data-target="#editarExamenModal"><i class="fas fa-edit"></i></button>
                                                <button type="submit" form="borrarExamen{{$e->id}}" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
                                            </td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                        </div>
                        <div class="tab-pane" id="instituciones" role="tabpanel">
                            @livewire('live-institucion')
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>    
</div>

@endsection
