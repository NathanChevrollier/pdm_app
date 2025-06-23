<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\VehiculeController;
use App\Http\Controllers\EmployeController;
use App\Http\Controllers\CommandeController;
use App\Http\Controllers\ContactController;
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
    // Tableau de bord
    Route::get('/dashboard', [\App\Http\Controllers\DashboardController::class, 'index'])
        ->name('dashboard');

    // Profil utilisateur
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Gestion des véhicules
    Route::resource('vehicules', VehiculeController::class);

    // Gestion des employés (uniquement pour les administrateurs)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin']], function () {
        Route::resource('employes', EmployeController::class);
    });
    
    // Tableau de bord personnel des employés
    Route::middleware('auth')->group(function () {
        Route::get('/mon-tableau-de-bord', [EmployeController::class, 'tableauDeBord'])->name('users.tableau-de-bord');
    });

    // Gestion des commandes
    Route::resource('commandes', CommandeController::class);
    Route::get('/commandes/export', [CommandeController::class, 'export'])
        ->name('commandes.export');

    // Gestion des salaires (uniquement pour les administrateurs)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin']], function () {
        Route::resource('salaires', SalaireController::class);
        Route::get('/salaires/generer', [SalaireController::class, 'generer'])->name('salaires.generer');
        Route::get('/salaires/export', [SalaireController::class, 'export'])->name('salaires.export');
        Route::get('/salaires/fiches', [SalaireController::class, 'fiches'])->name('salaires.fiches');
        Route::get('/salaires/statistiques', [SalaireController::class, 'statistiques'])->name('salaires.statistiques');
        Route::get('/salaires/{salaire}/payer', [SalaireController::class, 'payer'])->name('salaires.payer');
        Route::post('/salaires/marquer-paye', [SalaireController::class, 'marquerPaye'])->name('salaires.marquer-paye');
        Route::post('/salaires/deductions-taxes', [SalaireController::class, 'updateDeductionsTaxes'])->name('salaires.deductions-taxes');
    });
    
    // Gestion des utilisateurs (uniquement pour les administrateurs)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin']], function () {
        Route::resource('users', UserController::class);
    });
    
    // Journal des activités (uniquement pour les administrateurs)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin']], function () {
        Route::get('/activites', [\App\Http\Controllers\ActiviteController::class, 'index'])->name('activites.index');
        Route::get('/activites/export', [\App\Http\Controllers\ActiviteController::class, 'export'])->name('activites.export');
        Route::get('/activites/clear', [\App\Http\Controllers\ActiviteController::class, 'clear'])->name('activites.clear');
        Route::get('/activites/{activite}', [\App\Http\Controllers\ActiviteController::class, 'show'])->name('activites.show');
        Route::delete('/activites/{activite}', [\App\Http\Controllers\ActiviteController::class, 'destroy'])->name('activites.destroy');
    });
    
    // Gestion des objectifs (pour admin, gérant, co-gérant et manager)
    Route::group(['middleware' => [\App\Http\Middleware\CheckUserStatut::class . ':admin,gerant,co-gerant,manager']], function () {
        Route::post('/objectifs/update', [ObjectifController::class, 'update'])->name('objectifs.update');
        Route::post('/objectifs/user/{user}', [ObjectifController::class, 'updateUserObjectifs'])->name('objectifs.user.update');
    });
    
    // Route de test pour vérifier le layout
    Route::get('/test-layout', function() {
        return view('test-layout');
    })->name('test.layout');
    
    // Page de contact
    Route::get('/contact', [ContactController::class, 'index'])->name('contact');
});

// Redirection des routes non trouvées vers le tableau de bord
Route::fallback(function () {
    return redirect()->route('dashboard');
});
