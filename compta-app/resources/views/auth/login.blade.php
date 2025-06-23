<x-guest-layout>
    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-4">
        <h1 class="fs-2 fw-bold mb-2">Connexion à l'espace employé</h1>
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
                @if (Route::has('password.request'))
                    <a class="small text-primary" href="{{ route('password.request') }}">
                        {{ __('Mot de passe oublié ?') }}
                    </a>
                @endif
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

    <div class="mt-4 text-center">
        <p class="text-muted small">
            Pas encore de compte ?
            <a href="{{ route('register') }}" class="font-medium text-primary-600 hover:text-primary-500 dark:text-primary-400 dark:hover:text-primary-300">
                {{ __('Créer un compte') }}
            </a>
        </p>
    </div>
</x-guest-layout>
