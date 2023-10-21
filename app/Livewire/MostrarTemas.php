<?php

namespace App\Livewire;

use App\Models\Tema;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Concerns\InteractsWithTable;
use Filament\Tables\Contracts\HasTable;
use Filament\Tables\Table;
use Livewire\Component;

class MostrarTemas extends Component implements HasForms, HasTable
{
    use InteractsWithTable;
    use InteractsWithForms;

    public $temas;

    public function mount($temas){

        $this->temas = Tema::find(json_decode($temas));
    }

    public function table(Table $table): Table
    {
        return $table
            ->query($this->temas->toQuery())
            ->columns([
                TextColumn::make('nombre'),
                TextColumn::make('pdf'),
            ])
            ->filters([
                // ...
            ])
            ->actions([
                // ...
            ])
            ->bulkActions([
                // ...
            ]);
    }
    
    public function render()
    {
        return view('livewire.mostrar-temas');
    }
}
