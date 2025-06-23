@extends('layouts.app')

@section('title', 'Détails du véhicule')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <h4 class="fw-bold py-3 mb-4">
        <span class="text-muted fw-light">Véhicules /</span> Détails du véhicule
    </h4>

    <div class="row">
        <div class="col-md-12">
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Détails du véhicule</h5>
                    <div class="btn-group">
                        <a href="{{ route('vehicules.edit', $vehicule->id) }}" class="btn btn-outline-primary btn-sm">
                            <i class="bx bx-edit-alt me-1"></i> Modifier
                        </a>
                        <a href="{{ route('vehicules.index') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="bx bx-arrow-back me-1"></i> Retour
                        </a>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <tbody>
                                <tr>
                                    <th style="width: 30%">ID</th>
                                    <td>{{ $vehicule->id }}</td>
                                </tr>
                                <tr>
                                    <th>Nom du véhicule</th>
                                    <td>{{ $vehicule->nom ?? 'Non spécifié' }}</td>
                                </tr>
                                <tr>
                                    <th>Prix d'achat</th>
                                    <td>{{ number_format($vehicule->prix_achat, 2, ',', ' ') }} €</td>
                                </tr>
                                <tr>
                                    <th>Prix de vente</th>
                                    <td>{{ number_format($vehicule->prix_vente, 2, ',', ' ') }} €</td>
                                </tr>

                                <tr>
                                    <th>Créé le</th>
                                    <td>{{ $vehicule->created_at->format('d/m/Y H:i') }}</td>
                                </tr>
                                <tr>
                                    <th>Dernière modification</th>
                                    <td>{{ $vehicule->updated_at->format('d/m/Y H:i') }}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
