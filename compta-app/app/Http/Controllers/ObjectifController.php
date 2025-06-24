<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Objectif;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class ObjectifController extends Controller
{
    /**
     * Mettre à jour les objectifs globaux
     */
    public function update(Request $request)
    {
        // Vérifier les permissions (seuls admin, gérant, co-gérant peuvent modifier les objectifs)
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant'])) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }

        // Valider les données
        $validated = $request->validate([
            'objectif_ventes' => 'required|numeric|min:0',
            'objectif_vehicules' => 'required|integer|min:0',
            'objectif_commission' => 'required|numeric|min:0',
            'objectif_benefice' => 'required|numeric|min:0',
        ]);

        // Récupérer ou créer les objectifs globaux
        $objectifs = Objectif::getActiveObjectifs();
        
        // Mettre à jour les objectifs
        $objectifs->update([
            'objectif_ventes' => $validated['objectif_ventes'],
            'objectif_vehicules' => $validated['objectif_vehicules'],
            'objectif_commission' => $validated['objectif_commission'],
            'objectif_benefice' => $validated['objectif_benefice'],
        ]);

        // Rediriger avec un message de succès
        return redirect()->route('dashboard')
            ->with('success', 'Les objectifs globaux ont été mis à jour avec succès.');
    }

    /**
     * Mettre à jour les objectifs individuels d'un employé
     */
    public function updateUserObjectifs(Request $request, $userId)
    {
        // Vérifier les permissions
        if (!auth()->user() || !in_array(auth()->user()->statut, ['admin', 'gerant', 'co-gerant', 'manager'])) {
            return redirect()->back()
                ->with('error', 'Vous n\'avez pas les permissions nécessaires pour effectuer cette action.');
        }

        // Valider les données
        $validated = $request->validate([
            'objectif_ventes' => 'required|numeric|min:0',
            'objectif_vehicules' => 'required|integer|min:0',
            'objectif_commission' => 'required|numeric|min:0',
        ]);

        // Mettre à jour les objectifs de l'employé
        $employe = User::findOrFail($userId);
        $employe->update([
            'objectif_ventes' => $validated['objectif_ventes'],
            'objectif_vehicules' => $validated['objectif_vehicules'],
            'objectif_commission' => $validated['objectif_commission'],
        ]);

        // Rediriger avec un message de succès
        return redirect()->route('users.show', $employe->id)
            ->with('success', 'Les objectifs de l\'employé ont été mis à jour avec succès.');
    }
}
