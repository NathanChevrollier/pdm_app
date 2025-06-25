<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />
    
    <!-- Logo Premium Deluxe Motorsport -->
    <div class="app-brand justify-content-center mb-4">
        <a href="{{ route('login') }}" class="app-brand-link gap-2">
            <span class="app-brand-logo demo">
                <div style="font-size: 3rem; color: #696cff;">
                    <i class="bx bxs-car-garage"></i>
                </div>
            </span>
            <span class="app-brand-text demo text-body" style="font-family: 'Montserrat', sans-serif; font-weight: 700; font-size: 1.5rem; letter-spacing: 0.5px; background: linear-gradient(45deg, #696cff, #8592d8); -webkit-background-clip: text; -webkit-text-fill-color: transparent; text-transform: uppercase;">Premium Deluxe<br>Motorsport</span>
        </a>
    </div>
    
    <div class="text-center mb-4">
        <h1 class="fs-3 fw-bold mb-2">Connexion à l'espace employé</h1>
        <p class="text-muted">
            Accédez à votre tableau de bord de gestion des ventes
        </p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="mx-auto" style="max-width: 400px;">
        @csrf

        <!-- Email -->
        <div class="mb-3">
            <x-input-label for="email" :value="__('Adresse email')" />
            <div>
                <x-text-input 
                    id="email" 
                    class="form-control" 
                    type="email" 
                    name="email" 
                    :value="old('email')" 
                    required 
                    autofocus 
                    autocomplete="email" 
                    placeholder="votre@email.com"
                />
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <!-- Mot de passe -->
        <div class="mb-3">
            <div class="d-flex justify-content-between align-items-center">
                <x-input-label for="password" :value="__('Mot de passe')" />
            </div>
            <div>
                <x-text-input 
                    id="password" 
                    class="form-control" 
                    type="password" 
                    name="password" 
                    required 
                    autocomplete="current-password"
                    placeholder="••••••••"
                />
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Se souvenir de moi -->
        <div class="mb-3">
            <div class="form-check">
                <input id="remember_me" 
                       name="remember" 
                       type="checkbox" 
                       class="form-check-input"
                >
                <label for="remember_me" class="form-check-label">
                    {{ __('Se souvenir de moi') }}
                </label>
            </div>
        </div>

        <div class="d-grid gap-2 mb-3">
            <x-primary-button class="btn-lg">
                {{ __('Se connecter') }}
            </x-primary-button>
        </div>
    </form>

    <!-- Le lien vers la page d'inscription a été supprimé intentionnellement -->
</x-guest-layout>
