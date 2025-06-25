@extends('layouts.app')

@section('title', 'Modifier mon profil DOJ')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mon compte /</span> Modifier mon profil DOJ
    </h4>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
    </div>
    @endif

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <h5 class="card-header">Modifier mon profil DOJ</h5>
                <div class="card-body">
                    <form method="POST" action="{{ route('profile.update') }}">
                        @csrf
                        @method('PATCH')
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <label for="nom" class="form-label">Nom</label>
                                <input class="form-control" type="text" id="nom" name="nom" value="{{ old('nom', $user->nom) }}" required />
                                @error('nom')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                            <div class="col-md-6">
                                <label for="prenom" class="form-label">Prénom</label>
                                <input class="form-control" type="text" id="prenom" name="prenom" value="{{ old('prenom', $user->prenom) }}" required />
                                @error('prenom')
                                <div class="text-danger">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="email" class="form-label">Adresse e-mail</label>
                            <input class="form-control" type="email" id="email" name="email" value="{{ old('email', $user->email) }}" required />
                            @error('email')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="statut" class="form-label">Statut</label>
                            <input class="form-control" type="text" id="statut" value="DOJ (Department of Justice)" disabled />
                            <input type="hidden" name="statut" value="{{ $user->statut }}" />
                        </div>
                        
                        <div class="mb-3">
                            <label for="commission" class="form-label">Taux de commission</label>
                            <div class="input-group">
                                <input class="form-control" type="text" id="commission" value="0%" disabled />
                                <span class="input-group-text">%</span>
                            </div>
                            <div class="form-text text-info">Les utilisateurs DOJ n'ont pas de commission.</div>
                            <input type="hidden" name="commission" value="0" />
                        </div>
                        
                        <hr class="my-4">
                        
                        <h5 class="mb-3">Changer le mot de passe</h5>
                        <div class="mb-3">
                            <label for="password" class="form-label">Nouveau mot de passe</label>
                            <input class="form-control" type="password" id="password" name="password" />
                            <div class="form-text">Laissez vide si vous ne souhaitez pas changer de mot de passe</div>
                            @error('password')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="password_confirmation" class="form-label">Confirmer le mot de passe</label>
                            <input class="form-control" type="password" id="password_confirmation" name="password_confirmation" />
                            @error('password_confirmation')
                            <div class="text-danger">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mt-4">
                            <button type="submit" class="btn btn-primary me-2">Enregistrer les modifications</button>
                            <a href="{{ route('users.tableau-de-bord') }}" class="btn btn-outline-secondary">Annuler</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
