<?php

namespace App\Observers;

use App\Models\Intento;
use App\Models\Tema;

class IntentoObserver
{
    /**
     * Handle the Intento "saved" event.
     */
    public function saved(Intento $intento): void
    {
        $usuario = $intento->usuario;
        if ($usuario->hasRole('alumno') && ($usuario->pagos->count() > 0)){
            $intentos = Intento::where('user_id', $usuario->user_id)->where('calificacion',  '>=', '90')
                ->get(['prueba_id', 'calificacion'])->flatten()
                ->sortByDesc('calificacion')->groupBy('prueba_id')->flatten()->unique('prueba_id')->count();
            $temas = Tema::whereIn('modulo_id', $usuario->pagos->first()->curso->modulos->pluck('id'))->where('preguntar', '>', 0)->count();
            $usuario->update(['notes->avance' => round(($intentos / $temas * 100), 2) . '%']);
            $usuario->save();
        }
    }

    /**
     * Handle the Intento "updated" event.
     */
    public function updated(Intento $intento): void
    {
        //
    }

    /**
     * Handle the Intento "deleted" event.
     */
    public function deleted(Intento $intento): void
    {
        //
    }

    /**
     * Handle the Intento "restored" event.
     */
    public function restored(Intento $intento): void
    {
        //
    }

    /**
     * Handle the Intento "force deleted" event.
     */
    public function forceDeleted(Intento $intento): void
    {
        //
    }
}
