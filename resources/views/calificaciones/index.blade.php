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
        pagina = 1;
        $('#curso').change(function () {
            if (pagina > 1){
                try {
                    calif_tabla.clear();
                    calif_tabla.destroy();
                    calif_tabla = null;
                } catch (error) {
                    console.log(error);
                }
                $('#thead').html('');
                $('#tbody').html('');
            }
            pagina = 1;
            $('#cargar').show();
        });

        $('#cargar').click(function () {
            var optionSelected = $("option:selected", $('#curso'));
            var v = $('#curso').val();

            const params = {
				curso_id: v,
                page: pagina
			};
            pagina += 1;

			axios.post('/calificaciones/usuarios', params).then((response) => {                
                var t = '<th>Nombre</th>';
                t += '<th>Global</th>';
                for (let i = 0; i < response.data.modulos.length; i++) {
                    t += `<th>${response.data.modulos[i]}</th>`;
                }

                $('#thead').html(t);

                if (pagina == 2){
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
                }

                l = response.data.usuarios.length - 1;

				for (let i = 0; i < response.data.usuarios.length; i++) {
                    const u = response.data.usuarios[i];
                    const params2 = {
                        curso_id: v,
                        user_id: u
                    };
                    axios.post('/calificaciones', params2).then((response) => {
                        const usuario = response.data;
                            if (usuario.global != 0){
                            var t = [];
                            t.push(usuario.name);
                            t.push(usuario.global);
                            for (let k = 0; k < usuario.modulos.length; k++) {
                                const m = usuario.modulos[k];
                                t.push(m);
                            }
                            calif_tabla.row.add( t ).draw();
                        }
                    });
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
              <a class="nav-link active" href="/calificaciones">Avance curso</a>
            </li>
            <li class="nav-item">
              <a class="nav-link" href="/calificaciones/simulaciones">Simulaciones</a>
            </li>
          </ul>
    </div>
</div>
<div class="container">
    <div class="form-group">
        <label>Curso</label>
        <select id="curso" class="custom-select mr-2">
            <option value="-1" selected disabled>Elige un curso</option>
            @foreach (App\Models\Curso::select(['id', 'nombre'])->get() as $c)
                <option value="{{ $c->id }}">{{ $c->nombre }}</option>
            @endforeach
        </select>
    </div>
    <button id="cargar" class="btn btn-primary btn-sm my-3" style="display: none">Cargar 50</button>

    <div class="table-responsive">
        <table id="calif_tabla" class="table table-sm table-stripped">
            <thead>
                <tr id="thead">
                </tr>
            </thead>
            <tbody id="tbody"></tbody>
        </table>
    </div>
</div>

@endsection
