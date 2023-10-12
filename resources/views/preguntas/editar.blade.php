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
        ClassicEditor
            .create( document.querySelector( '#ayuda_input' ), {
                language: 'es'
            } )
            .then( editor => {
                ayuda = editor;
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
        ClassicEditor
            .create( document.querySelector( '#respuesta' ), {
                language: 'es'
            } )
            .then( editor => {
                console.log( editor );
            } )
            .catch( error => {
                console.error( error );
            } );
        $('#editarPregunta').submit(function(e){
            $('#contenido').val(contenido.getData());
            $('#ayuda').val(ayuda.getData());
            $(this).unbind('submit').submit();
        });
        $('#borrarPregunta').submit(function(e) {
            e.preventDefault();
            if(confirm('¿Eliminar pregunta? También se borrarán las respuestas asociadas.')){
                $(this).unbind('submit').submit();
            }
        });
        $('.borrarRespuesta').submit(function(e) {
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
        <h1 ><a href="/cursos/{{$pregunta->tema->modulo->curso->id}}/editar">{{$pregunta->tema->modulo->curso->nombre}}</a> / {{$pregunta->tema->nombre}}</h1>
    </div>
</div>
<div class="container">
    <form action="/preguntas/{{$pregunta->id}}" method="POST" id="borrarPregunta" class="mb-3">
        @csrf
        @method('DELETE')
    </form>
    <form action="/preguntas/{{$pregunta->id}}" method="post" id="editarPregunta">
        @csrf
        @method('PUT')
        <input type="hidden" name="tema_id" value="{{$pregunta->tema->id}}">
        <input type="hidden" name="curso_id" value="{{$pregunta->tema->modulo->curso->id}}">
        <input type="hidden" name="contenido" id="contenido">
        <input type="hidden" name="ayuda" id="ayuda">
        <p>Modifica la pregunta</p>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label for="pregunte">Pregunta</label>
                    <textarea id="contenido_input" form="editarPregunta">{{$pregunta->contenido}}</textarea>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Retroalimentación</label>
                    <textarea id="ayuda_input" form="editarPregunta">{{$pregunta->ayuda}}</textarea>
                </div>
            </div>
        </div>
    </form>
    <button class="btn btn-success btn-sm" type="submit" form="editarPregunta">Guardar</button>
    <button class="btn btn-danger btn-sm" type="submit" form="borrarPregunta">Borrar</button>
    <button class="btn btn-sm btn-primary" onclick="location.href = '/temas/{{$pregunta->tema->id}}/editar';">Regresar</button>

    <form action="/respuestas" method="post" id="nuevaPregunta" class="my-3">
        @csrf
        <input type="hidden" name="pregunta_id" value="{{$pregunta->id}}">
        <p>Añade una respuesta</p>
        <div class="form-group">
            <textarea id="respuesta" name="contenido" form="nuevaPregunta"></textarea>
            <div class="form-check">
                <input class="form-check-input" type="checkbox" value="1" name="correcta">
                <label class="form-check-label" for="defaultCheck1">
                  ¿Correcta?
                </label>
            </div>
        </div>
        <button class="btn btn-success btn-sm" type="submit">Guardar</button>
    </form>

    <h2>Respuestas</h2>
    <table class="table table-striped table-responsive table-sm">
        <tbody>
        @foreach($pregunta->respuestas as $r)
            <tr {{$r->correcta == 1 ? 'class=bg-success' : ''}}>
                <td>
                    <a href="/respuestas/{{$r->id}}/editar">{!!$r->contenido!!}</a>
                </td>
                <td>
                    <form action="/respuestas/{{$r->id}}" method="POST" class="borrarRespuesta">
                        @csrf
                        @method('DELETE')
                        <input type="submit" class="btn btn-danger btn-sm" value="Borrar">
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection