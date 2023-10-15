@extends('layouts.app')

@section('scripts')
    <script>
        $(document).ready(function() {
            $('#login_modal').modal('show');
        });
    </script>
@endsection

@section('content')
    <div class="modal" tabindex="-1" role="dialog" id="login_modal">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Iniciar sesión</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <form method="POST" action="#">
                        @csrf
                        <div class="form-group row">
                            <label for="email" class="col-md-4 col-form-label text-md-right">{{ __('E-Mail Address') }}</label>
                            <div class="col-md-6">
                                <input id="email" type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email') }}" required autocomplete="email" autofocus>
                                @error('email')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <label for="password" class="col-md-4 col-form-label text-md-right">{{ __('Password') }}</label>
                            <div class="col-md-6">
                                <input id="password" type="password" class="form-control @error('password') is-invalid @enderror" name="password" required autocomplete="current-password">
                                @error('password')
                                    <span class="invalid-feedback" role="alert">
                                        <strong>{{ $message }}</strong>
                                    </span>
                                @enderror
                            </div>
                        </div>

                        <div class="form-group row">
                            <div class="col-md-6 offset-md-4">
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="remember" id="remember" {{ old('remember') ? 'checked' : '' }}>
                                    <label class="form-check-label" for="remember">
                                        {{ __('Remember Me') }}
                                    </label>
                                </div>
                            </div>
                        </div>

                        <div class="form-group row mb-0">
                            <div class="col-md-8 offset-md-4">
                                <button type="submit" class="btn btn-primary">
                                    {{ __('Login') }}
                                </button>

                                @if (Route::has('password.request'))
                                    <a class="btn btn-link" href="{{ route('password.request') }}">
                                        {{ __('Forgot Your Password?') }}
                                    </a>
                                @endif
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div id="banner" class="carousel slide" data-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                @if ($settings->portada1)
                    <img src="/storage/{{ $settings->portada1 }}" class="d-block w-100" alt="...">
                @else
                    <img src="/img/banner2.jpg" class="d-block w-100" alt="...">
                @endif
            </div>
            <div class="carousel-item">
                @if ($settings->portada2)
                    <img src="/storage/{{ $settings->portada2 }}" class="d-block w-100" alt="...">
                @else
                    <img src="/img/banner3.jpg" class="d-block w-100" alt="...">
                @endif
            </div>
            <div class="carousel-item">
                @if ($settings->portada3)
                    <img src="/storage/{{ $settings->portada3 }}" class="d-block w-100" alt="...">
                @else
                    <img src="/img/banner4.jpg" class="d-block w-100" alt="...">
                @endif
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#banner" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#banner" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Siguiente</span>
        </button>
    </div>
    <div class="jumbotron bg-secondary d-flex justify-content-center">
        <div class="container row">
            <div class="col-lg-9">
                <p class="display-6">Nuestros cursos te preparan para cualquier examen de admisión de México.</p>
            </div>
            <div class="col-lg-3">
                <a class="btn btn-light btn-lg btn-block" href="#" role="button">Conoce más</a>
            </div>
        </div>
    </div>

    <div class="container">
        <span class="display-5">Contamos con todos los exámenes</span>
        <div class="d-flex align-content-md-start flex-wrap">
            @foreach (App\Models\Examen::select(['nombre', 'descripcion'])->get() as $e)
                <div class="col-lg-4 mb-3">
                    <div class="bg-white p-3" style="height:13rem">
                        <h5>{{ $e->nombre }}</h5>
                        <p>{{ $e->descripcion }}</p>
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <div class="jumbotron bg-dark text-white my-5 d-flex justify-content-center">
        <div class="container row">
            <div class="col-lg-4">
                <h1 class="display-5 text-white">Contáctanos</h1>
                <p class="lead">¿Dudas, comentarios? Escríbanos en los campos marcados y nos pondremos en contacto con usted a la brevedad.</p>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <input class="form-control form-control-lg" type="text" name="nombre" placeholder="Nombre completo" form="contacto">
                </div>
                <div class="form-group">
                    <input class="form-control form-control-lg" type="text" name="telefono" placeholder="Teléfono" form="contacto">
                </div>
            </div>
            <div class="col-lg-4">
                <div class="form-group">
                    <input class="form-control form-control-lg" type="email" name="correo" placeholder="Correo electrónico" form="contacto">
                </div>
                <button class="btn btn-lg btn-primary text-white" type="submit">
                    Enviar <span class="text-secondary"><i class="fas fa-chevron-right"></i></span>
                </button>
            </div>
        </div>
        <form action="/contacto" method="post" id="contacto">
            @csrf
        </form>
    </div>
@endsection
