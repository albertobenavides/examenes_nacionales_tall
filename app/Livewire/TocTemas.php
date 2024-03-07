<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class TocTemas extends Component
{
    public $tema;
    public $i;
    public $completada;

    public function mount(){
        $this->completada = isset(auth()->user()->notes[$this->tema->id][$this->i]);
    }

    public function render()
    {
        return view('livewire.toc-temas')->with([
            'tema' => $this->tema,
            'i' => $this->i,
            'completada' => $this->completada
        ]);
    }

    #[On('contenido_completado')]
    public function contenido_completado($id) {
        if ($this->i == $id) {
            $this->completada = !$this->completada;
        }
    }
}
