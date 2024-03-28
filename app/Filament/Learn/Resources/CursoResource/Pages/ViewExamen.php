<?php

namespace App\Filament\Learn\Resources\CursoResource\Pages;

use App\Filament\Learn\Resources\CursoResource;
use App\Models\Intento;
use App\Models\Pregunta;
use App\Models\Prueba;
use App\Models\Tema;
use Carbon\Carbon;
use Filament\Resources\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;

class ViewExamen extends Page
{
    protected static string $resource = CursoResource::class;

    protected static string $view = 'filament.learn.resources.curso-resource.pages.view-examen';

    public $modulo, $tema, $prueba, $preguntas, $intento;

    public function mount(int | string $record, $id)
    {
        if ($id > 0) {
            $prueba = Prueba::find($id);
            if (Auth::user()->rol_id == 1 or (Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $prueba->curso->id)->where('fin', '>=', Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)) {
                $preguntas_ids = Cache::remember("preguntas_prueba$id", 3600, function () use ($prueba) {
                    return Pregunta::whereIn('tema_id', $prueba->temas->where('pivot.preguntas', '>', 0)->pluck('id'))
                        ->pluck('id');
                    // ->sortBy(function($order) use($prueba){
                    //     return array_search($order['tema_id'], $prueba->temas->pluck('id')->toArray());
                    // });
                });
                $preguntas = Pregunta::select('id', 'contenido', 'tema_id')->whereIn('id', $preguntas_ids)
                    ->with(['respuestas' => function ($query) {
                        $query->select('id', 'contenido', 'pregunta_id')->inRandomOrder();
                    }])->get();
                $groups = $preguntas->groupby('tema_id');
                $groups_t = $groups;
                foreach ($groups as $key => $g) {
                    $total = $prueba->temas->find($key)->pivot->preguntas;
                    $groups_t[$key] = $g->random($total);
                }
                $preguntas = $groups_t->flatten();

                $intento = new Intento();
                $intento->prueba_id = $prueba->id;
                $intento->user_id = Auth::id();
                $intento->preguntas = json_encode($preguntas->pluck('id')->toArray());
                $intento->calificacion = -1;
                $intento->aciertos = -1;
                $intento->save();

                $this->prueba = $prueba;
                $this->preguntas = $preguntas;
                $this->intento = $intento;
            } else {
                return redirect('/pagos/crear?curso_id=' . $prueba->curso->id);
            }
        } else {
            $id = $id * -1;
            $tema = Tema::find($id);
            if (Auth::user()->rol_id == 1 or (Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon::today())->count() > 0 and Auth::user()->pagos->where('curso_id', $tema->modulo->curso->id)->where('fin', '>=', Carbon::today())->sortByDesc('promo_id')->first()->promo->examenes == true)) {
                $preguntas_ids = Cache::remember("preguntas_prueba$id", 3600, function () use ($tema) {
                    return Pregunta::whereIn('id', $tema->preguntas->pluck('id'))->pluck('id');
                });
                $preguntas = Pregunta::select('id', 'contenido', 'tema_id')->whereIn('id', $preguntas_ids)
                    ->with(['respuestas' => function ($query) {
                        $query->select('id', 'contenido', 'pregunta_id')->inRandomOrder();
                    }])
                    ->get();
                $preguntas = $preguntas->take($tema->preguntar);
                $prueba = new Prueba;
                $prueba->id = $tema->id * -1;
                $prueba->nombre = $tema->nombre;
                $prueba->curso_id = $tema->modulo->curso->id;

                $intento = new Intento();
                $intento->prueba_id = $prueba->id;
                $intento->user_id = Auth::id();
                $intento->preguntas = json_encode($preguntas->pluck('id')->toArray());
                $intento->calificacion = -1;
                $intento->aciertos = -1;
                $intento->save();

                $this->modulo = $tema->modulo;
                $this->tema = $tema;
                $this->prueba = $prueba;
                $this->preguntas = $preguntas;
                $this->intento = $intento;
            }
        }
    }
}
