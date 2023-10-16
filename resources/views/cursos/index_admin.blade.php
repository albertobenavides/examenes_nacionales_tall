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
            }
        });

        //  https://stackoverflow.com/a/40330287/3113008
        $('.eliminar_curso').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar curso? También se borrarán las pruebas, módulos, temas, preguntas y respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });
    });
</script>
    
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1><a href="/ajustes">Administración</a></h1>
    </div>
</div>
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            {{-- EDITAR CURSO --}}
            <div class="card">
                <h3 class="card-header">Cursos</h3>
                <div class="card-body">
                    <h4>Agregar curso</h4>
                    <form action="/cursos" method="post" id="agregar_curso">
                        @csrf
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
                                        <input type="file" name="imagen">
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
                            <textarea name="descripcion" class="form-control" form="agregar_curso"></textarea>
                        </div>
                        <div class="form-group">
                            <label for="mover">Prueba asociada</label>
                            <select class="custom-select" name="examen_id" form="agregar_curso" required>
                                <option selected disabled value="-1">Elige</option>
                                @foreach (App\Models\Examen::select(['id', 'nombre'])->get() as $e)
                                    <option value="{{$e->id}}">{{$e->nombre}}</option>
                                @endforeach
                            </select>
                        </div>
                        <button type="submit" class="btn btn-success">Guardar</button>
                        <a href="/ajustes" class="btn">Regresar</a>
                    </form>
                    <hr>
                    <h4>Todos los cursos</h4>
                    <div class="table-responsive">
                        <table class="table table-sm table-stripped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-center">Módulos</th>
                                    <th class="text-center">Temas</th>
                                    <th class="text-center">Exámenes</th>
                                    <th class="text-center">Visible</th>
                                    <th class="text-center">Imagen</th>
                                    <th>Prueba asociada</th>
                                    <th class="text-center">Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($cursos as $c)
                                <tr>
                                    <td>
                                        <a href="/cursos/{{$c->id}}/editar">
                                            {{$c->nombre}}
                                        </a>
                                    </td>
                                    <td class="text-center">
                                        {{$c->modulos->count()}}
                                    </td>
                                    <td class="text-center">
                                        {{$c->modulos->pluck('temas')->flatten()->count()}}
                                    </td>
                                    <td class="text-center">
                                        {{$c->pruebas->count()}}
                                    </td>
                                    <td class="text-center">
                                        @if ($c->activo == 0)
                                            No
                                        @else
                                            Sí
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @isset($c->imagen)
                                        <a href="/storage/{{$c->imagen}}" target="_blank"><i class="fas fa-image"></i></a>
                                        @else
                                        No
                                        @endisset
                                    </td>
                                    <td>
                                        {{$c->examen->nombre}}
                                    </td>
                                    <td class="text-center">
                                        <form action="/cursos/{{$c->id}}" method="post" class="eliminar_curso">
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