@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(document).ready(function(){
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
                var temas = []
                ui.item.parent().children().each(function( index, value ) {
                    temas.push($(value).attr('tema_id'));
                });
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/temas/' + temas[0],
                    type: 'PUT',
                    data: {temas : temas}
                });
            }
        });

        //  https://stackoverflow.com/a/40330287/3113008
        $('#eliminar_modulo').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar modulo? También se borrarán los temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

        $('.eliminar_tema').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar tema? También se borrarán las preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

    });
</script>
    
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1>
            <a href="/cursos/{{$modulo->curso->id}}/editar">{{$modulo->curso->nombre}}</a>
        </h1>
        <h2>{{$modulo->nombre}}</h2>
    </div>
</div>
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            {{-- EDITAR MÓDULO --}}
            <div class="card">
                <h3 class="card-header">Editar módulo</h3>
                <div class="card-body">
                    <form action="/modulos/{{$modulo->id}}" method="post" enctype="multipart/form-data" id="actualizar_modulo">
                        @csrf
                        @method('PUT')
                    </form>
                    <form action="/modulos/{{$modulo->id}}" method="post" id="eliminar_modulo">
                        @csrf
                        @method('DELETE')
                    </form>
                    @isset($modulo->imagen)
                        <img src="/storage/{{$modulo->imagen}}" class="img-fluid">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="eliminarImagen" value="checkedValue" form="actualizar_modulo">
                                ¿Eliminar imagen actual?
                            </label>
                        </div>
                    @else
                    <img src="https://fakeimg.pl/320x200/?text={{$modulo->nombre}}" class="img-fluid">
                    @endif
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn btn-primary btn-file btn-sm">
                            <span class="fileinput-new">Imagen</span>
                            <span class="fileinput-exists">Cambiar</span>
                            <input type="file" name="imagen" form="actualizar_modulo">
                        </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{$modulo->nombre}}" form="actualizar_modulo">
                    </div>
                    {{--
                    <div class="form-group">
                        <label for="copiar">Copiar a...</label>
                        <select class="custom-select">
                            <option selected value="-1">No copiar</option>
                            <option value="1">One</option>
                            <option value="2">Two</option>
                            <option value="3">Three</option>
                        </select>
                    </div>
                    --}}
                    <div class="form-group">
                        <label for="mover">Mover a...</label>
                        <select class="custom-select" name="curso_id" form="actualizar_modulo">
                            <option selected value="-1">No mover</option>
                            @foreach (App\Models\Curso::select(['id', 'nombre'])->get() as $c)
                                <option value="{{$c->id}}">{{$c->nombre}}</option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" form="actualizar_modulo">Guardar</button>
                    <button class="btn btn-danger" form="eliminar_modulo">Eliminar</button>
                    <a href="/cursos/{{$modulo->curso->id}}/editar" class="btn">Regresar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card mt-3">
                <h3 class="card-header">Temas</h3>
                <div class="card-body">
                    <h4>Agregar tema</h4>
                    <form action="/temas" method="post" enctype="multipart/form-data">
                        @csrf
                        <input type="hidden" name="modulo_id" value="{{$modulo->id}}">
                        <div class="form-group">
                            <label for="nombre">Nombre</label>
                            <input type="text" name="nombre" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label for="descripcion">Descripción</label>
                            <textarea name="descripcion" class="form-control"></textarea>
                        </div>
                        <div class="row">
                            <div class="col">
                                <label for="pdf">Adjuntar PDF</label> <br>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new">PDF</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="pdfTema">
                                    </span>
                                    <span class="fileinput-filename"></span>
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                </div>
                            </div>
                            <div class="col">
                                <label for="video">Adjuntar video</label><br>
                                <div class="fileinput fileinput-new" data-provides="fileinput">
                                    <span class="btn btn-primary btn-file">
                                        <span class="fileinput-new">Video</span>
                                        <span class="fileinput-exists">Cambiar</span>
                                        <input type="file" name="video">
                                    </span>
                                    <span class="fileinput-filename"></span>
                                    <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                                </div>
                            </div>
                            <div class="col">
                                <div class="form-group">
                                    <label for="">Video Youtube</label>
                                    <input type="text" class="form-control" name="youtube" aria-describedby="helpId" placeholder="Código del video">
                                    <small id="helpId" class="form-text text-muted">Código del video de Youtube para embeber en la página.</small>
                                  </div>
                            </div>
                        </div>
                        <p><strong>Nota</strong>: El video de Youtube tiene preferencia.</p>
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="/cursos/{{$modulo->curso->id}}/editar" class="btn">Regresar</a>
                    </form>
                    <hr>
                    <h4>Todos los temas</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-stripped">
                            <thead>
                                <tr>
                                    <th></th>
                                    <th>Nombre</th>
                                    <th>Descripción</th>
                                    <th class="text-center">PDF</th>
                                    <th class="text-center">Video</th>
                                    <th class="text-center">Preguntas</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody class="sortable">
                                @foreach ($modulo->temas->sortby('orden') as $t)
                                    <tr tema_id="{{$t->id}}">
                                        <td>
                                            <a href="#" class="manipular"><i class="fas fa-sort fa-2x"></i></a>
                                        </td>
                                        <td>
                                            <a href="/temas/{{$t->id}}/editar">{{$t->nombre}}</a>
                                        </td>
                                        <td>{{Str::limit($t->descripcion, 20)}}</td>
                                        <td class="text-center">
                                            @isset($t->pdf)
                                            <a href="/descargar/{{$t->pdf}}"><i class="fas fa-file-pdf"></i></a>
                                            @else
                                            No  
                                            @endisset
                                        </td>
                                        <td class="text-center">
                                            @isset($t->video)
                                            @if (strpos($t->video, ".") === false)
                                                <a href="youtube.com/{{$t->video}}"><i class="fab fa-youtube"></i></a>
                                            @else
                                                <a href="/mostrar/{{$t->video}}"><i class="fas fa-file-pdf"></i></a>
                                            @endif
                                            @else
                                            No
                                            @endisset
                                        </td>
                                        <td class="text-center">
                                            {{$t->preguntar}} / {{$t->preguntas->count()}}
                                        </td>
                                        <td class="text-center">
                                            <form action="/temas/{{$t->id}}" method="post" class="eliminar_tema">
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