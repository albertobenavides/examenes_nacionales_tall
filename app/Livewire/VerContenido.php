<?php

namespace App\Livewire;

use Livewire\Attributes\On;
use Livewire\Component;

class VerContenido extends Component
{
    public $tema;
    public $i;
    public $completada;

    public function mount(){
        $this->completada = isset(auth()->user()->notes) && isset(auth()->user()->notes[$this->tema->id]) && in_array($this->i, auth()->user()->notes[$this->tema->id]);
    }

    public function render()
    {
        return view('livewire.ver-contenido')->with([
            'tema' => $this->tema,
            'i' => $this->i,
            'completada' => $this->completada
        ]);
    }

    #[On('completar')]
    public function completar($i = null){
        if ($i == $this->i || $i == null){
            $notes = auth()->user()->notes;
    
            if (!isset($notes[$this->tema->id])) {
                $notes[$this->tema->id] = [];
            }
    
            if (in_array($this->i, $notes[$this->tema->id])){
                $key = array_search($this->i, $notes[$this->tema->id]);
                array_splice($notes[$this->tema->id], $key, 1);
            } else {
                array_push($notes[$this->tema->id], $this->i);
            }
    
            // Update the JSON column with the modified array
            $u = auth()->user();
            $u->notes = $notes;
            $u->save();
    
            $this->completada = !$this->completada;
            $this->dispatch('contenido_completado', id: $this->i);
        }
    }
}
