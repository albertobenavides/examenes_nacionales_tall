@extends('layouts.app')

@section('scripts')
<script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/ckeditor.js"></script>
<script src="https://cdn.ckeditor.com/ckeditor5/17.0.0/classic/translations/es.js"></script>
<script>
    $(function() {        
        ClassicEditor
            .create( document.querySelector( '#contenido_input' ), {
                language: 'es'
            } )
            .then( editor => {
                contenido = editor;
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );

        $('#editarRespuesta').submit(function(e){
            $('#contenido').val(contenido.getData());
            $(this).unbind('submit').submit();
        });
        $('#borrarRespuesta').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar respuesta?')){
                $(this).unbind('submit').submit();
            }
        });
    });
</script>
@endsection

@section('content')
<div class="jumbotron">
    <div class="container">
        <h1 ><a href="/cursos/{{$respuesta->pregunta->tema->modulo->curso->id}}/editar">{{$respuesta->pregunta->tema->modulo->curso->nombre}}</a> / {{$respuesta->pregunta->tema->nombre}}</h1>
    </div>
</div>
<div class="container">
    <form action="/respuestas/{{$respuesta->id}}" method="POST" id="borrarRespuesta" class="mb-3">
        @csrf
        @method('DELETE')
    </form>
    <form action="/respuestas/{{$respuesta->id}}" method="post" id="editarRespuesta">
        @csrf
        @method('PUT')
        <input type="hidden" name="pregunta_id" value="{{$respuesta->pregunta->id}}">
        <input type="hidden" name="contenido" id="contenido">
        <p>Modifica la respuesta</p>
        <div class="form-group">
            <textarea id="contenido_input" name="contenido" form="nuevaPregunta">{{$respuesta->contenido}}</textarea>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="correcta" {{$respuesta->correcta == 1 ? 'checked': ''}}>
                <label class="form-check-label" for="defaultCheck1">
                  ¿Correcta?
                </label>
            </div>
        </div>
    </form>
    <button class="btn btn-success btn-sm" type="submit" form="editarRespuesta">Guardar</button>
    <button class="btn btn-danger btn-sm" type="submit" form="borrarRespuesta">Borrar</button>
    <button class="btn btn-sm btn-primary" onclick="location.href = '/preguntas/editar/{{$respuesta->pregunta->id}}';">Regresar</button>
</div>
@endsection