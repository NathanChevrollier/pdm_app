<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\PointageController;
use App\Http\Controllers\SalaireController;
use App\Http\Controllers\StaticPageController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\ObjectifController;
use Illuminate\Support\Facades\Route;

// Page d'accueil redirige vers le tableau de bord
Route::get('/', function () {
    return redirect()->route('dashboard');
});

// Pages statiques accessibles à tous
Route::get('/confidentialite', [StaticPageController::class, 'privacy'])->name('privacy');
Route::get('/conditions-generales', [StaticPageController::class, 'terms'])->name('terms');

// Authentification
require __DIR__.'/auth.php';

// Routes protégées par authentification
Route::middleware(['auth'])->group(function () {
    // Routes accessibles à tous les utilisateurs authentifiés
    Route::get('/salaires', [SalaireController::class, 'index'])->name('salaires.index');
    
    // Profil utilisateur - accessible à tous les utilisateurs authentifiés
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
    
    // Appliquer le middleware de redirection pour les utilisateurs doj
    Route::middleware([\App\Http\Middleware\RedirectDojUsers::class])->group(function () {
        
        // Routes accessibles uniquement aux utilisateurs qui ne sont pas doj
        Route::middleware([\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant,co-gerant,manager,vendeur,stagiaire'])->group(function () {
            // Tableau de bord
            Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
                ->name('dashboard');

            // Gestion des véhicules
            Route::resource('vehicules', VehiculeController::class);
            
            // Gestion des commandes
            Route::resource('commandes', CommandeController::class);
            Route::get('/commandes/export', [CommandeController::class, 'export'])
                ->name('commandes.export');
                
            // Tableau de bord personnel des employés
            Route::get('/mon-tableau-de-bord', [UserController::class, 'tableauDeBord'])->name('users.tableau-de-bord');
            
            // Objectifs - Mise à jour des objectifs de la semaine
            Route::post('/objectifs/update', [SalaireController::class, 'updateObjectifs'])->name('objectifs.update');
        });
    });
    
    // Gestion des utilisateurs (pour les administrateurs et gérants)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant']], function () {
        Route::resource('users', UserController::class);
    });
    
    // Salaires - Actions de modification (accessibles uniquement à admin, gérant, co-gérant et manager)
    Route::prefix('salaires')->middleware([\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant,co-gerant,manager'])->group(function () {
        Route::get('/create', [SalaireController::class, 'create'])->name('salaires.create');
        Route::post('/', [SalaireController::class, 'store'])->name('salaires.store');
        Route::get('/{salaire}/edit', [SalaireController::class, 'edit'])->name('salaires.edit');
        Route::put('/{salaire}', [SalaireController::class, 'update'])->name('salaires.update');
        Route::delete('/{salaire}', [SalaireController::class, 'destroy'])->name('salaires.destroy');
        Route::post('/marquer-paye', [SalaireController::class, 'marquerPaye'])->name('salaires.marquer-paye');
        Route::post('/deductions-taxes', [SalaireController::class, 'updateDeductionsTaxes'])->name('salaires.deductions-taxes');
    });

    // Gestion des utilisateurs
    // Accès à la liste et aux détails pour tous les utilisateurs sauf doj
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant,co-gerant,manager,vendeur,stagiaire']], function () {
        Route::get('/users', [UserController::class, 'index'])->name('users.index');
        Route::get('/users/{user}', [UserController::class, 'show'])->name('users.show');
    });
    
    // Journal des activités (pour les administrateurs et gérants)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant']], function () {
        Route::get('/activites', [\App\Http\Controllers\ActiviteController::class, 'index'])->name('activites.index');
        Route::get('/activites/export', [\App\Http\Controllers\ActiviteController::class, 'export'])->name('activites.export');
        Route::get('/activites/clear', [\App\Http\Controllers\ActiviteController::class, 'clear'])->name('activites.clear');
        Route::get('/activites/{activite}', [\App\Http\Controllers\ActiviteController::class, 'show'])->name('activites.show');
        Route::delete('/activites/{activite}', [\App\Http\Controllers\ActiviteController::class, 'destroy'])->name('activites.destroy');
    });
    
    // Gestion des objectifs (pour admin, gérant, co-gérant et manager)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant,co-gerant,manager']], function () {
        // Renommage de la route pour éviter les conflits
        Route::post('/objectifs/global', [ObjectifController::class, 'update'])->name('objectifs.global.update');
        Route::post('/objectifs/user/{user}', [ObjectifController::class, 'updateUserObjectifs'])->name('objectifs.user.update');
    });
    
    // Route de test pour vérifier le layout
    Route::get('/test-layout', function() {
        return view('test-layout');
    })->name('test.layout');
    
    // Page de contact
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
    
    // Système de badgeuse
    Route::prefix('pointages')->group(function () {
        Route::get('/', [PointageController::class, 'index'])->name('pointages.index');
        Route::post('/entree', [PointageController::class, 'entree'])->name('pointages.entree');
        Route::post('/sortie', [PointageController::class, 'sortie'])->name('pointages.sortie');
        Route::post('/deconnexion-auto', [PointageController::class, 'deconnexionAuto'])->name('pointages.deconnexion-auto');
        
        // Routes pour les managers, co-gérants et gérants
        Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,manager,gerant,co-gerant']], function () {
            // Placer les routes spécifiques avant les routes avec paramètres génériques
            Route::get('/stats-employe/{id}', [PointageController::class, 'getStatsEmploye'])->name('pointages.stats-employe');
            
            // Routes avec paramètres génériques
            Route::get('/{id}/edit', [PointageController::class, 'edit'])->name('pointages.edit');
            Route::put('/{id}', [PointageController::class, 'corriger'])->name('pointages.corriger');
            Route::post('/{id}/incomplet', [PointageController::class, 'marquerIncomplet'])->name('pointages.incomplet');
        });
    });
});

// Route de test pour vérifier le problème de CSRF
Route::get('/test-csrf', function () {
    return view('test-csrf');
});

Route::post('/test-csrf-post', function () {
    return 'CSRF test passed!';
})->name('test-csrf-post');

// Redirection des routes non trouvées vers le tableau de bord
Route::fallback(function () {
    return redirect()->route('dashboard');
});
