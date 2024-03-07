<?php

namespace App\Livewire;

use Livewire\Component;

class VerContenido extends Component
{
    public $tema;
    public $i;
    public $completada;

    public function mount(){
        $this->completada = isset(auth()->user()->notes) && isset(auth()->user()->notes[$this->tema->id]) && array_key_exists($this->i, auth()->user()->notes[$this->tema->id]);
    }

    public function render()
    {
        return view('livewire.ver-contenido')->with([
            'tema' => $this->tema,
            'i' => $this->i,
            'completada' => $this->completada
        ]);
    }

    public function completar(){
        $notes = auth()->user()->notes;

        if (isset($notes) && isset($notes[$this->tema->id]) && array_key_exists($this->i, $notes[$this->tema->id])){
            unset($notes[$this->tema->id][$this->i]);
        } else {
            $notes[$this->tema->id][] = $this->i;
        }

        // Update the JSON column with the modified array
        auth()->user()->update([
            "notes" => $notes
        ]);

        $this->completada = !$this->completada;
        $this->dispatch('contenido_completado', id: $this->i);
    }
}
