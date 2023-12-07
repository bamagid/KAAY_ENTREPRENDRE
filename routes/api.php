<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SecteurController;
use App\Http\Controllers\EvenementController;
use App\Http\Controllers\RessourceController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});

Route::middleware('auth:api')->group(function(){
    Route::post('ajouter-ressource',[RessourceController::class,'ajouterRessource'])->name('ajouter-ressource');
    Route::post('/ressources/{id}', [RessourceController::class, 'modifierRessource']);
    Route::delete('/ressources/{id}', [RessourceController::class, 'supprimerRessource']);

    //route pour evenement 
    Route::get('/events', [EvenementController::class, 'index']);
Route::get('/events/{id}', [EvenementController::class, 'show']);
Route::post('/events', [EvenementController::class, 'store']);
Route::post('/events/{id}', [EvenementController::class, 'update']);
Route::delete('/events/{id}', [EvenementController::class, 'destroy']);
Route::post('/secteurs', [SecteurController::class, 'store']);
Route::delete('/secteurs/{id}', [SecteurController::class, 'destroy']);
});


    Route::post('/ajouter-role', [UserController::class, 'ajouterRole']);
    Route::post('/ajouter-utilisateur-entrepreneur-novice', [UserController::class, 'ajouterUtilisateurEntrepreneurNovice']);
    Route::post('/ajouter-utilisateur-entrepreneur-experimente', [UserController::class,'ajouterUtilisateurEntrepreneurExperimente']);
    Route::post('/ajouter-utilisateur-admin', [UserController::class,'ajouterUtilisateurAdmin']);
    Route::post('login', [UserController::class, 'login']);