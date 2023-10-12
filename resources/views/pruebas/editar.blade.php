@extends('layouts.app')

@section('styles')
<link rel="stylesheet" href="https://cdn.datatables.net/1.10.20/css/dataTables.bootstrap4.min.css">

@endsection

@section('scripts')
<script src="//cdn.datatables.net/1.10.20/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.10.20/js/dataTables.bootstrap4.min.js"></script>
<script>
    $(function(){
        $('.td').DataTable({
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

        $('.preguntas').change(function(){
            $.ajaxSetup({
                headers: {
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                }
            });
            $.ajax({
                url: '/examenes/{{$examen->id}}',
                type: 'PUT',
                data: {
                    tema_id : $(this).attr('tema_id'),
                    preguntas : $(this).val()
                }
            });
        });

        $('#eliminar_examen').submit(function(e) {
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
        <h1><a href="/cursos/{{$examen->curso->id}}/editar">{{$examen->curso->nombre}}</a></h1>
    </div>
</div>
<div class="container">
    <div class="row d-flex justify-content-center">
        <div class="col-md-10">
            {{-- EDITAR CURSO --}}
            <div class="card">
                <h3 class="card-header">Editar examen</h3>
                <div class="card-body">
                    <form action="/examenes/{{$examen->id}}" method="post" enctype="multipart/form-data" id="actualizar_examen">
                        @csrf
                        @method('PUT')
                    </form>
                    <form action="/examenes/{{$examen->id}}" method="post" id="eliminar_examen">
                        @csrf
                        @method('DELETE')
                    </form>
                    <div class="form-group">
                        <label for="nombre">Nombre</label>
                        <input type="text" name="nombre" class="form-control" value="{{$examen->nombre}}" form="actualizar_examen">
                    </div>
                    <div class="form-group">
                        <label for="descripcion">Descripción</label>
                        <textarea name="descripcion" class="form-control" form="actualizar_examen">{{$examen->descripcion}}</textarea>
                    </div>
                    <button type="submit" class="btn btn-success" form="actualizar_examen">Guardar</button>
                    <button class="btn btn-danger" form="eliminar_examen">Eliminar</button>
                    <a href="/cursos/{{$examen->curso->id}}/editar" class="btn">Regresar</a>

                    <h5 class="mt-3">Preguntas por tema</h5>
                    <div class="table-responsive">
                        <table class="table table-sm table-stripped">
                            <tbody>
                                @foreach ($examen->curso->modulos->sortby('orden') as $m)
                                @if ($m->temas->pluck('preguntas')->flatten()->count() <= 0)
                                @continue
                                @endif
                                <tr>
                                    <td colspan="2">
                                        <strong><a href="/modulos/{{$m->id}}/editar">{{$m->nombre}}</a></strong>
                                    </td>
                                </tr>
                                @foreach ($m->temas as $t)
                                @if ($t->preguntas->count() <= 0)
                                @continue                                    
                                @endif
                                <tr>
                                    <td>
                                        {{$t->nombre}}
                                    </td>
                                    <td style="width:15%">
                                        <input tema_id="{{$t->id}}" type="number" class="preguntas form-control form-control-sm text-center" min="0" max="{{$t->preguntas->count()}}" value="{{$examen->temas->find($t->id)->pivot->preguntas}}">
                                    </td>
                                </tr>
                                @endforeach
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
                <h3 class="card-header">Intentos</h3>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-sm table-stripped td">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th class="text-center">Calificación</th>
                                    <th class="text-center">Fecha</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach ($examen->intentos as $i)
                                <tr>
                                    <td>{{$i->usuario->name}}</td>
                                    <td class="text-center">{{$i->calificacion}}%</td>
                                    <td class="text-center">{{Carbon\Carbon::parse($i->created_at)->isoFormat('D MMM YY')}}</td>
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
