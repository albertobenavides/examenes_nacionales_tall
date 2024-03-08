@push('scripts')
    <script>
        @if ($tema->contenido[$i]['type'] == 'h5p')
            setTimeout(function() {
                let iframe = document.getElementById('embebed-{{ $i }}').nextElementSibling.contentWindow;
                iframe.H5P.externalDispatcher.on('xAPI', function(event) {
                    if (typeof event.data.statement.result !== 'undefined') {
                        let score = event.data.statement.result.score;
                        if (score.scaled > 0.9) {
                            let i = {{ $i }};
                            if (!Livewire.getByName('ver-contenido')[i].get('completada')) {
                                Livewire.dispatch('completar', {i: i});
                            }
                            confetti({
                                particleCount: 100,
                                spread: 70,
                                origin: {
                                    y: 0.6
                                }
                            });
                            var audio = new Audio('/sounds/success.mp3');
                            audio.play();
                        } else {
                            var audio = new Audio('/sounds/error.mp3');
                            audio.play();
                        }
                    }
                });
            }, 500);
        @endif
    </script>
@endpush
<div>
    <h2 class="lead mt-3" id='embebed-{{ $i }}'>{{ $tema->contenido[$i]['data']['titulo'] ?? 'Sección ' . $i + 1 }}</h2>
    @if ($tema->contenido[$i]['type'] == 'texto')
        {!! $tema->contenido[$i]['data']['texto'] !!}
    @elseif ($tema->contenido[$i]['type'] == 'h5p')
        <iframe onload="this.height=this.contentWindow.document.body.scrollHeight * 1.5;" src="/storage/{{ $tema->contenido[$i]['data']['h5p'] }}" class="w-full" frameborder="0"></script>
        </iframe>
        <p class="bg-white rounded"></p>
    @elseif ($tema->contenido[$i]['type'] == 'embebido')
        <p class="flex justify-center">
            {!! $tema->contenido[$i]['data']['embebido'] !!}
        </p>
    @elseif ($tema->contenido[$i]['type'] == 'video')
        <p class="flex justify-center">
            <video controls class="my-5">
                <source src="/storage/{{ $tema->contenido[$i]['data']['video'] }}" type="video/mp4">
            </video>
        </p>
    @endif
    @if ($tema->contenido[$i]['type'] != 'h5p')
        <div class="text-center flex justify-end">
            <button class="btn btn-primary text-center " wire:click="completar()">
                @if ($completada)
                    Desmarcar
                @else
                    Completado
                @endif
            </button>
        </div>
    @endif
</div>
