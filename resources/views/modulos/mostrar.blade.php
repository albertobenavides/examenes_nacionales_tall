@extends('layouts.app')

@section('styles')
    @filamentStyles
@endsection

@section('scripts')
    @filamentScripts
    <script>
        $(function() {
            $('#siguiente').hide();
            $('#videoYoutube').hide();
            $('#videoTema').parent().hide();
            respuesta = $('#respuesta').clone();
            $('#respuesta').remove();
            $('.modal').on('hidden.bs.modal', function(e) {
                $('#videoYoutube').removeAttr('src');
                $('#videoTema').attr('src', '');
                $('#videoTema').parent().get(0).load();
            })
            $('.mostrarVideo').click(function() {
                @if (Auth::user()->rol_id == 1 or
                        Auth::user()->pagos->where('curso_id', $modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->count() >
                            0 and
                            Auth::user()->pagos->where('curso_id', $modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->sortByDesc('promo_id')->first()->promo->videos ==
                                true)
                    $('.modal-title').html($(this).attr('data-nombre'));
                    if ($(this).attr('data-url').indexOf(".") === -1) {
                        $('#videoYoutube').attr('src', 'https://www.youtube.com/embed/' + $(this).attr('data-url'));
                        $('#videoYoutube').show();
                    } else {
                        $('#videoTema').attr('src', '/mostrar/' + $(this).attr('data-url') + '?curso_id={{ $modulo->curso->id }}');
                        // https://www.w3schools.com/Tags/av_met_load.asp
                        // https://stackoverflow.com/a/47592688/3113008
                        $('#videoTema').parent().get(0).load();
                        $('#videoTema').parent().show();
                    }
                    $('#pdfTema').hide();
                    $('#preguntasTema').hide();
                @else
                    window.location = '/pagos/crear?curso_id={{ $modulo->curso->id }}';
                @endif
            });
            $('.mostrarPDF').click(function() {
                @if (Auth::user()->rol_id == 1 ||
                        Auth::user()->pagos->where('curso_id', $modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->count() > 0)
                    $('.modal-title').html($(this).attr('data-nombre'));
                    $('#preguntasTema').hide();
                    $('#videoTema').parent().hide();
                    $('#videoYoutube').hide();
                    $('#pdfTema').attr('src', '/ViewerJS/index.html#../descargar/' + $(this).attr('data-url') + '?curso_id={{ $modulo->curso->id }}');
                    var t = $('#pdfTema').clone();
                    var p = $('#pdfTema').parent();
                    $('#pdfTema').remove();
                    p.append(t.clone());
                    $('#pdfTema').show();
                @else
                    window.location = '/pagos/crear?curso_id={{ $modulo->curso->id }}';
                @endif
            });
            $('.mostrarPreguntas').click(function() {
                $('#ayuda').html('');
                @if (Auth::user()->rol_id == 1 or
                        Auth::user()->pagos->where('curso_id', $modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->count() >
                            0 and
                            Auth::user()->pagos->where('curso_id', $modulo->curso->id)->where('fin', '>=', Carbon\Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes ==
                                true)
                    if (!$(this).hasClass('button')) {
                        $(this).hide();
                    }
                    $('#revisar').hide();
                    $('.modal-title').html($(this).attr('data-nombre'));
                    $('#videoTema').parent().hide();
                    $('#videoYoutube').hide();
                    $('#pdfTema').hide();
                    $('#preguntasTema').show();
                    $('#siguiente').attr('tema_id', $(this).attr('tema_id'));
                    $.ajaxSetup({
                        headers: {
                            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                        }
                    });
                    $.ajax({
                        url: '/preguntas/' + $(this).attr('tema_id'),
                        type: 'GET',
                        success: function(data) {
                            $('#revisar').attr('pregunta_id', data.id);
                            $('#contenido').html(data.contenido);
                            $('#respuestas').empty();
                            for (var i = 0; i < _.size(data.r); i++) {
                                var t = respuesta.clone();
                                t.removeAttr('id');
                                t.addClass('respuesta');
                                t.attr('respuesta_id', data.r[i].id);
                                t.html(data.r[i].contenido);
                                $('#respuestas').append(t);
                                $('.respuesta').click(function() {
                                    $('.respuesta').removeClass('bg-info');
                                    $(this).addClass('bg-info');
                                    $('#revisar').show();
                                });
                            }
                            MathJax.typeset();
                        }
                    });
                @else
                    window.location = '/pagos/crear?curso_id={{ $modulo->curso->id }}';
                @endif
            });
            $('#revisar').click(function(e) {
                $('#revisar').hide();
                $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
                    }
                });
                $.ajax({
                    url: '/preguntas/revisar/' + $(this).attr('pregunta_id'),
                    type: 'POST',
                    success: function(data) {
                        console.log(data);
                        $('#siguiente').show();
                        var t = $('.respuesta[respuesta_id="' + data.id + '"]');
                        t.addClass('bg-success');
                        if (t.hasClass('bg-info')) {
                            t.removeClass('bg-info');
                        }
                        $('.respuesta').off('click');
                        $('#ayuda').html(data.ayuda)
                    }
                });
            });
        });
    </script>
@endsection

@section('content')
    <div class="jumbotron pb-0">
        <div class="container">
            <h4>{{ Auth::user()->name }}</h4>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item">{{ $modulo->curso->nombre }}</li>
                    <li class="breadcrumb-item"><a href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a></li>
                    <li class="breadcrumb-item active">{{ $modulo->nombre }}</li>
                </ol>
            </nav>
            @if ($pago == null)
                <a href="/pagos/crear" class="btn btn-secondary">Inscríbete</a>
            @else
                <h5>Plan: {{ $pago->promo->nombre }}
                    @if (Auth::user()->por_admin == 1 && $pago->promo_id == 1)
                        <a href="/pagos/crear" class="btn btn-sm btn-secondary">Convenio CEAA</a>
                    @endif
                </h5>
                <h5>Vigencia: {{ Carbon\Carbon::parse($pago->fin)->format('d/m/Y') }}</h5>
            @endif

            <ul class="nav nav-tabs mt-3">
                <li class="nav-item">
                    <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/logros">Logros</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link active" href="/cursos/{{ $modulo->curso->id }}/clases">Clases</a>
                </li>
                {{-- <li class="nav-item">
                <a class="nav-link" href="/cursos/{{ $modulo->curso->id }}/examenes">Exámenes</a>
            </li> --}}
            </ul>
        </div>
    </div>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-9">
                <h5>TODO DETALLE CON LA PLATAFORMA, CONTENIDO, REACTIVOS Y EXÁMENES FAVOR DE COMUNICARSE AL 8131965935</h5>
                <h2 class="mt-3">Progreso</h2>
                @php
                    if ($totales <= 0) {
                        $val = 0;
                    } else {
                        $val = intval(($pasados / $totales) * 100);
                    }
                @endphp
                <div class="progress">
                    <div class="progress-bar" role="progressbar" style="width: {{ $val }}%" aria-valuenow="{{ $val }}" aria-valuemin="0" aria-valuemax="100"></div>
                </div>
                <p class="text-center">{{ $val }}%</p>
            </div>
            <div class="col-md-9">
                <p>Para avanzar en el porcentaje, tienes que sacar calificación mayor a 90 en los exámenes</p>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col-md-9">
                <livewire:mostrar-temas temas="{!! $temas->pluck('id') !!}" />
                {{-- <div class="table-responsive">
                    <table class="table table-sm table-stripped">
                        <thead>
                            <tr>
                                <th style="width:55%">
                                    <h2>Temas</h2>
                                </th>
                                <th style="width:15%" class="text-center">PDF</th>
                                <th style="width:15%" class="text-center">Videos</th>
                                <th style="width:15%" class="text-center">Ejercicios</th>
                                <th style="width:15%" class="text-center">Examen</th>
                                <th style="width:15%" class="text-center">Mejor calif.</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($temas as $t)
                                <tr class="bg-white">
                                    <td>
                                        <h5>{{ $t->nombre }}</h5>
                                    </td>
                                    <td class="text-center">
                                        @if ($t->pdf != null)
                                            <a href="#" data-toggle="modal" data-target="#mostrarVideo" class="mostrarPDF" data-url="{{ $t->pdf }}" data-nombre="{{ $t->nombre }}"><i class="fas fa-file-pdf fa-2x"></i></a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($t->video != null)
                                            <a href="#" data-toggle="modal" data-target="#mostrarVideo" class="mostrarVideo" data-url="{{ $t->video }}" data-nombre="{{ $t->nombre }}"><i class="fas fa-film fa-2x"></i></a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($t->preguntas->count() > 0)
                                            <a href="#" data-toggle="modal" data-nombre="{{ $t->nombre }}" data-target="#mostrarVideo" class="mostrarPreguntas button" tema_id="{{ $t->id }}"><i
                                                    class="fas fa-tasks fa-2x"></i></a>
                                        @endif
                                    </td>
                                    <td class="text-center">
                                        @if ($t->preguntar > 0)
                                            <a href="/examenes/-{{ $t->id }}">
                                                @if ($t->max >= 90)
                                                    <span class="text-secondary"><i class="fas fa-medal fa-2x"></i></span>
                                                @else
                                                    <i class="fas fa-flag-checkered fa-2x"></i>
                                                @endif
                                            </a>
                                        @endif
                                    </td>
                                    <td>
                                        {{ $t->max }}
                                    </td>
                                </tr>
                            @endforeach
                            @if ($temas->count() == 0)
                                <h4 class="text-center my-4">Estamos actualizando esta sección.</h4>
                            @endif
                        </tbody>
                    </table>
                </div> --}}
            </div>
        </div>
    </div>

    <div id="mostrarVideo" class="modal" tabindex="-1" role="dialog" aria-labelledby="myExtraLargeModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title"></h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <iframe width="100%" height="480" id="videoYoutube" src="#" frameborder="0" allow="accelerometer; autoplay; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                    <video controls class="w-100" controlsList="nodownload">
                        <source id="videoTema" src="" type="video/mp4">
                        Tu navegador no soporta videos.
                    </video>
                    {{-- https://stackoverflow.com/a/36234568/3113008 --}}
                    <iframe id="pdfTema" src = "" style="height:80vh; width:100%" allowfullscreen webkitallowfullscreen></iframe>
                    <div id="preguntasTema">
                        <p class="lead text-center" id="contenido"></p>
                        <div class="list-group" id="respuestas">
                            <div class="list-group-item list-group-item-action" id="respuesta">lala</div>
                        </div>
                        <div id="ayuda"></div>
                        <button class="btn btn-primary mt-2" id="revisar">Revisar</button>
                        <button class="btn btn-primary mt-2 mostrarPreguntas" id="siguiente">Siguiente</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection
