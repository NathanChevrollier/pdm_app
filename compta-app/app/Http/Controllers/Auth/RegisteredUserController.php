<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'nom' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'statut' => ['required', 'in:admin,employe'],
            'commission' => ['required_if:statut,employe', 'numeric', 'between:0,100', 'nullable'],
        ]);

        // Seuls les administrateurs peuvent créer des comptes administrateur
        if ($request->statut === 'admin' && !(auth()->check() && auth()->user()->isAdmin())) {
            return back()->with('error', 'Seuls les administrateurs peuvent créer des comptes administrateur.');
        }

        $user = User::create([
            'nom' => $request->nom,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'statut' => $request->statut,
            'commission' => $request->statut === 'employe' ? $request->commission : 0,
        ]);

        event(new Registered($user));

        // Si c'est un nouvel utilisateur qui s'inscrit, on le connecte
        if (!auth()->check()) {
            Auth::login($user);
            return redirect(route('dashboard', absolute: false));
        }

        // Si c'est un admin qui crée un compte, on reste sur la page d'administration
        return back()->with('success', 'Utilisateur créé avec succès.');
    }
}
