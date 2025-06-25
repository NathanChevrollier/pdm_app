<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Afficher le formulaire de modification du profil.
     */
    public function edit(Request $request): View
    {
        $user = $request->user();
        
        // Si l'utilisateur est un DOJ, afficher une vue spécifique
        if ($user->statut === 'doj') {
            // Vérifier si on demande la modification du profil
            if ($request->has('edit')) {
                return view('profile.doj-edit', [
                    'user' => $user,
                ]);
            }
            
            // Sinon, afficher la vue de consultation du profil DOJ
            return view('users.doj-tableau-de-bord', [
                'employe' => $user,
            ]);
        }
        
        // Sinon, afficher la vue standard
        return view('profile.edit', [
            'user' => $user,
        ]);
    }

    /**
     * Mettre à jour les informations du profil de l'utilisateur.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();
        $data = $request->validated();

        // Mise à jour des informations de base
        $user->fill([
            'nom' => $data['nom'],
            'email' => $data['email'],
        ]);
        
        // Si le prénom est fourni, le mettre à jour
        if (isset($data['prenom'])) {
            $user->prenom = $data['prenom'];
        }

        // Mise à jour du mot de passe si fourni
        if (isset($data['password']) && $data['password']) {
            $user->password = Hash::make($data['password']);
        }

        // Réinitialiser la vérification d'email si l'email a changé
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // Redirection spécifique pour les utilisateurs DOJ
        if ($user->statut === 'doj') {
            return redirect()->route('users.tableau-de-bord')
                ->with('success', 'Votre profil a été mis à jour avec succès.');
        }

        // Redirection standard pour les autres utilisateurs
        return redirect()->route('profile.edit')
            ->with('success', 'Votre profil a été mis à jour avec succès.');
    }

    /**
     * Supprimer le compte de l'utilisateur.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('status', 'Votre compte a été supprimé avec succès.');
    }
}
