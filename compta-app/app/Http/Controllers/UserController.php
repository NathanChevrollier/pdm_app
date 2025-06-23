<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(): View
    {
        // Récupérer tous les utilisateurs et les trier par statut (hiérarchie)
        $users = User::orderByRaw("CASE 
            WHEN statut = 'admin' THEN 1
            WHEN statut = 'gerant' THEN 2
            WHEN statut = 'co-gerant' THEN 3
            WHEN statut = 'manager' THEN 4
            WHEN statut = 'vendeur' THEN 5
            WHEN statut = 'stagiaire' THEN 6
            ELSE 7 END")
            ->paginate(10);
        $currentUser = auth()->user();
        
        return view('users.index', compact('users', 'currentUser'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(): View
    {
        $currentUser = auth()->user();
        $currentUserStatutLevel = User::$statutsHierarchie[$currentUser->statut] ?? 999;
        
        // Vérifier que l'utilisateur a au moins le niveau manager (niveau 4)
        if ($currentUserStatutLevel > 4) { // Plus grand = plus bas dans la hiérarchie
            return view('errors.unauthorized', [
                'message' => 'Vous devez être au moins manager pour créer des utilisateurs.'
            ]);
        }
        
        $statuts = array_keys(User::$statutsHierarchie);
        return view('users.create', compact('statuts', 'currentUser'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request): RedirectResponse
    {
        $statutsValides = array_keys(User::$statutsHierarchie);
        
        $validated = $request->validate([
            'nom' => 'required|string|max:255',
            'prenom' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
            'statut' => 'required|string|in:' . implode(',', $statutsValides),
            'commission' => 'nullable|numeric|min:0|max:100',
        ]);

        // Vérifier que l'utilisateur actuel a le droit de créer un utilisateur
        $currentUser = auth()->user();
        $requestedStatutLevel = User::$statutsHierarchie[$request->statut] ?? 999;
        $currentUserStatutLevel = User::$statutsHierarchie[$currentUser->statut] ?? 999;
        
        // Vérifier que l'utilisateur a au moins le niveau manager (niveau 4)
        if ($currentUserStatutLevel > 4) { // Plus grand = plus bas dans la hiérarchie
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous devez être au moins manager pour créer des utilisateurs.');
        }
        
        // Vérifier que l'utilisateur ne crée pas un utilisateur avec un statut supérieur au sien
        if ($requestedStatutLevel < $currentUserStatutLevel) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas créer un utilisateur avec un statut supérieur au vôtre.');
        }
        
        // Vérifier que l'utilisateur ne crée pas un utilisateur avec le même statut que lui
        // Exception pour les admin et gérant qui peuvent créer des utilisateurs de même niveau
        if ($requestedStatutLevel == $currentUserStatutLevel && $currentUserStatutLevel > 2) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas créer un utilisateur avec le même statut que vous.');
        }
        
        // Si la commission n'est pas spécifiée, utiliser la valeur par défaut pour le statut
        if (empty($validated['commission'])) {
            $validated['commission'] = User::getDefaultCommissionRate($validated['statut']);
        }

        // Hasher le mot de passe
        $validated['password'] = Hash::make($validated['password']);
        
        // Créer l'utilisateur
        User::create($validated);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur créé avec succès.');
    }

    /**
     * Display the specified resource.
     */
    public function show(User $user): View
    {
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(User $user): View
    {
        $currentUser = auth()->user();
        $currentUserStatutLevel = User::$statutsHierarchie[$currentUser->statut] ?? 999;
        
        // Vérifier si l'utilisateur modifie son propre profil
        $isOwnProfile = $currentUser->id === $user->id;
        
        // Si ce n'est pas son propre profil, vérifier les permissions
        if (!$isOwnProfile) {
            // Vérifier que l'utilisateur a au moins le niveau co-gérant (niveau 3)
            if ($currentUserStatutLevel > 3) {
                return view('errors.unauthorized', [
                    'message' => 'Vous devez être au moins co-gérant pour modifier d\'autres utilisateurs.'
                ]);
            }
            
            // Vérifier que l'utilisateur a un statut supérieur à celui qu'il veut modifier
            $userToEditStatutLevel = User::$statutsHierarchie[$user->statut] ?? 999;
            if ($currentUserStatutLevel >= $userToEditStatutLevel) {
                return view('errors.unauthorized', [
                    'message' => 'Vous ne pouvez modifier que les utilisateurs avec un statut inférieur au vôtre.'
                ]);
            }
        }
        
        $statuts = array_keys(User::$statutsHierarchie);
        return view('users.edit', compact('user', 'statuts', 'currentUser', 'isOwnProfile'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user): RedirectResponse
    {
        $currentUser = auth()->user();
        $statutsValides = array_keys(User::$statutsHierarchie);
        $currentUserStatutLevel = User::$statutsHierarchie[$currentUser->statut] ?? 999;
        
        // Vérifier si l'utilisateur modifie son propre profil
        $isOwnProfile = $currentUser->id === $user->id;
        $isAdmin = $currentUser->statut === 'admin';
        
        // Si ce n'est pas son propre profil, vérifier les permissions
        if (!$isOwnProfile) {
            // Vérifier que l'utilisateur a au moins le niveau manager (niveau 4)
            if ($currentUserStatutLevel > 4) {
                return redirect()->route('users.index')
                    ->with('error', 'Vous devez être au moins manager pour modifier d\'autres utilisateurs.');
            }
            
            // Vérifier que l'utilisateur a un statut supérieur à celui qu'il veut modifier
            $userToEditStatutLevel = User::$statutsHierarchie[$user->statut] ?? 999;
            if ($currentUserStatutLevel >= $userToEditStatutLevel) {
                return redirect()->route('users.index')
                    ->with('error', 'Vous ne pouvez modifier que les utilisateurs avec un statut inférieur au vôtre.');
            }
        }
        
        // Si l'utilisateur modifie son propre profil, il ne peut pas changer son statut
        if ($isOwnProfile && $user->statut !== $request->statut) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas modifier votre propre statut.');
        }
        
        // Définir les règles de validation en fonction du profil de l'utilisateur
        $validationRules = [];
        
        // Seul l'admin peut modifier le nom, prénom et email d'un autre utilisateur
        if ($isOwnProfile || $isAdmin) {
            $validationRules['nom'] = 'required|string|max:255';
            $validationRules['prenom'] = 'required|string|max:255';
            $validationRules['email'] = [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($user->id),
            ];
        } else {
            // Pour les non-admins qui modifient d'autres utilisateurs, conserver les valeurs existantes
            $validated['nom'] = $user->nom;
            $validated['prenom'] = $user->prenom;
            $validated['email'] = $user->email;
        }
        
        // Tous les utilisateurs peuvent modifier le statut et la commission selon les règles
        $validationRules['statut'] = 'required|string|in:' . implode(',', $statutsValides);
        $validationRules['commission'] = 'nullable|numeric|min:0|max:100';
        
        // Valider les données selon les règles définies
        $validatedData = $request->validate($validationRules);
        
        // Fusionner les données validées avec les valeurs existantes pour les champs non modifiables
        if (!empty($validated)) {
            $validatedData = array_merge($validated, $validatedData);
        }
        
        // Vérifier que l'utilisateur ne tente pas de définir un statut supérieur au sien
        $requestedStatutLevel = User::$statutsHierarchie[$request->statut] ?? 999;
        
        if ($requestedStatutLevel < $currentUserStatutLevel) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas attribuer un statut supérieur au vôtre.');
        }
        
        // Vérifier que l'utilisateur ne définit pas un statut égal au sien
        // Exception pour les admin et gérant qui peuvent créer des utilisateurs de même niveau
        if (!$isOwnProfile && $requestedStatutLevel == $currentUserStatutLevel && $currentUserStatutLevel > 2) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas attribuer le même statut que le vôtre.');
        }
        
        // Vérifier que l'utilisateur ne définit pas une commission supérieure à la sienne
        $currentUserCommission = $currentUser->commission ?: User::getDefaultCommissionRate($currentUser->statut);
        $requestedCommission = $request->filled('commission') ? $request->commission : User::getDefaultCommissionRate($request->statut);
        
        if (!$isAdmin && $requestedCommission > $currentUserCommission) {
            return redirect()->back()
                ->withInput()
                ->with('error', 'Vous ne pouvez pas attribuer une commission supérieure à la vôtre (' . $currentUserCommission . '%).');
        }
        
        // Si la commission n'est pas spécifiée, utiliser la valeur par défaut pour le statut
        if (empty($validatedData['commission'])) {
            $validatedData['commission'] = User::getDefaultCommissionRate($validatedData['statut']);
        }

        // Seul l'admin peut modifier le mot de passe d'un autre utilisateur
        if ($request->filled('password') && ($isOwnProfile || $isAdmin)) {
            $request->validate([
                'password' => 'required|string|min:8|confirmed',
            ]);
            $validatedData['password'] = Hash::make($request->password);
        }

        $user->update($validatedData);

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur mis à jour avec succès.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user): RedirectResponse
    {
        $currentUser = auth()->user();
        
        // Empêcher la suppression de son propre compte
        if ($user->id === $currentUser->id) {
            return redirect()->route('users.index')
                            ->with('error', 'Vous ne pouvez pas supprimer votre propre compte.');
        }
        
        // Vérifier que l'utilisateur actuel a le droit de supprimer cet utilisateur
        if (!$currentUser->hasHigherOrEqualStatutThan($user)) {
            return redirect()->route('users.index')
                            ->with('error', 'Vous ne pouvez pas supprimer un utilisateur avec un statut supérieur au vôtre.');
        }

        $user->delete();

        return redirect()->route('users.index')
                        ->with('success', 'Utilisateur supprimé avec succès.');
    }
}
