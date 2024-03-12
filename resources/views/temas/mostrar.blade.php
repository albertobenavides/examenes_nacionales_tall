@extends('temas.base')

@push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            @if ($tema->contenido != null)
                let currentParagraphName = document.getElementById('current-paragraph-name');
                let currentParagraphPercent = document.getElementById('current-paragraph-percent');

                new ScrollProgress.Init(
                    "#cursor",
                    "#menu",
                    progress => {
                        try {
                            let value_t = document.getElementById(progress.Id + '-p').value;
                            document.getElementById(progress.Id + '-p').value = (value_t < progress.Percent) ? progress.Percent : value_t;
                        } catch (error) {
                            //
                        }
                    },
                    id => {
                        document.querySelectorAll('a[href*="embebed-"]').forEach(element => element.classList.remove('active-meny-item'));
                        document.querySelector(`[href="#${id}"]`).classList.add('active-meny-item');
                    }
                );
            @endif
        });
    </script>
@endpush

@section('contenido')
    <div class="px-2">
        <h1 class="lead">{{ $tema->nombre }}</h1>
        <hr>
        @if ($tema->contenido != null)
            @for ($i = 0; $i < count($tema->contenido); $i++)
                <livewire:ver-contenido :tema="$tema" :i="$i" />
            @endfor
        @endif
    </div>
@endsection
