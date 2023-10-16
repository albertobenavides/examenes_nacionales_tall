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
        sim = $('#simulacion').html();
        $('#curso').change(function () {
            $('#simulacion').show();
            var optionSelected = $("option:selected", this);
            var v = this.value;

            const params = {
				curso_id: v,
			};

			axios.post('/calificaciones/simulaciones', params).then((response) => {
                var t = sim;
                
				for (let i = 0; i < response.data.length; i++) {
                    const simulacion = response.data[i];
                    
                    t += `<option value="${simulacion.id}">${simulacion.nombre}</option>`;
                }
                $('#simulacion').html(t);
			});
        });

        $('#simulacion').change(function () {
            var optionSelected = $("option:selected", this);
            var v = this.value;

            const params = {
				simulacion_id: v,
			};

            try {
                calif_tabla.clear();
                calif_tabla.destroy();
                calif_tabla = null;
            } catch (error) {
                console.log(error);
            }
            $('#tbody').html('');

            calif_tabla = $('#calif_tabla').DataTable({
                destroy: true,
                lengthChange: false,
                buttons: [ 'excel' ], //buttons: [ 'copy', 'excel', 'pdf', 'colvis' ],
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

            calif_tabla.buttons().container()
            .appendTo( '#calif_tabla_wrapper .col-md-6:eq(0)' );

			axios.post('/calificaciones/simulaciones', params).then((response) => {
                $('#tbody').html('');
                for (let i = 0; i < response.data.length; i++) {
                    const calificacion = response.data[i];
                    var t = [];

                    t.push(calificacion.nombre);
                    t.push(calificacion.minimo);
                    t.push(calificacion.maximo);
                    t.push(calificacion.promedio);
                    t.push(calificacion.total);
                    calif_tabla.row.add( t ).draw();
                }
			});
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron pb-0">
    <div class="container">
        <h1><a href="/ajustes">Administración</a> / Calificaciones</h1>

        <ul class="nav nav-tabs mt-3">
            <li class="nav-item">
              <a class="nav-link" href="/calificaciones">Avance curso</a>
            </li>
            <li class="nav-item">
              <a class="nav-link active" href="/calificaciones/simulaciones">Simulaciones</a>
            </li>
          </ul>
    </div>
</div>
<div class="container">
    <div class="form-inline">
        <select id="curso" class="custom-select mr-2">
            <option value="-1" selected disabled>Elige un curso</option>
            @foreach (App\Models\Curso::get(['id', 'nombre'])->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
        
        <select id="simulacion" class="custom-select" style="display: none">
            <option value="-1" selected disabled>Elige simulación</option>
        </select>
    </div>

    <div class="table-responsive">
        <table id="calif_tabla" class="table table-sm table-stripped">
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Calif. Mayor</th>
                    <th>Calif. Menor</th>
                    <th>Calif. Promedio</th>
                    <th>Intentos</th>
                </tr>
            </thead>
            <tbody id="tbody">

            </tbody>
        </table>
    </div>
</div>

@endsection
