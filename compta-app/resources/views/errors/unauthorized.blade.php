@extends('layouts.app')

@section('content')
<div class="container-xxl flex-grow-1 container-p-y">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body">
                    <div class="text-center mb-4">
                        <h1 class="display-1 text-danger">403</h1>
                        <h4 class="text-primary">Accès non autorisé</h4>
                        <p class="mb-4">{{ $message ?? 'Vous n\'avez pas les permissions nécessaires pour accéder à cette ressource.' }}</p>
                        <a href="{{ route('dashboard') }}" class="btn btn-primary">Retour au tableau de bord</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
