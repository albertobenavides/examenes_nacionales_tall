@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function(){
        $('table').DataTable({
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
            "ordering": false
        });

        $(".sortable" ).sortable({
            handle: ".manipular",
            connectWith: ".sortable",
            deactivate: function( event, ui ) {
                var modulos = []
                ui.item.parent().children().each(function( index, value ) {
                    modulos.push($(value).attr('modulo_id'));
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/modulos/' + modulos[0],
                    type: 'PUT',
                    data: {modulos : modulos}
                });
            }
        });

        $('#eliminar_curso').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar curso? También se borrarán las pruebas, módulos, temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

        $('.eliminar_modulo').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar módulo? También se borrarán los temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

        $('.preguntas').change(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/examenes/' + $(this).attr('prueba_id'),
                type: 'PUT',
                data: {
                    modulo_id : $(this).attr('modulo_id'),
                    preguntas : $(this).val()
                }
            });
        });

       //  https://stackoverflow.com/a/40330287/3113008
        $('.borrarModulo').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar modulo? También se borrarán los temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

        $('.borrarPrueba').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar examen?')){
                $(this).unbind('submit').submit();
            }
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1 >{{$curso->nombre}}</h1>
    </div>
</div>
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            {{-- EDITAR CURSO --}}
            <div class="card">
                <h3 class="card-header">Editar curso</h3>
                <div class="card-body">
                    <form action="/cursos/{{$curso->id}}" method="post" enctype="multipart/form-data" id="actualizar_curso">
                        @csrf
                        @method('PUT')
                    </form>
                    <form action="/cursos/{{$curso->id}}" method="post" id="eliminar_curso">
                        @csrf
                        @method('DELETE')
                    </form>
                    @if (isset($curso->imagen))
                        <img src="/storage/{{$curso->imagen}}" class="img-fluid">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="eliminarImagen" value="checkedValue" form="actualizar_curso">
                                ¿Eliminar imagen actual?
                            </label>
                        </div>
                    @else
                    <img src="https://fakeimg.pl/320x200/?text={{$curso->nombre}}" class="img-fluid">
                    @endif
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn btn-primary btn-file">
                            <span class="fileinput-new">Imagen</span>
                            <span class="fileinput-exists">Cambiar</span>
                            <input type="file" name="imagen" form="actualizar_curso">
                        </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{$curso->nombre}}" form="actualizar_curso">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="form-control" form="actualizar_curso">{{$curso->descripcion}}</textarea>
                    </div>
                    <div class="form-group">
                        <label for="mover">Prueba asociada</label>
                        <select class="custom-select" name="examen_id" form="actualizar_curso" required>
                            <option selected disabled value="-1">Elige</option>
                            @foreach (App\Examen::select(['id', 'nombre'])->get() as $e)
                                <option value="{{$e->id}}" {{$curso->examen->id == $e->id ? 'selected' : ''}}>{{$e->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="custom-control custom-switch">
                        <input type="checkbox" class="custom-control-input" id="activo" name="activo" {{$curso->activo == 1 ? 'checked' : ''}} form="actualizar_curso">
                        <label class="custom-control-label" for="activo">Visible</label>
                      </div>
                    <button type="submit" class="btn btn-success" form="actualizar_curso">Guardar</button>
                    <button class="btn btn-danger" form="eliminar_curso">Eliminar</button>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card mt-3">
                <h3 class="card-header">Módulos</h3>
                <div class="card-body">
                    <h4>Agregar módulo</h4>
                    <form action="/modulos" method="post" id="agregar_modulo">
                        @csrf
                        <input type="hidden" name="curso_id" value="{{$curso->id}}">
                        <div class="form-group">
                            <label for="imagen">Imagen</label> <br>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <div class="fileinput-preview" data-trigger="fileinput">
                                    <img src="https://fakeimg.pl/320x200/?text=320x200" class="card-img-top">
                                </div>
                                <div>
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new">Elegir</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="imagen" form="agregar_modulo">
                                    </span>
                                    <a href="#" class="btn btn-primary fileinput-exists" data-dismiss="fileinput">Quitar</a>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control" form="agregar_modulo"></textarea>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                    </form>
                    <hr>
                    <h4>Todos los módulos</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-stripped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">Imagen</th>
                                    <th class="text-center">Temas</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="sortable">
                                @foreach ($curso->modulos->sortby('orden') as $m)
                                <tr modulo_id="{{$m->id}}">
                                    <td>
                                        <a href="#" class="manipular"><i class="fas fa-sort fa-2x"></i></a>
                                    </td>
                                    <td>
                                        <a href="/modulos/{{$m->id}}/editar">
                                            {{$m->nombre}}
                                        </a>
                                    </td>
                                    <td>{{Str::limit($m->descripcion, 20)}}</td>
                                    <td class="text-center">
                                        @isset($m->imagen)
                                        <a href="/storage/{{$m->imagen}}" target="_blank"><i class="fas fa-image"></i></a>
                                        @else
                                        No
                                        @endisset
                                    </td>
                                    <td class="text-center">
                                        {{$m->temas->count()}}
                                    </td>
                                    <td class="text-center">
                                        <form action="/modulos/{{$m->id}}" method="post" class="eliminar_modulo">
                                            @csrf
                                            @method('DELETE')
                                                <button type="submit" class="btn bnt-link text-danger"><i class="fas fa-trash"></i></button>
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

    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card mt-3">
                <h3 class="card-header">Exámenes</h3>
                <div class="card-body">
                    <h4>Agregar examen</h4>
                    <form action="/examenes" method="post">
                        @csrf
                        <input type="hidden" id="curso_id" name="curso_id" value="{{$curso->id}}">
                        <label for="nombreExamen">Nombre</label>
                        <input type="text" id="nombreExamen" name="nombreExamen" class="form-control" placeholder="Nombre del Examen" required>
                        <label for="nombreDescripcion">Descripción</label>
                        <input type="text" id="descripcionExamen" name="descripcionExamen" class="form-control" placeholder="Descripcion del Examen">
                        <button type="submit" id="crearExamen" class="btn btn-success mt-2">Añadir</button>
                    </form>

                    <hr>

                    <h3>Exámenes</h3>
                    <div class="table-responsive">
                        <table class="table table-sm table-striped text-nowrap">
                            <thead class="thead-inverse">
                                <tr>
                                    <th>#</th>
                                    <th>Nombre</th>
                                    <th class="text-center">Módulos</th>
                                    <th class="text-center">Temas</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($curso->pruebas as $p)
                                <tr>
                                    <td scope="row">{{$p->id}}</td>
                                    <td><a href="/examenes/{{$p->id}}/editar">{{$p->nombre}}</a></td>
                                    <td class="text-center"></td>
                                    <td class="text-center"></td>
                                    <td class="text-center">
                                        <form action="/examenes/{{$p->id}}" method="post" class="borrarPrueba">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="btn bnt-link text-danger"><i class="fas fa-trash"></i></button>
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
