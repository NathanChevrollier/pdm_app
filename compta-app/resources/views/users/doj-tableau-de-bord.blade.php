@extends('layouts.app')

@section('title', 'Mon Profil DOJ')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Mon compte /</span> Profil DOJ
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
                <h5 class="card-header">Informations du profil DOJ</h5>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Nom</label>
                                <input type="text" class="form-control" value="{{ $employe->nom }}" disabled>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <div class="form-group">
                                <label class="form-label">Prénom</label>
                                <input type="text" class="form-control" value="{{ $employe->prenom }}" disabled>
                            </div>
                        </div>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Adresse e-mail</label>
                        <input type="email" class="form-control" value="{{ $employe->email }}" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Statut</label>
                        <input type="text" class="form-control" value="DOJ (Department of Justice)" disabled>
                    </div>
                    
                    <div class="mb-3">
                        <label class="form-label">Taux de commission</label>
                        <div class="input-group">
                            <input type="text" class="form-control" value="0%" disabled>
                            <span class="input-group-text">%</span>
                        </div>
                        <div class="form-text text-info">Les utilisateurs DOJ n'ont pas de commission.</div>
                    </div>
                    
                    <div class="mt-4">
                        <a href="{{ route('profile.edit', ['edit' => 'true']) }}" class="btn btn-primary me-2">Modifier mon profil</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
