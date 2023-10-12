@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">
@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/translations/es.js"></script>
<script>
    $(document).ready(function(){
        ClassicEditor
            .create( document.querySelector( '#pregunta' ), {
                language: 'es'
            } )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#ayuda' ), {
                language: 'es'
            } )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
        
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
            }
        });

        $('.borrarPregunta').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar pregunta? También se borrarán las respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });

        $('#eliminar_tema').submit(function(e) {
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
        <h1 ><a href="/cursos/{{$tema->modulo->curso->id}}/editar">{{$tema->modulo->curso->nombre}}</a></h1>
        <h2><a href="/modulos/{{$tema->modulo->id}}/editar">{{$tema->modulo->nombre}}</a></h2>
        <h3>{{$tema->nombre}}</h3>
    </div>
</div>
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            {{-- EDITAR TEMA --}}
            <div class="card">
                <h3 class="card-header">Editar tema</h3>
                <div class="card-body">
                    <form action="/temas/{{$tema->id}}" method="post" enctype="multipart/form-data" id="actualizar_tema">
                        @csrf
                        @method('PUT')
                    </form>
                    <form action="/temas/{{$tema->id}}" method="post" id="eliminar_tema">
                        @csrf
                        @method('DELETE')
                    </form>
                    @if (isset($tema->imagen))
                        <img src="/storage/{{$tema->imagen}}" class="img-fluid">
                        <div class="form-check">
                            <label class="form-check-label">
                                <input type="checkbox" class="form-check-input" name="eliminarImagen" value="checkedValue" form="actualizar_tema">
                                ¿Eliminar imagen actual?
                            </label>
                        </div>
                    @else
                    <img src="https://fakeimg.pl/320x200/?text={{$tema->nombre}}" class="img-fluid">
                    @endif
                    <div class="fileinput fileinput-new" data-provides="fileinput">
                        <span class="btn btn-primary btn-file">
                            <span class="fileinput-new">Imagen</span>
                            <span class="fileinput-exists">Cambiar</span>
                            <input type="file" name="imagen" form="actualizar_tema">
                        </span>
                        <span class="fileinput-filename"></span>
                        <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                    </div>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{$tema->nombre}}" form="actualizar_tema">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="form-control" form="actualizar_tema">{{$tema->descripcion}}</textarea>
                    </div>
                    <div class="row">
                        <div class="col">
                            <label for="pdf">Adjuntar PDF</label> <br>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <span class="btn btn-primary btn-file">
                                    <span class="fileinput-new">PDF</span>
                                    <span class="fileinput-exists">Cambiar</span>
                                    <input type="file" name="pdfTema" form="actualizar_tema">
                                </span>
                                <span class="fileinput-filename"></span>
                                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                            </div>
                            @isset($tema->pdf)
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="eliminarPDF" value="checkedValue" form="actualizar_tema">
                                    ¿Eliminar PDF actual?
                                </label>
                            </div>
                            @endisset
                        </div>
                        <div class="col">
                            <label for="video">Adjuntar video</label> <br>
                            <div class="fileinput fileinput-new" data-provides="fileinput">
                                <span class="btn btn-primary btn-file">
                                    <span class="fileinput-new">Video</span>
                                    <span class="fileinput-exists">Cambiar</span>
                                    <input type="file" name="video" form="actualizar_tema">
                                </span>
                                <span class="fileinput-filename"></span>
                                <a href="#" class="close fileinput-exists" data-dismiss="fileinput" style="float: none">&times;</a>
                            </div>
                            @isset($tema->video)
                            <div class="form-check">
                                <label class="form-check-label">
                                    <input type="checkbox" class="form-check-input" name="eliminarVideo" value="checkedValue" form="actualizar_tema">
                                    ¿Eliminar video actual?
                                </label>
                            </div>
                            @endisset
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="">Video Youtube</label>
                                <input type="text" class="form-control" name="youtube" value="{{$tema->video}}" form="actualizar_tema">
                                <small class="form-text text-muted">Código del video de Youtube para embeber en la página.</small>
                              </div>
                        </div>
                        <div class="col">
                            <div class="form-group">
                                <label for="preguntar">Preguntar <em>x</em> de {{$tema->preguntas->count()}}</label>
                                <input type="number" class="form-control text-right" name="preguntar" value="{{$tema->preguntar}}" min="0" max="{{$tema->preguntas->count()}}" form="actualizar_tema">
                                <small class="form-text text-muted">Selecciona la cantidad de preguntas que deseas preguntar a los alumnos.</small>
                              </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="mover">Mover a...</label>
                        <select class="custom-select" name="modulo_id" form="actualizar_tema">
                            <option selected value="-1">No mover</option>
                            @foreach (App\Curso::select('id', 'nombre')->get() as $c)
                                <optgroup label="{{$c->nombre}}">
                                @foreach (App\Modulo::select('id', 'nombre')->where('curso_id', $c->id)->get() as $m)
                                    <option value="{{$m->id}}">{{$m->nombre}}</option>
                                @endforeach
                                </optgroup>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-success" form="actualizar_tema">Guardar</button>
                    <button class="btn btn-danger" form="eliminar_tema">Eliminar</button>
                    <a href="/modulos/{{$tema->modulo->id}}/editar" class="btn">Regresar</a>
                </div>
            </div>
        </div>
    </div>

    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            <div class="card mt-3">
                <h3 class="card-header">Preguntas</h3>
                <div class="card-body">
                    <form action="/preguntas" method="post" enctype="multipart/form-data" class="mb-3">
                        @csrf
                        <input type="hidden" name="tema_id" value="{{$tema->id}}">
                        <input type="hidden" name="curso_id" value="{{$tema->modulo->curso->id}}">
                        <label>Añade múltiples preguntas</label>
                        <input type="file" name="preguntas">
                        <small class="form-text text-muted">Utiliza un archivo <code>CSV</code> que tenga el siguiente formato por línea:</small>
                        <small class="form-text text-muted">"pregunta"|"respuesta1"|"respuesta2"|"..."|"respuestaN"|"retroalimentación"</small>
                        <small class="form-text text-muted">La primera respuesta se marcará como correcta.</small>
                        <button class="btn btn-success">Guardar</button>
                    </form>

                    <form action="/preguntas" method="post" id="nuevaPregunta" class="mb-3">
                        @csrf
                        <input type="hidden" name="tema_id" value="{{$tema->id}}">
                        <input type="hidden" name="curso_id" value="{{$tema->modulo->curso->id}}">
                        <p>O añade una pregunta</p>
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label for="pregunte">Pregunta</label>
                                    <textarea id="pregunta" name="contenido" form="nuevaPregunta"></textarea>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label>Retroalimentación</label>
                                    <textarea id="ayuda" name="ayuda" form="nuevaPregunta"></textarea>
                                </div>
                            </div>
                        </div>
                        <button class="btn btn-success" type="submit">Guardar</button>
                    </form>

                    <hr>

                    <h4>Todas las preguntas</h4>
                    <div class="table-responsive">
                        <table class="table table-striped table-sm">
                            <thead>
                                <tr>
                                    <th>Pregunta</th>
                                    <th class="text-center">Respuestas</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                            @foreach(App\Pregunta::where('tema_id', $tema->id)->get() as $p)
                                <tr>
                                    <td>
                                        <a href="/preguntas/editar/{{$p->id}}">{!! Str::limit($p->contenido, 50) !!}</a>
                                    </td>
                                    <td class="text-center"></td>
                                    <td class="text-center">
                                        <form action="/preguntas/{{$p->id}}" method="POST" class="borrarPregunta">
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