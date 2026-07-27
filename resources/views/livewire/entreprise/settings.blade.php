<div>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">Informations de l'entreprise</h3>
        </div>
        <form wire:submit="save">
            <div class="card-body">
                @if (session('status'))
                    <div class="alert alert-success">{{ session('status') }}</div>
                @endif

                <div class="row">
                    <div class="col-md-8">
                        <div class="form-group">
                            <label>Nom / Raison sociale</label>
                            <input type="text" wire:model="nom" class="form-control">
                            @error('nom') <span class="text-danger">{{ $message }}</span> @enderror
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>NINEA</label>
                                <input type="text" wire:model="ninea" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Devise</label>
                                <input type="text" wire:model="devise" class="form-control" maxlength="10">
                                @error('devise') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Adresse / Siège social</label>
                            <input type="text" wire:model="adresse" class="form-control">
                        </div>
                        <div class="row">
                            <div class="col-md-6 form-group">
                                <label>Téléphone</label>
                                <input type="text" wire:model="telephone" class="form-control">
                            </div>
                            <div class="col-md-6 form-group">
                                <label>Email</label>
                                <input type="email" wire:model="email" class="form-control">
                                @error('email') <span class="text-danger">{{ $message }}</span> @enderror
                            </div>
                        </div>
                        <div class="form-group">
                            <label>Site web</label>
                            <input type="text" wire:model="site_web" class="form-control">
                        </div>
                    </div>
                    <div class="col-md-4 text-center">
                        <label class="d-block">Logo</label>
                        @if ($logo)
                            <img src="{{ $logo->temporaryUrl() }}" class="img-fluid mb-2" style="max-height: 150px;">
                        @elseif ($currentLogoUrl)
                            <img src="{{ $currentLogoUrl }}" class="img-fluid mb-2" style="max-height: 150px;">
                        @else
                            <div class="text-muted mb-2">Aucun logo</div>
                        @endif
                        <input type="file" wire:model="logo" accept="image/*" class="form-control-file">
                        <div wire:loading wire:target="logo" class="text-muted small mt-1">Envoi en cours...</div>
                        @error('logo') <span class="text-danger d-block">{{ $message }}</span> @enderror
                    </div>
                </div>
            </div>
            <div class="card-footer">
                @can('entreprise.update')
                    <button type="submit" class="btn btn-primary">Enregistrer</button>
                @endcan
            </div>
        </form>
    </div>
</div>
