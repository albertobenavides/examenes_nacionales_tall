<div>
	<div>
		<div class="row">
			<div class="col-md-6">
				<label for="siglasInstitucion">Siglas</label>
				<input type="text" wire:model="siglas" class="form-control form-control-sm" placeholder="Siglas" required>
			</div>
			<div class="col-md-6">
				<label for="nombreInstitucion">Nombre</label>
				<input type="text" wire:model="nombre" class="form-control form-control-sm" placeholder="Nombre de la institución" required>
				@error('nombre') <small class="text-danger">{{ $message }}</small> @enderror
			</div>
		</div>
		<div class="row mt-2">
			<div class="col-md-4">
				<label for="estadoInstitucion">Estado</label>
				<input type="text" wire:model="estado" class="form-control form-control-sm" placeholder="Estado " required>
			</div>
			<div class="col-md-4">
				<label for="paisInstitucion">País</label>
				<input type="text" wire:model="pais" class="form-control form-control-sm" placeholder="País" required>                                           
			</div>
			<div class="col-md-4">
				<label>Curso asociado</label>
				<select wire:model="examen_id" class="custom-select custom-select-sm" required>
					<option value="-1" selected disabled>Elige</option>
					@foreach(App\Curso::select(['id', 'nombre'])->get() as $e)
						<option value="{{$e->id}}">{{$e->nombre}}</option>
					@endforeach
				</select>
			</div>
		</div>
		<div class="mt-3 text-center">
			<label>Imagen</label><br>
			@if ($imagen != null)
				<img src="{{Str::contains($imagen, 'instituciones') ? '/storage/' . $imagen : $imagen->temporaryUrl()}}" class="img-fluid">
			@else
				<img src="https://fakeimg.pl/300x113/?text=300x113" class="img-fluid">
			@endif
			<br>
			<input type="file" wire:model="imagen" class="mt-1" id="i_imagen{{$i}}">
		</div>
		<div class="text-center my-3">
			<button wire:click="save" type="submit" class="btn btn-sm btn-success mt-2">{{$c_edit ? 'Editar' : 'Añadir'}} institución</button>
			@if ($c_edit)
				<button wire:click="resetFilters" type="submit" class="btn btn-sm btn-secondary mt-2">Cancelar</button>
			@endif
		</div>
	</div>
	<div class="table-responsive">
		<table class="table text-nowrap">
			<thead>
				<th>#</th>
				<th>Siglas</th>
				<th>Estado</th>
				<th>País</th>
				<th>Curso</th>
				<td>Acciones</td>
			</thead>
			<tbody>
				@foreach($instituciones as $i)
				<tr>
					<td>{{$i->id}}</td>
					<td>{{$i->siglas}}</td>
					<td>{{$i->estado}}</td>
					<td>{{$i->pais}}</td>
					<td>{{$i->curso ? $i->curso->nombre : ''}}</td>
					<td>
						<button wire:click="edit({{$i->id}})" type="button" class="btn btn-primary btn-sm">
							<i class="fas fa-edit"></i>
						</button>
						<button wire:click="confirmDelete({{$i->id}})" type="button" class="btn btn-sm btn-danger"><i class="fas fa-trash"></i></button>
					</td>
				</tr>
				@endforeach
			</tbody>
		</table>
	</div>
	
	{{-- Modales --}}
	<div class="modal" id="borrarInstitucionModal" tabindex="-1" aria-labelledby="borrarInstitucionLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="borrarInstitucionLabel">Borrar institución</h5>
					<button type="button" class="close" data-dismiss="modal" aria-label="Close">
						<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					Confirma que quieres borrar <b>{{$this->nombre}}</b>?
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-secondary" data-dismiss="modal">No</button>
					<button type="button" wire:click="delete" class="btn btn-primary" data-dismiss="modal">Sí</button>
				</div>
			</div>
		</div>
	</div>
</div>
