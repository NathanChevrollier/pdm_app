<x-guest-layout>
    <form method="POST" action="{{ route('register') }}" class="space-y-4">
        @csrf

        <!-- Nom -->
        <div>
            <x-input-label for="nom" :value="__('Nom complet')" />
            <x-text-input id="nom" class="block mt-1 w-full" type="text" name="nom" :value="old('nom')" required autofocus autocomplete="name" />
            <x-input-error :messages="$errors->get('nom')" class="mt-2" />
        </div>

        <!-- Email -->
        <div>
            <x-input-label for="email" :value="__('Email')" />
            <x-text-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email')" required autocomplete="username" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
            <!-- Statut (uniquement visible par les administrateurs) -->
            <div>
                <x-input-label for="statut" :value="__('Rôle')" />
                <select id="statut" name="statut" class="block mt-1 w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 dark:focus:border-indigo-600 focus:ring-indigo-500 dark:focus:ring-indigo-600 rounded-md shadow-sm">
                    <option value="employe" {{ old('statut') == 'employe' ? 'selected' : '' }}>Employé</option>
                    <option value="admin" {{ old('statut') == 'admin' ? 'selected' : '' }}>Administrateur</option>
                </select>
                <x-input-error :messages="$errors->get('statut')" class="mt-2" />
            </div>

            <!-- Commission (uniquement pour les employés) -->
            <div id="commission-field" style="display: none;">
                <x-input-label for="commission" :value="__('Commission (%)')" />
                <x-text-input id="commission" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100" name="commission" :value="old('commission')" />
                <x-input-error :messages="$errors->get('commission')" class="mt-2" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pourcentage de commission pour les ventes (entre 0 et 100)</p>
            </div>
        @else
            <!-- Champ caché pour les nouveaux utilisateurs (par défaut employé) -->
            <input type="hidden" name="statut" value="employe">
            
            <!-- Commission pour les nouveaux employés -->
            <div>
                <x-input-label for="commission" :value="__('Commission (%)')" />
                <x-text-input id="commission" class="block mt-1 w-full" type="number" step="0.01" min="0" max="100" name="commission" :value="old('commission', 0)" required />
                <x-input-error :messages="$errors->get('commission')" class="mt-2" />
                <p class="mt-1 text-sm text-gray-500 dark:text-gray-400">Pourcentage de commission pour vos ventes (entre 0 et 100)</p>
            </div>
        @endif

        <!-- Mot de passe -->
        <div>
            <x-input-label for="password" :value="__('Mot de passe')" />
            <x-text-input id="password" class="block mt-1 w-full"
                            type="password"
                            name="password"
                            required autocomplete="new-password" />
            <x-input-error :messages="$errors->get('password')" class="mt-2" />
        </div>

        <!-- Confirmation du mot de passe -->
        <div>
            <x-input-label for="password_confirmation" :value="__('Confirmer le mot de passe')" />
            <x-text-input id="password_confirmation" class="block mt-1 w-full"
                            type="password"
                            name="password_confirmation" required />
            <x-input-error :messages="$errors->get('password_confirmation')" class="mt-2" />
        </div>

        <div class="flex items-center justify-between mt-6">
            @if(auth()->check())
                <a href="{{ route('employes.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800">
                    {{ __('Retour à la liste') }}
                </a>
            @else
                <a class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                    {{ __('Déjà inscrit ?') }}
                </a>
            @endif

            <x-primary-button>
                {{ auth()->check() ? __('Créer l\'utilisateur') : __('S\'inscrire') }}
            </x-primary-button>
        </div>

        @if(auth()->check() && auth()->user()->isAdmin())
        <script>
            // Afficher/masquer le champ commission en fonction du rôle
            document.getElementById('statut').addEventListener('change', function() {
                const commissionField = document.getElementById('commission-field');
                const commissionInput = document.getElementById('commission');
                
                if (this.value === 'employe') {
                    commissionField.style.display = 'block';
                    commissionInput.required = true;
                } else {
                    commissionField.style.display = 'none';
                    commissionInput.required = false;
                    commissionInput.value = '0';
                }
            });

            // Déclencher l'événement au chargement de la page
            document.addEventListener('DOMContentLoaded', function() {
                document.getElementById('statut').dispatchEvent(new Event('change'));
            });
        </script>
        @endif
    </form>
</x-guest-layout>
